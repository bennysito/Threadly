<?php
// product_info.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⭐ 1. REQUIRED FILES FOR SELLER CARD FUNCTIONALITY ⭐
// We need Database.php for the seller profile lookup inside the function
require_once __DIR__ . "/../Back_End/Models/Database.php"; 

$product = null;
$productId = null;
$product_seller_id = null; // Initialize the new variable

// --------------------------------------------------------------------------------------------------
// ⭐ NEW FUNCTION: Renders the Seller Profile Card ⭐
// --------------------------------------------------------------------------------------------------

function render_seller_profile_card($seller_id) {
    if (!$seller_id) return;
    
    // Using a new Database connection instance for the function
    $db = new Database();
    $conn = $db->threadly_connect;
    
    // Assuming your user table is named 'users' and uses 'user_id' for the primary key
    $sql = "SELECT username, profile_picture FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // echo "<p class='text-red-500'>Error fetching seller data: " . $conn->error . "</p>";
        return;
    }
    
    $stmt->bind_param('i', $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();
    $stmt->close();
    $db->close_db();

    if ($seller) {
        $username = htmlspecialchars($seller['username'] ?? 'Anonymous Seller');
        // Assuming profile pictures are in the 'uploads' directory
        $profilePic = htmlspecialchars($seller['profile_picture'] ?? 'default_profile.png');
        
        // Link points to the seller's dashboard, passing the user ID
        $dashboardLink = "seller_dashboard.php?view_user=" . $seller_id; 

        echo "
        <div class='seller-profile-card bg-white p-6 shadow-lg rounded-lg border border-gray-100 mt-8'>
            <h3 class='text-xl font-semibold mb-4 text-gray-700 border-b pb-2'>Sold By</h3>
            
            <div class='flex items-center space-x-4 mb-4'>
                
                <a href='{$dashboardLink}' class='flex-shrink-0 cursor-pointer'>
                    <img src='uploads/{$profilePic}' alt='{$username}' 
                                 class='w-16 h-16 object-cover rounded-full border-2 border-amber-500'>
                </a>
                
                <div class='flex-1'>
                    <a href='{$dashboardLink}' class='text-2xl font-bold text-gray-900 hover:text-amber-600 transition-colors'>{$username}</a>
                    <p class='text-sm text-gray-500'>View this seller's products.</p> 
                </div>
            </div>
            
            <a href='{$dashboardLink}' class='inline-block w-full text-center bg-amber-500 text-white font-semibold py-3 rounded-lg 
                                 hover:bg-amber-600 transition-colors duration-200 shadow-md'>
                View All {$username}'s Products
            </a>
        </div>
        ";
    }
}

// --------------------------------------------------------------------------------------------------
// END OF FUNCTION DEFINITION
// --------------------------------------------------------------------------------------------------

// === 1. Try to get product by ID (Recommended & Modern Way) ===
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id'];
    require_once __DIR__ . "/../Back_End/Models/Search_db.php";
    $search = new Search();
    $product = $search->getById($productId);
}

// === 2. Fallback for old links (still supported) ===
if (!$product && isset($_GET['name'])) {
    $product = [
        'name'          => $_GET['name'] ?? 'Unknown Product',
        'image'         => $_GET['image'] ?? 'panti.png',
        'hover_image'   => $_GET['hover_image'] ?? 'underwear_women.png',
        'price'         => max(0, (int)($_GET['price'] ?? 0)),
        'category'      => $_GET['category'] ?? 'Products',
        'description'   => 'No description provided.',
        'condition'     => 'Gently Used',
        'sizes'         => 'M', // fallback
    ];
}

// === 3. If still no product → 404 ===
if (!$product) {
    http_response_code(404);
    die('<div class="text-center py-20"><h1 class="text-4xl font-bold text-gray-800">404 - Product Not Found</h1></div>');
}

