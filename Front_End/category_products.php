<?php
require_once __DIR__ . "/../Back_End/Models/Categories.php";

// Get category from URL
$categoryName = isset($_GET['category']) ? $_GET['category'] : "Unknown";

// Dummy products for now
$products = [
    ["name" => "Product 1", "image" => "panti.png"],
    ["name" => "Product 2", "image" => "baggy_pants.png"],
    ["name" => "Product 3", "image" => "underwear_women.png"],
    ["name" => "Product 4", "image" => "jacket_hoodie.png"],
    ["name" => "Product 5", "image" => "panti.png"],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products - <?= htmlspecialchars($categoryName) ?></title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h1 { margin-bottom: 20px; }
.product-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.product-item {
    border: 1px solid #ccc;
    padding: 10px;
    width: 150px;
    text-align: center;
    border-radius: 8px;
}
.product-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
}
</style>
</head>
<body>

<h1>Products in <?= htmlspecialchars($categoryName) ?></h1>

<div class="product-grid">
    <?php foreach($products as $product): ?>
        <div class="product-item">
            <img src="Images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <p><?= htmlspecialchars($product['name']) ?></p>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
