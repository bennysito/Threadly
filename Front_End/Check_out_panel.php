<?php
// checkout.php - Pure PHP + HTML + Tailwind + Vanilla JS (NO REACT)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - GoPacs Fitness Pack</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
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
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="min-h-screen bg-black p-4 font-sans">
  <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg">

    <!-- Main Checkout Form -->
    <form id="checkoutForm">
      
      <!-- Header -->
      <div class="border-b border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <a href="#" class="text-black hover:text-amber-600 flex items-center gap-2">
            ← Delivery Address
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
            <button type="button" class="text-amber-600 font-medium">Change</button>
          </div>
        </div>
      </div>

      <div class="p-6">

        <!-- Products Ordered -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold mb-4">Products Ordered</h2>

          <!-- Combo Product -->
          <div class="flex items-center gap-4 pb-4 border-b border-gray-200 mb-4">
            <input type="checkbox" id="combo" checked onchange="calculateTotal()"
                   class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
            <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                <img src="Images/baggy_pants.png" alt="blank" >
              <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
            </div>
            <div class="flex-1">
              <h3 class="font-medium">Jacket</h3>
              <p class="text-sm text-gray-600">Quantity: 1</p>
            </div>
            <div class="text-right font-semibold">₱747</div>
          </div>

          <!-- Protection Plan -->
          <div class="flex items-center gap-4">
            <input type="checkbox" id="protection" onchange="calculateTotal()"
                   class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
            <div class="w-16 h-16 bg-gray-100 rounded"><img src="Images/baggy_pants.png" alt="blank" ></div>
            <div class="flex-1">
              <h3 class="font-medium">Swimsuit Secondhand /h3>
              <p class="text-sm text-gray-600">Quantity:1</p>
            </div>
            <div class="text-right font-semibold">₱11</div>
          </div>
        </div>

        <!-- Courier & Shipping -->
        <div class="mb-8 bg-gray-50 p-5 rounded-lg border">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Courier</h2>
            <button type="button" class="text-amber-600 text-sm font-medium">Request Edit</button>
          </div>

          <div class="text-sm space-y-3">
            <div class="flex items-center justify-between">
              <span>Standard Shipping (Get by 11~18 Dec)</span>
              <span>Shipping Fee: ₱12</span>
            </div>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold mb-4">Payment Method</h2>

          <!-- Payment Method Tabs -->
          <div class="flex gap-3 mb-6">
            <button type="button" onclick="switchTab('cash')" id="tab-cash"
                    class="px-6 py-2 rounded border border-amber-600 bg-amber-50 font-medium">Cash / Debit Card</button>
            <button type="button" onclick="switchTab('ewallet')" id="tab-ewallet"
                    class="px-6 py-2 rounded border border-gray-300 font-medium">E-Wallet</button>
            <button type="button" onclick="switchTab('bank')" id="tab-bank"
                    class="px-6 py-2 rounded border border-gray-300 font-medium">Bank Transfer</button>
          </div>

          <!-- GCash & Other Options (now selectable with radio buttons) -->
          <div class="space-y-4">
            <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
              <input type="radio" name="payment" value="gcash1" class="w-5 h-5 text-amber-600">
              <i data-lucide="credit-card" class="w-8 h-8 text-amber-600"></i>
              <div>
                <div class="font-medium">GCash</div>
                <div class="text-sm text-gray-600">Login to GCash wallet online within 2 hours</div>
              </div>
            </label>

            <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
              <input type="radio" name="payment" value="gcash2" class="w-5 h-5 text-amber-600">
              <i data-lucide="wallet" class="w-8 h-8 text-amber-600"></i>
              <div>
                <div class="font-medium">Pay Pal</div>
                <div class="text-sm text-gray-600">Pay through Paypal wallet online within 30 mins</div>
              </div>
            </label>

            <label class="flex items-center gap-4 p-4 border rounded-lg hover:border-amber-600 cursor-pointer transition">
              <input type="radio" name="payment" value="maya" class="w-5 h-5 text-amber-600">
              <i data-lucide="landmark" class="w-8 h-8 text-amber-600"></i>
              <div>
                <div class="font-medium">Maya / BPI</div>
                <div class="text-sm text-gray-600">Pay using BPI online banking</div>
              </div>
            </label>
          </div>
        </div>

      </div>

      <!-- Order Summary Footer -->
      <div class="border-t p-6 bg-gray-50">
        <div class="flex justify-end items-start gap-12">
          <div class="text-right space-y-2">
            <div class="flex justify-between gap-10 text-sm">
              <span class="text-gray-700">Merchandise Subtotal:</span>
              <span id="subtotal">₱747</span>
            </div>
            <div class="flex justify-between gap-10 text-sm">
              <span class="text-gray-700">Shipping Subtotal:</span>
              <span>₱12</span>
            </div>
            <div class="flex justify-between gap-10 text-lg font-bold text-amber-600">
              <span>Total Payment:</span>
              <span id="total">₱759</span>
            </div>
          </div>
          <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-10 py-4 rounded-lg font-semibold text-lg">
            Place Order
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- JavaScript for calculations and tab switching -->
  <script>
    lucide.createIcons();

    function calculateTotal() {
      const combo = document.getElementById('combo').checked;
      const protection = document.getElementById('protection').checked;

      const comboPrice = combo ? 747 : 0;
      const protectionPrice = protection ? 11 : 0;
      const shipping = 12;
      const subtotal = comboPrice + protectionPrice;
      const total = subtotal + shipping;

      document.getElementById('subtotal').textContent = `₱${subtotal}`;
      document.getElementById('total').textContent = `₱${total}`;
    }

    function switchTab(tab) {
      document.querySelectorAll('#tab-cash, #tab-ewallet, #tab-bank').forEach(btn => {
        btn.classList.remove('border-amber-600', 'bg-amber-50');
        btn.classList.add('border-gray-300');
      });
      document.getElementById(`tab-${tab}`).classList.add('border-amber-600', 'bg-amber-50');
      document.getElementById(`tab-${tab}`).classList.remove('border-gray-300');
    }

    // Initialize
    calculateTotal();
    switchTab('cash'); // default tab
  </script>
</body>
</html>