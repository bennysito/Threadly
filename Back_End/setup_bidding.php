<?php
// setup_bidding.php
// Run this ONCE to create the bidding table in the database
// After running, delete this file for security

require_once __DIR__ . "/Models/Bidding.php";

header('Content-Type: application/json');

try {
    $bidding = new Bidding();
    
    if ($bidding->createBiddingTable()) {
        echo json_encode([
            'success' => true,
            'message' => 'Bidding table created successfully!',
            'next_step' => 'You can now delete this setup_bidding.php file from your server for security.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create bidding table'
        ]);
    }
    
    $bidding->closeConnection();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
