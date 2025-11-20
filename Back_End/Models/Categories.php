<?php
require_once __DIR__ . "/Database.php";

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllCategories() {
        $result = $this->db->threadly_connect->query("SELECT * FROM categories");
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }
}
