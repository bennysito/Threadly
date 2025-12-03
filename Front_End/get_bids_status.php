<?php
/**
 * get_bids_status.php
 * Returns current bid status for user's bids (used for live updates on my_bids.php)
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . "/../Back_End/Models/Database.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->threadly_connect;

try {
    // Check schema
    $schemaCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
    $hasProductId = ($schemaCheck && $schemaCheck->num_rows > 0);
    
    $statusCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_status'");
    $hasBidStatus = ($statusCheck && $statusCheck->num_rows > 0);
    
    // Build query based on schema
    if ($hasProductId) {
        // New schema
        $sql = "
            SELECT 
                bid_id,
                bid_status
            FROM bids
            WHERE user_id = ?
        ";
    } else {
        // Old schema
        $sql = "
            SELECT 
                bid_id,
                " . ($hasBidStatus ? "bid_status" : "'pending' as bid_status") . "
            FROM bids
            WHERE user_id = ?
        ";
    }
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $bids = [];
    
    while ($row = $result->fetch_assoc()) {
        $bids[] = [
            'bid_id' => intval($row['bid_id']),
            'bid_status' => $row['bid_status']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'bids' => $bids
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
