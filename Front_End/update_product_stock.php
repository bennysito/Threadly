<?php
// update_product_stock.php
// Handles stock/quantity updates for products

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

// Validate input
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

if ($quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity cannot be negative']);
    exit;
}

$db = new Database();
$conn = $db->threadly_connect;

// Verify the product belongs to the current user
$verifyStmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND seller_id = ?");
if (!$verifyStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$verifyStmt->bind_param('ii', $product_id, $user_id);
$verifyStmt->execute();
$result = $verifyStmt->get_result();

if ($result->num_rows === 0) {
    $verifyStmt->close();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Product not found or unauthorized']);
    exit;
}

$verifyStmt->close();

// Update the quantity
$updateStmt = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ? AND seller_id = ?");
if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$updateStmt->bind_param('iii', $quantity, $product_id, $user_id);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock']);
}

$updateStmt->close();
$db->close_db();
?>
