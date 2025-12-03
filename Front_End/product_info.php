<?php
// product_info.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product = null;
$productId = null;
$productSellerId = null;

// --- STATIC POOL OF NAMES FOR SIMULATION ---
// This array is used to consistently generate a random-looking name based on seller ID.
$seller_name_pool = [
    'EchoThreads', 'VibrantWares', 'ZenithMarket', 'SwiftGoods', 'UrbanCanvas',
    'GlobalSource', 'CraftedDreams', 'SolarDeals', 'NexusRetail', 'PinnacleShop',
    'BlueSkies Trading', 'GreenLeaf Goods', 'Starlight Merchants', 'Crimson Peak Sales'
];

// --------------------------------------------------------------------------------------------------
// ⭐ FUNCTION: Generates a consistent random name based on SELLER ID + PRODUCT ID ⭐
// --------------------------------------------------------------------------------------------------
function generate_random_seller_name($seller_id, $product_id, $pool) {
    // --- ADDITIONAL STATIC POOL FOR SIMULATION ---
    $modifier_pool = [
        'Premium', 'Elite', 'Curated', 'Direct', 'Exclusive',
        'Official', 'Trusted', 'Deluxe', 'Artisan', 'Fine'
    ];
    
    // --- KEY CHANGE: Combine Seller ID and Product ID to create the seed ---
    // This ensures that the same seller has a different 'random' name for each item.
    $seed_string = $seller_id . '_' . $product_id;
    
    // Convert the combined string into a numeric hash
    $hash = crc32($seed_string);
    
    // Use the hash to seed the random number generator
    // This ensures the same SELLER+PRODUCT ID combination always produces the same name
    mt_srand($hash);
    
    // 1. Select a base name from the primary pool
    $base_name_index = mt_rand(0, count($pool) - 1);
    $base_name = $pool[$base_name_index];
    
    // 2. Select a modifier/adjective
    $modifier_index = mt_rand(0, count($modifier_pool) - 1);
    $modifier = $modifier_pool[$modifier_index];

    // 3. Select a random number suffix (100 to 999)
    $suffix_number = mt_rand(100, 999);
    
    // Concatenate the parts for a more complex, yet consistent, random name.
    return $modifier . ' ' . $base_name . ' ' . $suffix_number;
}

// --------------------------------------------------------------------------------------------------
// ⭐ FUNCTION: Renders the Seller Profile Card ⭐
// --------------------------------------------------------------------------------------------------

function render_seller_profile_card($seller_id, $product_id) { // Now requires $product_id
    global $seller_name_pool; // Access the global name pool

    if (!$seller_id || !$product_id) return;
    
    // --- SIMULATED SELLER DATA (Replace with actual database fetch) ---
    // Generate the consistent, randomized seller name using BOTH IDs
    $username = generate_random_seller_name($seller_id, $product_id, $seller_name_pool);
    
    $profilePicPath = 'Images/man3.png'; // Placeholder image
    
    $dashboardLink = "seller_dashboard.php?view_user=" . urlencode($seller_id);

    $safeDashboardLink = htmlspecialchars($dashboardLink);
    $safeProfilePicPath = htmlspecialchars($profilePicPath);
    $safeSellerId = htmlspecialchars($seller_id);
    $safeUsername = htmlspecialchars($username);

    echo "
    <div class='seller-profile-card bg-white p-6 shadow-lg rounded-lg border border-gray-100 mt-8'>
        <h3 class='text-xl font-semibold mb-4 text-gray-700 border-b pb-2'>Sold By</h3>
        
        <div class='flex items-center space-x-4 mb-4'>
            
            <a href='{$safeDashboardLink}' class='flex-shrink-0 cursor-pointer'>
                <img src='{$safeProfilePicPath}' alt='{$safeUsername}'
                    class='w-16 h-16 object-cover rounded-full border-2 border-amber-500'>
            </a>
            
            <div class='flex-1'>
                <a href='{$safeDashboardLink}' class='text-2xl font-bold text-gray-900 hover:text-amber-600 transition-colors'>{$safeUsername}</a>
                <p class='text-sm text-gray-500'>View this seller's products.</p>
            </div>
        </div>
        
        <div class='flex space-x-3 mt-4'>
            <a href='view_products.php' class='flex-1 text-center bg-gray-200 text-gray-800 font-semibold py-3 rounded-lg
                hover:bg-gray-300 transition-colors duration-200 shadow-sm'>
                View All Products
            </a>
            <button onclick='chatNowSeller(\"{$safeSellerId}\", \"{$safeUsername}\")' class='flex-1 text-center bg-amber-500 text-white font-semibold py-3 rounded-lg
                hover:bg-amber-600 transition-colors duration-200 shadow-md'>
                Chat Now
            </button>
        </div>
    </div>
    ";
}

