<?php
/**
 * Complete Bidding System Diagnostic & Test
 * Verify the bid placement and display system
 */
session_start();
require_once __DIR__ . '/../Back_End/Models/Database.php';

$db = new Database();
$conn = $db->threadly_connect;

// Test results
$tests = [
    'schema_check' => false,
    'bid_display' => false,
    'bid_action' => false,
    'seller_products' => false
];

echo "<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; }
.test { margin: 20px 0; padding: 15px; border-left: 4px solid #ccc; }
.pass { border-color: #22c55e; background: #f0fdf4; }
.fail { border-color: #ef4444; background: #fef2f2; }
.warn { border-color: #f59e0b; background: #fffbeb; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background: #f3f4f6; }
.code { background: #f3f4f6; padding: 2px 6px; font-family: monospace; }
h2 { color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
.summary { background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 20px 0; }
</style>
</head>
<body>

<h1>🔍 Threadly Bidding System Diagnostic</h1>
<p>Testing bid placement, retrieval, and management systems...</p>
";

// ============================================================================
// TEST 1: Schema Detection
// ============================================================================
echo "<div class='test pass'>";
echo "<h3>✓ TEST 1: Database Schema Detection</h3>";

$colCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
$hasProductId = ($colCheck && $colCheck->num_rows > 0);

$statusCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_status'");
$hasBidStatus = ($statusCheck && $statusCheck->num_rows > 0);

$createdAtCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'created_at'");
$hasCreatedAt = ($createdAtCheck && $createdAtCheck->num_rows > 0);

echo "<p><strong>Schema Type:</strong> " . ($hasProductId ? "NEW (product_id) - using direct JOIN" : "OLD (session_id) - using bidding_session JOIN") . "</p>";
echo "<p><strong>Has bid_status column:</strong> " . ($hasBidStatus ? "✓ Yes" : "✗ No (run migration to add)") . "</p>";
echo "<p><strong>Has created_at column:</strong> " . ($hasCreatedAt ? "✓ Yes" : "✗ No (run migration to add)") . "</p>";

$tests['schema_check'] = true;
echo "</div>";

// ============================================================================
// TEST 2: Check Existing Bids
// ============================================================================
echo "<div class='test " . ($hasProductId ? "pass" : "warn") . "'>";
echo "<h3>✓ TEST 2: Existing Bids in Database</h3>";

$bidCount = $conn->query("SELECT COUNT(*) as count FROM bids")->fetch_assoc();
echo "<p><strong>Total bids in database:</strong> " . $bidCount['count'] . "</p>";

if ($bidCount['count'] > 0) {
    echo "<p><strong>Sample bids:</strong></p>";
    echo "<table>";
    echo "<tr><th>Bid ID</th><th>User ID</th><th>Amount</th><th>Session ID</th><th>Status</th><th>Time</th></tr>";
    
    $bidsResult = $conn->query("SELECT bid_id, user_id, bid_amount, session_id, 
        " . ($hasBidStatus ? "bid_status" : "'pending' as bid_status") . ",
        " . ($hasCreatedAt ? "created_at" : "bit_time as created_at") . "
        FROM bids LIMIT 5");
    
    while ($row = $bidsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['bid_id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>₱" . number_format($row['bid_amount'], 2) . "</td>";
        echo "<td>" . $row['session_id'] . "</td>";
        echo "<td>" . ucfirst($row['bid_status']) . "</td>";
        echo "<td>" . substr($row['created_at'], 0, 16) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    $tests['schema_check'] = true;
}

echo "</div>";

// ============================================================================
// TEST 3: Test Bid Display for Sample Seller
// ============================================================================
echo "<div class='test'>";
echo "<h3>TEST 3: Bid Retrieval Test (Seller ID = 28)</h3>";

$seller_id = 28;

// Use the exact query from seller_dashboard.php
if ($hasProductId) {
    $bidsSql = "
        SELECT b.bid_id, b.product_id, b.user_id, b.bid_amount, b.bid_status, 
               b.bid_message, b.created_at, p.product_name, u.username, u.email
        FROM bids b
        JOIN products p ON b.product_id = p.product_id
        JOIN users u ON b.user_id = u.user_id
        WHERE p.seller_id = ?
        ORDER BY b.created_at DESC
    ";
} else {
    $bidsSql = "
        SELECT b.bid_id, bs.product_id, b.user_id, b.bid_amount, 
               " . ($hasBidStatus ? "b.bid_status" : "'pending' as bid_status") . ",
               '' as bid_message, " . ($hasCreatedAt ? "b.created_at" : "b.bit_time as created_at") . ",
               p.product_name, u.username, u.email
        FROM bids b
        LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
        LEFT JOIN products p ON bs.product_id = p.product_id
        LEFT JOIN users u ON b.user_id = u.id
        WHERE p.seller_id = ?
        ORDER BY " . ($hasCreatedAt ? "b.created_at" : "b.bit_time") . " DESC
    ";
}

$bidsStmt = $conn->prepare($bidsSql);
if (!$bidsStmt) {
    echo "<div class='test fail'><strong>✗ Query prepare failed:</strong> " . $conn->error . "</div>";
} else {
    $bidsStmt->bind_param('i', $seller_id);
    if (!$bidsStmt->execute()) {
        echo "<div class='test fail'><strong>✗ Query execute failed:</strong> " . $bidsStmt->error . "</div>";
    } else {
        $bidsResult = $bidsStmt->get_result();
        $bidCount = $bidsResult->num_rows;
        
        if ($bidCount > 0) {
            echo "<div class='pass'>";
            echo "<p><strong>✓ Found " . $bidCount . " bids for seller 28</strong></p>";
            echo "<table>";
            echo "<tr><th>Bid</th><th>Product</th><th>Bidder</th><th>Amount</th><th>Status</th></tr>";
            while ($row = $bidsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>#" . $row['bid_id'] . "</td>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>" . $row['username'] . " (" . $row['email'] . ")</td>";
                echo "<td>₱" . number_format($row['bid_amount'], 2) . "</td>";
                echo "<td>" . ucfirst($row['bid_status']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
            $tests['bid_display'] = true;
        } else {
            echo "<div class='warn'>";
            echo "<p><strong>⚠ No bids found for seller 28</strong></p>";
            
            // Check seller's products
            $prodStmt = $conn->prepare("SELECT product_id, product_name FROM products WHERE seller_id = ?");
            $prodStmt->bind_param('i', $seller_id);
            $prodStmt->execute();
            $prodResult = $prodStmt->get_result();
            
            if ($prodResult->num_rows > 0) {
                echo "<p>Seller 28 has " . $prodResult->num_rows . " products:</p>";
                echo "<ul>";
                while ($row = $prodResult->fetch_assoc()) {
                    echo "<li>Product " . $row['product_id'] . ": " . $row['product_name'] . "</li>";
                }
                echo "</ul>";
                echo "<p><em>No bids have been placed on these products yet. Try placing a test bid!</em></p>";
            } else {
                echo "<p><em>Seller 28 has no products. Add a product first before testing bids.</em></p>";
            }
            
            echo "</div>";
        }
        
        $bidsStmt->close();
    }
}

echo "</div>";

// ============================================================================
// TEST 4: Check Bidding Feature Support
// ============================================================================
echo "<div class='test'>";
echo "<h3>TEST 4: Bidding Feature Support</h3>";

$biddingProducts = $conn->query("SELECT COUNT(*) as count FROM products WHERE bidding = 1")->fetch_assoc();
echo "<p><strong>Products with bidding enabled:</strong> " . $biddingProducts['count'] . "</p>";

if ($biddingProducts['count'] > 0) {
    echo "<p><strong>Sample bidding products:</strong></p>";
    echo "<table>";
    echo "<tr><th>Product</th><th>Price</th><th>Seller</th></tr>";
    
    $prodResult = $conn->query("
        SELECT p.product_id, p.product_name, p.price, u.username
        FROM products p
        LEFT JOIN users u ON p.seller_id = u.id
        WHERE p.bidding = 1
        LIMIT 5
    ");
    
    while ($row = $prodResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['product_name'] . "</td>";
        echo "<td>₱" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    $tests['bid_action'] = true;
}

echo "</div>";

// ============================================================================
// SUMMARY
// ============================================================================
echo "<div class='summary'>";
echo "<h2>📊 Test Summary</h2>";

$allPass = array_reduce($tests, function($carry, $item) {
    return $carry && $item;
}, true);

$passCount = count(array_filter($tests));
$totalTests = count($tests);

echo "<p><strong>Tests Passed: " . $passCount . "/" . $totalTests . "</strong></p>";
echo "<ul>";
foreach ($tests as $name => $result) {
    $icon = $result ? "✓" : "✗";
    echo "<li>" . $icon . " " . ucfirst(str_replace('_', ' ', $name)) . "</li>";
}
echo "</ul>";

echo "<p><strong>Status:</strong> ";
if ($allPass) {
    echo "✓ <span style='color: #22c55e;'><strong>All tests passed! Your bidding system is working correctly.</strong></span>";
} else {
    echo "⚠ <span style='color: #f59e0b;'><strong>Some tests need attention. See details above.</strong></span>";
}
echo "</p>";

// Recommendations
echo "<h3>Recommendations:</h3>";
echo "<ul>";
if (!$hasBidStatus) {
    echo "<li>🔧 <strong>Run Migration:</strong> Visit <span class='code'>Back_End/migrate_bids_add_status.php</span> to add bid_status support</li>";
}
if ($bidCount['count'] == 0) {
    echo "<li>📝 <strong>Place Test Bid:</strong> Create a product with bidding enabled and place a test bid</li>";
}
if ($biddingProducts['count'] == 0) {
    echo "<li>➕ <strong>Add Products:</strong> Create products with bidding enabled to test the system</li>";
}
echo "</ul>";

echo "</div>";

echo "</body></html>";
?>
