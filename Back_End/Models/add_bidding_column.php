<?php
// Migration: add `bidding` column to `products` table if missing
require_once __DIR__ . "/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

// Check if column exists
$res = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
if ($res && $res->num_rows > 0) {
    echo "Column 'bidding' already exists.\n";
} else {
    $sql = "ALTER TABLE products ADD COLUMN bidding TINYINT(1) NOT NULL DEFAULT 0";
    if ($conn->query($sql) === TRUE) {
        echo "Added column 'bidding' to products table.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
    }
}

$db->close_db();

?>
