<?php
require_once __DIR__ . "/Database.php";

/**
 * Search helper that adapts to existing `products` table columns.
 * It detects common column names (product_id/product_name or id/name) and
 * returns normalized rows with keys: id, name, description, image, hover_image,
 * price, category, availability, created_at.
 */
class Search {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Return true if there is a FULLTEXT index that includes all $cols on the products table.
     */
    private function hasFulltextIndex($conn, array $cols): bool {
        // Normalize
        $cols = array_values($cols);
        $res = $conn->query("SHOW INDEX FROM products");
        if (!$res) return false;

        $indexes = [];
        while ($row = $res->fetch_assoc()) {
            $idxName = $row['Key_name'];
            $indexType = strtoupper($row['Index_type'] ?? '');
            $colName = $row['Column_name'];
            if (!isset($indexes[$idxName])) {
                $indexes[$idxName] = ['type' => $indexType, 'cols' => []];
            }
            $indexes[$idxName]['cols'][] = $colName;
        }

        foreach ($indexes as $info) {
            if ($info['type'] !== 'FULLTEXT') continue;
            // check that all cols are in this index
            $hasAll = true;
            foreach ($cols as $c) {
                if (!in_array($c, $info['cols'])) { $hasAll = false; break; }
            }
            if ($hasAll) return true;
        }

        return false;
    }

