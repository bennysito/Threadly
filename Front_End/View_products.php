<?php
// view_products.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allProducts = [];

// Define the path to the Search model based on your file structure (Front_End/view_products.php -> Back_End/Models/Search_db.php)
$searchFilePath = __DIR__ . "/../Back_End/Models/Search_db.php"; 

// --- 1. Database Connection and Fetch ---
if (file_exists($searchFilePath)) {
    require_once $searchFilePath;
    
    // Check if the Search class exists before trying to instantiate
    if (class_exists('Search')) {
        $search = new Search();
        
        // Assuming Search_db.php has a method to get ALL products (e.g., getAllProducts())
        try {
            // Note: You must ensure this method exists and is correctly implemented in Search_db.php
            $allProducts = $search->getAllProducts();
        } catch (Exception $e) {
            // Handle database error gracefully
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
// -------------------------------------------------------------------------
if (empty($allProducts)) {
    // Simulated data matching your products table structure (image_a54cfb.png)
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
                
                <?php foreach ($allProducts as $product): ?>
                    <?php
                        // Safely extract and format data from the database result
                        $id = htmlspecialchars($product['product_id'] ?? '');
                        $name = htmlspecialchars($product['product_name'] ?? 'No Name');
                        // Using 'description' column from the products table (image_a6bcd9.png)
                        $description = htmlspecialchars($product['description'] ?? ''); 
                        $price = number_format((float)($product['price'] ?? 0), 2);
                        // Using 'image_url' column from the products table (image_a6bcd9.png)
                        $image_url = htmlspecialchars($product['image_url'] ?? 'Images/default_product.png'); 
                        
                        // Link to the detailed product info page
                        $product_link = "product_info.php?id=" . $id; 
                    ?>
                    
                    <a href="<?= $product_link ?>" class="product-card bg-white rounded-lg shadow-md overflow-hidden block">
                        
                        <div class="relative w-full aspect-square bg-gray-100 overflow-hidden">
                            <img src="<?= $image_url ?>" alt="<?= $name ?>" class="w-full h-full object-cover transition duration-300 transform hover:scale-105">
                            
                            <button type="button" class="absolute top-3 right-3 p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition" onclick="event.preventDefault(); toggleWishlist(this, <?= $id ?>)">
                                <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 truncate mb-1"><?= $name ?></h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-2"><?= $description ?></p>
                            <p class="text-xl font-bold text-gray-800">₱<?= $price ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
                
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