// === 4. Extract Seller Data Safely ===
// IMPORTANT: Use the key returned by Search::getById (e.g., 'user_id', 'seller_id', or an alias like 'product_seller_id')
$productSellerId = $product['product_seller_id'] ?? $product['seller_id'] ?? $product['user_id'] ?? $product['owner_id'] ?? null;

// === 5. Extract other data safely (Only user-entered fields) ===
$productName        = $product['name'] ?? 'Unknown Product';
$productImage       = $product['image'] ?? 'panti.png';
$productPrice       = (float)($product['price'] ?? 0);
$categoryName       = $product['category'] ?? 'Products';
$description        = $product['description'] ?? 'No description available.';
$quantity           = $product['availability'] ?? 'available';
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
        .heart-icon.liked { fill: #ef4444 !important; stroke: #ef4444 !important; }
        .size-btn.active { background-color: black; color: white; border-color: black; }
        .thumbnail { transition: all 0.3s; }
        .thumbnail:hover { border-color: #1f2937 !important; transform: scale(1.05); }
        /* Add CSS for JS-controlled animations */
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            10%, 90% { opacity: 1; }
        }
        /* Add for the JS functions to work */
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
            <a href="category.php?category=<?= urlencode($categoryName) ?>" class="hover:text-black">
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

                    <button onclick="toggleWishlist(this, <?= $productId ?? 'null' ?>)"
                                 class="absolute top-4 right-4 p-3 bg-white rounded-full shadow-xl z-10 hover:scale-110 transition">
                        <svg class="heart-icon w-7 h-7 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

                <div class="border-t pt-6 mt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Make an Offer (Bidding)</h3>
                    <div id="biddingSection" class="space-y-3">
                        <input type="number" id="bidAmount" placeholder="Enter your bid amount" 
                                         min="<?= number_format($productPrice, 2) ?>" step="0.01" 
                                         class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500" 
                                         value="<?= number_format($productPrice, 2) ?>">
                        <textarea id="bidMessage" placeholder="Add a message (optional)" 
                                         class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" 
                                         rows="3"></textarea>
                        <button onclick="placeBid(<?= $productId ?? 'null' ?>)"
                                         class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold hover:bg-amber-600 transition">
                            PLACE BID
                        </button>
                        <div id="bidStatus" class="text-sm text-gray-600 hidden"></div>
                        <div id="highestBidInfo" class="text-sm text-gray-600 p-3 bg-gray-50 rounded-lg hidden">
                            <p class="font-semibold">Current Highest Bid:</p>
                            <p id="highestBidAmount"></p>
                            <p id="highestBidder"></p>
                            <p id="totalBids"></p>
                        </div>
                    </div>
                </div>

                <button onclick="addToBag(<?= $productId ?? 'null' ?>)"
                                 class="w-full bg-black text-white py-4 rounded-full font-bold text-lg hover:bg-gray-800 transition flex items-center justify-center gap-3 shadow-lg mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    ADD TO BAG
                </button>

                <?php if ($productSellerId): ?>
                    <section id="seller-details-section">
                        <?php render_seller_profile_card($productSellerId); ?>
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
    </script>
    
    <script>
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
         * ⭐ IMPORTANT: THIS HANDLER REQUIRES AN ELEMENT WITH ID="profileButton" 
         * TO BE PRESENT IN YOUR Nav_bar.php FILE.
         */
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton'); 
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    showAccountSettings();
                });
            }
        });
    </script>
    
    <script src="js/product_page_functions.js"></script>
    <script>
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        if(profileBtn) {
            profileBtn.addEventListener('click', () => {
                profileDropdown.classList.toggle('hidden');
            });
        }
        // Load bid info on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof CURRENT_PRODUCT_ID !== 'undefined' && CURRENT_PRODUCT_ID) {
                // Assuming 'loadBidInfo' is defined in product_page_functions.js
                if (typeof loadBidInfo === 'function') {
                    loadBidInfo(CURRENT_PRODUCT_ID);
                }
            }
        });
    </script>
</body>
</html>