<?php
// place_bid.php
// Handles placing a bid on a product

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";
require_once __DIR__ . "/../Back_End/Models/Bidding.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to place a bid']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$bid_amount = floatval($_POST['bid_amount'] ?? 0);
$bid_message = trim($_POST['bid_message'] ?? '');

// Validation
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

if ($bid_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Bid amount must be greater than 0']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // Get product to verify it exists and get current price
    $stmt = $conn->prepare("SELECT product_id, product_name, price FROM products WHERE product_id = ?");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // Check if bid amount is reasonable (at least equal to product price)
    $productPrice = floatval($product['price'] ?? 0);
    if ($bid_amount < $productPrice) {
        echo json_encode(['success' => false, 'message' => 'Bid amount must be at least ₱' . number_format($productPrice, 2)]);
        exit;
    }

    // Place the bid using Bidding class
    $bidding = new Bidding();
    $bidResult = $bidding->placeBid($product_id, $user_id, $bid_amount, $bid_message);
    
    if ($bidResult['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Bid placed successfully!',
            'bid_id' => $bidResult['bid_id'],
            'bid_amount' => $bid_amount,
            'product_name' => $product['product_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => $bidResult['message']]);
    }
    
    $bidding->closeConnection();
    $db->close_db();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
