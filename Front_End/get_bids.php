<?php
// get_bids.php
// Get bid information for a product

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";

header('Content-Type: application/json');

$product_id = intval($_GET['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->threadly_connect;
    $conn = $db->threadly_connect;
    
    // Check what columns exist in the bids table
    $colRes = $conn->query("SHOW COLUMNS FROM bids");
    $existingCols = [];
    if ($colRes) {
        while ($row = $colRes->fetch_assoc()) {
            $existingCols[] = $row['Field'];
        }
    }
    
    $highest_bid = null;
    $all_bids = [];
    $user_bid = null;
    
    // Get data based on table schema
    if (in_array('product_id', $existingCols)) {
        // New schema - query by product_id
        $stmt = $conn->prepare("
            SELECT bid_id, user_id, bid_amount, bid_status, bid_message, created_at
            FROM bids
            WHERE product_id = ?
            ORDER BY bid_amount DESC, created_at DESC
        ");
        if ($stmt) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $all_bids = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            $highest_bid = count($all_bids) > 0 ? $all_bids[0] : null;
        }
        
        // Get user's bid if logged in
        if (isset($_SESSION['user_id'])) {
            $userStmt = $conn->prepare("
                SELECT bid_id, bid_amount, bid_status, bid_message, created_at
                FROM bids
                WHERE product_id = ? AND user_id = ?
                ORDER BY created_at DESC
                LIMIT 1
            ");
            if ($userStmt) {
                $userStmt->bind_param('ii', $product_id, $_SESSION['user_id']);
                $userStmt->execute();
                $userResult = $userStmt->get_result();
                $user_bid = $userResult->fetch_assoc();
                $userStmt->close();
            }
        }
    } else {
        // Old schema - query via bidding_session
        // Get session for this product
        $sessionStmt = $conn->prepare("
            SELECT session_id FROM bidding_session
            WHERE product_id = ?
            LIMIT 1
        ");
        if ($sessionStmt) {
            $sessionStmt->bind_param('i', $product_id);
            $sessionStmt->execute();
            $sessionResult = $sessionStmt->get_result();
            $sessionRow = $sessionResult->fetch_assoc();
            $sessionStmt->close();
            
            if ($sessionRow) {
                $session_id = $sessionRow['session_id'];
                
                // Get all bids for this session
                $stmt = $conn->prepare("
                    SELECT bid_id, user_id, bid_amount
                    FROM bids
                    WHERE session_id = ?
                    ORDER BY bid_amount DESC
                ");
                if ($stmt) {
                    $stmt->bind_param('i', $session_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $all_bids = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();
                    
                    $highest_bid = count($all_bids) > 0 ? $all_bids[0] : null;
                }
                
                // Get user's bid if logged in
                if (isset($_SESSION['user_id'])) {
                    $userStmt = $conn->prepare("
                        SELECT bid_id, bid_amount
                        FROM bids
                        WHERE session_id = ? AND user_id = ?
                        ORDER BY bid_id DESC
                        LIMIT 1
                    ");
                    if ($userStmt) {
                        $userStmt->bind_param('ii', $session_id, $_SESSION['user_id']);
                        $userStmt->execute();
                        $userResult = $userStmt->get_result();
                        $user_bid = $userResult->fetch_assoc();
                        $userStmt->close();
                    }
                }
            }
        }
    }
    
    $db->close_db();
    
    echo json_encode([
        'success' => true,
        'highest_bid' => $highest_bid,
        'all_bids_count' => count($all_bids),
        'user_bid' => $user_bid,
        'all_bids' => $all_bids
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
