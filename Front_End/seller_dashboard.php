<?php
// profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php"; // adjust path if needed
require_once "../Back_End/Models/Categories.php";

// Ensure logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database(); // your Database class that returns mysqli-like object
$conn = $db->get_connection();
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, username, email, contact_number FROM users WHERE id = ?");
if (!$stmt) die("Prepare failed: " . $db->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Store original values for JS revert
$original_user = $user;

// Handle form submission
$success_msg = '';
$error_msg = '';
// Handle different POST actions: profile update OR add product
// Add product will be submitted with `action=add_product`
// Keep profile update behaviour as before for other POSTs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'add_product') {
    // Process add new product
    $pname = trim($_POST['product_name'] ?? '');
    $pcategory = trim($_POST['category'] ?? '');
    $pdescription = trim($_POST['description'] ?? '');
    $pquantity = intval($_POST['quantity'] ?? 0);
    $pprice = floatval($_POST['price'] ?? 0);
    if ($pname === '' || $pcategory === '' || $pdescription === '' || $pquantity < 0 || $pprice <= 0) {
      $error_msg = "Please fill in all product fields with valid values.";
    } elseif (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
      $error_msg = "Please provide a valid product image.";
    } else {
      // Save uploaded image to Front_End/uploads/
      $uploadsDir = __DIR__ . '/uploads/';
      if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
      $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['picture']['name']));
      $filename = time() . '_' . $safeName;
      $target = $uploadsDir . $filename;
      if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
        $error_msg = "Failed to save uploaded image.";
      } else {
        // Attempt to insert into products table by detecting actual columns to avoid schema mismatch
        $conn = $db->threadly_connect;
        $existingCols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM products");
        if ($resCols) {
          while ($r = $resCols->fetch_assoc()) { $existingCols[] = $r['Field']; }
        }

        $pick = function(array $candidates) use ($existingCols) {
          foreach ($candidates as $c) { if (in_array($c, $existingCols)) return $c; }
          return null;
        };
      }
        $nameCol = $pick(['product_name','name','title']);
        $descCol = $pick(['description','product_description','details']);
        $priceCol = $pick(['price','product_price','amount']);
        $categoryCol = $pick(['category','category_id','product_category']);
        $quantityCol = $pick(['quantity','stock','availability']);
        $imageCol = $pick(['image','product_image','image_url','img']);
        $sellerCol = $pick(['seller_id','user_id','owner_id']);

        $cols = [];
        $params = [];
        $types = '';

        if ($nameCol)  { $cols[] = "`$nameCol`"; $params[] = $pname; $types .= 's'; }
        if ($descCol)  { $cols[] = "`$descCol`"; $params[] = $pdescription; $types .= 's'; }
        if ($priceCol) { $cols[] = "`$priceCol`"; $params[] = $pprice; $types .= 'd'; }
        if ($categoryCol) { $cols[] = "`$categoryCol`"; $params[] = $pcategory; $types .= 's'; }
        if ($quantityCol) { $cols[] = "`$quantityCol`"; $params[] = $pquantity; $types .= 'i'; }
        if ($imageCol) { $cols[] = "`$imageCol`"; $params[] = $filename; $types .= 's'; }
        if ($sellerCol) { $cols[] = "`$sellerCol`"; $params[] = $user_id; $types .= 'i'; }

        if (empty($cols)) {
          $error_msg = "Products table does not contain any of the expected columns. Unable to insert.";
        } else {
          $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
          $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES ($placeholders)";
          $stmt = $conn->prepare($sql);
          if (!$stmt) {
            $error_msg = "Prepare failed for dynamic insert: " . $conn->error;
          } else {
            // bind params dynamically
            $bind_names[] = $types;
            for ($i=0;$i<count($params);$i++) { $bind_names[] = &$params[$i]; }
            // helper to pass by reference
            $refValues = function($arr) {
              $refs = [];
              foreach ($arr as $k => $v) $refs[$k] = &$arr[$k];
              return $refs;
            };
            call_user_func_array([$stmt, 'bind_param'], $refValues($bind_names));
            if ($stmt->execute()) {
              $success_msg = "Product added successfully.";
            } else {
              $error_msg = "Failed to add product: " . $stmt->error;
            }
            $stmt->close();
          }
        }
      }
    }
  } else {
    // existing profile update handling
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password && $new_password !== $confirm_password) {
        $error_msg = "New password and confirm password do not match!";
    } else {
        // Update user data
        $query = "UPDATE users SET first_name=?, last_name=?, username=?, email=?, contact_number=?";
        $params = [$first_name, $last_name, $username, $email, $contact_number];
        $types = "sssss";

        if ($new_password) {
            $query .= ", user_password=?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE id=?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        if (!$stmt) die("Prepare failed: " . $db->error);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success_msg = "Profile updated successfully!";
            // Refresh $user to reflect updated values
            $user = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'email' => $email,
                'contact_number' => $contact_number
            ];
            $original_user = $user;
        } else {
            $error_msg = "Update failed: " . $stmt->error;
        }
    }
  }

// Helper for safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Load categories for product form
$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();

