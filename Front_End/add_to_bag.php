<?php
// add_to_bag_panel.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Assuming cart data is stored in a session array
$cartItems = $_SESSION['cart'] ?? [];
$hasItems = !empty($cartItems);
?>

<div id="bagPanel" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col">

    <div class="flex justify-between items-center p-4 border-b border-gray-200 flex-shrink-0">
        <h2 class="text-xl font-semibold">SHOPPING BAG</h2>
        <button id="closeBagBtn" class="text-gray-500 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="bagItemsContainer" class="p-4 overflow-y-auto flex-1">
        
        <?php if ($hasItems): ?>
            <div class="space-y-4">
                <div class="flex border-b pb-2">
                    <img src="Images/placeholder-shirt.jpg" alt="Item 1" class="w-16 h-16 object-cover rounded mr-3">
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Vintage Tee</p>
                        <p class="text-xs text-gray-500">Size: M | Qty: 1</p>
                        <p class="text-sm font-bold mt-1">$25.00</p>
                    </div>
                </div>
                <div class="flex border-b pb-2">
                    <img src="Images/placeholder-hoodie.jpg" alt="Item 2" class="w-16 h-16 object-cover rounded mr-3">
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Essential Hoodie</p>
                        <p class="text-xs text-gray-500">Size: L | Qty: 2</p>
                        <p class="text-sm font-bold mt-1">$90.00</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-gray-600 h-full flex flex-col justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 text-gray-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <p class="mb-4 text-lg font-semibold">Your bag is empty.</p>
                <a href="index.php" class="text-white bg-black px-6 py-2 rounded-full font-semibold hover:bg-amber-600 transition-colors duration-200">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>

    </div>
    
    <?php if ($hasItems): ?>
        <div class="p-4 border-t border-gray-200 flex-shrink-0">
            <div class="flex justify-between font-semibold text-lg mb-3">
                <span>Subtotal:</span>
                <span>$115.00</span> </div>
            <a href="checkout.php" class="block w-full text-center text-white bg-amber-500 px-4 py-3 rounded-full font-bold hover:bg-amber-600 transition-colors duration-200">
                CHECKOUT
            </a>
        </div>
    <?php endif; ?>

</div>

<div id="bagOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Get the panel elements
    const bagPanel = document.getElementById('bagPanel');
    const closeBagBtn = document.getElementById('closeBagBtn');
    const bagOverlay = document.getElementById('bagOverlay');
    
    // ⭐ Get the Bag trigger ⭐
    const openBagIconBtn = document.getElementById('openBagBtn'); 

    // Function to open the panel
    function openBagPanel() {
        bagPanel.classList.remove('translate-x-full'); // Slides in
        bagOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    // Function to close the panel
    function closeBagPanel() {
        bagPanel.classList.add('translate-x-full'); // Slides out
        bagOverlay.classList.add('hidden');
        document.body.style.overflow = ''; 
    }

    // Set up listener for the main icon (assuming the cart icon in nav_bar has the id 'openBagBtn')
    if (openBagIconBtn) {
        openBagIconBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            openBagPanel();
        });
    }

    // Event listeners to close the panel
    if (closeBagBtn) closeBagBtn.addEventListener('click', closeBagPanel);
    if (bagOverlay) bagOverlay.addEventListener('click', closeBagPanel); 

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && bagPanel && !bagPanel.classList.contains('translate-x-full')) {
            closeBagPanel();
        }
    });

</script>