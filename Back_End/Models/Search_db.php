<?php
require_once 'Database.php';

class Search {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->get_connection();
    }

    public function search($query, $limit = 50) {
        $query = "%$query%";
        $stmt = $this->conn->prepare("
            SELECT product_id, product_name, image_url, price, category_id, availability
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
}
?>
