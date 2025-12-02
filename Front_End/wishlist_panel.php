<?php
// wishlist_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch wishlist items if logged in
$wishlistItems = [];
if ($isLoggedIn) {
    require_once __DIR__ . "/../Back_End/Models/Database.php";
    
    $db = new Database();
    $conn = $db->threadly_connect;
    $user_id = $_SESSION['user_id'];
    
    $sql = "
        SELECT 
            wi.wishlist_item_id,
            p.product_id,
            p.product_name,
            p.price,
            p.image_url
        FROM wishlist w
        JOIN wishlist_item wi ON w.wishlist_id = wi.wishlist_id
        JOIN products p ON wi.product_id = p.product_id
        WHERE w.user_id = ?
        ORDER BY wi.added_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $wishlistItems[] = $row;
        }
        $stmt->close();
    }
    $db->close_db();
}
?>

<div id="wishlistPanel" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">

    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold">WISHLIST</h2>
        <button id="closeWishlistBtn" class="text-gray-500 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="wishlistItemsContainer" class="p-4 overflow-y-auto h-[calc(100vh-64px)]">
        
        <?php if ($isLoggedIn): ?>
            <?php if (empty($wishlistItems)): ?>
                <div class="p-6 text-center text-gray-600">
                    <p>Your wishlist is empty.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($wishlistItems as $item): ?>
                        <div class="border rounded-lg p-3 flex gap-3" id="wishlist-product-<?= $item['product_id'] ?>">
                            <a href="product_info.php?id=<?= $item['product_id'] ?>" class="flex-shrink-0">
                                <img src="uploads/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-16 h-16 object-cover rounded">
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="product_info.php?id=<?= $item['product_id'] ?>" class="font-semibold text-sm hover:text-amber-600 truncate block">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </a>
                                <p class="text-amber-600 font-bold text-sm">₱<?= number_format((float)$item['price'], 2) ?></p>
                                <button onclick="removeWishlistItem(<?= $item['product_id'] ?>)" class="text-gray-500 hover:text-red-500 text-xs mt-1">
                                    Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="p-6 text-center text-gray-600">
                <p class="mb-4">You must be logged in to view your Wishlist.</p>
                <a href="login.php" class="text-white bg-amber-500 px-4 py-2 rounded-full font-semibold hover:bg-amber-600 transition-colors duration-200">
                    Log In Now
                </a>
            </div>
        <?php endif; ?>

    </div>

</div>

<div id="wishlistOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Get the panel elements
    const wishlistPanel = document.getElementById('wishlistPanel');
    const closeWishlistBtn = document.getElementById('closeWishlistBtn');
    const wishlistOverlay = document.getElementById('wishlistOverlay');
    
    // ⭐ Get BOTH Wishlist triggers ⭐
    const openWishlistIconBtn = document.getElementById('openWishlistBtn'); 
    const openWishlistDropdownBtn = document.getElementById('openWishlistDropdownBtn'); 

    // Function to open the panel
    function openWishlistPanel() {
        wishlistPanel.classList.remove('translate-x-full'); // Slides in
        wishlistOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    // Function to close the panel
    function closeWishlistPanel() {
        wishlistPanel.classList.add('translate-x-full'); // Slides out
        wishlistOverlay.classList.add('hidden');
        document.body.style.overflow = ''; 
    }

    // Function to set up the listener
    function setupWishlistListener(element) {
        if (element) {
            element.addEventListener('click', (e) => {
                e.preventDefault(); 
                openWishlistPanel();
            });
        }
    }

    // Set up listeners for both the main icon and the dropdown link
    setupWishlistListener(openWishlistIconBtn);
    setupWishlistListener(openWishlistDropdownBtn);

    // Event listeners to close the panel
    if (closeWishlistBtn) closeWishlistBtn.addEventListener('click', closeWishlistPanel);
    if (wishlistOverlay) wishlistOverlay.addEventListener('click', closeWishlistPanel); 

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && wishlistPanel && !wishlistPanel.classList.contains('translate-x-full')) {
            closeWishlistPanel();
        }
    });

    // Function to remove item from wishlist
    window.removeWishlistItem = function(productId) {
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
                    // Remove from wishlist panel
                    const itemElement = document.getElementById('wishlist-product-' + productId);
                    if (itemElement) {
                        itemElement.style.animation = 'fadeOut 0.3s ease';
                        setTimeout(() => {
                            itemElement.remove();
                            
                            // Check if wishlist is now empty
                            const container = document.getElementById('wishlistItemsContainer');
                            const items = container.querySelectorAll('[id^="wishlist-product-"]');
                            if (items.length === 0) {
                                container.innerHTML = '<div class="p-6 text-center text-gray-600"><p>Your wishlist is empty.</p></div>';
                            }
                        }, 300);
                    }
                    
                    // Update heart icon on category_products page
                    const hearts = document.querySelectorAll(`.heart[data-product-id="${productId}"]`);
                    hearts.forEach(heart => {
                        heart.classList.remove('active');
                    });
                    
                    // Show notification
                    if (typeof showToast === 'function') {
                        showToast('Removed from wishlist');
                    }
                } else {
                    alert(data.message || 'Error removing item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing item');
            });
        }
    };

</script>

<style>
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(10px);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0; }
        10%, 90% { opacity: 1; }
    }
    
    .animate-slideIn {
        animation: slideIn 0.3s ease;
    }
</style>