<?php
// Simple test script to verify product update functionality

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "Back_End/Models/Database.php";

// Create a test session if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;  // Test user ID - replace with actual user ID
}

$db = new Database();
$conn = $db->threadly_connect;

// Check for existing products
echo "<h2>Checking for products to update...</h2>";
$user_id = $_SESSION['user_id'];
$sql = "SELECT product_id, product_name, price, quantity FROM products WHERE seller_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No products found for seller ID: $user_id</p>";
    echo "<p>Available sellers:</p>";
    $all = $conn->query("SELECT DISTINCT seller_id FROM products LIMIT 5");
    while ($row = $all->fetch_assoc()) {
        echo "- Seller ID: " . $row['seller_id'] . "<br>";
    }
} else {
    $product = $result->fetch_assoc();
    $product_id = $product['product_id'];
    
    echo "<p>Found product: " . htmlspecialchars($product['product_name']) . "</p>";
    echo "<p>Current price: " . $product['price'] . "</p>";
    echo "<p>Current quantity: " . $product['quantity'] . "</p>";
    
    // Test update
    echo "<h3>Attempting update...</h3>";
    
    $new_name = "Updated Product Name - " . time();
    $new_price = 99.99;
    $new_quantity = 50;
    $new_desc = "This is an updated description";
    
    $updateSql = "UPDATE products SET 
                  product_name=?, 
                  price=?, 
                  quantity=?, 
                  description=?
                  WHERE product_id=? AND seller_id=?";
    
    $updateStmt = $conn->prepare($updateSql);
    if (!$updateStmt) {
        echo "<p style='color: red;'>Prepare failed: " . $conn->error . "</p>";
    } else {
        $updateStmt->bind_param('sdisii', $new_name, $new_price, $new_quantity, $new_desc, $product_id, $user_id);
        if ($updateStmt->execute()) {
            echo "<p style='color: green;'>✓ Update successful!</p>";
            echo "<p>Updated product ID: $product_id</p>";
            echo "<p>New name: " . htmlspecialchars($new_name) . "</p>";
            echo "<p>New price: $new_price</p>";
            echo "<p>New quantity: $new_quantity</p>";
            
            // Verify update
            $verify = $conn->prepare("SELECT product_name, price, quantity FROM products WHERE product_id = ?");
            $verify->bind_param('i', $product_id);
            $verify->execute();
            $verifyResult = $verify->get_result();
            $verifyRow = $verifyResult->fetch_assoc();
            
            echo "<h3>Verification:</h3>";
            echo "<p>Verified name: " . htmlspecialchars($verifyRow['product_name']) . "</p>";
            echo "<p>Verified price: " . $verifyRow['price'] . "</p>";
            echo "<p>Verified quantity: " . $verifyRow['quantity'] . "</p>";
            $verify->close();
        } else {
            echo "<p style='color: red;'>✗ Update failed: " . $updateStmt->error . "</p>";
        }
        $updateStmt->close();
    }
}

$stmt->close();
$db->close_db();
?>
