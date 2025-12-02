<?php
// toggle_wishlist.php
// Handles adding/removing products from wishlist

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to wishlist', 'liked' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID', 'liked' => false, 'debug' => 'product_id=' . $product_id]);
    exit;
}

$db = new Database();
$conn = $db->threadly_connect;

// Check if product exists
$checkStmt = $conn->prepare("SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?");
if (!$checkStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => false]);
    exit;
}

$checkStmt->bind_param('i', $product_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    echo json_encode(['success' => false, 'message' => 'Product not found', 'liked' => false]);
    exit;
}

$productData = $checkResult->fetch_assoc();
$checkStmt->close();

// Create or get user's wishlist
$wishlistStmt = $conn->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ?");
if (!$wishlistStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => false]);
    exit;
}

$wishlistStmt->bind_param('i', $user_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlist = $wishlistResult->fetch_assoc();
$wishlistStmt->close();

$wishlist_id = null;

if (!$wishlist) {
    // Create new wishlist for user
    $createStmt = $conn->prepare("INSERT INTO wishlist (user_id) VALUES (?)");
    if (!$createStmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => false]);
        exit;
    }
    $createStmt->bind_param('i', $user_id);
    if (!$createStmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to create wishlist: ' . $createStmt->error, 'liked' => false]);
        $createStmt->close();
        exit;
    }
    $wishlist_id = $createStmt->insert_id;
    $createStmt->close();
} else {
    $wishlist_id = $wishlist['wishlist_id'];
}

// Check if product already in wishlist
$existStmt = $conn->prepare("SELECT wishlist_item_id FROM wishlist_item WHERE wishlist_id = ? AND product_id = ?");
if (!$existStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => false]);
    exit;
}

$existStmt->bind_param('ii', $wishlist_id, $product_id);
$existStmt->execute();
$existResult = $existStmt->get_result();
$exists = $existResult->fetch_assoc();
$existStmt->close();

if ($exists) {
    // Remove from wishlist
    $deleteStmt = $conn->prepare("DELETE FROM wishlist_item WHERE wishlist_id = ? AND product_id = ?");
    if (!$deleteStmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => true]);
        exit;
    }
    $deleteStmt->bind_param('ii', $wishlist_id, $product_id);
    if (!$deleteStmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete: ' . $deleteStmt->error, 'liked' => true]);
        $deleteStmt->close();
        exit;
    }
    $deleteStmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Removed from wishlist', 'liked' => false]);
} else {
    // Add to wishlist - variant_id is NULL since we don't use product variants
    $insertStmt = $conn->prepare("INSERT INTO wishlist_item (wishlist_id, product_id, variant_id) VALUES (?, ?, NULL)");
    if (!$insertStmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error, 'liked' => false]);
        exit;
    }
    $insertStmt->bind_param('ii', $wishlist_id, $product_id);
    if (!$insertStmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert: ' . $insertStmt->error, 'liked' => false, 'wishlist_id' => $wishlist_id, 'product_id' => $product_id]);
        $insertStmt->close();
        exit;
    }
    $insertStmt->close();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Added to wishlist', 
        'liked' => true,
        'product_name' => $productData['product_name'],
        'price' => $productData['price'],
        'image_url' => $productData['image_url']
    ]);
}

$db->close_db();
?>
