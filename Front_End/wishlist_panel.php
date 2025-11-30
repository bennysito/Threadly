<?php
// wishlist_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
            <div class="p-6 text-center text-gray-600">
                <p>Your wishlist items will be displayed here.</p>
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

</script>