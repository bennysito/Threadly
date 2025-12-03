<?php
/**
 * Bidding Setup Helper
 * This script helps set up the bidding system
 * 
 * IMPORTANT: This is for development/testing only
 * For production, use the seller_dashboard.php to manage bidding per product
 */

require_once "Back_End/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

$message = '';
$action = $_GET['action'] ?? '';

echo "<h2>Bidding System Setup</h2>";

// Step 1: Add bidding column if missing
if ($action === 'add_column') {
    $colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
    if (!($colRes && $colRes->num_rows > 0)) {
        $sql = "ALTER TABLE products ADD COLUMN bidding TINYINT(1) NOT NULL DEFAULT 0";
        if ($conn->query($sql) === TRUE) {
            $message = "<p style='color: green;'>✅ Column 'bidding' added successfully</p>";
        } else {
            $message = "<p style='color: red;'>❌ Error: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        $message = "<p style='color: blue;'>ℹ️ Column 'bidding' already exists</p>";
    }
}

// Step 2: Enable bidding on first N products for testing
if ($action === 'enable_sample') {
    $limit = intval($_GET['limit'] ?? 5);
    $sql = "UPDATE products SET bidding = 1 WHERE product_id IN (SELECT product_id FROM (SELECT product_id FROM products ORDER BY product_id ASC LIMIT ?) t)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    if ($stmt->execute()) {
        $message = "<p style='color: green;'>✅ Enabled bidding on first " . $limit . " products</p>";
    } else {
        $message = "<p style='color: red;'>❌ Error: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

// Display current status
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
$hasBiddingColumn = ($colRes && $colRes->num_rows > 0);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Bidding Setup</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .button { 
            display: inline-block; 
            padding: 10px 20px; 
            margin: 5px; 
            background: #0066cc; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .button:hover { background: #0052a3; }
        .status { 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 4px; 
            background: #f0f0f0;
        }
        .info { color: #0066cc; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>

<h1>Bidding System Setup Helper</h1>

<?php if ($message): ?>
    <div class="status">
        <?= $message ?>
    </div>
<?php endif; ?>

<h2>Setup Steps</h2>

<div class="status">
    <h3>Step 1: Prepare Database</h3>
    <?php if ($hasBiddingColumn): ?>
        <p style="color: green; font-weight: bold;">✅ Bidding column exists</p>
    <?php else: ?>
        <p>The bidding column needs to be added to the products table.</p>
        <a href="?action=add_column" class="button">Add Bidding Column</a>
    <?php endif; ?>
</div>

<div class="status">
    <h3>Step 2: Test with Sample Data (Optional)</h3>
    <?php if ($hasBiddingColumn): ?>
        <p>Enable bidding on sample products for testing:</p>
        <a href="?action=enable_sample&limit=5" class="button">Enable on 5 Products</a>
        <a href="?action=enable_sample&limit=10" class="button">Enable on 10 Products</a>
    <?php else: ?>
        <p style="color: #999;">Complete Step 1 first</p>
    <?php endif; ?>
</div>

<div class="status">
    <h3>Step 3: Current Status</h3>
    <?php
    if ($hasBiddingColumn) {
        $countResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE bidding = 1");
        $row = $countResult->fetch_assoc();
        echo "<p>✅ Bidding column exists</p>";
        echo "<p>Products with bidding enabled: <strong>" . $row['count'] . "</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Bidding column not found</p>";
    }
    ?>
</div>

<?php
if ($hasBiddingColumn) {
    // Show bidding products table
    $result = $conn->query("SELECT product_id, product_name, price, bidding, quantity FROM products ORDER BY product_id DESC LIMIT 15");
    
    if ($result && $result->num_rows > 0) {
        echo "<h2>Products (Last 15)</h2>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Product ID</th>";
        echo "<th>Product Name</th>";
        echo "<th>Price</th>";
        echo "<th>Bidding Enabled</th>";
        echo "<th>Quantity</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['product_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>₱" . number_format($row['price'], 2) . "</td>";
            echo "<td>" . ($row['bidding'] ? "✅ Yes" : "❌ No") . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
}
?>

<hr>
<p><a href="index.php">Go to Homepage</a> | <a href="Front_End/seller_dashboard.php">Seller Dashboard</a> | <a href="test_bidding_display.php">View Bidding Display Test</a></p>

</body>
</html>

<?php
$db->close_db();
?>
