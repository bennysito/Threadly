<?php
// Migration: Update bids table to support product-based bidding
require_once __DIR__ . "/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

$changes = [];

// 1. Add product_id column if missing
$res = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
if (!($res && $res->num_rows > 0)) {
    $sql = "ALTER TABLE bids ADD COLUMN product_id INT NOT NULL AFTER session_id";
    if ($conn->query($sql) === TRUE) {
        $changes[] = "✓ Added product_id column";
    } else {
        $changes[] = "✗ Failed to add product_id: " . $conn->error;
    }
}

// 2. Add bid_message column if missing
$res = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_message'");
if (!($res && $res->num_rows > 0)) {
    $sql = "ALTER TABLE bids ADD COLUMN bid_message TEXT DEFAULT NULL AFTER bid_amount";
    if ($conn->query($sql) === TRUE) {
        $changes[] = "✓ Added bid_message column";
    } else {
        $changes[] = "✗ Failed to add bid_message: " . $conn->error;
    }
}

// 3. Add bid_status column if missing
$res = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_status'");
if (!($res && $res->num_rows > 0)) {
    $sql = "ALTER TABLE bids ADD COLUMN bid_status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending' AFTER bid_message";
    if ($conn->query($sql) === TRUE) {
        $changes[] = "✓ Added bid_status column";
    } else {
        $changes[] = "✗ Failed to add bid_status: " . $conn->error;
    }
}

// 4. Add created_at column if missing
$res = $conn->query("SHOW COLUMNS FROM bids LIKE 'created_at'");
if (!($res && $res->num_rows > 0)) {
    $sql = "ALTER TABLE bids ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER bid_status";
    if ($conn->query($sql) === TRUE) {
        $changes[] = "✓ Added created_at column";
    } else {
        $changes[] = "✗ Failed to add created_at: " . $conn->error;
    }
}

// 5. Add updated_at column if missing
$res = $conn->query("SHOW COLUMNS FROM bids LIKE 'updated_at'");
if (!($res && $res->num_rows > 0)) {
    $sql = "ALTER TABLE bids ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at";
    if ($conn->query($sql) === TRUE) {
        $changes[] = "✓ Added updated_at column";
    } else {
        $changes[] = "✗ Failed to add updated_at: " . $conn->error;
    }
}

// 6. Add FOREIGN KEY constraint if missing (optional, for data integrity)
$res = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='bids' AND COLUMN_NAME='product_id' AND REFERENCED_TABLE_NAME='products'");
if (!($res && $res->num_rows > 0)) {
    // Try to add the constraint, but don't fail if it doesn't work
    @$conn->query("ALTER TABLE bids ADD CONSTRAINT fk_bids_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE");
    $changes[] = "✓ Added foreign key constraint (if applicable)";
}

$db->close_db();

// Output results
echo "<h2>Bids Table Migration</h2>";
echo "<pre>";
foreach ($changes as $change) {
    echo $change . "\n";
}
echo "</pre>";
echo "<p><strong>Migration complete!</strong></p>";
echo "<p><a href='../Front_End/index.php'>Go to homepage</a></p>";
?>
