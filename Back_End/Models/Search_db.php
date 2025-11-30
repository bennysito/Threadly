<?php
require_once 'Database.php';

class Search {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->get_connection();
    }

    /**
     * Search products by name or description
     */
    public function search($query, $limit = 50) {
        $query = "%$query%";
        $stmt = $this->conn->prepare("
            SELECT product_id AS id, 
                   product_name AS name, 
                   image_url AS image, 
                   price, 
                   category_id AS category, 
                   availability
            FROM products
            WHERE product_name LIKE ? OR description LIKE ?
            LIMIT ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ssi", $query, $query, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        return $products;
    }

    /**
     * Get recent products (for Daily Discover section)
     */
    public function getRecent($limit = 12) {
        $stmt = $this->conn->prepare("
            SELECT product_id AS id, 
                   product_name AS name, 
                   image_url AS image, 
                   image_url AS hover_image,
                   price, 
                   category_id AS category,
                   availability,
                   created_at
            FROM products 
            WHERE availability = 'available'
            ORDER BY created_at DESC 
            LIMIT ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        return $products;
    }

    /**
     * Get product by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT product_id AS id, 
                   product_name AS name, 
                   description,
                   image_url AS image, 
                   price, 
                   category_id AS category,
                   availability,
                   created_at
            FROM products 
            WHERE product_id = ? 
            LIMIT 1
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $product = $result->fetch_assoc();
        $stmt->close();
        
        return $product;
    }

    /**
     * Get products by category
     */
    public function getByCategory($category_id, $limit = 50) {
        $stmt = $this->conn->prepare("
            SELECT product_id AS id, 
                   product_name AS name, 
                   image_url AS image, 
                   price, 
                   category_id AS category,
                   availability
            FROM products 
            WHERE category_id = ? AND availability = 'available'
            LIMIT ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ii", $category_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        return $products;
    }

    /**
     * Get all available products
     */
    public function getAllProducts($limit = 100) {
        $stmt = $this->conn->prepare("
            SELECT product_id AS id, 
                   product_name AS name, 
                   image_url AS image, 
                   price, 
                   category_id AS category,
                   availability,
                   created_at
            FROM products 
            WHERE availability = 'available'
            ORDER BY created_at DESC 
            LIMIT ?
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        return $products;
    }
}
?>