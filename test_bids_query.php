<?php
session_start();
$_SESSION['user_id'] = 28; // Set test seller ID

require_once "Back_End/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;
$user_id = 28;

// Check schema
$colCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
$hasProductId = ($colCheck && $colCheck->num_rows > 0);

echo "<h2>Testing Bids Query for Seller ID: $user_id</h2>";
echo "<p>Has product_id column: " . ($hasProductId ? "YES" : "NO") . "</p>";

if (!$hasProductId) {
    echo "<h3>Using OLD SCHEMA (session_id)</h3>";
    
    // Check bidding_session table
    $sessionCheck = $conn->query("SELECT COUNT(*) as count FROM bidding_session");
    $row = $sessionCheck->fetch_assoc();
    echo "<p>Total bidding sessions: " . $row['count'] . "</p>";
    
    // Show sessions with products
    $result = $conn->query("
        SELECT bs.session_id, bs.product_id, p.product_name, p.seller_id
        FROM bidding_session bs
        LEFT JOIN products p ON bs.product_id = p.product_id
    ");
    echo "<h4>Bidding Sessions:</h4>";
    while ($row = $result->fetch_assoc()) {
        echo "Session " . $row['session_id'] . " | Product: " . $row['product_name'] . " | Seller: " . $row['seller_id'] . "<br>";
    }
    
    // Test the actual query
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
    $bidsStmt->bind_param('i', $user_id);
    $bidsStmt->execute();
    $bidsResult = $bidsStmt->get_result();
    
    echo "<h4>Bids on Your Products (Seller $user_id):</h4>";
    $count = 0;
    while ($row = $bidsResult->fetch_assoc()) {
        $count++;
        echo "Bid #" . $row['bid_id'] . " | Product: " . $row['product_name'] . " | Amount: ₱" . $row['bid_amount'] . " | Bidder: " . $row['first_name'] . " " . $row['last_name'] . "<br>";
    }
    
    if ($count == 0) {
        echo "No bids found for your products.<br>";
    }
    
    $bidsStmt->close();
}
?>
