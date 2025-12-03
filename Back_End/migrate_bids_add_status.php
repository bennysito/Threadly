<?php
/**
 * Migration: Add bid_status and created_at columns to old schema bids table
 */
require_once __DIR__ . '/Back_End/Models/Database.php';

$db = new Database();
$conn = $db->threadly_connect;

echo "<pre>";
echo "=== Migrating Bids Table ===\n\n";

// Check current schema
echo "Current bids table structure:\n";
$desc = $conn->query("DESC bids");
$has_bid_status = false;
$has_created_at = false;

while ($row = $desc->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    if ($row['Field'] === 'bid_status') $has_bid_status = true;
    if ($row['Field'] === 'created_at') $has_created_at = true;
}

echo "\n";

// Add bid_status column if it doesn't exist
if (!$has_bid_status) {
    echo "Adding bid_status column...\n";
    $sql = "ALTER TABLE bids ADD COLUMN bid_status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending' AFTER bid_amount";
    if ($conn->query($sql)) {
        echo "✓ bid_status column added successfully\n";
    } else {
        echo "✗ Error adding bid_status: " . $conn->error . "\n";
    }
} else {
    echo "✓ bid_status column already exists\n";
}

echo "\n";

// Add created_at column if it doesn't exist  
if (!$has_created_at) {
    echo "Adding created_at column...\n";
    $sql = "ALTER TABLE bids ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER bid_status";
    if ($conn->query($sql)) {
        echo "✓ created_at column added successfully\n";
    } else {
        echo "✗ Error adding created_at: " . $conn->error . "\n";
    }
} else {
    echo "✓ created_at column already exists\n";
}

echo "\n";

// If bit_time exists but created_at is newly added, sync the data
if (!$has_created_at) {
    echo "Syncing bit_time data to created_at...\n";
    $sql = "UPDATE bids SET created_at = bit_time WHERE bit_time IS NOT NULL";
    if ($conn->query($sql)) {
        echo "✓ Data synced successfully\n";
    } else {
        echo "✗ Error syncing data: " . $conn->error . "\n";
    }
}

echo "\n=== Migration Complete ===\n";
echo "\nUpdated bids table structure:\n";
$desc = $conn->query("DESC bids");
while ($row = $desc->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "</pre>";
?>
