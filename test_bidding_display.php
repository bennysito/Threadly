<?php
/**
 * Quick Test: Bidding Products
 * This script checks if the bidding products are being fetched correctly
 * Visit: http://localhost/xampp/htdocs/Threadly/test_bidding_display.php
 */

require_once "Back_End/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

// Check if bidding column exists
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
$hasBiddingColumn = ($colRes && $colRes->num_rows > 0);

echo "<h2>Bidding Products Test</h2>";

if (!$hasBiddingColumn) {
    echo "<p style='color: red; font-weight: bold;'>❌ Bidding column does not exist in products table</p>";
    echo "<p>Run: <code>Back_End/Models/add_bidding_column.php</code></p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ Bidding column exists</p>";
}

// Check for products with bidding enabled
$sql = "SELECT product_id, product_name, price, image_url, bidding, quantity
        FROM products 
        WHERE quantity > 0
        LIMIT 5";

$result = $conn->query($sql);

echo "<h3>Sample Products (First 5):</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Bidding</th><th>Quantity</th><th>Image</th></tr>";

if ($result) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        $bidding_status = ($hasBiddingColumn && $row['bidding']) ? "✅ Enabled" : "❌ Disabled";
        echo "<tr>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>₱" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . $bidding_status . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>" . htmlspecialchars($row['image_url'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    if ($count === 0) {
        echo "<tr><td colspan='6'>No products found</td></tr>";
    }
}
echo "</table>";

// Count bidding products
if ($hasBiddingColumn) {
    $countResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE bidding = 1 AND quantity > 0");
    $countRow = $countResult->fetch_assoc();
    $biddingCount = $countRow['count'];
    
    echo "<h3>Bidding Statistics:</h3>";
    echo "<p>Products with bidding enabled: <strong style='color: " . ($biddingCount > 0 ? "green" : "red") . ";'>" . $biddingCount . "</strong></p>";
    
    if ($biddingCount > 0) {
        echo "<h4>Products with Bidding Enabled:</h4>";
        echo "<ul>";
        $biddingResult = $conn->query("SELECT product_id, product_name, price FROM products WHERE bidding = 1 AND quantity > 0 LIMIT 10");
        while ($bRow = $biddingResult->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($bRow['product_name']) . " (ID: " . $bRow['product_id'] . ") - ₱" . number_format($bRow['price'], 2) . "</li>";
        }
        echo "</ul>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>View Homepage</a> | <a href='Front_End/seller_dashboard.php'>Go to Seller Dashboard</a></p>";
?>
