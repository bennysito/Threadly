<?php
session_start();
require_once 'Back_End/Models/Database.php';
require_once 'Back_End/Models/Bidding.php';

$db = new Database();
$conn = $db->threadly_connect;

echo "=== BIDS TABLE DIAGNOSTIC ===\n\n";

// Check table structure
echo "1. BIDS TABLE STRUCTURE:\n";
$result = $conn->query('DESC bids');
if ($result) {
    echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Check if bidding_session table exists
echo "2. BIDDING_SESSION TABLE STRUCTURE:\n";
$result = $conn->query('DESC bidding_session');
if ($result) {
    echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "No bidding_session table found\n\n";
}

// Count bids
echo "3. TOTAL BIDS IN DATABASE:\n";
$result = $conn->query('SELECT COUNT(*) as total FROM bids');
$row = $result->fetch_assoc();
echo "Total bids: " . $row['total'] . "\n\n";

// Show all bids
echo "4. ALL BIDS DATA (with joins):\n";
$result = $conn->query('
    SELECT b.*, 
    IFNULL(p.product_name, "NO PRODUCT") as product_name,
    IFNULL(u.username, "NO USER") as username
    FROM bids b
    LEFT JOIN products p ON b.product_id = p.product_id
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN users u ON b.user_id = u.user_id
    LIMIT 10
');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n5. BIDS FOR LOGGED IN USER (if any):\n";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "Current user_id: $user_id\n\n";
    
    // Test Bidding class method
    echo "Testing Bidding->getUserBids($user_id):\n";
    $bidding = new Bidding();
    $bids = $bidding->getUserBids($user_id);
    echo "Result count: " . count($bids) . "\n";
    echo json_encode($bids, JSON_PRETTY_PRINT) . "\n";
    
} else {
    echo "Not logged in\n";
}
?>
