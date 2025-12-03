<?php
// update_product_details.php
// Handles product details updates (name, price, quantity, description)

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
$product_name = trim($_POST['product_name'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$description = trim($_POST['description'] ?? '');
$pbidding = isset($_POST['bidding']) ? 1 : null;

// Validate input
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

if (empty($product_name)) {
    echo json_encode(['success' => false, 'message' => 'Product name cannot be empty']);
    exit;
}

if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
    exit;
}

if ($quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity cannot be negative']);
    exit;
}

if (empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Description cannot be empty']);
    exit;
}

$db = new Database();
$conn = $db->threadly_connect;

// Verify the product belongs to the current user (handle NULL seller_id)
$verifyStmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND (seller_id = ? OR seller_id IS NULL)");
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

// Update the product details - also assign seller_id if NULL
$updateCols = "seller_id = ?, product_name = ?, price = ?, quantity = ?, description = ?";
$updateTypes = 'isdis';
$updateParams = [$user_id, $product_name, $price, $quantity, $description];

// If the products table contains a 'bidding' column, include it in the update
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
if ($colRes && $colRes->num_rows > 0) {
    // treat null (not provided) as 0 for safety
    $bval = ($pbidding === null) ? 0 : intval($pbidding);
    $updateCols .= ", bidding = ?";
    $updateTypes .= 'i';
    $updateParams[] = $bval;
}

$updateCols .= " WHERE product_id = ? AND (seller_id = ? OR seller_id IS NULL)";
$updateTypes .= 'ii';
$updateParams[] = $product_id;
$updateParams[] = $user_id;

$sql = "UPDATE products SET " . $updateCols;
$updateStmt = $conn->prepare($sql);
if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// bind_param requires references
$bindArr = [];
$bindArr[] = $updateTypes;
for ($i = 0; $i < count($updateParams); $i++) {
    $bindArr[] = &$updateParams[$i];
}
call_user_func_array([$updateStmt, 'bind_param'], $bindArr);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product details updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $updateStmt->error]);
}

$updateStmt->close();
$db->close_db();
?>
