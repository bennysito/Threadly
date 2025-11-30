<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product = null;
$productId = null;

// === 1. Try to get product by ID (Recommended & Modern Way) ===
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id'];
    require_once __DIR__ . "/../Back_End/Models/Search_db.php";
    $search = new Search();
    $product = $search->getById($productId);
}

// === 2. Fallback for old links (still supported) ===
if (!$product && isset($_GET['name'])) {
    $product = [
        'name'        => $_GET['name'] ?? 'Unknown Product',
        'image'       => $_GET['image'] ?? 'panti.png',
        'hover_image' => $_GET['hover_image'] ?? 'underwear_women.png',
        'price'       => max(0, (int)($_GET['price'] ?? 0)),
        'category'    => $_GET['category'] ?? 'Products',
        'description' => 'No description provided.',
        'condition'   => 'Gently Used',
        'sizes'       => 'M', // fallback
    ];
}

// === 3. If still no product → 404 ===
if (!$product) {
    http_response_code(404);
    die('<div class="text-center py-20"><h1 class="text-4xl font-bold text-gray-800">404 - Product Not Found</h1></div>');
}

// === Extract data safely (Dynamic!) ===
$productName       = $product['name'] ?? 'Unknown Product';
$productImage      = $product['image'] ?? 'panti.png';
$productHoverImage = $product['hover_image'] ?? $productImage;
$productPrice      = (int)($product['price'] ?? 0);
$categoryName      = $product['category'] ?? 'Products';
$description       = $product['description'] ?? 'No description available.';
$condition         = $product['condition'] ?? 'Gently Used';
$sizeString        = $product['sizes'] ?? 'M';
$sizes             = array_filter(array_map('trim', explode(',', $sizeString)));

// Extra images (if you have them in DB)
$extraImages = array_filter([
    $product['image2'] ?? null,
    $product['image3'] ?? null,
    $product['image4'] ?? null,
]);

$allThumbnails = array_unique(array_filter([$productImage, $productHoverImage, ...$extraImages]));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($productName) ?> - Threaldy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/produc_info.css">
    <style>
        .heart-icon.liked { fill: #ef4444 !important; stroke: #ef4444 !important; }
        .size-btn.active { background-color: black; color: white; border-color: black; }
        .thumbnail { transition: all 0.3s; }
        .thumbnail:hover { border-color: #1f2937 !important; transform: scale(1.05); }
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">

    <!-- Navigation -->
    <?php require "Nav_bar.php"; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-8">
            <a href="index.php" class="hover:text-black">Home</a>
            <span class="mx-2">></span>
            <a href="category.php?category=<?= urlencode($categoryName) ?>" class="hover:text-black">
                <?= htmlspecialchars($categoryName) ?>
            </a>
            <span class="mx-2">></span>
            <span class="text-black font-medium"><?= htmlspecialchars($productName) ?></span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            <!-- Image Gallery -->
            <div class="flex flex-col gap-5">

                <!-- Main Image -->
                <div class="relative bg-gray-100 rounded-2xl overflow-hidden aspect-square shadow-lg">
                    <img id="mainImage"
                         src="Images/<?= htmlspecialchars($productImage) ?>"
                         alt="<?= htmlspecialchars($productName) ?>"
                         class="w-full h-full object-cover">

                    <!-- Wishlist Heart -->
                    <button onclick="toggleWishlist(this, <?= $productId ?? 'null' ?>)"
                            class="absolute top-4 right-4 p-3 bg-white rounded-full shadow-xl z-10 hover:scale-110 transition">
                        <svg class="heart-icon w-7 h-7 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                </div>

                <!-- Thumbnails -->
                <?php if (count($allThumbnails) > 1): ?>
                <div class="flex gap-3 flex-wrap">
                    <?php foreach ($allThumbnails as $thumb): ?>
                        <img src="Images/<?= htmlspecialchars($thumb) ?>"
                             alt="Thumbnail"
                             class="thumbnail w-20 h-20 object-cover rounded-lg border-2 border-gray-300 cursor-pointer shadow-sm"
                             onclick="document.getElementById('mainImage').src = this.src">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Details -->
            <div class="flex flex-col gap-6">

                <h1 class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($productName) ?></h1>

                <div class="text-4xl font-bold text-gray-900">₱<?= number_format($productPrice) ?>.00</div>

                <p class="text-sm text-gray-600"><strong>Sold by:</strong> Benedictbenedt</p>

                <!-- Condition -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Condition</label>
                    <span class="px-5 py-2 bg-black text-white rounded font-medium">
                        <?= htmlspecialchars($condition) ?>
                    </span>
                </div>

                <!-- Size Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Size</label>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($sizes as $index => $size): ?>
                            <button type="button"
                                    onclick="selectSize(this)"
                                    class="size-btn px-6 py-3 border-2 rounded font-medium transition <?= $index === 0 ? 'active border-black bg-black text-white' : 'border-gray-300 hover:border-black' ?>">
                                <?= htmlspecialchars($size) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Add to Bag -->
                <button onclick="addToBag(<?= $productId ?? 'null' ?>)"
                        class="w-full bg-black text-white py-4 rounded-full font-bold text-lg hover:bg-gray-800 transition flex items-center justify-center gap-3 shadow-lg mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    ADD TO BAG
                </button>

                <!-- Product Details Accordion -->
                <div class="border-t pt-6 mt-6">
                    <details class="mb-4 group">
                        <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600 flex justify-between items-center">
                            Description
                            <span class="text-xl group-open:rotate-180 transition">↓</span>
                        </summary>
                        <p class="text-gray-600 mt-3 leading-relaxed"><?= nl2br(htmlspecialchars($description)) ?></p>
                    </details>

                    <details class="mb-4 group">
                        <summary class="font-semibold text-gray-900 cursor-pointer hover:text-amber-600 flex justify-between items-center">
                            Shipping & Returns
                            <span class="text-xl group-open:rotate-180 transition">↓</span>
                        </summary>
                        <p class="text-gray-600 mt-3">Standard shipping: 3–7 days • Free returns within 14 days</p>
                    </details>
                </div>
            </div>
        </div>

        <!-- Seller Profile (Optional - Keep or Replace) -->
        <div class="border-t mt-12 pt-8">
            <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-gray-300">
                        <img src="Images/sanemi.png" alt="Seller" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-xl font-bold">Benedict</h4>
                        <p class="text-sm text-gray-600">Trusted Seller</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button class="px-6 py-3 bg-black text-white rounded font-bold hover:bg-amber-600 transition">Chat Now</button>
                    <button class="px-6 py-3 border-2 border-gray-300 rounded font-bold hover:bg-gray-50 transition">View Shop</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
        }

        function selectSize(btn) {
            document.querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('active', 'bg-black', 'text-white', 'border-black');
                b.classList.add('border-gray-300');
            });
            btn.classList.add('active', 'bg-black', 'text-white', 'border-black');
            btn.classList.remove('border-gray-300');
        }

        function toggleWishlist(btn, productId) {
            const heart = btn.querySelector('.heart-icon');
            heart.classList.toggle('liked');
            // Optional: AJAX call to save wishlist
        }

        function addToBag(productId) {
            if (!productId) {
                alert("Product ID not available.");
                return;
            }
            // Optional: Send AJAX to add_to_cart.php
            alert("Added to bag! (Product ID: " + productId + ")");
        }
    </script>
</body>
</html>