<?php
// view_products.php
if (session_status() === PHP_SESSION_NONE) {
     session_start();
}

$allProducts = [];

// Define the path to the Search model
$searchFilePath = __DIR__ . "/../Back_End/Models/Search_db.php";

// --- 1. Database Connection and Fetch ---
if (file_exists($searchFilePath)) {
     require_once $searchFilePath;

     if (class_exists('Search')) {
          $search = new Search();
          try {
               $allProducts = $search->getAllProducts();
          } catch (Exception $e) {
               $errorMessage = "Database fetch error: " . $e->getMessage();
               error_log($errorMessage);
          }
     } else {
          $errorMessage = "Error: Search class not found in Search_db.php";
          error_log($errorMessage);
     }
} else {
     $errorMessage = "Error: Search_db.php not found at path: " . $searchFilePath;
     error_log($errorMessage);
}

// -------------------------------------------------------------------------
// ⭐ 2. FALLBACK / SIMULATION (If database fetching failed or file not found) ⭐
// The structure is kept the same, but the data is simulated.
// -------------------------------------------------------------------------
if (empty($allProducts)) {
     // Simulated data matching your products table structure
     $allProducts = [
          [
               'product_id' => 1,
               'product_name' => 'Pullover Hoodie',
               'description' => 'Unisex fit',
               'price' => 599.00,
               'image_url' => 'Images/jacket_hoodie.png',
               'seller_id' => 8,
               'availability' => 'available'
          ],
          [
               'product_id' => 2,
               'product_name' => 'Streetwear Baggy Jeans',
               'description' => 'Extra denim baggy',
               'price' => 1250.00,
               'image_url' => 'Images/baggy_pants.png',
               'seller_id' => 8,
               'availability' => 'available'
          ],
          [
               'product_id' => 3,
               'product_name' => 'Tummy Shaping Panties',
               'description' => 'Tummy control seamless underwear',
               'price' => 180.00,
               'image_url' => 'Images/panti.png',
               'seller_id' => 9,
               'availability' => 'available'
          ],
          [
               'product_id' => 4,
               'product_name' => 'Sporty Briefs 3-Pack',
               'description' => 'Breathable microfiber women\'s briefs',
               'price' => 450.00,
               'image_url' => 'Images/underwear_women.png',
               'seller_id' => 10,
               'availability' => 'available'
          ],
     ];
}

// -------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/category_products.css"> 
    <style>
        .product-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">

<?php require "Nav_bar.php"; ?>
<?php require "wishlist_panel.php"; ?>
<?php require "notification_panel.php"; ?> 
 <?php require "add_to_bag.php"; ?> 
 <?php require "messages_panel.php"; ?>

 <div class="max-w-7xl mx-auto px-4 py-12">

<h1 class="text-4xl font-bold text-gray-900 mb-4">🛒 All Products</h1>
 <p class="text-gray-600 mb-10">Browse the entire collection available on Threadly.</p>

          <?php if (!empty($allProducts)): ?>
               <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                    <!-- Product listing intentionally removed by request. -->
               </div>
        <?php else: ?>
            <div class="text-center py-20 border border-dashed border-gray-300 rounded-lg bg-white">
                <?php if (isset($errorMessage)): ?>
                    <p class="text-2xl font-semibold text-red-500">Error Loading Products</p>
                    <p class="text-gray-500 mt-2">Could not connect to the database or retrieve data. (<?= htmlspecialchars($errorMessage) ?>)</p>
                <?php else: ?>
                    <p class="text-2xl font-semibold text-gray-700">No Products Found</p>
                    <p class="text-gray-500 mt-2">The product database is currently empty.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <script src="js/product_page_functions.js"></script>
    <script>
        // Placeholder functions for modal/panel visibility (must be defined or loaded)
        function toggleWishlist(button, productId) {
             console.log("Toggle Wishlist for ID:", productId);
             // Implement AJAX call to update wishlist here
        }
        
        function addToBag(productId) {
 console.log("Added to Bag:", productId);
// Implement logic to add to cart/bag here
}
 </script>
</body>
</html>