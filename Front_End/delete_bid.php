<?php
// delete_bid.php
// Delete a bid placed by the current user

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

// Validate input
if ($bid_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid bid ID']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // First, verify that this bid belongs to the current user and not already accepted/rejected
    $verifyStmt = $conn->prepare("
        SELECT b.bid_id, b.bid_status
        FROM bids b
        WHERE b.bid_id = ? AND b.user_id = ?
    ");
    
    if (!$verifyStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $verifyStmt->bind_param('ii', $bid_id, $user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    $bidData = $verifyResult->fetch_assoc();
    $verifyStmt->close();
    
    if (!$bidData) {
        echo json_encode(['success' => false, 'message' => 'Bid not found or not authorized']);
        exit;
    }
    
    // Check if bid can be deleted (only if pending or rejected)
    if ($bidData['bid_status'] && !in_array($bidData['bid_status'], ['pending', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete an accepted bid']);
        exit;
    }
    
    // Delete the bid
    $deleteStmt = $conn->prepare("
        DELETE FROM bids 
        WHERE bid_id = ? AND user_id = ?
    ");
    
    if (!$deleteStmt) {
        throw new Exception("Delete prepare failed: " . $conn->error);
    }
    
    $deleteStmt->bind_param('ii', $bid_id, $user_id);
    
    if (!$deleteStmt->execute()) {
        throw new Exception("Delete execution failed: " . $deleteStmt->error);
    }
    
    $affected_rows = $deleteStmt->affected_rows;
    $deleteStmt->close();
    
    if ($affected_rows === 0) {
        throw new Exception("Bid could not be deleted");
    }
    
    error_log("Bid deleted: bid_id=$bid_id, user_id=$user_id");
    
    echo json_encode([
        'success' => true,
        'message' => 'Bid deleted successfully',
        'bid_id' => $bid_id
    ]);
    
} catch (Exception $e) {
    error_log("Error deleting bid: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
