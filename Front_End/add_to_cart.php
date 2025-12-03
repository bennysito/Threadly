<?php
// add_to_cart.php
// Adds a product (by ID) to the user's session cart. Returns JSON.

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$product_id = intval($_POST['product_id'] ?? 0);
$qty = intval($_POST['qty'] ?? 1);
$qty = max(1, $qty);

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product id']);
    exit;
}

$item = null;

// Try to fetch product from DB if available
@require_once __DIR__ . "/../Back_End/Models/Database.php";
try {
    if (class_exists('Database')) {
        $db = new Database();
        $conn = $db->threadly_connect;

        $stmt = $conn->prepare('SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $item = [
                    'id' => (int)$row['product_id'],
                    'name' => $row['product_name'],
                    'price' => (float)$row['price'],
                    'qty' => $qty,
                    'total' => (float)$row['price'] * $qty,
                    'image_url' => $row['image_url'] ?? ''
                ];
            }
            $stmt->close();
        }

        $db->close_db();
    }
} catch (Exception $e) {
    // fall through to a simulated item
}

if (!$item) {
    // Fallback: create a simple item using the provided ID (for dev environments)
    $item = [
        'id' => $product_id,
        'name' => 'Product #' . $product_id,
        'price' => 0.00,
        'qty' => $qty,
        'total' => 0.00,
        'image_url' => ''
    ];
}

// Initialize cart if needed
if (!isset($_SESSION['shopping_bag']) || !is_array($_SESSION['shopping_bag'])) {
    $_SESSION['shopping_bag'] = [];
}

// Try to merge with existing item if present
$found = false;
foreach ($_SESSION['shopping_bag'] as &$c) {
    if (isset($c['product_id']) && $c['product_id'] == $item['id']) {
        $c['quantity'] += $item['qty'];
        $c['total'] = $c['price'] * $c['quantity'];
        $found = true;
        break;
    }
}
unset($c);

if (!$found) {
    $_SESSION['shopping_bag'][] = [
        'product_id' => $item['id'],
        'product_name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['qty'],
        'total' => $item['total'],
        'image_url' => $item['image_url']
    ];
}

// Return success with new cart count
$count = count($_SESSION['shopping_bag']);

echo json_encode(['success' => true, 'message' => 'Added to cart', 'count' => $count, 'cart' => $_SESSION['shopping_bag']]);

?>
