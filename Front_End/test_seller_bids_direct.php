<?php
session_start();
require_once __DIR__ . "/../Back_End/Models/Database.php";

// Simulate being logged in as seller 28
$_SESSION['user_id'] = 28;
$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->get_connection();

echo "<h2>Testing Bid Fetch for Seller 28</h2>";

// Check if bids table has product_id or uses session_id
$colCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
$hasProductId = ($colCheck && $colCheck->num_rows > 0);

echo "<p>Has product_id column: " . ($hasProductId ? "Yes" : "No") . "</p>";

if ($hasProductId) {
    echo "<p>Would use NEW schema query</p>";
    $bidsSql = "
        SELECT 
            b.bid_id, 
            b.product_id, 
            b.user_id,
            b.bid_amount, 
            b.bid_status, 
            b.bid_message, 
            b.created_at,
            p.product_name, 
            p.image_url, 
            p.price,
            u.username,
            u.email,
            u.contact_number,
            u.first_name,
            u.last_name
        FROM bids b
        JOIN products p ON b.product_id = p.product_id
        JOIN users u ON b.user_id = u.user_id
        WHERE p.seller_id = ?
        ORDER BY b.created_at DESC
    ";
} else {
    echo "<p>Using OLD schema query</p>";
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
}

$bidsStmt = $conn->prepare($bidsSql);
if (!$bidsStmt) {
    echo "<p style='color:red;'>PREPARE ERROR: " . $conn->error . "</p>";
    exit;
}

echo "<p>Prepared statement OK</p>";

$bidsStmt->bind_param('i', $user_id);
echo "<p>Bound param (seller_id=$user_id)</p>";

if (!$bidsStmt->execute()) {
    echo "<p style='color:red;'>EXECUTE ERROR: " . $bidsStmt->error . "</p>";
    exit;
}

echo "<p>Query executed OK</p>";

$bidsResult = $bidsStmt->get_result();
echo "<p>Got result set. Rows: " . $bidsResult->num_rows . "</p>";

if ($bidsResult->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Bid ID</th><th>Product</th><th>Bidder</th><th>Amount</th></tr>";
    while ($row = $bidsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['bid_id'] . "</td>";
        echo "<td>" . $row['product_name'] . "</td>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        echo "<td>₱" . number_format($row['bid_amount'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:orange;'>No bids found for seller 28</p>";
    
    // Debug: check what products seller 28 has
    echo "<h3>Seller 28's Products:</h3>";
    $prodStmt = $conn->prepare("SELECT product_id, product_name FROM products WHERE seller_id = ?");
    $prodStmt->bind_param('i', $user_id);
    $prodStmt->execute();
    $prodResult = $prodStmt->get_result();
    if ($prodResult->num_rows > 0) {
        echo "<ul>";
        while ($row = $prodResult->fetch_assoc()) {
            echo "<li>Product " . $row['product_id'] . ": " . $row['product_name'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No products found</p>";
    }
    $prodStmt->close();
}

$bidsStmt->close();
?>