    /**
     * Inspect products table columns and return an associative map of column names.
     */
    private function detectColumns($conn) {
        $cols = [];
        $res = $conn->query("SHOW COLUMNS FROM products");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $cols[] = $row['Field'];
            }
        }

        // Helper to choose a preferred column from candidates
        $pick = function(array $candidates) use ($cols) {
            foreach ($candidates as $c) {
                if (in_array($c, $cols)) return $c;
            }
            return null;
        };

        return [
            'id' => $pick(['product_id','id']),
            'name' => $pick(['product_name','name','title']),
            'description' => $pick(['description','product_description','details']),
            'image' => $pick(['image','product_image','image_url','img']),
            'hover_image' => $pick(['hover_image','hover','hover_img']),
            'price' => $pick(['price','product_price','amount']),
            'category' => $pick(['category','category_id','product_category']),
            'availability' => $pick(['availability','stock','quantity','is_active']),
            'created_at' => $pick(['created_at','created','added_at'])
        ];
    }

    /**
     * Normalize a result row to standard keys.
     */
    private function normalizeRow($row, $colsMap) {
        $out = [];
        $out['id'] = $row[$colsMap['id']] ?? ($row['id'] ?? null);
        $out['name'] = $row[$colsMap['name']] ?? ($row['name'] ?? null);
        $out['description'] = $row[$colsMap['description']] ?? ($row['description'] ?? null);
        $out['image'] = $row[$colsMap['image']] ?? ($row['image'] ?? null);
        $out['hover_image'] = $row[$colsMap['hover_image']] ?? ($row['hover_image'] ?? null);
        $out['price'] = $row[$colsMap['price']] ?? ($row['price'] ?? null);
        $out['category'] = $row[$colsMap['category']] ?? ($row['category'] ?? null);
        $out['availability'] = $row[$colsMap['availability']] ?? ($row['availability'] ?? null);
        $out['created_at'] = $row[$colsMap['created_at']] ?? ($row['created_at'] ?? null);
        return $out;
    }

    /**
     * Search products by term. Uses FULLTEXT MATCH when possible, falls back to LIKE.
     * Returns array of normalized associative arrays.
     */
    public function search(string $term, int $limit = 50): array {
        $conn = $this->db->threadly_connect;
        $term = trim($term);
        if ($term === '') return [];

        $colsMap = $this->detectColumns($conn);

        // Build select list mapping existing columns to normalized aliases
        $select = [];
        $mapPairs = [
            'id'=>'id','name'=>'name','description'=>'description','image'=>'image',
            'hover_image'=>'hover_image','price'=>'price','category'=>'category',
            'availability'=>'availability','created_at'=>'created_at'
        ];
        foreach ($mapPairs as $alias=>$colKey) {
            $col = $colsMap[$colKey];
            if ($col) {
                $select[] = "`$col` AS `$alias`";
            }
        }

        // If no known columns found, return empty
        if (empty($select)) {
            $this->db->close_db();
            return [];
        }

        $selectSql = implode(', ', $select);

        // Build search: try FULLTEXT if name and description are present and a FULLTEXT index exists
        $useFulltext = ($colsMap['name'] && $colsMap['description'] && $this->hasFulltextIndex($conn, [$colsMap['name'], $colsMap['description']]));

        if ($useFulltext) {
            $sql = "SELECT $selectSql, MATCH(`{$colsMap['name']}`, `{$colsMap['description']}`) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance
                FROM products
                WHERE MATCH(`{$colsMap['name']}`, `{$colsMap['description']}`) AGAINST(? IN NATURAL LANGUAGE MODE)
                ORDER BY relevance DESC
                LIMIT ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ssi', $term, $term, $limit);
                $stmt->execute();
                $res = $stmt->get_result();
                $out = [];
                while ($r = $res->fetch_assoc()) {
                    $out[] = $this->normalizeRow($r, $colsMap);
                }
                $stmt->close();
                $this->db->close_db();
                return $out;
            }
        }

        // Fallback to LIKE on name/description (or whichever columns present)
        $like = '%' . $term . '%';
        $whereParts = [];
        $params = [];
        $paramTypes = '';
        if ($colsMap['name']) { $whereParts[] = "`{$colsMap['name']}` LIKE ?"; $params[] = $like; $paramTypes .= 's'; }
        if ($colsMap['description']) { $whereParts[] = "`{$colsMap['description']}` LIKE ?"; $params[] = $like; $paramTypes .= 's'; }
        if (empty($whereParts)) {
            $this->db->close_db();
            return [];
        }

        $whereSql = implode(' OR ', $whereParts);
        $sql2 = "SELECT $selectSql FROM products WHERE $whereSql LIMIT ?";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            error_log('Search->search prepare failed: ' . $conn->error);
            $this->db->close_db();
            return [];
        }

        // bind params dynamically
        $types = $paramTypes . 'i';
        $params[] = $limit;
        $bind_names[] = $types;
        for ($i=0;$i<count($params);$i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array([$stmt2, 'bind_param'], $this->refValues($bind_names));
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $out2 = [];
        while ($r = $res2->fetch_assoc()) {
            $out2[] = $this->normalizeRow($r, $colsMap);
        }
        $stmt2->close();
        $this->db->close_db();
        return $out2;
    }

    /**
     * Get a single product by id (handles product_id vs id column names).
     */
    public function getById($id) {
        $conn = $this->db->threadly_connect;
        $colsMap = $this->detectColumns($conn);

        $select = [];
        $mapPairs = [
            'id'=>'id','name'=>'name','description'=>'description','image'=>'image',
            'hover_image'=>'hover_image','price'=>'price','category'=>'category',
            'availability'=>'availability','created_at'=>'created_at'
        ];
        foreach ($mapPairs as $alias=>$colKey) {
            $col = $colsMap[$colKey];
            if ($col) {
                $select[] = "`$col` AS `$alias`";
            }
        }
        if (empty($select)) { $this->db->close_db(); return null; }
        $selectSql = implode(', ', $select);

        $idCol = $colsMap['id'] ?? null;
        if (!$idCol) { $this->db->close_db(); return null; }

        $sql = "SELECT $selectSql FROM products WHERE `$idCol` = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { $this->db->close_db(); return null; }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        $this->db->close_db();
        return $row ? $this->normalizeRow($row, $colsMap) : null;
    }

    /**
     * Get products by category value (exact match). Returns normalized rows.
     */
    public function getByCategory(string $category, int $limit = 50): array {
        $conn = $this->db->threadly_connect;
        $colsMap = $this->detectColumns($conn);

        $select = [];
        $mapPairs = [
            'id'=>'id','name'=>'name','description'=>'description','image'=>'image',
            'hover_image'=>'hover_image','price'=>'price','category'=>'category',
            'availability'=>'availability','created_at'=>'created_at'
        ];
        foreach ($mapPairs as $alias=>$colKey) {
            $col = $colsMap[$colKey];
            if ($col) {
                $select[] = "`$col` AS `$alias`";
            }
        }
        if (empty($select)) { $this->db->close_db(); return []; }
        $selectSql = implode(', ', $select);

        $catCol = $colsMap['category'] ?? null;
        if (!$catCol) { $this->db->close_db(); return []; }

        $sql = "SELECT $selectSql FROM products WHERE `$catCol` = ? LIMIT ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { $this->db->close_db(); return []; }
        $stmt->bind_param('si', $category, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) { $out[] = $this->normalizeRow($r, $colsMap); }
        $stmt->close();
        $this->db->close_db();
        return $out;
    }

    /**
     * Get recent products ordered by created_at if available, else by id desc.
     */
    public function getRecent(int $limit = 12): array {
        $conn = $this->db->threadly_connect;
        $colsMap = $this->detectColumns($conn);

        $select = [];
        $mapPairs = [
            'id'=>'id','name'=>'name','description'=>'description','image'=>'image',
            'hover_image'=>'hover_image','price'=>'price','category'=>'category',
            'availability'=>'availability','created_at'=>'created_at'
        ];
        foreach ($mapPairs as $alias=>$colKey) {
            $col = $colsMap[$colKey];
            if ($col) {
                $select[] = "`$col` AS `$alias`";
            }
        }
        if (empty($select)) { $this->db->close_db(); return []; }
        $selectSql = implode(', ', $select);

        $orderBy = $colsMap['created_at'] ? "`{$colsMap['created_at']}` DESC" : ($colsMap['id'] ? "`{$colsMap['id']}` DESC" : '1');
        $sql = "SELECT $selectSql FROM products ORDER BY $orderBy LIMIT ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { $this->db->close_db(); return []; }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) { $out[] = $this->normalizeRow($r, $colsMap); }
        $stmt->close();
        $this->db->close_db();
        return $out;
    }

    // Helper for call_user_func_array with references
    private function refValues($arr) {
        $refs = [];
        foreach ($arr as $k => $v) $refs[$k] = &$arr[$k];
        return $refs;
    }
}

?>
