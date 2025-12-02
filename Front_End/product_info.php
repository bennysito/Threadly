<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product = null;
$productId = null;

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
        'name'        => $_GET['name'] ?? 'Unknown Product',
        'image'       => $_GET['image'] ?? 'panti.png',
        'hover_image' => $_GET['hover_image'] ?? 'underwear_women.png',
        'price'       => max(0, (int)($_GET['price'] ?? 0)),
        'category'    => $_GET['category'] ?? 'Products',
        'description' => 'No description provided.',
        'condition'   => 'Gently Used',
        'sizes'       => 'M', // fallback
    ];
}

// === 3. If still no product → 404 ===
if (!$product) {
    http_response_code(404);
    die('<div class="text-center py-20"><h1 class="text-4xl font-bold text-gray-800">404 - Product Not Found</h1></div>');
}

// === Extract data safely (Only user-entered fields) ===
$productName       = $product['name'] ?? 'Unknown Product';
$productImage      = $product['image'] ?? 'panti.png';
$productPrice      = (float)($product['price'] ?? 0);
$categoryName      = $product['category'] ?? 'Products';
$description       = $product['description'] ?? 'No description available.';
$quantity          = $product['availability'] ?? 'available';
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
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">

    <!-- Navigation -->
    <?php require "Nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?> 

    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Breadcrumb -->
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

            <!-- Image Gallery -->
            <div class="flex flex-col gap-5">

                <!-- Main Image -->
                <div class="relative bg-gray-100 rounded-2xl overflow-hidden aspect-square shadow-lg">
                    <img id="mainImage"
                         src="<?= htmlspecialchars($productImage) ?>"
                         alt="<?= htmlspecialchars($productName) ?>"
                         class="w-full h-full object-cover">

                    <!-- Wishlist Heart -->
                    <button onclick="toggleWishlist(this, <?= $productId ?? 'null' ?>)"
                            class="absolute top-4 right-4 p-3 bg-white rounded-full shadow-xl z-10 hover:scale-110 transition">
                        <svg class="heart-icon w-7 h-7 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex flex-col gap-6">

                <h1 class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($productName) ?></h1>

                <div class="text-4xl font-bold text-gray-900">₱<?= number_format($productPrice, 2) ?></div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Category</label>
                    <p class="text-gray-700"><?= htmlspecialchars($categoryName) ?></p>
                </div>

                <!-- Bidding Section -->
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

                <!-- Add to Bag -->
                <button onclick="addToBag(<?= $productId ?? 'null' ?>)"
                        class="w-full bg-black text-white py-4 rounded-full font-bold text-lg hover:bg-gray-800 transition flex items-center justify-center gap-3 shadow-lg mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    ADD TO BAG
                </button>

                <!-- Description Section -->
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

    <script>
        function toggleWishlist(btn, productId) {
            if (!productId) {
                alert("Product ID not available.");
                return;
            }

            const heart = btn.querySelector('.heart-icon');

            // Send AJAX request to toggle wishlist
            fetch('toggle_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    if (data.liked) {
                        heart.classList.add('liked');
                        // Add to wishlist panel in real-time
                        addItemToWishlistPanel(productId, data.product_name, data.price, data.image_url);
                        showToast('Added to wishlist!');
                    } else {
                        heart.classList.remove('liked');
                        // Remove from wishlist panel in real-time
                        removeItemFromWishlistPanel(productId);
                        showToast('Removed from wishlist');
                    }
                } else {
                    alert(data.message || 'Error updating wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error);
            });
        }

        function addItemToWishlistPanel(productId, productName, price, imageUrl) {
            const container = document.getElementById('wishlistItemsContainer');
            if (!container) return;

            // Check if item already exists
            if (document.getElementById('wishlist-product-' + productId)) {
                return;
            }

            // Remove empty message if it exists
            const emptyMsg = container.querySelector('.text-center');
            if (emptyMsg && emptyMsg.textContent.includes('empty')) {
                emptyMsg.remove();
            }

            // Create item element
            const itemHtml = `
                <div class="border rounded-lg p-3 flex gap-3 animate-slideIn" id="wishlist-product-${productId}">
                    <a href="product_info.php?id=${productId}" class="flex-shrink-0">
                        <img src="uploads/${imageUrl}" alt="${productName}" class="w-16 h-16 object-cover rounded">
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="product_info.php?id=${productId}" class="font-semibold text-sm hover:text-amber-600 truncate block">
                            ${productName}
                        </a>
                        <p class="text-amber-600 font-bold text-sm">₱${parseFloat(price).toFixed(2)}</p>
                        <button onclick="removeWishlistItem(${productId})" class="text-gray-500 hover:text-red-500 text-xs mt-1">
                            Remove
                        </button>
                    </div>
                </div>
            `;

            // Insert at the top
            container.insertAdjacentHTML('afterbegin', itemHtml);
        }

        function removeItemFromWishlistPanel(productId) {
            const item = document.getElementById('wishlist-product-' + productId);
            if (item) {
                item.style.animation = 'fadeOut 0.3s';
                setTimeout(() => {
                    item.remove();
                    // Check if empty now
                    const container = document.getElementById('wishlistItemsContainer');
                    if (container && !container.querySelector('[id^="wishlist-product-"]')) {
                        container.innerHTML = '<div class="p-6 text-center text-gray-600"><p>Your wishlist is empty.</p></div>';
                    }
                }, 300);
            }
        }

        function removeWishlistItem(productId) {
            if (confirm('Remove from wishlist?')) {
                fetch('remove_wishlist_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        removeItemFromWishlistPanel(productId);
                        showToast('Removed from wishlist');
                        
                        // Unfill the heart on product page if it exists
                        const heartButtons = document.querySelectorAll(`button[onclick*="toggleWishlist"]`);
                        heartButtons.forEach(btn => {
                            const heart = btn.querySelector('.heart-icon');
                            if (heart) {
                                heart.classList.remove('liked');
                            }
                        });
                    } else {
                        alert('Error removing item');
                    }
                })
                .catch(error => alert('Error: ' + error));
            }
        }

        function showToast(message) {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #333; color: white; padding: 12px 20px; border-radius: 6px; z-index: 9999; animation: fadeInOut 2s;';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 1500);
        }

        function addToBag(productId) {
            if (!productId) {
                alert("Product ID not available.");
                return;
            }
            // Optional: Send AJAX to add_to_cart.php
            alert("Added to bag! (Product ID: " + productId + ")");
        }

        // Bidding Functions
        function loadBidInfo(productId) {
            fetch('get_bids.php?product_id=' + productId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.highest_bid) {
                    const highestBidInfo = document.getElementById('highestBidInfo');
                    document.getElementById('highestBidAmount').textContent = 
                        '₱' + parseFloat(data.highest_bid.bid_amount).toFixed(2);
                    document.getElementById('highestBidder').textContent = 
                        'By: ' + (data.highest_bid.full_name || 'Anonymous');
                    document.getElementById('totalBids').textContent = 
                        'Total bids: ' + data.all_bids_count;
                    highestBidInfo.classList.remove('hidden');

                    // Set minimum bid to highest bid + 1
                    const bidAmount = document.getElementById('bidAmount');
                    bidAmount.min = (parseFloat(data.highest_bid.bid_amount) + 1).toFixed(2);

                    // Show user's existing bid if they have one
                    if (data.user_bid) {
                        const bidStatus = document.getElementById('bidStatus');
                        bidStatus.textContent = 'Your current bid: ₱' + parseFloat(data.user_bid.bid_amount).toFixed(2) + 
                            ' (Status: ' + data.user_bid.bid_status + ')';
                        bidStatus.classList.remove('hidden');
                        bidAmount.value = parseFloat(data.user_bid.bid_amount).toFixed(2);
                    }
                }
            })
            .catch(error => console.error('Error loading bids:', error));
        }

        function placeBid(productId) {
            if (!productId) {
                alert("Product ID not available.");
                return;
            }

            const bidAmount = document.getElementById('bidAmount').value;
            const bidMessage = document.getElementById('bidMessage').value;

            if (!bidAmount || bidAmount <= 0) {
                alert("Please enter a valid bid amount");
                return;
            }

            // Show loading state
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Placing bid...';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('bid_amount', bidAmount);
            formData.append('bid_message', bidMessage);

            fetch('place_bid.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'PLACE BID';

                if (data.success) {
                    showToast('✓ ' + data.message);
                    document.getElementById('bidMessage').value = '';
                    
                    // Reload bid info
                    setTimeout(() => {
                        loadBidInfo(productId);
                    }, 500);
                } else {
                    alert('Error: ' + (data.message || 'Failed to place bid'));
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.textContent = 'PLACE BID';
                console.error('Error:', error);
                alert('Error placing bid: ' + error);
            });
        }

        // Load bid info on page load
        document.addEventListener('DOMContentLoaded', function() {
            const productId = <?= $productId ?? 'null' ?>;
            if (productId) {
                loadBidInfo(productId);
            }
        });
    </script>
</body>
</html>