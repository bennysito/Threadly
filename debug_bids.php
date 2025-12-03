<?php
session_start();

require_once "Back_End/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

echo "<h2>DEBUG: Bids System</h2>";

// Check bids table structure
echo "<h3>1. Bids Table Structure:</h3>";
$result = $conn->query('SHOW COLUMNS FROM bids');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
}

// Check users table structure
echo "<h3>2. Users Table Structure (ID column):</h3>";
$result = $conn->query('SHOW COLUMNS FROM users LIKE "%id%"');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
}

// Check products table structure
echo "<h3>3. Products Table Structure (seller_id):</h3>";
$result = $conn->query('SHOW COLUMNS FROM products LIKE "%seller%"');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
}

// Count all bids
echo "<h3>4. Total Bids in Database:</h3>";
$result = $conn->query('SELECT COUNT(*) as count FROM bids');
$row = $result->fetch_assoc();
echo "Total: " . $row['count'] . " bids<br>";

// Show all bids with product info
echo "<h3>5. All Bids with Product Info:</h3>";
$result = $conn->query('
    SELECT 
        b.bid_id, 
        b.product_id, 
        b.user_id,
        b.bid_amount,
        p.product_name,
        p.seller_id
    FROM bids b
    LEFT JOIN products p ON b.product_id = p.product_id
    ORDER BY b.bid_id DESC
');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Bid #" . $row['bid_id'] . 
             " | Product: " . $row['product_name'] . 
             " | Product ID: " . $row['product_id'] . 
             " | Seller ID: " . $row['seller_id'] . 
             " | Bidder User ID: " . $row['user_id'] . 
             " | Amount: " . $row['bid_amount'] . "<br>";
    }
}

// If logged in, show seller's products
if (isset($_SESSION['user_id'])) {
    $seller_id = $_SESSION['user_id'];
    echo "<h3>6. Your (Seller ID: $seller_id) Products:</h3>";
    $result = $conn->query("SELECT product_id, product_name, seller_id FROM products WHERE seller_id = $seller_id");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Product #" . $row['product_id'] . ": " . $row['product_name'] . " (Seller: " . $row['seller_id'] . ")<br>";
        }
    } else {
        echo "You have no products.<br>";
    }
    
    echo "<h3>7. Bids on YOUR Products:</h3>";
    $result = $conn->query("
        SELECT 
            b.bid_id, 
            b.product_id,
            p.product_name,
            b.bid_amount,
            b.user_id
        FROM bids b
        JOIN products p ON b.product_id = p.product_id
        WHERE p.seller_id = $seller_id
    ");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Bid #" . $row['bid_id'] . " on " . $row['product_name'] . " for ₱" . $row['bid_amount'] . " (User: " . $row['user_id'] . ")<br>";
        }
    } else {
        echo "No bids on your products.<br>";
    }
} else {
    echo "<h3>6. Not logged in</h3>";
}
?>
