<?php
// get_bids.php
// Get bid information for a product

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Bidding.php";

header('Content-Type: application/json');

$product_id = intval($_GET['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $bidding = new Bidding();
    
    // Ensure table exists
    $bidding->createBiddingTable();
    
    // Get highest bid
    $highest_bid = $bidding->getHighestBid($product_id);
    
    // Get all bids
    $all_bids = $bidding->getBidsForProduct($product_id);
    
    // Get user's bid if logged in
    $user_bid = null;
    if (isset($_SESSION['user_id'])) {
        $user_bid = $bidding->getUserBidForProduct($_SESSION['user_id'], $product_id);
    }
    
    echo json_encode([
        'success' => true,
        'highest_bid' => $highest_bid,
        'all_bids_count' => count($all_bids),
        'user_bid' => $user_bid,
        'all_bids' => $all_bids
    ]);
    
    $bidding->closeConnection();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
