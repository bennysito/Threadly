<?php
/**
 * Check_out_panel.php (or checkout.php) - Fully Styled Checkout Page
 * Handles fetching cart data, calculating totals, and displaying the form.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// NOTE: Include the nav bar first, so it sits at the top of the HTML body.
// If your nav bar relies on Tailwind, it will be styled correctly.
require "nav_bar.php"; 

// --- PHP Data Fetching and Calculation ---
$cartItems = $_SESSION['cart'] ?? [];
$productsInCart = [];
$merchandiseSubtotal = 0;
$shippingFee = 12.00; // Fixed shipping fee

// --- Database/Session Logic ---
// (Using your fallback data for demonstration)
if (true) {
    $productsInCart = [
        ['id' => 101, 'name' => 'Jacket (Combo)', 'price' => 747.00, 'qty' => 1, 'total' => 747.00, 'image_url' => 'Images/baggy_pants.png', 'checked' => true],
        ['id' => 102, 'name' => 'Swimsuit Secondhand', 'price' => 11.00, 'qty' => 1, 'total' => 11.00, 'image_url' => 'Images/baggy_pants.png', 'checked' => false],
    ];
    
    foreach ($productsInCart as $p) {
        if ($p['checked']) {
            $merchandiseSubtotal += $p['total'];
        }
    }
}

$totalPayment = $merchandiseSubtotal + $shippingFee;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ... (Tailwind config from your original code) ...
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="min-h-screen bg-black p-4 font-sans"> 

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg mt-12 sm:mt-4">
        <form id="checkoutForm" method="POST" action="process_order.php">
            
            <div class="border-b border-gray-200 p-6">
                <h1 class="text-2xl font-bold mb-4">Final Checkout</h1>
                <div class="flex items-center justify-between">
                    <a href="index.php" class="text-black hover:text-amber-600 flex items-center gap-2 font-medium">
                        ← Back to Shopping
                    </a>
                    <div class="flex items-center gap-6 text-sm">
                        <span class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-amber-600"></i>
                            CNK-07-JUL-1
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-amber-600"></i>
                            Langkas, Dalaguete, Cebu
                        </span>
                        <button type="button" class="text-amber-600 font-medium">Change Address</button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Products Ordered</h2>
                    <?php if (empty($productsInCart)): ?>
                        <p class="text-gray-500 p-4 border rounded">Your cart is empty. Please add items to proceed to checkout.</p>
                    <?php else: ?>
                        <?php foreach ($productsInCart as $product): ?>
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-200 mb-4 product-item">
                                <input type="checkbox" id="product-<?= $product['id'] ?>" name="products[]" value="<?= $product['id'] ?>"
                                        data-price="<?= $product['total'] ?>" <?= $product['checked'] ? 'checked' : '' ?> onchange="calculateTotal()"
                                        class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                                
                                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-cover w-full h-full">
                                </div>
                                
                                <div class="flex-1">
                                    <h3 class="font-medium"><?= htmlspecialchars($product['name']) ?></h3>
                                    <p class="text-sm text-gray-600">Quantity: <?= $product['qty'] ?> @ ₱<?= number_format($product['price'], 2) ?></p>
                                    <input type="hidden" name="qty[<?= $product['id'] ?>]" value="<?= $product['qty'] ?>">
                                </div>
                                <div class="text-right font-semibold">₱<?= number_format($product['total'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mb-8 bg-gray-50 p-5 rounded-lg border">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Courier Details</h2>
                        <button type="button" class="text-amber-600 text-sm font-medium">Request Edit</button>
                    </div>

                    <div class="text-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span>Standard Shipping (Get by 11~18 Dec)</span>
                            <span id="shipping-display" class="font-medium">Shipping Fee: ₱<?= number_format($shippingFee, 2) ?></span>
                        </div>
                        <input type="hidden" id="shippingFee" value="<?= $shippingFee ?>">
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                    
                    <div class="flex gap-3 mb-6">
                        <button type="button" onclick="switchTab('cash')" id="tab-cash"
                                class="px-6 py-2 rounded border border-gray-300 font-medium transition-colors">Cash / Debit Card</button>
                        <button type="button" onclick="switchTab('ewallet')" id="tab-ewallet"
                                class="px-6 py-2 rounded border border-gray-300 font-medium transition-colors">E-Wallet</button>
                        <button type="button" onclick="switchTab('bank')" id="tab-bank"
                                class="px-6 py-2 rounded border border-gray-300 font-medium transition-colors">Bank Transfer</button>
                    </div>

                    <div id="payment-options" class="space-y-4">
                        <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="gcash" checked class="w-5 h-5 text-amber-600">
                            <i data-lucide="credit-card" class="w-8 h-8 text-amber-600"></i>
                            <div><div class="font-medium">GCash</div><div class="text-sm text-gray-600">Login to GCash wallet online within 2 hours</div></div>
                        </label>
                        <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-amber-600">
                            <i data-lucide="wallet" class="w-8 h-8 text-amber-600"></i>
                            <div><div class="font-medium">Pay Pal</div><div class="text-sm text-gray-600">Pay through Paypal wallet online within 30 mins</div></div>
                        </label>
                        <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="bpi" class="w-5 h-5 text-amber-600">
                            <i data-lucide="landmark" class="w-8 h-8 text-amber-600"></i>
                            <div><div class="font-medium">Maya / BPI</div><div class="text-sm text-gray-600">Pay using BPI online banking</div></div>
                        </label>
                    </div>
                </div>

            </div>

            <div class="border-t p-6 bg-gray-50">
                <div class="flex justify-end items-start gap-12">
                    <div class="text-right space-y-2">
                        <div class="flex justify-between gap-10 text-sm">
                            <span class="text-gray-700">Merchandise Subtotal:</span>
                            <span id="subtotal" class="font-medium">₱<?= number_format($merchandiseSubtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between gap-10 text-sm">
                            <span class="text-gray-700">Shipping Subtotal:</span>
                            <span class="font-medium">₱<?= number_format($shippingFee, 2) ?></span>
                        </div>
                        <div class="flex justify-between gap-10 text-xl font-bold text-amber-600">
                            <span>Total Payment:</span>
                            <span id="total">₱<?= number_format($totalPayment, 2) ?></span>
                        </div>
                    </div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-10 py-4 rounded-lg font-semibold text-lg transition-colors">
                        Place Order
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();

        const productCheckboxes = document.querySelectorAll('input[name="products[]"]');
        const shippingFeeElement = document.getElementById('shippingFee');
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total');
        const shippingAmount = parseFloat(shippingFeeElement.value);

        function calculateTotal() {
            let merchandiseSubtotal = 0;
            productCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const itemTotal = parseFloat(checkbox.dataset.price); 
                    merchandiseSubtotal += itemTotal;
                }
            });

            const total = merchandiseSubtotal + shippingAmount;

            const formatter = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });

            subtotalElement.textContent = `₱${formatter.format(merchandiseSubtotal)}`;
            totalElement.textContent = `₱${formatter.format(total)}`;
        }

        function switchTab(tab) {
            document.querySelectorAll('#tab-cash, #tab-ewallet, #tab-bank').forEach(btn => {
                btn.classList.remove('border-amber-600', 'bg-amber-50');
                btn.classList.add('border-gray-300');
            });
            document.getElementById(`tab-${tab}`).classList.add('border-amber-600', 'bg-amber-50');
            document.getElementById(`tab-${tab}`).classList.remove('border-gray-300');
        }

        calculateTotal();
        switchTab('cash');
    </script>
</body>
</html>