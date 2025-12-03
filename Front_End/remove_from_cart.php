<?php
// remove_from_cart.php
// Removes a product from the user's shopping bag. Returns JSON.

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product id']);
    exit;
}

// Initialize shopping bag if needed
if (!isset($_SESSION['shopping_bag']) || !is_array($_SESSION['shopping_bag'])) {
    $_SESSION['shopping_bag'] = [];
}

// Find and remove the item from the shopping bag
$found = false;
foreach ($_SESSION['shopping_bag'] as $index => $item) {
    if (isset($item['product_id']) && $item['product_id'] == $product_id) {
        unset($_SESSION['shopping_bag'][$index]);
        $found = true;
        break;
    }
}

// Reindex array to remove gaps
$_SESSION['shopping_bag'] = array_values($_SESSION['shopping_bag']);

if (!$found) {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    exit;
}

// Return success with updated cart count
$count = count($_SESSION['shopping_bag']);

echo json_encode([
    'success' => true,
    'message' => 'Item removed from cart',
    'count' => $count,
    'cart' => $_SESSION['shopping_bag']
]);
?>
