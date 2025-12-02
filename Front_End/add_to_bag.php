<?php
/**
 * main_page.php (or wherever the panels are included)
 * * Contains the PHP logic for session management, the HTML structure for the 
 * Shopping Bag (right slide-in), and the necessary Tailwind CSS utility 
 * classes and JavaScript for panel functionality and checkout redirection.
 */

// PHP Session Management: 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Placeholder data for demonstration
// Replace with actual database/session loop if connecting to a backend.
$demoItems = [
    ['name' => 'Vintage Tee', 'size' => 'M', 'qty' => 1, 'price' => 25.00, 'img' => 'Images/placeholder-shirt.jpg'],
    ['name' => 'Essential Hoodie', 'size' => 'L', 'qty' => 2, 'price' => 90.00, 'img' => 'Images/placeholder-hoodie.jpg'],
];
$totalSubtotal = 115.00; // Calculated sum of demo items (25.00 + 90.00)
$hasItems = !empty($demoItems); 
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
                <?php foreach ($demoItems as $item): ?>
                <div class="flex border-b pb-2">
                    <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded mr-3">
                    <div class="flex-1">
                        <p class="font-semibold text-sm"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="text-xs text-gray-500">Size: <?= htmlspecialchars($item['size']) ?> | Qty: <?= htmlspecialchars($item['qty']) ?></p>
                        <p class="text-sm font-bold mt-1">$<?= number_format($item['price'] * $item['qty'], 2) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-gray-600 h-full flex flex-col justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 text-gray-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <p class="mb-4 text-lg font-semibold">Your bag is empty.</p>
                <a onclick="closeBagPanel()" class="text-white bg-black px-6 py-2 rounded-full font-semibold hover:bg-amber-600 transition-colors duration-200 cursor-pointer">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>

    </div>
    
    <?php if ($hasItems): ?>
        <div class="p-4 border-t border-gray-200 flex-shrink-0">
            <div class="flex justify-between font-semibold text-lg mb-3">
                <span>Subtotal:</span>
                <span>$<?= number_format($totalSubtotal, 2) ?></span> 
            </div>
            <button id="checkoutBtn" class="block w-full text-center text-white bg-amber-500 px-4 py-3 rounded-full font-bold hover:bg-amber-600 transition-colors duration-200">
                CHECKOUT
            </button>
        </div>
    <?php endif; ?>

</div>

<div id="bagOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>


<script>
    // Panel Elements
    const bagPanel = document.getElementById('bagPanel');
    const closeBagBtn = document.getElementById('closeBagBtn');
    const bagOverlay = document.getElementById('bagOverlay');
    const openBagIconBtn = document.getElementById('openBagBtn'); // Assuming your main cart icon has this ID
    const checkoutBtn = document.getElementById('checkoutBtn'); 

    // --- BAG PANEL FUNCTIONS ---

    function openBagPanel() {
        bagPanel.classList.remove('translate-x-full'); // Slide in from right
        bagOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent main page scrolling
    }

    function closeBagPanel() {
        bagPanel.classList.add('translate-x-full'); // Slide out to right
        bagOverlay.classList.add('hidden');
        document.body.style.overflow = ''; // Restore main page scrolling
    }
    
    // --- CHECKOUT REDIRECT LOGIC ---
    
    function redirectToCheckout() {
        // 1. Close the Shopping Bag panel visually
        closeBagPanel(); 
        
        // 2. Redirect the browser to your full checkout page file
        window.location.href = 'Check_out_panel.php'; 
    }

    // --- EVENT LISTENERS ---

    // Open Bag Panel via main navigation icon (if it exists)
    if (openBagIconBtn) {
        openBagIconBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            openBagPanel();
        });
    }

    // Listener for the CHECKOUT button in the shopping bag
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', redirectToCheckout);
    }

    // Close listeners for Bag Panel
    if (closeBagBtn) closeBagBtn.addEventListener('click', closeBagPanel);
    if (bagOverlay) bagOverlay.addEventListener('click', closeBagPanel); 
    
    // Close panel on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (bagPanel && !bagPanel.classList.contains('translate-x-full')) {
                closeBagPanel();
            }
        }
    });

</script>