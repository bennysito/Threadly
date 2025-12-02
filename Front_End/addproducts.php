<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../Back_End/Models/Categories.php";

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
}

if (!isset($_SESSION['user_id'])) {
    echo "<div class=\"p-4 bg-red-50 text-red-700 rounded\">Please log in to add products.</div>";
    return;
}

$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();
?>

<form method="POST" action="add_product_handler.php" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded">
  <div>
    <label class="text-sm text-gray-600 block mb-1">Product Name</label>
    <input type="text" name="product_name" class="w-full input-bg" required>
  </div>

  <div>
    <label class="text-sm text-gray-600 block mb-1">Category</label>
    <select name="category" class="w-full input-bg" required>
      <option value="">Select a category</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= e($cat['id'] ?? $cat['category_id'] ?? '') ?>"><?= e($cat['name'] ?? $cat['category'] ?? '') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="text-sm text-gray-600 block mb-1">Product Description</label>
    <textarea name="description" rows="4" class="w-full input-bg" required></textarea>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="text-sm text-gray-600 block mb-1">Quantity</label>
      <input type="number" name="quantity" class="w-full input-bg" min="0" required>
    </div>
    <div>
      <label class="text-sm text-gray-600 block mb-1">Price</label>
      <input type="number" step="0.01" name="price" class="w-full input-bg" min="0" required>
    </div>
  </div>

  <div>
    <label class="text-sm text-gray-600 block mb-1">Picture</label>
    <input type="file" name="picture" accept="image/*" required>
  </div>

  <div>
    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded">Add Product</button>
  </div>
</form>
