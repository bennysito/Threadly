<?php
require_once 'Database.php';

class Search {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->get_connection();
    }

    // Search by name/description
    public function search($query, $limit = 50) {
        $query = "%$query%";
        $stmt = $this->conn->prepare("
            SELECT p.product_id AS id, 
                   p.product_name AS name, 
                   p.image_url AS image,
                   p.image_url AS hover_image,
                   p.price, 
                   c.name AS category
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.product_name LIKE ? OR p.description LIKE ?
            LIMIT ?
        ");
        $stmt->bind_param("ssi", $query, $query, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }

    // Get recent products
    public function getRecent($limit = 24) {
        $stmt = $this->conn->prepare("
            SELECT p.product_id AS id, 
                   p.product_name AS name, 
                   p.image_url AS image,
                   p.image_url AS hover_image,
                   p.price, 
                   c.name AS category,
                   p.availability,
                   p.created_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.availability = 'available'
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }

    // Get single product by ID
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT p.product_id AS id, 
                   p.product_name AS name, 
                   p.description,
                   p.image_url AS image,
                   p.price, 
                   c.name AS category,
                   p.availability,
                   p.created_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.product_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        return $product;
    }

    // Get products by CATEGORY NAME (e.g. "Dresses")
    public function getByCategory($categoryName, $limit = 24) {
        $stmt = $this->conn->prepare("
            SELECT p.product_id AS id, 
                   p.product_name AS name, 
                   p.image_url AS image,
                   p.image_url AS hover_image,
                   p.price, 
                   c.name AS category,
                   p.availability
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE c.name = ? AND p.availability = 'available'
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("si", $categoryName, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }

    // Get all products
    public function getAllProducts($limit = 100) {
        $stmt = $this->conn->prepare("
            SELECT p.product_id AS id, 
                   p.product_name AS name, 
                   p.image_url AS image,
                   p.image_url AS hover_image,
                   p.price, 
                   c.name AS category
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.availability = 'available'
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }
}
?>