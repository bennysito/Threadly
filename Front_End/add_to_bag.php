<?php

/**
 * add_to_bag.php
 * Contains the HTML structure for the Shopping Bag (right slide-in panel)
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- FUNCTION TO RENDER BAG CONTENT ---
function renderBagContent($bagItems) {
    $totalSubtotal = 0;
    $hasItems = !empty($bagItems);
    foreach ($bagItems as $item) {
        $totalSubtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    
    ob_start(); // Start output buffering to capture the HTML
    ?>
    
    <div id="bagItemsContainer" class="p-4 overflow-y-auto flex-1">
        <?php if (!$hasItems): ?>
            <div class="text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <p class="text-gray-500 mb-4">Your bag is empty</p>
                <a href="index.php" class="text-amber-600 hover:underline font-semibold">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($bagItems as $item): 
                // Use product_id for removing items
                $product_id = htmlspecialchars($item['product_id'] ?? '');
            ?>
                <div class="flex gap-4 mb-4 pb-4 border-b border-gray-200">
                    <img src="uploads/<?= htmlspecialchars($item['image_url'] ?? 'Images/jacket_hoodie.png') ?>"
                        alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>"
                        class="w-20 h-20 object-cover rounded">
                    <div class="flex-1">
                        <h3 class="font-semibold text-sm"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></h3>
                        <p class="text-amber-600 font-bold">₱<?= number_format($item['price'] ?? 0, 2) ?></p>
                        <p class="text-xs text-gray-500">Qty: <?= intval($item['quantity'] ?? 1) ?></p>
                        <button onclick="window.removeItemFromBag(<?= $product_id ?>)" class="text-xs text-red-500 hover:text-red-700 mt-1">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($hasItems): ?>
        <div id="bagSummary" class="p-4 border-t border-gray-200 flex-shrink-0">
            <div class="flex justify-between font-semibold text-lg mb-3">
                <span>Subtotal:</span>
                <span>₱<?= number_format($totalSubtotal, 2) ?></span>
            </div>
            <button id="checkoutBtn" onclick="redirectToCheckout()" class="block w-full text-center text-white bg-amber-500 px-4 py-3 rounded-full font-bold hover:bg-amber-600 transition-colors duration-200">
                CHECKOUT
            </button>
        </div>
    <?php else: ?>
        <div id="bagSummary" class="flex-shrink-0"></div> <?php endif; ?>
    
    <?php
    return ob_get_clean(); // Return the captured HTML
}

// Check if this script is being requested via AJAX for content refresh
if (isset($_GET['action']) && $_GET['action'] === 'refresh_bag_content') {
    // Get updated bag items from session
    $bagItems = $_SESSION['shopping_bag'] ?? [];
    echo renderBagContent($bagItems);
    exit; // Stop execution after sending the content
}

// If not an AJAX request, proceed to render the full panel structure
$bagItems = $_SESSION['shopping_bag'] ?? [];
$hasItems = !empty($bagItems);
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

    <div id="bagContentWrapper" class="flex-1 flex flex-col">
        <?= renderBagContent($bagItems) ?>
    </div>

</div>

<div id="bagOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<script>
    // Panel Elements
    const bagPanel = document.getElementById('bagPanel');
    const closeBagBtn = document.getElementById('closeBagBtn');
    const bagOverlay = document.getElementById('bagOverlay');
    const openBagIconBtn = document.getElementById('openBagBtn'); // Assuming this exists in Nav_bar.php
    const bagContentWrapper = document.getElementById('bagContentWrapper');

    // --- BAG PANEL FUNCTIONS ---
    function openBagPanel() {
        if (bagPanel) {
            bagPanel.classList.remove('translate-x-full');
            if (bagOverlay) bagOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // ⭐ CRITICAL: Refresh content immediately upon opening to ensure the latest state ⭐
            window.refreshBagPanelContent();
        }
    }

    function closeBagPanel() {
        if (bagPanel) {
            bagPanel.classList.add('translate-x-full');
            if (bagOverlay) bagOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // --- NEW: FUNCTION TO REFRESH BAG CONTENT VIA AJAX ---
    function refreshBagPanelContent() {
        if (!bagContentWrapper) return;
        
        // Fetch the HTML content dynamically from this same script
        fetch('add_to_bag.php?action=refresh_bag_content')
            .then(response => response.text())
            .then(html => {
                // Replace the entire content wrapper's inner HTML with the new content
                bagContentWrapper.innerHTML = html;
                
                // Re-attach event listener to the dynamically created checkout button
                const newCheckoutBtn = bagContentWrapper.querySelector('#checkoutBtn');
                if (newCheckoutBtn) {
                    newCheckoutBtn.removeEventListener('click', redirectToCheckout); // Remove old/potential listener
                    newCheckoutBtn.addEventListener('click', redirectToCheckout);
                }
            })
            .catch(error => {
                console.error('Error refreshing bag content:', error);
            });
    }

    // --- DUMMY FUNCTION FOR REMOVING ITEMS (Needs server-side implementation in remove_from_cart.php) ---
    function removeItemFromBag(productId) {
        console.log("Removing item:", productId);
        
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + encodeURIComponent(productId)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                refreshBagPanelContent(); // Refresh the panel after successful removal
            } else {
                alert(data.message || 'Failed to remove item.');
            }
        })
        .catch(error => {
            console.error('Remove error:', error);
            alert('Error removing item from bag');
        });
    }


    // Make essential functions globally available for external scripts (like product_info.php)
    window.openBagPanel = openBagPanel;
    window.closeBagPanel = closeBagPanel;
    window.refreshBagPanelContent = refreshBagPanelContent;
    window.removeItemFromBag = removeItemFromBag;


    // --- CHECKOUT REDIRECT LOGIC ---
    function redirectToCheckout() {
        closeBagPanel();
        window.location.href = 'Check_out_panel.php';
    }

    // --- EVENT LISTENERS ---
    document.addEventListener('DOMContentLoaded', () => {
        // Find open button again since it might be rendered later by Nav_bar.php
        const deferredOpenBagBtn = document.getElementById('openBagBtn');
        if (deferredOpenBagBtn) {
            deferredOpenBagBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openBagPanel();
            });
        }
        
        // Listeners for closing the panel (always present)
        if (closeBagBtn) closeBagBtn.addEventListener('click', closeBagPanel);
        if (bagOverlay) bagOverlay.addEventListener('click', closeBagPanel);

        // Listeners for checkout button (only exists if items are present at initial load)
        const initialCheckoutBtn = document.getElementById('checkoutBtn');
        if (initialCheckoutBtn) {
            initialCheckoutBtn.addEventListener('click', redirectToCheckout);
        }
    });

    // Close panel on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (bagPanel && !bagPanel.classList.contains('translate-x-full')) {
                closeBagPanel();
            }
        }
    });
</script>