// --------------------------------------------------------------------------------------------------
// END OF FUNCTION DEFINITION
// --------------------------------------------------------------------------------------------------

// === 1. Try to get product by ID ===
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $searchFilePath = __DIR__ . "/../Back_End/Models/Search_db.php";
    
    if (file_exists($searchFilePath)) {
        require_once $searchFilePath;
        $search = new Search();
        $product = $search->getById($productId);
    } else {
        // Fallback for environment where backend path is not correct (use hardcoded data)
        // Simulated Seller ID is 101 for this fallback
        $product = [
            'product_id'    => $productId,
            'seller_id'     => 101, // Simulated Seller ID
            'name'      => 'Premium Comfort Knit Sweater',
            'image'         => 'Images/default_product.png',
            'price'         => 850.00,
            'category'      => 'Clothing',
            'description'   => "A soft, luxurious knit sweater designed for maximum comfort and style. Perfect for chilly evenings. Made from 100% organic cotton.\n\nMaterial: Cotton\nCondition: New",
        ];
    }
}

// === 2. If still no product → 404 ===
if (!$product) {
    http_response_code(404);
    die('<div class="text-center py-20"><h1 class="text-4xl font-bold text-gray-800">404 - Product Not Found</h1><p class="mt-4 text-lg text-gray-600">The product ID or link is invalid.</p></div>');
}

// === 3. Extract Data Safely ===
$productId          = $product['product_id'] ?? $productId;
$productSellerId    = $product['seller_id'] ?? $product['user_id'] ?? $product['owner_id'] ?? 101;
$productName        = $product['name'] ?? 'Unknown Product';
$productImage       = $product['image'] ?? 'Images/panti.png';
$productPrice       = (float)($product['price'] ?? 0);
$categoryName       = $product['category'] ?? 'Products';
$description        = $product['description'] ?? 'No description available.';
$quantity           = $product['availability'] ?? 'available';

// === 3.5 Check if Bidding is Enabled ===
$biddingEnabled = false;
if ($productId && file_exists(__DIR__ . '/../Back_End/Models/Database.php')) {
    require_once __DIR__ . '/../Back_End/Models/Database.php';
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // Check if bidding column exists and is enabled
    $colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
    if ($colRes && $colRes->num_rows > 0) {
        $stmt = $conn->prepare("SELECT bidding FROM products WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $biddingEnabled = ($row && $row['bidding'] == 1);
            $stmt->close();
        }
    }
    $db->close_db();
}

// === 4. CHECK WISHLIST STATUS (NEW/FIXED LOGIC) ===
$isProductInWishlist = false;

