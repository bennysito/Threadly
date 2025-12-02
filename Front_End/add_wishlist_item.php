<?php
// /add_wishlist_item.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $response['message'] = 'Login required to add to wishlist.';
    echo json_encode($response);
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if (!$productId) {
    http_response_code(400);
    $response['message'] = 'Invalid product ID.';
    echo json_encode($response);
    exit;
}

@require_once __DIR__ . "/Back_End/Models/Database.php"; 

if (!class_exists('Database')) {
    $response['success'] = true;
    $response['message'] = 'Item added (Simulated).';
    echo json_encode($response);
    exit;
}

try {
    $db = new Database();
    $conn = $db->threadly_connect;

    // 1. Get or Create Wishlist ID
    $sql_wishlist = "SELECT wishlist_id FROM wishlist WHERE user_id = ?";
    $stmt_wishlist = $conn->prepare($sql_wishlist);
    $stmt_wishlist->bind_param('i', $userId);
    $stmt_wishlist->execute();
    $result_wishlist = $stmt_wishlist->get_result();
    $wishlist = $result_wishlist->fetch_assoc();

    if (!$wishlist) {
        // Create new wishlist if none exists
        $sql_insert_list = "INSERT INTO wishlist (user_id) VALUES (?)";
        $stmt_insert_list = $conn->prepare($sql_insert_list);
        $stmt_insert_list->bind_param('i', $userId);
        $stmt_insert_list->execute();
        $wishlistId = $conn->insert_id;
        $stmt_insert_list->close();
    } else {
        $wishlistId = $wishlist['wishlist_id'];
    }
    $stmt_wishlist->close();

    // 2. Add product to wishlist_item (Checking for duplicates first is recommended, but we use INSERT IGNORE/ON DUPLICATE KEY UPDATE for simplicity)
    $sql_insert_item = "
        INSERT INTO wishlist_item (wishlist_id, product_id, added_at) 
        VALUES (?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE added_at=NOW()";
        
    $stmt_insert_item = $conn->prepare($sql_insert_item);
    $stmt_insert_item->bind_param('ii', $wishlistId, $productId);
    
    if ($stmt_insert_item->execute()) {
        $response['success'] = true;
        $response['message'] = 'Product added to wishlist.';
    } else {
        $response['message'] = 'Database error adding item: ' . $conn->error;
    }
    $stmt_insert_item->close();
    $db->close_db();

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Server Error: ' . $e->getMessage();
}

echo json_encode($response);
?>