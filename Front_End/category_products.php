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

<h1>Categor for <?= htmlspecialchars($categoryName) ?></h1>

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
