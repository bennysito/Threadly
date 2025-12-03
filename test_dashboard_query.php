<?php
require_once 'Back_End/Models/Database.php';
$db = new Database();
$conn = $db->threadly_connect;

$seller_id = 28; // The test seller

echo "=== Testing Seller Dashboard Query ===\n";

// Check users table structure
echo "\nUsers table structure:\n";
$usersCol = $conn->query("SHOW COLUMNS FROM users LIMIT 5");
while ($row = $usersCol->fetch_assoc()) {
    echo "- " . $row['Field'] . "\n";
}

// Test the exact query from seller_dashboard
echo "\n=== Running Seller Dashboard Query ===\n";
$bidsSql = "
    SELECT 
        b.bid_id, 
        bs.product_id, 
        b.user_id,
        b.bid_amount, 
        'pending' as bid_status, 
        '' as bid_message, 
        b.bit_time as created_at,
        p.product_name, 
        p.image_url, 
        p.price,
        u.username,
        u.email,
        u.contact_number,
        u.first_name,
        u.last_name
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE p.seller_id = ?
    ORDER BY b.bit_time DESC
";

$bidsStmt = $conn->prepare($bidsSql);
if ($bidsStmt) {
    $bidsStmt->bind_param('i', $seller_id);
    if ($bidsStmt->execute()) {
        $bidsResult = $bidsStmt->get_result();
        echo "Bids found: " . $bidsResult->num_rows . "\n";
        while ($row = $bidsResult->fetch_assoc()) {
            echo "Bid: " . $row['bid_id'] . " - Product: " . $row['product_name'] . " - Amount: " . $row['bid_amount'] . " - User: " . $row['username'] . "\n";
        }
    } else {
        echo "Query execute error: " . $bidsStmt->error . "\n";
    }
    $bidsStmt->close();
} else {
    echo "Query prepare error: " . $conn->error . "\n";
}

// Check what products seller 28 has
echo "\n=== Seller 28's Products ===\n";
$prodStmt = $conn->prepare("SELECT product_id, product_name, seller_id FROM products WHERE seller_id = ?");
$prodStmt->bind_param('i', $seller_id);
$prodStmt->execute();
$prodResult = $prodStmt->get_result();
echo "Products: " . $prodResult->num_rows . "\n";
while ($row = $prodResult->fetch_assoc()) {
    echo "- Product ID: " . $row['product_id'] . ", Name: " . $row['product_name'] . "\n";
}
$prodStmt->close();

// Check if bid 8 user exists
echo "\n=== Check Bid 8 User ===\n";
$bid8User = $conn->query("SELECT b.bid_id, b.user_id, u.id, u.username FROM bids b LEFT JOIN users u ON b.user_id = u.id WHERE b.bid_id = 8");
while ($row = $bid8User->fetch_assoc()) {
    echo "Bid: " . $row['bid_id'] . ", Bid user_id: " . $row['user_id'] . ", Users table id: " . $row['id'] . ", Username: " . $row['username'] . "\n";
}
?>
