<?php
require_once 'Back_End/Models/Database.php';

$db = new Database();
$conn = $db->threadly_connect;

echo "Bids table structure:\n";
$result = $conn->query('DESC bids');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' (' . $row['Type'] . ') - Null: ' . $row['Null'] . ', Key: ' . $row['Key'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n\nSample bids data:\n";
$sampleResult = $conn->query('SELECT * FROM bids LIMIT 5');
if ($sampleResult) {
    while($row = $sampleResult->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}
?>
