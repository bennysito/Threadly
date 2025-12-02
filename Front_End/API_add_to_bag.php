<?php
/**
 * API_add_to_bag.php - API endpoint for adding products to shopping bag
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $response['message'] = 'Please log in to add items to your bag.';
    echo json_encode($response);
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

if (!$productId) {
    http_response_code(400);
    $response['message'] = 'Invalid product ID.';
    echo json_encode($response);
    exit;
}

// Initialize or get shopping bag from session
if (!isset($_SESSION['shopping_bag'])) {
    $_SESSION['shopping_bag'] = [];
}

// Add product to bag
if ($action === 'add') {
    // Check if product already in bag
    $found = false;
    foreach ($_SESSION['shopping_bag'] as &$item) {
        if ($item['product_id'] == $productId) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        // Get product details from database
        require_once _DIR_ . '/../Back_End/Models/Database.php';
        $db = new Database();
        $conn = $db->get_connection();
        
        $stmt = $conn->prepare("SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();
            
            if ($product) {
                $_SESSION['shopping_bag'][] = [
                    'product_id' => $product['product_id'],
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'] ?? 'Images/jacket_hoodie.png',
                    'quantity' => 1
                ];
                $response['success'] = true;
                $response['message'] = 'Product added to bag successfully!';
            } else {
                $response['message'] = 'Product not found.';
            }
        } else {
            $response['message'] = 'Database error.';
        }
        $db->close_db();
    } else {
        $response['success'] = true;
        $response['message'] = 'Product quantity updated in bag!';
    }
} else {
    $response['message'] = 'Invalid action.';
}

echo json_encode($response);
?>