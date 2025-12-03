<?php
require_once 'Back_End/Models/Database.php';
$db = new Database();
$conn = $db->threadly_connect;

echo "<pre>";
echo "=== Bids Table Schema ===\n";
$desc = $conn->query("DESC bids");
while ($row = $desc->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n=== Test Query ===\n";
$seller_id = 28;
$sql = "
    SELECT 
        b.bid_id, 
        bs.product_id, 
        b.user_id,
        b.bid_amount,
        p.product_name,
        p.seller_id
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN products p ON bs.product_id = p.product_id
    WHERE p.seller_id = ?
    ORDER BY b.bit_time DESC
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $seller_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        echo "Bids found: " . $result->num_rows . "\n\n";
        while ($row = $result->fetch_assoc()) {
            echo "Bid: " . json_encode($row) . "\n";
        }
    } else {
        echo "Execute error: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "Prepare error: " . $conn->error . "\n";
}

echo "</pre>";
