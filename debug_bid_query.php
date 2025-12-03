<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'Back_End/Models/Database.php';
$db = new Database();
$conn = $db->threadly_connect;

$seller_id = 28;

// First verify the products
$prod = $conn->query("SELECT product_id FROM products WHERE seller_id = $seller_id");
echo "Products for seller $seller_id: " . $prod->num_rows . "\n";

// Test without users join first
$test1 = $conn->query("
    SELECT 
        b.bid_id, 
        bs.product_id, 
        b.user_id,
        b.bid_amount,
        p.product_name
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    WHERE p.seller_id = $seller_id
");

echo "Bids without users join: " . $test1->num_rows . "\n";
while ($row = $test1->fetch_assoc()) {
    echo "- Bid " . $row['bid_id'] . " on product " . $row['product_id'] . " (" . $row['product_name'] . ")\n";
}

// Test with users join
$test2 = $conn->query("
    SELECT 
        b.bid_id, 
        bs.product_id, 
        b.user_id,
        b.bid_amount,
        p.product_name,
        u.username
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE p.seller_id = $seller_id
");

echo "\nBids with users join: " . $test2->num_rows . "\n";
while ($row = $test2->fetch_assoc()) {
    echo "- Bid " . $row['bid_id'] . " from user " . $row['user_id'] . " (" . $row['username'] . ")\n";
}

// Check what bid 8 looks like
echo "\n\nDirect bid 8 check:\n";
$bid8 = $conn->query("
    SELECT 
        b.bid_id,
        b.user_id,
        b.session_id,
        bs.product_id,
        p.product_name,
        p.seller_id,
        u.id as user_table_id,
        u.username
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE b.bid_id = 8
");

if ($bid8->num_rows > 0) {
    $row = $bid8->fetch_assoc();
    echo "Bid ID: " . $row['bid_id'] . "\n";
    echo "User ID (in bids): " . $row['user_id'] . "\n";
    echo "User ID (in users table): " . $row['user_table_id'] . "\n";
    echo "Username: " . $row['username'] . "\n";
    echo "Session ID: " . $row['session_id'] . "\n";
    echo "Product ID: " . $row['product_id'] . "\n";
    echo "Product Name: " . $row['product_name'] . "\n";
    echo "Seller ID: " . $row['seller_id'] . "\n";
} else {
    echo "Bid 8 not found\n";
}
?>
