<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
$categoryName = $_GET['category'] ?? "All Products";

$products = [
    ["name" => "Classic Jacket",        "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 499],
    ["name" => "Cozy Sweater",          "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 299],
    ["name" => "Slim Pants",            "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 399],
    ["name" => "Everyday Brief",        "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 149],
    ["name" => "Pajama Set",            "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 349],
    ["name" => "Denim Jacket",          "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 699],
    ["name" => "Hoodie",                "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 459],
    ["name" => "Jogger Pants",          "image" => "panti.png", "hover_image" => "underwear_women.png", "price" => 379],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoryName) ?></title>
    <link rel="stylesheet" href="CSS/category_products.css">
</head>
<body>

<h1><?= htmlspecialchars($categoryName) ?></h1>

<div class="grid">
    <?php foreach($products as $p): ?>
    <div class="card">
        <div class="img-container">
            <!-- Main image -->
            <img src="Images/<?= htmlspecialchars($p['image']) ?>" 
                 alt="<?= htmlspecialchars($p['name']) ?>" 
                 class="main-img">

            <!-- Hover image (fades in on hover) -->
            <img src="Images/<?= htmlspecialchars($p['hover_image']) ?>" 
                 alt="<?= htmlspecialchars($p['name']) ?> hover" 
                 class="hover-img">
        </div>

        <div class="info">
            <div class="price-row">
                <div class="price">₱<?= number_format($p['price']) ?></div>
                <svg class="heart" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <p class="name"><?= htmlspecialchars($p['name']) ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</body>
</html>