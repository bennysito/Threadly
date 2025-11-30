<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$query = $_GET['q'] ?? '';
$products = [];

if ($query !== '') {
    require_once __DIR__ . '/../Back_End/Models/Search_db.php';
    try {
        $search = new Search();
        $products = $search->search($query, 50);
    } catch (Exception $e) {
        echo "<div class='p-4 bg-red-100 text-red-800 mb-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Search results - Threadly</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<?php include 'nav_bar.php'; ?>

<main class="max-w-7xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Search results</h1>

  <?php if ($query === ''): ?>
    <p>No search term provided.</p>
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
          $price = isset($p['price']) ? number_format((float)$p['price'], 2) : '0.00';
          $category = $p['category'] ?? '';
          $availability = $p['availability'] ?? '';
          $link = !empty($id) ? "product_info.php?id=$id" : "#";
        ?>
        <a href="<?= $link ?>" class="block bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition">
          <div class="aspect-square bg-gray-100 flex items-center justify-center">
            <img src="/Threadly/<?= htmlspecialchars($image) ?>" 
                 alt="<?= htmlspecialchars($name) ?>" 
                 class="w-full h-full object-cover"
                 onerror="this.src='Images/panti.png'">
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-1"><?= htmlspecialchars($name) ?></h3>
            <div class="flex justify-between text-sm text-gray-600">
              <span>Category: <?= htmlspecialchars($category) ?></span>
              <span class="font-bold">₱<?= $price ?></span>
            </div>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($availability) ?></p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>
</body>
</html>