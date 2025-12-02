<?php
// test_bidding_setup.php
// Test script to verify bidding system setup

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/Back_End/Models/Database.php";
require_once __DIR__ . "/Back_End/Models/Bidding.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bidding System Setup Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { background: #d4edda; border-color: #28a745; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>🧪 Bidding System Setup Test</h1>
    
    <?php
    
    // Test 1: Database Connection
    echo '<div class="test">';
    echo '<h2>Test 1: Database Connection</h2>';
    try {
        $db = new Database();
        $conn = $db->threadly_connect;
        if ($conn) {
            echo '<div class="success">✓ Database connected successfully</div>';
        } else {
            echo '<div class="error">✗ Database connection failed</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Error: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 2: Create Bidding Table
    echo '<div class="test">';
    echo '<h2>Test 2: Create Bidding Table</h2>';
    try {
        $bidding = new Bidding();
        if ($bidding->createBiddingTable()) {
            echo '<div class="success">✓ Bidding table created or already exists</div>';
        } else {
            echo '<div class="error">✗ Failed to create bidding table</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Error: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 3: Check Table Structure
    echo '<div class="test">';
    echo '<h2>Test 3: Check Table Structure</h2>';
    try {
        $db = new Database();
        $conn = $db->threadly_connect;
        $result = $conn->query("DESCRIBE bids");
        
        if ($result && $result->num_rows > 0) {
            echo '<div class="success">✓ Bidding table structure verified</div>';
            echo '<table style="border-collapse: collapse; width: 100%; margin-top: 10px;">';
            echo '<tr style="background: #f5f5f5;"><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Field</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Type</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr><td style="border: 1px solid #ddd; padding: 8px;">' . $row['Field'] . '</td><td style="border: 1px solid #ddd; padding: 8px;">' . $row['Type'] . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error">✗ Bidding table not found</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Error: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 4: Test Bid Placement (with sample data)
    echo '<div class="test">';
    echo '<h2>Test 4: Sample Bid Placement</h2>';
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="info">ℹ️ Not logged in. Cannot test bid placement. Log in and try again.</div>';
    } else {
        try {
            $bidding = new Bidding();
            $testResult = $bidding->placeBid(1, $_SESSION['user_id'], 1000.00, 'Test bid');
            
            if ($testResult['success']) {
                echo '<div class="success">✓ Bid placement test successful (Bid ID: ' . $testResult['bid_id'] . ')</div>';
            } else {
                echo '<div class="error">✗ Bid placement failed: ' . $testResult['message'] . '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error: ' . $e->getMessage() . '</div>';
        }
    }
    echo '</div>';
    
    // Test 5: Check Required Tables
    echo '<div class="test">';
    echo '<h2>Test 5: Check Required Tables</h2>';
    try {
        $db = new Database();
        $conn = $db->threadly_connect;
        
        $tables = ['products', 'users', 'bids'];
        foreach ($tables as $table) {
            $result = $conn->query("SELECT 1 FROM $table LIMIT 1");
            if ($result !== false) {
                echo '<div class="success">✓ Table "' . $table . '" exists</div>';
            } else {
                echo '<div class="error">✗ Table "' . $table . '" missing or inaccessible</div>';
            }
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Error: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Summary
    echo '<div class="test info">';
    echo '<h2>📋 Summary</h2>';
    echo '<p>If all tests passed, your bidding system is ready!</p>';
    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ul>';
    echo '<li>1. Log in to your account</li>';
    echo '<li>2. Go to any product page</li>';
    echo '<li>3. Scroll to "Make an Offer (Bidding)" section</li>';
    echo '<li>4. Enter a bid amount and click "PLACE BID"</li>';
    echo '</ul>';
    echo '</div>';
    
    ?>
    
</body>
</html>
