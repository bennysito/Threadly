<?php
require_once "Back_End/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

echo "<h2>Complete Bids Analysis</h2>";

// Show bidding_session table
echo "<h3>Bidding Sessions:</h3>";
$result = $conn->query("SELECT * FROM bidding_session");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Session #" . $row['session_id'] . " | Product ID: " . $row['product_id'] . " | Status: " . $row['status'] . "<br>";
    }
} else {
    echo "No bidding sessions found.<br>";
}

// Show all bids with full details
echo "<h3>All Bids with Full Details:</h3>";
$result = $conn->query("
    SELECT 
        b.bid_id,
        b.session_id,
        b.user_id,
        b.bid_amount,
        b.bit_time,
        bs.product_id,
        p.product_name,
        p.seller_id,
        u.first_name,
        u.last_name,
        u.username
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    LEFT JOIN users u ON b.user_id = u.id
    ORDER BY b.bid_id DESC
");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Bid #" . $row['bid_id'] . 
             " | Session: " . $row['session_id'] . 
             " | Product: " . $row['product_name'] . 
             " (ID: " . $row['product_id'] . ")" .
             " | Seller: " . $row['seller_id'] . 
             " | Bidder: " . $row['first_name'] . " " . $row['last_name'] . 
             " (User: " . $row['user_id'] . ")" .
             " | Amount: ₱" . $row['bid_amount'] . "<br>";
    }
} else {
    echo "No bids found.<br>";
}

// Show seller 28's products
echo "<h3>Seller 28's Products:</h3>";
$result = $conn->query("SELECT product_id, product_name FROM products WHERE seller_id = 28");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Product #" . $row['product_id'] . ": " . $row['product_name'] . "<br>";
    }
} else {
    echo "Seller 28 has no products.<br>";
}
?>
