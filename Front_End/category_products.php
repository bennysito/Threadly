<?php
require_once __DIR__ . "/../Back_End/Models/Categories.php";


$categoryName = isset($_GET['category']) ? $_GET['category'] : "Unknown";

//--static ni siya ako rani usbon puhon
$products = [
    ["name" => "Jacket", "image" => "panti.png"],
    ["name" => "Sweater", "image" => "baggy_pants.png"],
    ["name" => "Pants", "image" => "underwear_women.png"],
    ["name" => "Brief", "image" => "jacket_hoodie.png"],
    ["name" => "Pjama 5", "image" => "panti.png"],
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
.product-card {
    width: 16rem; 
    background: #fff;
    border: 1px solid #e5e7eb; 
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    overflow: hidden;
}
.product-card .image-area {
    width: 100%;
    height: 10rem; 
    background: #f3f4f6; 
}
.product-card .image-area img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.product-card .card-body {
    padding: 0.75rem;
}
.product-card .price {
    margin-top: 0.25rem;
    font-weight: 700;
    color: #111827; 
}
.product-card .meta {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280; 
}
</style>
</head>
<body>

<h1>Categor for <?= htmlspecialchars($categoryName) ?></h1>

<div class="product-grid">
    <?php foreach($products as $product): ?>
        <div class="product-card">
            <div class="image-area">
                <img src="Images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="card-body">
                <div class="text-sm text-gray-700"><?= htmlspecialchars($product['name']) ?></div>
                <div class="price">₱<?= htmlspecialchars(number_format($product['price'] ?? 0, 2)) ?></div>
                
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
