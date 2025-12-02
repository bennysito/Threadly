<?php
// remove_wishlist_item.php
// Handles removing items from wishlist

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

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$db = new Database();
$conn = $db->threadly_connect;

// Get user's wishlist ID
$wishlistStmt = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ?");
if (!$wishlistStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$wishlistStmt->bind_param('i', $user_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlist = $wishlistResult->fetch_assoc();
$wishlistStmt->close();

if (!$wishlist) {
    echo json_encode(['success' => false, 'message' => 'Wishlist not found']);
    exit;
}

$wishlist_id = $wishlist['wishlist_id'];

// Delete the item
$deleteStmt = $conn->prepare("DELETE FROM wishlist_item WHERE wishlist_id = ? AND product_id = ?");
if (!$deleteStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$deleteStmt->bind_param('ii', $wishlist_id, $product_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}

$deleteStmt->close();
$db->close_db();
?>
