<?php
require_once 'Back_End/Models/Database.php';
$db = new Database();
$conn = $db->threadly_connect;

// Check if bidding_session table exists
$tableCheck = $conn->query('SHOW TABLES LIKE "bidding_session"');
if ($tableCheck->num_rows == 0) {
    echo 'ERROR: bidding_session table does not exist';
    exit;
}

// Check bidding_session structure
echo "\n=== Bidding Session Table Structure ===\n";
$colRes = $conn->query('SHOW COLUMNS FROM bidding_session');
while ($row = $colRes->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

// Check if any sessions exist
echo "\n=== Bidding Sessions ===\n";
$sessions = $conn->query('SELECT * FROM bidding_session LIMIT 10');
if ($sessions->num_rows == 0) {
    echo "No sessions found in database!\n";
} else {
    while ($row = $sessions->fetch_assoc()) {
        echo "Session ID: " . $row['session_id'] . ", Product ID: " . $row['product_id'] . ", Status: " . $row['status'] . "\n";
    }
}

// Check if existing bids have session_id references
echo "\n=== Bids with Session References ===\n";
$bidsWithSession = $conn->query('
    SELECT b.bid_id, b.session_id, b.user_id, b.bid_amount, bs.product_id
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
');
if ($bidsWithSession->num_rows == 0) {
    echo "No bids found!\n";
} else {
    while ($row = $bidsWithSession->fetch_assoc()) {
        echo "Bid ID: " . $row['bid_id'] . ", Session: " . $row['session_id'] . ", Product ID: " . ($row['product_id'] ?? 'NULL') . "\n";
    }
}

echo "\n=== Raw Bids Table ===\n";
$allBids = $conn->query('SELECT * FROM bids');
echo "Total bids: " . $allBids->num_rows . "\n";
while ($row = $allBids->fetch_assoc()) {
    echo "Bid ID: " . $row['bid_id'] . ", Session ID: " . $row['session_id'] . ", User: " . $row['user_id'] . ", Amount: " . $row['bid_amount'] . "\n";
}
?>