// Load seller's products
$sellerProducts = [];
$conn = $db->threadly_connect;
$existingCols = [];
$resCols = $conn->query("SHOW COLUMNS FROM products");
if ($resCols) {
  while ($r = $resCols->fetch_assoc()) { $existingCols[] = $r['Field']; }
}

$pick = function(array $candidates) use ($existingCols) {
  foreach ($candidates as $c) { if (in_array($c, $existingCols)) return $c; }
  return null;
};

$idCol = $pick(['id','product_id']);
$nameCol = $pick(['product_name','name','title']);
$priceCol = $pick(['price','product_price','amount']);
$imageCol = $pick(['image','product_image','image_url','img']);
$quantityCol = $pick(['quantity','stock','availability']);
$sellerCol = $pick(['seller_id','user_id','owner_id']);
$descCol = $pick(['description','product_description','details']);

if ($idCol && $nameCol && $sellerCol) {
  $selectCols = [$idCol, $nameCol];
  if ($priceCol) $selectCols[] = $priceCol;
  if ($imageCol) $selectCols[] = $imageCol;
  if ($quantityCol) $selectCols[] = $quantityCol;
  if ($descCol) $selectCols[] = $descCol;
  
  $selectStr = implode(',', array_map(function($c) { return "`$c`"; }, $selectCols));
  $sql = "SELECT $selectStr FROM products WHERE `$sellerCol` = ? ORDER BY `$idCol` DESC";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
      $sellerProducts[] = $row;
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
<title>Profile Details — Threadly</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>
<style>
.input-bg { 
    background-color: #F5F5F5; 
    border-radius: 6px; 
    padding: .65rem .75rem; 
    border: 1px solid #bfb5b5ff; /* visible border */
    transition: all 0.2s ease;
}

.input-bg:focus { 
    outline: none; 
    border-color: #3B82F6; /* Tailwind blue-500 */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); /* subtle glow */
    background-color: #fff;
}
</style>
<script>
let editing = false;
const originalValues = <?= json_encode($original_user) ?>;

function toggleEdit() {
    editing = !editing;
    document.querySelectorAll('.editable').forEach(el => {
        el.readOnly = !editing;
        el.classList.toggle('bg-white', editing);
        el.classList.toggle('bg-gray-100', !editing);
    });

    document.getElementById('passwordFields').style.display = editing ? 'block' : 'none';
    const btn = document.getElementById('editBtn');
    btn.textContent = editing ? 'Cancel' : 'Edit Details';

    if (!editing) {
        // Revert values to original
        Object.keys(originalValues).forEach(key => {
            const field = document.querySelector(`[name=${key}]`);
            if (field) field.value = originalValues[key] ?? '';
        });
        document.querySelectorAll('[name=new_password], [name=confirm_password]').forEach(f => f.value = '');
    }
}
</script>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include 'nav_bar.php'; ?>

<main class="max-w-6xl mx-auto px-6 py-10">
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
      <nav class="text-sm text-gray-600 space-x-6">
        <a class="font-semibold text-gray-800" href="#">Account Details</a>
        <a href="#" class="hover:underline">My Reviews</a>
        <a href="#" class="hover:underline">My Wishlist</a>
        <a href="#" class="hover:underline">My Orders</a>
        <a href="#" id="productsTab" class="hover:underline">Products</a>
      </nav>
    </div>

    <!-- Products subsections (hidden by default) -->
    <div id="productsSection" style="display:none;" class="mb-6">
      <div class="mb-4 flex space-x-4 border-b pb-4">
        <button id="tabMyProducts" class="px-3 py-2 text-sm font-semibold text-gray-800">My products</button>
        <button id="tabAddProduct" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Add new product</button>
        <button id="tabSoldProducts" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Sold products</button>
      </div>

      <div id="myProductsContent" class="tab-content">
        <?php if (empty($sellerProducts)): ?>
          <div class="p-4 bg-gray-50 rounded">You don't have any products yet.</div>
        <?php else: ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach($sellerProducts as $prod): ?>
              <div class="bg-white rounded border border-gray-200 overflow-hidden hover:shadow-lg transition">
                <?php if (isset($prod[$imageCol]) && $prod[$imageCol]): ?>
                  <img src="uploads/<?= e($prod[$imageCol]) ?>" alt="<?= e($prod[$nameCol] ?? '') ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                  <div class="w-full h-48 bg-gray-200 flex items-center justify-center">No Image</div>
                <?php endif; ?>
                <div class="p-4">
                  <h3 class="font-semibold text-gray-800 mb-2"><?= e($prod[$nameCol] ?? '') ?></h3>
                  <?php if (isset($prod[$priceCol])): ?>
                    <p class="text-amber-600 font-bold mb-1">$<?= number_format($prod[$priceCol], 2) ?></p>
                  <?php endif; ?>
                  <?php if (isset($prod[$quantityCol])): ?>
                    <p class="text-sm text-gray-600 mb-3">Stock: <?= intval($prod[$quantityCol]) ?></p>
                  <?php endif; ?>
                  <button class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded">Edit</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div id="addProductContent" class="tab-content" style="display:none;">
        <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded">
          <input type="hidden" name="action" value="add_product">
          <div>
            <label class="text-sm text-gray-600 block mb-1">Product Name</label>
            <input type="text" name="product_name" class="w-full input-bg" required>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Category</label>
            <select name="category" class="w-full input-bg" required>
              <option value="">Select a category</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?= e($cat['name'] ?? $cat['category'] ?? '') ?>"><?= e($cat['name'] ?? $cat['category'] ?? '') ?></option>
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
      </div>

      <div id="soldProductsContent" class="tab-content" style="display:none;">
        <div class="p-4 bg-gray-50 rounded">No sold products yet.</div>
      </div>
    </div>

    <div id="accountSection">

    <?php if ($success_msg): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= e($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= e($error_msg) ?></div>
    <?php endif; ?>

    <form method="POST">
      <!-- Email -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Email Address</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Contact Number -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Contact Number</label>
        <input type="text" name="contact_number" value="<?= e($user['contact_number'] ?? '') ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Name row -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="text-sm text-gray-600 block mb-2">First Name</label>
          <input type="text" name="first_name" value="<?= e($user['first_name']) ?>" class="w-full input-bg editable" readonly>
        </div>

        <div>
          <label class="text-sm text-gray-600 block mb-2">Last Name</label>
          <input type="text" name="last_name" value="<?= e($user['last_name']) ?>" class="w-full input-bg editable" readonly>
        </div>
      </div>

      <!-- Username -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Username</label>
        <input type="text" name="username" value="<?= e($user['username']) ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Password fields -->
      <div id="passwordFields" style="display:none;">
        <div class="mb-4">
          <label class="text-sm text-gray-600 block mb-2">New Password</label>
          <input type="password" name="new_password" class="w-full input-bg editable">
        </div>
        <div class="mb-4">
          <label class="text-sm text-gray-600 block mb-2">Confirm Password</label>
          <input type="password" name="confirm_password" class="w-full input-bg editable">
        </div>
      </div>

      <div class="flex space-x-4">
        <button type="button" id="editBtn" onclick="toggleEdit()" class="px-4 py-2 border border-black bg-white border-color-black text-black rounded hover:bg-black hover:text-white">Edit Details</button>
        <button type="submit" id="saveBtn" class="px-4 py-2 border border-whte bg-amber-600 text-white rounded hover:bg-white hover:text-black hover:border-black" style="display:inline-block;">Save Changes</button>
      </div>
    </form>
  </div>
</main>

<script>
// Hide Save button unless editing (apply only to account/profile form)
const accountForm = document.querySelector('#accountSection form');
if (accountForm) {
  accountForm.addEventListener('submit', function(e) {
    if (!editing) e.preventDefault();
  });
}

// Products tab toggles
const productsTab = document.getElementById('productsTab');
const productsSection = document.getElementById('productsSection');
const accountSection = document.getElementById('accountSection');
const tabMy = document.getElementById('tabMyProducts');
const tabAdd = document.getElementById('tabAddProduct');
const tabSold = document.getElementById('tabSoldProducts');
const myContent = document.getElementById('myProductsContent');
const addContent = document.getElementById('addProductContent');
const soldContent = document.getElementById('soldProductsContent');

function openProducts() {
  productsSection.style.display = 'block';
  accountSection.style.display = 'none';
  showTab('my');
}

function closeProducts() {
  productsSection.style.display = 'none';
  accountSection.style.display = 'block';
}

if (productsTab) {
  productsTab.addEventListener('click', function(e){ e.preventDefault(); openProducts(); });
}

function clearTabStyles(){
  [tabMy, tabAdd, tabSold].forEach(b=>{ if (b) { b.classList.remove('text-gray-800'); b.classList.add('text-gray-600'); } });
}

function hideContents(){
  [myContent, addContent, soldContent].forEach(c=>{ if (c) c.style.display = 'none'; });
}

function showTab(name){
  hideContents();
  clearTabStyles();
  if (name === 'my') { if (myContent) myContent.style.display = 'block'; if (tabMy) { tabMy.classList.add('text-gray-800'); tabMy.classList.remove('text-gray-600'); } }
  if (name === 'add') { if (addContent) addContent.style.display = 'block'; if (tabAdd) { tabAdd.classList.add('text-gray-800'); tabAdd.classList.remove('text-gray-600'); } }
  if (name === 'sold') { if (soldContent) soldContent.style.display = 'block'; if (tabSold) { tabSold.classList.add('text-gray-800'); tabSold.classList.remove('text-gray-600'); } }
}

if (tabMy) tabMy.addEventListener('click', function(e){ e.preventDefault(); showTab('my'); });
if (tabAdd) tabAdd.addEventListener('click', function(e){ e.preventDefault(); showTab('add'); });
if (tabSold) tabSold.addEventListener('click', function(e){ e.preventDefault(); showTab('sold'); });

// Auto-navigate to My Products if success message is showing
window.addEventListener('load', function() {
  const successMsg = document.querySelector('.bg-green-100');
  if (successMsg) {
    setTimeout(function() {
      openProducts();
      showTab('my');
    }, 800);
  }
});

</script>
</body>
</html>


