<?php
require_once __DIR__ . "/Database.php";

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function searchProducts(string $term = '', int $limit = 50) {
        $conn = $this->db->threadly_connect;

        if (empty(trim($term))) {
            $sql = "SELECT 
                        p.id AS id,
                        p.product_name AS name,
                        p.image_url AS image,
                        p.image_url AS hover_image,
                        p.price,
                        p.category_id AS category,
                        p.description,
                        c.name AS category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    ORDER BY p.id DESC
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $limit);
        } else {
            $sql = "SELECT 
                        p.id AS id,
                        p.product_name AS name,
                        p.image_url AS image,
                        p.image_url AS hover_image,
                        p.price,
                        p.category_id AS category,
                        p.description,
                        c.name AS category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.product_name LIKE ? OR p.description LIKE ?
                    ORDER BY p.id DESC
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $like = '%' . $term . '%';
            $stmt->bind_param('ssi', $like, $like, $limit);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        $this->db->close_db();
        return $products;
    }

    public function getProductById(int $id) {
        $conn = $this->db->threadly_connect;

        $sql = "SELECT 
                    p.id,
                    p.product_name AS name,
                    p.image_url AS image,
                    p.image_url AS hover_image,
                    p.price,
                    p.category_id AS category,
                    p.description,
                    p.availability,
                    c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();
        $this->db->close_db();
        return $product ?: null;
    }

    public function getAllProducts(int $limit = 100) {
        return $this->searchProducts('', $limit);
    }
}
?>