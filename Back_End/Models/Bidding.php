<?php
require_once "Database.php";

class Bidding {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->threadly_connect;
    }

    // Create bidding table if it doesn't exist
    public function createBiddingTable() {
        $sql = "CREATE TABLE IF NOT EXISTS bids (
            bid_id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            bid_amount DECIMAL(10, 2) NOT NULL,
            bid_status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
            bid_message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            INDEX idx_product_id (product_id),
            INDEX idx_user_id (user_id),
            INDEX idx_status (bid_status)
        )";
        
        if ($this->conn->query($sql) === FALSE) {
            error_log("Bidding table creation error: " . $this->conn->error);
            return false;
        }
        return true;
    }

    // Place a new bid
    public function placeBid($product_id, $user_id, $bid_amount, $bid_message = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO bids (product_id, user_id, bid_amount, bid_message, bid_status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param('iids', $product_id, $user_id, $bid_amount, $bid_message);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Bid placed successfully', 'bid_id' => $this->conn->insert_id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to place bid: ' . $error];
        }
    }

    // Get all bids for a product
    public function getBidsForProduct($product_id) {
        $stmt = $this->conn->prepare("
            SELECT b.bid_id, b.product_id, b.bid_message, b.bid_status, b.session_id, b.bid_amount,
            ,b.bit_team, u.full_name, u.email
            FROM bids b
            JOIN users u ON b.user_id = u.user_id
            WHERE b.product_id = ?
            ORDER BY b.bid_amount DESC, b.created_at DESC
        ");
        
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bids = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $bids;
    }

    // Get user's bids
    public function getUserBids($user_id) {
        $stmt = $this->conn->prepare("
            SELECT b.bid_id, b.product_id, b.bid_message, b.bid_status, b.session_id, b.bid_amount,
            b.bit_time, p.product_name, p.image_url, p.price
            FROM bids b
            JOIN products p ON b.product_id = p.product_id
            WHERE b.user_id = ?
            ORDER BY b.bit_time DESC
        ");
        
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bids = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $bids;
    }

    // Get user's bid for a specific product
    public function getUserBidForProduct($user_id, $product_id) {
        $stmt = $this->conn->prepare("
            SELECT b.bid_id, b.bid_amount, b.bid_status, b.bid_message, b.created_at
            FROM bids b
            WHERE b.user_id = ? AND b.product_id = ?
            ORDER BY b.created_at DESC
            LIMIT 1
        ");
        
        $stmt->bind_param('ii', $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bid = $result->fetch_assoc();
        $stmt->close();
        
        return $bid;
    }

    // Get highest bid for a product
    public function getHighestBid($product_id) {
        $stmt = $this->conn->prepare("
            SELECT b.bid_id, b.user_id, b.bid_amount, b.bid_status, b.created_at, u.full_name
            FROM bids b
            JOIN users u ON b.user_id = u.user_id
            WHERE b.product_id = ? AND b.bid_status = 'pending'
            ORDER BY b.bid_amount DESC, b.created_at DESC
            LIMIT 1
        ");
        
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bid = $result->fetch_assoc();
        $stmt->close();
        
        return $bid;
    }

    // Update bid status
    public function updateBidStatus($bid_id, $status) {
        $valid_statuses = ['pending', 'accepted', 'rejected', 'withdrawn'];
        if (!in_array($status, $valid_statuses)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE bids SET bid_status = ? WHERE bid_id = ?");
        $stmt->bind_param('si', $status, $bid_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    // Withdraw a bid
    public function withdrawBid($bid_id, $user_id) {
        $stmt = $this->conn->prepare("
            UPDATE bids SET bid_status = 'withdrawn' 
            WHERE bid_id = ? AND user_id = ? AND bid_status = 'pending'
        ");
        
        $stmt->bind_param('ii', $bid_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    // Delete old/rejected bids (cleanup)
    public function deleteBid($bid_id) {
        $stmt = $this->conn->prepare("DELETE FROM bids WHERE bid_id = ?");
        $stmt->bind_param('i', $bid_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function closeConnection() {
        $this->db->close_db();
    }
}
?>
