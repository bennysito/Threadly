<?php
require_once __DIR__ . "/Database.php";

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Search products by term. Matches name and description with LIKE.
     * Returns array of associative arrays with product data.
     */
    public function searchProducts(string $term, int $limit = 50) {
        $conn = $this->db->threadly_connect;

        $sql = "SELECT id, name, image, hover_image, price, category FROM products WHERE name LIKE ? OR description LIKE ? LIMIT ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log('Products->searchProducts prepare failed: ' . $conn->error);
            return [];
        }

        $like = '%' . $term . '%';
        $stmt->bind_param('ssi', $like, $like, $limit);
        $stmt->execute();
        $res = $stmt->get_result();

        $products = [];
        while ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        $this->db->close_db();
        return $products;
    }

    public function getProductById(int $id) {
        $conn = $this->db->threadly_connect;
        $sql = "SELECT id, name, image, hover_image, price, category, description FROM products WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();
        $stmt->close();
        $this->db->close_db();
        return $product ?: null;
    }
}

?>
