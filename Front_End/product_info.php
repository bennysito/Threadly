<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$productName = $_GET['name'] ?? 'Product';
$productImage = $_GET['image'] ?? 'panti.png';
$productHoverImage = $_GET['hover_image'] ?? 'underwear_women.png';
$productPrice = (int)($_GET['price'] ?? 0);
$categoryName = $_GET['category'] ?? 'Products';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"  href="CSS/produc_info.css">
</head>
<body class="bg-gray-50">

<!-- Nav bar -->
<div><?php require "Nav_bar.php"; ?></div>


<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- home text ,function to go back at homepage-->
    <div class="mb-8 text-sm text-gray-600">
        <a href="index.php" class="hover:text-amber-600">Home</a>
        
       
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        
            <!-- Logo kaotng Threaldy-->
        <a  href="index.php" class="flex items-center flex-shrink-0">
        <img src="Images/Threadly_logo.png" alt="Logo" class="h-16 w-auto ml-5/6 logo-scale">
        <span class="chewy-font ml-6">Threadly</span>
        </a>

        <!-- image gallery-->
        <div class="flex flex-col gap-4">
        <!-- Main Image -->
            <div class="relative bg-gray-100 rounded-lg overflow-hidden aspect-square flex items-center justify-center">
                <img id="mainImage" src="Images/<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($productName) ?>" class="w-full h-full object-cover">
                
                <!-- Discount text -->
                <div class="absolute top-4 left-4 bg-black text-white px-3 py-1 rounded-full text-sm font-bold">
                    60% OFF
                </div>
                
                <!-- heart icon -->
                <button onclick="toggleHeart(event)" class="absolute top-4 right-4 p-2 bg-white rounded-full shadow-lg hover-icon">
                    <svg class="heart-icon w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
            </div>

            <!-- image preview -->
            <div class="flex gap-3">
                <img src="Images/<?= htmlspecialchars($productImage) ?>" alt="Thumb 1" class="w-20 h-20 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400" onclick="changeMainImage(this.src)">
                <img src="Images/<?= htmlspecialchars($productHoverImage) ?>" alt="Thumb 2" class="w-20 h-20 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400" onclick="changeMainImage(this.src)">
                <img src="Images/<?= htmlspecialchars($productImage) ?>" alt="Thumb 3" class="w-20 h-20 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400" onclick="changeMainImage(this.src)">
                <img src="Images/<?= htmlspecialchars($productHoverImage) ?>" alt="Thumb 4" class="w-20 h-20 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400" onclick="changeMainImage(this.src)">
            </div>
        </div>

        <!-- product info, right side-->
        <div class="flex flex-col gap-6">
            
            <!-- rating  -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($productName) ?></h1>
            </div>

            <!-- price -->
            <div>
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-gray-900">₱<?= number_format($productPrice) ?>.00</span>
                   
                </div>
                <p class="text-sm text-gray-600 mt-2"><strong>Benedictbenedt</strong></p>
            </div>

            <!-- Condition sa cloth-->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-3">Condition</label>
                <div class="flex gap-3">
                    <span class="px-4 py-2 border-2 border-black rounded font-medium bg-black text-white">Gently Used</span>
                </div>
            </div>

            <!-- size m,l,medium-->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-3">Size</label>
                <div class="flex flex-wrap gap-2">
                    <span class="px-4 py-2 border-2 border-gray-300 rounded font-medium text-gray-700 bg-gray-50">M</span>
                </div>
            </div>

            <!-- add to bag button -->
            <button class="w-full bg-black text-white py-3 rounded-full font-bold text-lg hover:bg-amber-600 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>

                ADD TO BAG
            </button>

            <!-- Product Details Section -->
            <div class="border-t pt-6">
                <details class="mb-4">
                    <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600">Description</summary>
                    <p class="text-gray-600 mt-2">BenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedict.</p>
                </details>
                
                <details class="mb-4">
                    <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600">Shipping & Returns</summary>
                    <p class="text-gray-600 mt-2">BenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedict</p>
                </details>
                
                <details>
                    <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600">Condition Details</summary>
                    <p class="text-gray-600 mt-2">BenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedictBenedictbenedictbenedictbenedict.</p>
                </details>
            </div>
        </div>
    </div>

    <!-- seller profile-->
    <div class="border-t pt-8 mt-8">
       
        
        <!-- seller horizontal-->
        <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-6 gap-6">
            
            <!-- avatar  -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-300 flex items-center justify-center flex-shrink-0">
                    <img src="Images/sanemi.png" alt="Seller" class="w-full h-full object-cover">
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Benedict</h4>
                   
                </div>
            </div>

            <!-- stats of seller -->
            <div class="flex gap-12 flex-shrink-0">
                <div>
                    <p class="text-sm text-gray-600">Ratings</p>
                    <p class="text-lg font-bold text-amber-500">2.5K</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Products</p>
                    <p class="text-lg font-bold text-gray-900">4</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Response Rate</p>
                    <p class="text-lg font-bold text-black">99%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Response Time</p>
                    <p class="text-lg font-bold text-black">within minutes</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Joined</p>
                    <p class="text-lg font-bold text-gray-900">5 years ago</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Follower</p>
                    <p class="text-lg font-bold text-gray-900">2.1K</p>
                </div>
            </div>

            <!-- Seller Interact -->
            <div class="flex gap-3 flex-shrink-0">
                <button class="px-6 py-2 bg-black text-white rounded font-semibold hover:bg-amber-600 border-black">
                    Chat Now
                </button>
                <button class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded font-semibold hover:bg-gray-50 transition">
                    View Shop
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    function toggleHeart(event) {
        const heart = event.currentTarget.querySelector('.heart-icon');
        heart.classList.toggle('liked');
    }

    function selectSize(btn) {
        document.querySelectorAll('.size-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.add('border-gray-300');
            b.classList.remove('border-black');
        });
        btn.classList.add('active');
        btn.classList.add('border-black');
        btn.classList.remove('border-gray-300');
    }

    function selectColor(btn) {
        document.querySelectorAll('.color-option').forEach(b => {
            b.classList.remove('selected');
        });
        btn.classList.add('selected');
    }
</script>

<script src="JS/script.js"></script>

</body>
</html>
