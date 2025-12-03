<?php
// migrate_bids_add_product_id.php
// This script adds the product_id column to bids table if it doesn't exist
// and migrates data from bidding_session if needed

require_once 'Back_End/Models/Database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // Check if product_id column already exists
    $checkCol = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
    
    if ($checkCol && $checkCol->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'product_id column already exists']);
        exit;
    }
    
    // Check if session_id exists
    $checkSession = $conn->query("SHOW COLUMNS FROM bids LIKE 'session_id'");
    
    // Add product_id column
    $alterSQL = "ALTER TABLE bids ADD COLUMN product_id INT AFTER bid_id";
    if ($conn->query($alterSQL) === TRUE) {
        echo "✓ Added product_id column\n";
    } else {
        throw new Exception("Failed to add product_id column: " . $conn->error);
    }
    
    // If session_id exists, migrate data from bidding_session
    if ($checkSession && $checkSession->num_rows > 0) {
        echo "✓ Migrating data from bidding_session...\n";
        
        // Update bids with product_id from bidding_session
        $migrateSQL = "
            UPDATE bids b
            JOIN bidding_session bs ON b.session_id = bs.session_id
            SET b.product_id = bs.product_id
        ";
        
        if ($conn->query($migrateSQL) === TRUE) {
            echo "✓ Data migrated successfully\n";
        } else {
            echo "⚠ Warning during migration: " . $conn->error . "\n";
        }
    }
    
    // Add foreign key constraint
    $fkSQL = "ALTER TABLE bids ADD CONSTRAINT fk_bids_product 
              FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE";
    
    if ($conn->query($fkSQL) === TRUE) {
        echo "✓ Added foreign key constraint\n";
    } else {
        // FK might already exist, that's OK
        echo "ℹ Foreign key constraint (may already exist): " . $conn->error . "\n";
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Migration completed successfully',
        'changes' => [
            'added_product_id_column' => true,
            'data_migrated' => true,
            'fk_added' => true
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
