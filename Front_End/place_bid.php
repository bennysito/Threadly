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

    // Try to insert bid directly, handling both old and new table schemas
    $insertSuccess = false;
    $insertError = '';
    $bidId = null;
    
    // First, try to check what columns exist in the bids table
    $colRes = $conn->query("SHOW COLUMNS FROM bids");
    $existingCols = [];
    if ($colRes) {
        while ($row = $colRes->fetch_assoc()) {
            $existingCols[] = $row['Field'];
        }
    }
    
    // Build the INSERT statement based on available columns
    if (in_array('product_id', $existingCols)) {
        // New schema - has product_id column
        $stmt = $conn->prepare("
            INSERT INTO bids (product_id, user_id, bid_amount, bid_message, bid_status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        if ($stmt) {
            $stmt->bind_param('iids', $product_id, $user_id, $bid_amount, $bid_message);
            if ($stmt->execute()) {
                $insertSuccess = true;
                $bidId = $stmt->insert_id;
            } else {
                $insertError = $stmt->error;
            }
            $stmt->close();
        } else {
            $insertError = $conn->error;
        }
    } else {
        // Old schema - has session_id, no product_id
        // Create or get session for this product
        $sessionStmt = $conn->prepare("
            SELECT session_id FROM bidding_session 
            WHERE product_id = ? AND status = 'ongoing'
            LIMIT 1
        ");
        
        if ($sessionStmt) {
            $sessionStmt->bind_param('i', $product_id);
            $sessionStmt->execute();
            $sessionResult = $sessionStmt->get_result();
            $sessionRow = $sessionResult->fetch_assoc();
            $session_id = $sessionRow['session_id'] ?? null;
            $sessionStmt->close();
            
            // If no session exists, create one
            if (!$session_id) {
                $createSessionStmt = $conn->prepare("
                    INSERT INTO bidding_session (product_id, start_time, end_time, status)
                    VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'ongoing')
                ");
                if ($createSessionStmt) {
                    $createSessionStmt->bind_param('i', $product_id);
                    if ($createSessionStmt->execute()) {
                        $session_id = $conn->insert_id;
                    }
                    $createSessionStmt->close();
                }
            }
            
            // Now insert the bid with session_id
            if ($session_id) {
                $stmt = $conn->prepare("
                    INSERT INTO bids (session_id, user_id, bid_amount)
                    VALUES (?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param('iid', $session_id, $user_id, $bid_amount);
                    if ($stmt->execute()) {
                        $insertSuccess = true;
                        $bidId = $stmt->insert_id;
                    } else {
                        $insertError = $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $insertError = $conn->error;
                }
            } else {
                $insertError = "Could not create bidding session";
            }
        } else {
            $insertError = $conn->error;
        }
    }
    
    if ($insertSuccess) {
        echo json_encode([
            'success' => true,
            'message' => 'Bid placed successfully!',
            'bid_id' => $bidId,
            'bid_amount' => $bid_amount,
            'product_name' => $product['product_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to place bid: ' . $insertError]);
    }
    
    $db->close_db();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
