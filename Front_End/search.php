<?php
// Search handler — performs DB lookup and renders product cards.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$query = '';
if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
}

// If a query is present, use Search model to search the products table
$products = [];
if ($query !== '') {
    require_once __DIR__ . '/../Back_End/Models/Search.php';
    $search = new Search();
    // returns normalized rows with keys: id, name, description, image, hover_image, price, category, availability
    $products = $search->search($query, 50);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search results</title>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php include 'nav_bar.php'; ?>
    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Search results</h1>

        <?php if ($query === ''): ?>
            <p>No search term provided. Try entering a query in the search box.</p>
        <?php else: ?>
            <p class="mb-4">Showing results for: <strong><?= htmlspecialchars($query) ?></strong></p>

            <?php if (empty($products)): ?>
                <div class="p-4 bg-white rounded border">No products found.</div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($products as $p):
                        $id = $p['id'] ?? '';
                        $name = $p['name'] ?? 'Product';
                        $image = $p['image'] ?? 'panti.png';
                        $hover = $p['hover_image'] ?? $image;
                        $priceRaw = $p['price'] ?? null;
                        $price = $priceRaw !== null ? number_format((int)$priceRaw) : '0';
                        $category = $p['category'] ?? '';
                        // Link by id (preferred). Fall back to passing params if id missing.
                        if (!empty($id)) {
                            $link = "product_info.php?id=" . urlencode($id);
                        } else {
                            $link = "product_info.php?name=" . urlencode($name) . "&image=" . urlencode($image) . "&hover_image=" . urlencode($hover) . "&price=" . urlencode($price) . "&category=" . urlencode($category);
                        }
                    ?>
                    <a href="<?= $link ?>" class="block bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            <img src="Images/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1"><?= htmlspecialchars($name) ?></h3>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">Brand</div>
                                <div class="font-bold">₱<?= $price ?>.00</div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