if (isset($_SESSION['user_id']) && $productId) {
    // Check if the required file exists before requiring it
    $dbPath = __DIR__ . '/../Back_End/Models/Database.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
        $db = new Database();
        $conn = $db->threadly_connect;
        
        $stmt = $conn->prepare("
            SELECT 1
            FROM wishlist w
            JOIN wishlist_item wi ON w.wishlist_id = wi.wishlist_id
            WHERE w.user_id = ? AND wi.product_id = ?
            LIMIT 1
        ");
        
        if ($stmt) {
            $stmt->bind_param('ii', $_SESSION['user_id'], $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $isProductInWishlist = true;
            }
            
            $stmt->close();
        }
        
        $db->close_db();
    }
}
// ================================================

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($productName) ?> - Threaldy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/produc_info.css">
    <style>
        .heart-icon.active { fill: #ef4444 !important; stroke: #ef4444 !important; }
        .size-btn.active { background-color: black; color: white; border-color: black; }
        @keyframes fadeInOut { 0%, 100% { opacity: 0; } 10%, 90% { opacity: 1; } }
        .animate-slideIn { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">

    <?php require "Nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?>
    <?php require "add_to_bag.php"; ?>
    <?php require "messages_panel.php"; ?>
    
    <div class="max-w-7xl mx-auto px-4 py-8">

        <nav class="text-sm text-gray-600 mb-8">
            <a href="index.php" class="hover:text-black">Home</a>
            <span class="mx-2">></span>
            <a href="<?= htmlspecialchars("category.php?category=" . urlencode($categoryName)) ?>" class="hover:text-black">
                <?= htmlspecialchars($categoryName) ?>
            </a>
            <span class="mx-2">></span>
            <span class="text-black font-medium"><?= htmlspecialchars($productName) ?></span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            <div class="flex flex-col gap-5">

                <div class="relative bg-gray-100 rounded-2xl overflow-hidden aspect-square shadow-lg">
                    <img id="mainImage"
                        src="<?= htmlspecialchars($productImage) ?>"
                        alt="<?= htmlspecialchars($productName) ?>"
                        class="w-full h-full object-cover">

                    <button onclick="window.toggleWishlist(<?= $productId ?? 'null' ?>)"
                        class="absolute top-4 right-4 p-3 bg-white rounded-full shadow-xl z-10 hover:scale-110 transition"
                        data-product-id="<?= $productId ?? 'null' ?>">
                        <svg class="heart-icon heart w-7 h-7 text-gray-700 <?= $isProductInWishlist ? 'active' : '' ?>"
                            data-product-id="<?= $productId ?? 'null' ?>"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-6">

                <h1 class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($productName) ?></h1>

                <div class="text-4xl font-bold text-gray-900">₱<?= number_format($productPrice, 2) ?></div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Category</label>
                    <p class="text-gray-700"><?= htmlspecialchars($categoryName) ?></p>
                </div>

                

                <button id="addToBagBtn" onclick="addToBag(<?= $productId ?? 'null' ?>)"
                    class="w-full bg-black text-white py-4 rounded-full font-bold text-lg hover:bg-gray-800 transition flex items-center justify-center gap-3 shadow-lg mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    ADD TO BAG
                </button>

                <?php if ($biddingEnabled): ?>
                    <div class="border-2 border-amber-500 bg-amber-50 rounded-xl p-6 mt-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-amber-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.997 6.91a.75.75 0 1 0-1.5 0 .75.75 0 0 0 1.5 0ZM9.997 12a.75.75 0 1 0-1.5 0 .75.75 0 0 0 1.5 0ZM9.997 17.09a.75.75 0 1 0-1.5 0 .75.75 0 0 0 1.5 0ZM15 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            <h3 class="text-xl font-bold text-amber-900">PLACE A BID</h3>
                        </div>
                        
                        <p class="text-sm text-amber-800 mb-4">Starting price: <span class="font-bold">₱<?= number_format($productPrice, 2) ?></span></p>

                        <form id="bidForm" onsubmit="submitBid(event, <?= $productId ?>)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Your Bid Amount (₱)</label>
                                <input type="number" id="bidAmount" name="bid_amount" step="0.01" min="<?= $productPrice ?>" 
                                    placeholder="Enter amount (minimum ₱<?= number_format($productPrice, 2) ?>)"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                    required>
                                <p class="text-xs text-gray-600 mt-1">Bid must be at least ₱<?= number_format($productPrice, 2) ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Message (Optional)</label>
                                <textarea id="bidMessage" name="bid_message" rows="3" 
                                    placeholder="Add a message for the seller..."
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-bold hover:bg-amber-700 transition flex items-center justify-center gap-2 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-7H6" />
                                </svg>
                                PLACE BID
                            </button>
                        </form>

                        <div id="bidStatus" class="mt-4 hidden p-3 rounded-lg text-sm font-medium"></div>

                        <div id="biddingInfo" class="mt-6 pt-6 border-t border-amber-300">
                            <p class="text-sm text-amber-800 mb-3 font-medium">Current Bidding Info:</p>
                            <div id="bidInfoContent" class="text-sm text-amber-700 space-y-2">
                                <p>Loading bidding information...</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($productSellerId && $productId): ?>
                    <section id="seller-details-section">
                        <?php render_seller_profile_card($productSellerId, $productId); ?>
                    </section>
                <?php endif; ?>
                

                <div class="border-t pt-6 mt-6">
                    <details class="group">
                        <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600 flex justify-between items-center">
                            Description
                            <span class="text-xl group-open:rotate-180 transition">↓</span>
                        </summary>
                        <p class="text-gray-600 mt-3 leading-relaxed"><?= nl2br(htmlspecialchars($description)) ?></p>
                    </details>
                </div>
            </div>
        </div>
    </div>

    <div id="accountSettingsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 overflow-y-auto" onclick="hideAccountSettings()">
        <div class="relative w-full max-w-lg mx-auto p-4 md:p-8 mt-20" onclick="event.stopPropagation()">
            
            <div class="bg-white rounded-xl shadow-2xl">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-2xl font-bold text-gray-800">Account Settings</h3>
                    <button type="button" onclick="hideAccountSettings()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <p class="text-gray-600">Manage your account profile, security, and seller status.</p>
                    
                    <a href="profile_management.php" class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg font-medium text-gray-700 transition">
                        Profile Management
                    </a>
                    <a href="change_password.php" class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg font-medium text-gray-700 transition">
                        Security & Password
                    </a>
                    <a href="seller_dashboard.php" class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg font-medium text-gray-700 transition">
                        Seller Dashboard
                    </a>
                    
                    <hr class="my-2">
                    
                    <a href="logout.php" class="w-full text-center block p-3 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
                        Log Out
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const CURRENT_PRODUCT_ID = <?= $productId ?? 'null' ?>;
        const CURRENT_SELLER_ID = <?= $productSellerId ?? 'null' ?>;
        const BIDDING_ENABLED = <?= $biddingEnabled ? 'true' : 'false' ?>;
        const PRODUCT_PRICE = <?= $productPrice ?>;
    </script>
    
    <script>
        /**
         * Submit a bid on the product
         */
        function submitBid(event, productId) {
            event.preventDefault();
            
            if (!productId) {
                alert('No product selected');
                return;
            }

            // Check if user is logged in
            if (!<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>) {
                alert('Please log in to place a bid');
                window.location.href = 'login.php';
                return;
            }

            const bidAmount = document.getElementById('bidAmount').value;
            const bidMessage = document.getElementById('bidMessage').value;
            const statusDiv = document.getElementById('bidStatus');
            const submitBtn = event.target.querySelector('button[type="submit"]');

            // Validate bid amount
            if (!bidAmount || isNaN(bidAmount) || parseFloat(bidAmount) < PRODUCT_PRICE) {
                statusDiv.className = 'mt-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                statusDiv.textContent = 'Bid amount must be at least ₱' + PRODUCT_PRICE.toFixed(2);
                statusDiv.classList.remove('hidden');
                return;
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Placing bid...';
            statusDiv.classList.add('hidden');

            // Send bid to server
            fetch('place_bid.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + encodeURIComponent(productId) + 
                      '&bid_amount=' + encodeURIComponent(bidAmount) +
                      '&bid_message=' + encodeURIComponent(bidMessage)
            })
            .then(r => r.json())
            .then(data => {
                statusDiv.classList.remove('hidden');
                
                if (data.success) {
                    statusDiv.className = 'mt-4 p-3 rounded-lg text-sm font-medium bg-green-100 text-green-700';
                    statusDiv.textContent = '✅ ' + data.message;
                    
                    // Reset form
                    document.getElementById('bidForm').reset();
                    
                    // Reload bid info
                    loadBidInfo(productId);
                    
                    // Re-enable button after 2 seconds
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'PLACE BID';
                    }, 2000);
                } else {
                    statusDiv.className = 'mt-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                    statusDiv.textContent = '❌ ' + (data.message || 'Failed to place bid');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'PLACE BID';
                }
            })
            .catch(err => {
                console.error('Bid error:', err);
                statusDiv.className = 'mt-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                statusDiv.textContent = '❌ Network error while placing bid';
                statusDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'PLACE BID';
            });
        }

        /**
         * Load bidding information for the product
         */
        function loadBidInfo(productId) {
            if (!productId) return;

            fetch('get_bids.php?product_id=' + encodeURIComponent(productId))
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const content = document.getElementById('bidInfoContent');
                if (!content) return;

                let html = '';

                // Highest bid
                if (data.highest_bid) {
                    html += '<div class="bg-amber-100 p-2 rounded">';
                    html += '<p class="font-semibold">💰 Highest Bid: ₱' + parseFloat(data.highest_bid.bid_amount).toFixed(2) + '</p>';
                    html += '</div>';
                } else {
                    html += '<p>No bids yet. Be the first to bid!</p>';
                }

                // Total bids count
                if (data.all_bids_count > 0) {
                    html += '<p>Total bids: <span class="font-semibold">' + data.all_bids_count + '</span></p>';
                }

                // User's bid
                if (data.user_bid) {
                    html += '<div class="bg-green-100 p-2 rounded mt-2">';
                    html += '<p class="font-semibold">✓ Your Bid: ₱' + parseFloat(data.user_bid.bid_amount).toFixed(2) + '</p>';
                    html += '<p class="text-xs">Status: ' + (data.user_bid.bid_status || 'Pending') + '</p>';
                    if (data.user_bid.bid_message) {
                        html += '<p class="text-xs italic">Message: ' + htmlEscape(data.user_bid.bid_message) + '</p>';
                    }
                    html += '</div>';
                }

                content.innerHTML = html;
            })
            .catch(err => console.error('Error loading bid info:', err));
        }

        /**
         * Escape HTML special characters
         */
        function htmlEscape(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load bid info when page loads (if bidding is enabled)
        document.addEventListener('DOMContentLoaded', function() {
            if (BIDDING_ENABLED && CURRENT_PRODUCT_ID) {
                loadBidInfo(CURRENT_PRODUCT_ID);
                
                // Refresh bid info every 10 seconds
                setInterval(() => {
                    loadBidInfo(CURRENT_PRODUCT_ID);
                }, 10000);
            }
        });
    </script>
    
    <script>
        /**
         * Account Settings Modal Handlers
         */
        function showAccountSettings() {
            const modal = document.getElementById('accountSettingsModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // Prevent background scrolling
            }
        }

        function hideAccountSettings() {
            const modal = document.getElementById('accountSettingsModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        /**
         * ⭐ CHAT NOW HANDLER ⭐
         */
        function chatNowSeller(sellerId, sellerName) {
            // Check if the chat function is globally available
            if (typeof window.showMessagesPanel === 'function') {
                window.showMessagesPanel({
                    contactId: sellerId,
                    contactName: sellerName,
                    isNewChat: true, // Indicate a new chat intent
                    // productId: CURRENT_PRODUCT_ID
                });
                console.log("Chat Now clicked for Seller ID:", sellerId);
            } else {
                alert("Error: Chat functionality (showMessagesPanel) is not loaded. Ensure messages_panel.php is included correctly.");
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Handler for the main profile button (assumed to be in Nav_bar.php)
            const profileButton = document.getElementById('profileButton');
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    showAccountSettings();
                });
            }
            
            if (typeof CURRENT_PRODUCT_ID !== 'undefined' && CURRENT_PRODUCT_ID) {
                if (typeof loadBidInfo === 'function') {
                    // loadBidInfo(CURRENT_PRODUCT_ID);
                }
        
                // Add to Bag — calls add_to_cart.php and refreshes the bag panel
                function addToBag(productId, qty = 1) {
                    if (!productId) return alert('No product selected');

                    const btn = document.getElementById('addToBagBtn');
                    if (btn) btn.disabled = true;

                    fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'product_id=' + encodeURIComponent(productId) + '&qty=' + encodeURIComponent(qty)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data || !data.success) {
                            if (data && data.message && data.message.includes('log in')) {
                                window.location.href = 'login.php';
                                return;
                            }
                            alert(data.message || 'Failed to add item to bag');
                            return;
                        }

                        // Optional: show the bag panel and refresh it
                        // Refresh the bag contents
                        if (typeof window.refreshBagPanelContent === 'function') window.refreshBagPanelContent();

                        // Try to open the bag panel using the exported function; if missing, fallback to direct DOM manipulation
                        if (typeof window.openBagPanel === 'function') {
                            window.openBagPanel();
                        } else {
                            const panel = document.getElementById('bagPanel');
                            const overlay = document.getElementById('bagOverlay');
                            if (panel && overlay) {
                                panel.classList.remove('translate-x-full');
                                overlay.classList.remove('hidden');
                                document.body.style.overflow = 'hidden';
                            }
                        }

                        if (typeof showToast === 'function') showToast('Added to bag');
                    })
                    .catch(err => {
                        console.error('Add to bag failed', err);
                        alert('Network error while adding to bag');
                    })
                    .finally(() => { if (btn) btn.disabled = false; });
                }
                
                // Expose addToBag function globally for the button click
                window.addToBag = addToBag;
            }
        });
    </script>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Navbar and Panel Toggles ---
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            if(profileBtn) {
                profileBtn.addEventListener('click', () => {
                    profileDropdown.classList.toggle('hidden');
                });
            }
        });
    </script>

    <script src="js/product_page_functions.js"></script>
</body>
</html>