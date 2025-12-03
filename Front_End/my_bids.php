<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/../Back_End/Models/Database.php";

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->threadly_connect;

// Check if bids table has new schema (product_id) or old schema (session_id)
$schemaCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
$hasProductId = ($schemaCheck && $schemaCheck->num_rows > 0);

// Check if bid_status column exists
$statusCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'bid_status'");
$hasBidStatus = ($statusCheck && $statusCheck->num_rows > 0);

// Build query based on actual schema
if ($hasProductId) {
    // New schema with product_id
    $sql = "
        SELECT 
            b.bid_id,
            b.bid_amount,
            b.created_at,
            b.user_id,
            b.product_id,
            p.product_name,
            p.image_url,
            p.price,
            b.bid_status
        FROM bids b
        LEFT JOIN products p ON b.product_id = p.product_id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ";
} else {
    // Old schema with session_id
    $sql = "
        SELECT 
            b.bid_id,
            b.bid_amount,
            " . ($hasBidStatus ? "b.created_at" : "b.bit_time as created_at") . ",
            b.user_id,
            bs.product_id,
            p.product_name,
            p.image_url,
            p.price,
            " . ($hasBidStatus ? "b.bid_status" : "'pending' as bid_status") . "
        FROM bids b
        LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
        LEFT JOIN products p ON bs.product_id = p.product_id
        WHERE b.user_id = ?
        ORDER BY " . ($hasBidStatus ? "b.created_at" : "b.bit_time") . " DESC
    ";
}

$stmt = $conn->prepare($sql);
$bids = [];

if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bids = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids - Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-accepted { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-withdrawn { background-color: #f3f4f6; color: #374151; }
        
        .delete-btn {
            background-color: #ef4444;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .delete-btn:hover {
            background-color: #dc2626;
        }
        
        .delete-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .bid-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">
    <?php require "nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?> 
    <div class="max-w-6xl mx-auto px-4 py-8">
        
        <h1 class="text-4xl font-bold text-gray-900 mb-8">My Bids</h1>

        <?php if (empty($bids)): ?>
            <div class="text-center py-20">
                <p class="text-2xl text-gray-600 mb-4">You haven't placed any bids yet.</p>
                <a href="index.php" class="text-white bg-black px-6 py-3 rounded-full font-semibold hover:bg-gray-800 transition">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($bids as $bid): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition" data-bid-id="<?= $bid['bid_id'] ?>">
                        <div class="flex flex-col md:flex-row">
                            
                            <!-- Product Image -->
                            <div class="md:w-24 flex-shrink-0">
                                <img src="<?= htmlspecialchars($bid['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($bid['product_name']) ?>"
                                     class="w-full h-24 object-cover">
                            </div>

                            <!-- Bid Details -->
                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        <a href="product_info.php?id=<?= $bid['product_id'] ?>" class="hover:text-amber-600">
                                            <?= htmlspecialchars($bid['product_name']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        Original Price: <span class="font-semibold text-gray-900">₱<?= number_format((float)$bid['price'], 2) ?></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Bid Amount -->
                            <div class="p-6 bg-gray-50 flex flex-col justify-center items-center min-w-[200px]">
                                <p class="text-gray-600 text-sm mb-2">Your Bid Amount</p>
                                <p class="text-3xl font-bold text-amber-600">₱<?= number_format((float)$bid['bid_amount'], 2) ?></p>
                            </div>

                            <!-- Status -->
                            <div class="p-6 flex flex-col justify-center items-center min-w-[180px]">
                                <span class="status-badge status-<?= $bid['bid_status'] ?> bid-status-badge">
                                    <?= ucfirst($bid['bid_status']) ?>
                                </span>
                                <p class="text-xs text-gray-500 mt-3">
                                    <?= date('M d, Y', strtotime($bid['created_at'])) ?>
                                </p>
                                <!-- Delete button - only show for pending and rejected bids -->
                                <?php if (in_array($bid['bid_status'], ['pending', 'rejected'])): ?>
                                    <div class="bid-card-actions mt-4">
                                        <button class="delete-btn" onclick="deleteBid(<?= $bid['bid_id'] ?>, this)">
                                            🗑️ Delete
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
    // Delete bid function
    function deleteBid(bidId, buttonElement) {
        if (!confirm('Are you sure you want to delete this bid? This action cannot be undone.')) {
            return;
        }
        
        // Disable button while deleting
        buttonElement.disabled = true;
        buttonElement.textContent = 'Deleting...';
        
        fetch('delete_bid.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'bid_id=' + bidId
        })
        .then(response => response.json())
        .then(data => {
            console.log('Delete response:', data);
            if (data.success) {
                // Fade out and remove the bid card
                const bidCard = document.querySelector(`[data-bid-id="${bidId}"]`);
                if (bidCard) {
                    bidCard.style.transition = 'all 0.3s ease';
                    bidCard.style.opacity = '0';
                    bidCard.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        bidCard.remove();
                        
                        // Check if there are any bids left
                        const bidCards = document.querySelectorAll('[data-bid-id]');
                        if (bidCards.length === 0) {
                            // Reload page to show "no bids" message
                            location.reload();
                        }
                    }, 300);
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to delete bid'));
                buttonElement.disabled = false;
                buttonElement.textContent = '🗑️ Delete';
            }
        })
        .catch(error => {
            alert('Error: ' + error);
            console.error('Delete error:', error);
            buttonElement.disabled = false;
            buttonElement.textContent = '🗑️ Delete';
        });
    }
    
    // Poll for bid status updates every 5 seconds
    function checkForStatusUpdates() {
        fetch('get_bids_status.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.bids && data.bids.length > 0) {
                data.bids.forEach(bid => {
                    const bidElement = document.querySelector(`[data-bid-id="${bid.bid_id}"]`);
                    if (bidElement) {
                        const statusElement = bidElement.querySelector('.bid-status-badge');
                        const currentStatus = statusElement.textContent.toLowerCase().trim();
                        const newStatus = bid.bid_status.toLowerCase();
                        
                        // Update if status changed
                        if (currentStatus !== newStatus) {
                            // Update class
                            statusElement.className = `status-badge status-${newStatus} bid-status-badge`;
                            statusElement.textContent = bid.bid_status.charAt(0).toUpperCase() + bid.bid_status.slice(1);
                            
                            // Flash animation
                            bidElement.style.backgroundColor = '#e8f5e9';
                            setTimeout(() => {
                                bidElement.style.transition = 'background-color 0.3s ease';
                                bidElement.style.backgroundColor = 'white';
                            }, 100);
                            
                            // If status is now accepted, hide delete button
                            if (newStatus === 'accepted') {
                                const deleteBtn = bidElement.querySelector('.delete-btn');
                                if (deleteBtn) {
                                    deleteBtn.parentElement.style.display = 'none';
                                }
                            }
                        }
                    }
                });
            }
        })
        .catch(error => console.log('Status check:', error));
    }
    
    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Check immediately
        checkForStatusUpdates();
        
        // Then check every 5 seconds
        setInterval(checkForStatusUpdates, 5000);
    });
    </script>

</body>
</html>
