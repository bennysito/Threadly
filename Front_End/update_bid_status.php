<?php
// update_bid_status.php
// Update bid status (approve/reject) by seller

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";

header('Content-Type: application/json');

// Check authentication
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get POST data
$bid_id = intval($_POST['bid_id'] ?? 0);
$bid_status = $_POST['bid_status'] ?? '';

// Validate input
if ($bid_id <= 0 || !in_array($bid_status, ['accepted', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid bid ID or status']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // Check if bids table has product_id or uses session_id
    $colCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
    $hasProductId = ($colCheck && $colCheck->num_rows > 0);
    
    // First, verify that this bid belongs to one of the seller's products
    if ($hasProductId) {
        // New schema with product_id
        $verifyStmt = $conn->prepare("
            SELECT b.bid_id, p.seller_id
            FROM bids b
            JOIN products p ON b.product_id = p.product_id
            WHERE b.bid_id = ? AND p.seller_id = ?
        ");
    } else {
        // Old schema with session_id
        $verifyStmt = $conn->prepare("
            SELECT b.bid_id, p.seller_id
            FROM bids b
            LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
            LEFT JOIN products p ON bs.product_id = p.product_id
            WHERE b.bid_id = ? AND p.seller_id = ?
        ");
    }
    
    if (!$verifyStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $verifyStmt->bind_param('ii', $bid_id, $user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    
    if ($verifyResult->num_rows === 0) {
        $verifyStmt->close();
        echo json_encode(['success' => false, 'message' => 'Bid not found or not authorized']);
        exit;
    }
    
    $verifyStmt->close();
    
    // Update the bid status
    // Check if bid_status column exists in old schema
    $statusColCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_status'");
    $hasBidStatusCol = ($statusColCheck && $statusColCheck->num_rows > 0);
    
    if ($hasBidStatusCol) {
        // Has bid_status column (either new schema or migrated old schema)
        $updateStmt = $conn->prepare("
            UPDATE bids 
            SET bid_status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE bid_id = ?
        ");
        if (!$updateStmt) {
            throw new Exception("Update prepare failed: " . $conn->error);
        }
        $updateStmt->bind_param('si', $bid_status, $bid_id);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Update execution failed: " . $updateStmt->error);
        }
        
        $affected_rows = $updateStmt->affected_rows;
        error_log("Bid update: bid_id=$bid_id, status=$bid_status, affected_rows=$affected_rows");
        $updateStmt->close();
        
        if ($affected_rows === 0) {
            throw new Exception("No rows were updated. Bid may not exist.");
        }
    } else {
        // Old schema without bid_status column - cannot update status
        throw new Exception("Database schema error: bid_status column not found in bids table");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Bid status updated successfully',
        'bid_id' => $bid_id,
        'new_status' => $bid_status
    ]);
    
} catch (Exception $e) {
    error_log("Error updating bid status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
