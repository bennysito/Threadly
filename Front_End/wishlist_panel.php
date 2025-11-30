<?php
// Ensure session is started if this file is included separately
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']); 
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
            
            <div class="flex items-start py-4 border-b border-gray-100 last:border-b-0">
                <img src="Images/off_shoulder_knit_dress.png" alt="Off Shoulder Knit Dress" class="w-20 h-20 object-cover mr-4 rounded-md flex-shrink-0">
                
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 line-clamp-2">Off Shoulder Sequin Knit Mini Dress</p>
                    <p class="text-sm text-amber-600 font-bold">P2,500.00</p>
                    <p class="text-xs text-gray-500">BROWN / XS</p>
                    
                    <button class="mt-1 px-3 py-1 text-sm bg-gray-300 text-gray-800 rounded-full cursor-not-allowed font-semibold">
                        SOLD OUT
                    </button>
                </div>

                <div class="flex flex-col items-end justify-between h-20">
                    <button class="text-gray-400 hover:text-red-500 ml-4 p-1" title="Remove from Wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.76 5.895m13.784-.42c.059-.101.12-.204.181-.308A6.47 6.47 0 0 0 12 3.75 6.47 6.47 0 0 0 5.42 5.167c.06.104.122.207.181.308m12.067 0h-12.067" />
                        </svg>
                    </button>
                    <span class="text-xs text-gray-400 line-through mr-1">P3,000.00</span>
                </div>
            </div>

            <div class="flex items-start py-4 border-b border-gray-100 last:border-b-0">
                <img src="Images/product_placeholder.png" alt="Summer Maxi Dress" class="w-20 h-20 object-cover mr-4 rounded-md flex-shrink-0">
                
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 line-clamp-2">Summer Floral Halter Maxi Dress</p>
                    <p class="text-sm text-amber-600 font-bold">P1,800.00</p>
                    <p class="text-xs text-gray-500">BLUE / S</p>
                    
                    <p class="mt-1 text-xs text-green-600 font-semibold">In Stock</p>
                </div>

                <div class="flex flex-col items-end justify-between h-20">
                    <button class="text-gray-400 hover:text-red-500 ml-4 p-1" title="Remove from Wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.76 5.895m13.784-.42c.059-.101.12-.204.181-.308A6.47 6.47 0 0 0 12 3.75 6.47 6.47 0 0 0 5.42 5.167c.06.104.122.207.181.308m12.067 0h-12.067" />
                        </svg>
                    </button>
                    <span class="text-xs text-gray-400 line-through mr-1"></span>
                </div>
            </div>
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
    
    // Get the buttons that open the panel (Selector for heart icon based on its SVG path)
    const openWishlistMainBtn = document.querySelector('nav a[href="#"] svg path[d*="M21 8.25c0-2.485"]')?.closest('a'); 
    const openWishlistDropdownBtn = document.getElementById('openWishlistDropdownBtn'); 

    // Function to open the panel
    function openWishlistPanel(e) {
        if (e) e.preventDefault();
        
        // Hide the profile dropdown if it's open (important if opened via dropdown link)
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
            profileDropdown.classList.add('hidden'); 
        }

        wishlistPanel.classList.remove('translate-x-full');
        wishlistOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling background
    }

    // Function to close the panel
    function closeWishlistPanel() {
        wishlistPanel.classList.add('translate-x-full');
        wishlistOverlay.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Event listeners to open the panel
    if (openWishlistMainBtn) {
        openWishlistMainBtn.addEventListener('click', openWishlistPanel);
    }

    if (openWishlistDropdownBtn) {
        openWishlistDropdownBtn.addEventListener('click', openWishlistPanel);
    }

    // Event listeners to close the panel
    closeWishlistBtn.addEventListener('click', closeWishlistPanel);
    wishlistOverlay.addEventListener('click', closeWishlistPanel); // Close when clicking the overlay

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !wishlistPanel.classList.contains('translate-x-full')) {
            closeWishlistPanel();
        }
    });

</script>