<?php
// profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php"; // adjust path if needed
require_once "../Back_End/Models/Categories.php";
require_once "../Back_End/Models/Bidding.php";

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
if (!$stmt) die("Prepare failed: " . $conn->error);
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

// Check for success message from query parameter (after redirect)
if (isset($_GET['success'])) {
  $success_msg = htmlspecialchars($_GET['success']);
}

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
    $pbidding = isset($_POST['bidding']) ? 1 : 0;
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
        $biddingCol = $pick(['bidding','enable_bidding','bidding_enabled','is_bidding']);
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
        if ($biddingCol) { $cols[] = "`$biddingCol`"; $params[] = $pbidding; $types .= 'i'; }
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
  } else if (isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    // Handle edit product
    $product_id = intval($_POST['product_id'] ?? 0);
    $pname = trim($_POST['product_name'] ?? '');
    $pprice = floatval($_POST['price'] ?? 0);
    $pquantity = intval($_POST['quantity'] ?? 0);
    $pdescription = trim($_POST['description'] ?? '');
    $pbidding = isset($_POST['bidding']) ? 1 : 0;
    
    // Debug logging
    error_log("Edit product attempt: ID=$product_id, Name=$pname, Price=$pprice, Qty=$pquantity");
    
    if ($product_id <= 0) {
      $error_msg = "Invalid product ID: " . intval($_POST['product_id'] ?? 'empty');
    } elseif ($pname === '') {
      $error_msg = "Product name cannot be empty.";
    } elseif ($pprice <= 0) {
      $error_msg = "Price must be greater than 0 (received: $pprice).";
    } elseif ($pquantity < 0) {
      $error_msg = "Quantity cannot be negative.";
    } elseif ($pdescription === '') {
      $error_msg = "Description cannot be empty.";
    } else {
      $conn = $db->threadly_connect;
      
      // Verify product exists and belongs to user (handle NULL seller_id for unassigned products)
      $verifyStmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND (seller_id = ? OR seller_id IS NULL)");
      if ($verifyStmt) {
        $verifyStmt->bind_param('ii', $product_id, $user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyResult->num_rows === 0) {
          $error_msg = "Product not found or you don't have permission to edit it.";
        } else {
          // Build the UPDATE query - also set seller_id if it's NULL
          $updateQuery = "UPDATE products SET 
                          seller_id=?,
                          product_name=?, 
                          price=?, 
                          quantity=?, 
                          description=?";
          $updateParams = [$user_id, $pname, $pprice, $pquantity, $pdescription];
          $updateTypes = 'isdis';
          
          // Handle new image if uploaded
          if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['picture']['name']));
            $filename = time() . '_' . $safeName;
            $target = $uploadsDir . $filename;
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
              $updateQuery .= ", image_url=?";
              $updateParams[] = $filename;
              $updateTypes .= 's';
              error_log("Image uploaded: $filename");
            }
          }

            // Include bidding flag if column exists in DB (we attempt to set it)
            $colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
            if ($colRes && $colRes->num_rows > 0) {
              $updateQuery .= ", bidding=?";
              $updateParams[] = $pbidding;
              $updateTypes .= 'i';
            }
          
          $updateQuery .= " WHERE product_id=? AND (seller_id=? OR seller_id IS NULL)";
          $updateParams[] = $product_id;
          $updateParams[] = $user_id;
          $updateTypes .= 'ii';
          
          error_log("Update query: $updateQuery with types: $updateTypes");
          
          $updateStmt = $conn->prepare($updateQuery);
          if ($updateStmt) {
            // bind_param requires parameters to be passed by reference. Use call_user_func_array
            // and build an array of references to avoid "expected parameter to be a reference" issues
            $bindParams = [];
            $bindParams[] = $updateTypes;
            for ($i = 0; $i < count($updateParams); $i++) {
              $bindParams[] = &$updateParams[$i];
            }
            call_user_func_array([$updateStmt, 'bind_param'], $bindParams);

            if ($updateStmt->execute()) {
              error_log("Product updated successfully: product_id=$product_id");
              // Redirect to refresh the page and see updated products
              header("Location: seller_dashboard.php?success=Product updated successfully");
              exit;
            } else {
              $error_msg = "Failed to update product: " . $updateStmt->error;
              error_log("Update failed: " . $updateStmt->error);
            }
            $updateStmt->close();
          } else {
            $error_msg = "Database error: " . $conn->error;
            error_log("Prepare failed: " . $conn->error);
          }
        }
        $verifyStmt->close();
      } else {
        $error_msg = "Database error: " . $conn->error;
        error_log("Verify prepare failed: " . $conn->error);
      }
    }
  } else {
    // existing profile update handling
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $contact_number = $_POST['contact_number'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password && $new_password !== $confirm_password) {
        $error_msg = "New password and confirm password do not match!";
    } else {
        // Update user data
        // Only update email if it's provided and not empty
        if (!empty($email)) {
            $query = "UPDATE users SET first_name=?, last_name=?, username=?, email=?, contact_number=?";
            $params = [$first_name, $last_name, $username, $email, $contact_number];
            $types = "sssss";
        } else {
            // Skip email if empty to avoid duplicate empty string error
            $query = "UPDATE users SET first_name=?, last_name=?, username=?, contact_number=?";
            $params = [$first_name, $last_name, $username, $contact_number];
            $types = "ssss";
        }

        if ($new_password) {
            $query .= ", user_password=?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE id=?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
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

// include `bidding` in select only if the column exists
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
$includeBidding = ($colRes && $colRes->num_rows > 0);

$sql = "SELECT product_id, product_name, price, image_url, quantity, description" . ($includeBidding ? ", bidding" : "") . " 
        FROM products 
        WHERE seller_id = ? 
        ORDER BY product_id DESC";
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

// Fetch bids for seller's products
$sellerBids = [];

// First check if bids table has product_id or uses session_id
$colCheck = $conn->query("SHOW COLUMNS FROM bids LIKE 'product_id'");
$hasProductId = ($colCheck && $colCheck->num_rows > 0);

if ($hasProductId) {
    // New schema with product_id
    $bidsSql = "
        SELECT 
            b.bid_id, 
            b.product_id, 
            b.user_id,
            b.bid_amount, 
            b.bid_status, 
            b.bid_message, 
            b.created_at,
            p.product_name, 
            p.image_url, 
            p.price,
            u.username,
            u.email,
            u.contact_number,
            u.first_name,
            u.last_name
        FROM bids b
        JOIN products p ON b.product_id = p.product_id
        JOIN users u ON b.user_id = u.user_id
        WHERE p.seller_id = ?
        ORDER BY b.created_at DESC
    ";
} else {
    // Old schema with session_id
    $bidsSql = "
        SELECT 
            b.bid_id, 
            bs.product_id, 
            b.user_id,
            b.bid_amount, 
            b.bid_status, 
            b.bid_message, 
            b.created_at,
            p.product_name, 
            p.image_url, 
            p.price,
            u.username,
            u.email,
            u.contact_number,
            u.first_name,
            u.last_name
        FROM bids b
        LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
        LEFT JOIN products p ON bs.product_id = p.product_id
        LEFT JOIN users u ON b.user_id = u.id
        WHERE p.seller_id = ?
        ORDER BY b.bit_time DESC
    ";
}

$bidsStmt = $conn->prepare($bidsSql);
if ($bidsStmt) {
    $bidsStmt->bind_param('i', $user_id);
    $bidsStmt->execute();
    $bidsResult = $bidsStmt->get_result();
    while ($row = $bidsResult->fetch_assoc()) {
        $sellerBids[] = $row;
    }
    $bidsStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<title>Profile Details — Threadly</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    font-family: 'Inter', sans-serif;
}
.font-chewy {
    font-family: 'Chewy', cursive;
}
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

/* Bid Styles */
.bid-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    transition: all 0.25s ease;
    border: 1px solid #e5e7eb;
}

.bid-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    border-color: #b45309;
}

.bid-header {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    align-items: flex-start;
}

.bid-product-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: #f3f4f6;
}

.bid-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bid-product-info {
    flex: 1;
}

.bid-product-name {
    font-weight: 600;
    color: #111;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.bid-product-price {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.bid-product-price strong {
    color: #111;
    font-weight: 600;
}

.bid-status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bid-status-pending {
    background: #fef3c7;
    color: #92400e;
}

.bid-status-accepted {
    background: #d1fae5;
    color: #065f46;
}

.bid-status-rejected {
    background: #fee2e2;
    color: #7f1d1d;
}

.bid-status-withdrawn {
    background: #f3f4f6;
    color: #374151;
}

.bid-details {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.bid-detail-item {
    display: flex;
    flex-direction: column;
}

.bid-detail-label {
    font-size: 0.85rem;
    color: #666;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bid-detail-value {
    font-size: 1.05rem;
    color: #111;
    font-weight: 600;
}

.bid-amount-highlight {
    font-size: 1.75rem;
    color: #b45309;
}

.bid-customer-info {
    font-size: 0.95rem;
    line-height: 1.6;
}

.bid-customer-name {
    font-weight: 700;
    color: #111;
}

.bid-message {
    background-color: #f3f4f6;
    padding: 1rem;
    border-left: 3px solid #b45309;
    border-radius: 4px;
    font-size: 0.95rem;
    color: #374151;
    font-style: italic;
}

.bid-actions {
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.bid-action-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.bid-approve-btn {
    background-color: #10b981;
    color: white;
}

.bid-approve-btn:hover {
    background-color: #059669;
}

.bid-reject-btn {
    background-color: #ef4444;
    color: white;
}

.bid-reject-btn:hover {
    background-color: #dc2626;
}

.bid-action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.no-bids-message {
    text-align: center;
    padding: 2rem;
    color: #666;
    background: #f9fafb;
    border-radius: 8px;
}

.bids-section {
    margin-top: 3rem;
    padding: 2rem;
    background-color: #f9fafb;
    border-radius: 12px;
}

.bids-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #1f2937;
}

@media (max-width: 768px) {
    .bid-header {
        flex-direction: column;
    }
    
    .bid-details {
        grid-template-columns: 1fr;
    }
    
    .bid-actions {
        justify-content: center;
    }
}
</style>
</style>
<script>
let editing = false;
window.originalValues = <?= json_encode($original_user) ?>;
window.userValues = <?= json_encode($user) ?>;

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
        Object.keys(window.originalValues).forEach(key => {
            const field = document.querySelector(`[name=${key}]`);
            if (field) field.value = window.originalValues[key] ?? '';
        });
        document.querySelectorAll('[name=new_password], [name=confirm_password]').forEach(f => f.value = '');
    }
}
</script>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include 'nav_bar.php'; ?>
<?php require "wishlist_panel.php"; ?>
<?php require "notification_panel.php"; ?> 
<?php require "add_to_bag.php"; ?> 
<?php require "messages_panel.php"; ?> 

<main class="max-w-6xl mx-auto px-6 py-10">
  <div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-6">
      <nav class="text-sm text-gray-600 space-x-6">
        <a id="navAccount" href="#" class="hover:underline">Account Details</a>
        <a href="#" class="hover:underline">My Reviews</a>
        <a href="#" class="hover:underline">My Wishlist</a>
        <a href="#" class="hover:underline">My Orders</a>
        <?php if ($isSeller): ?>
          <a id="navSeller" href="seller_dashboard.php" class="hover:underline">Seller Center</a>
        <?php else: ?>
          <a href="Verify_Seller.php" class="hover:underline">Become a Seller</a>
        <?php endif; ?>
      </nav>
    </div>
    <!-- Products subsections (hidden by default) -->
    <div id="productsSection" style="display:none;" class="mb-6">
      <div class="mb-4 flex space-x-4 border-b pb-4">
        <button id="tabMyProducts" class="px-3 py-2 text-sm font-semibold text-gray-800">My products</button>
        <button id="tabAddProduct" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Add new product</button>
        <button id="tabSoldProducts" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Sold products</button>
        <button id="tabReceivedBids" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Received Bids</button>
      </div>

      <div id="myProductsContent" class="tab-content">
        <?php if (empty($sellerProducts)): ?>
          <div class="p-4 bg-gray-50 rounded">You don't have any products yet.</div>
        <?php else: ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach($sellerProducts as $prod): ?>
              <div class="bg-white rounded border border-gray-200 overflow-hidden hover:shadow-lg transition">
                <?php if (isset($prod['image_url']) && $prod['image_url']): ?>
                  <img src="uploads/<?= e($prod['image_url']) ?>" alt="<?= e($prod['product_name'] ?? '') ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                  <div class="w-full h-48 bg-gray-200 flex items-center justify-center">No Image</div>
                <?php endif; ?>
                <div class="p-4">
                  <h3 class="font-semibold text-gray-800 mb-2"><?= e($prod['product_name'] ?? '') ?></h3>
                  <?php if (isset($prod['price'])): ?>
                    <p class="text-amber-600 font-bold mb-1">$<?= number_format($prod['price'], 2) ?></p>
                  <?php endif; ?>
                  <?php if (isset($prod['quantity'])): ?>
                    <p class="text-sm text-gray-600 mb-3">Stock: <?= intval($prod['quantity']) ?></p>
                  <?php endif; ?>
                  <button class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded editProductBtn" data-product-id="<?= e($prod['product_id'] ?? '') ?>" data-product-name="<?= e($prod['product_name'] ?? '') ?>" data-product-price="<?= e($prod['price'] ?? '') ?>" data-product-quantity="<?= e($prod['quantity'] ?? '') ?>" data-product-description="<?= e($prod['description'] ?? '') ?>" data-product-image="<?= e($prod['image_url'] ?? '') ?>" data-product-bidding="<?= e($prod['bidding'] ?? 0) ?>">Edit</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div id="addProductContent" class="tab-content" style="display:none;">
        <form id="addProductForm" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded">
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
                <option value="<?= e($cat['id'] ?? $cat['category_id'] ?? '') ?>"><?= e($cat['name'] ?? $cat['category'] ?? '') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Product Description</label>
            <textarea name="description" rows="4" class="w-full input-bg" required></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" id="addProductBidding" name="bidding" value="1">
            <label for="addProductBidding" class="text-sm text-gray-600">Enable bidding for this product</label>
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
            <button type="submit" id="addProductSubmitBtn" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed">Add Product</button>
          </div>
        </form>
      </div>

      <div id="soldProductsContent" class="tab-content" style="display:none;">
        <div class="p-4 bg-gray-50 rounded">No sold products yet.</div>
      </div>

      <div id="receivedBidsContent" class="tab-content" style="display:none;">
        <div class="bids-section" style="background: transparent; padding: 0; margin-top: 0;">
          <?php if (empty($sellerBids)): ?>
            <div class="no-bids-message">
              <p>You haven't received any bids yet.</p>
              <p style="font-size: 0.9rem; color: #999;">When customers place bids on your products, they will appear here.</p>
            </div>
          <?php else: ?>
            <?php foreach ($sellerBids as $bid): ?>
              <div class="bid-card" data-bid-id="<?= $bid['bid_id'] ?>">
                <!-- Product and Status Header -->
                <div class="bid-header">
                  <div class="bid-product-image">
                    <?php if (!empty($bid['image_url'])): ?>
                      <img src="uploads/<?= e($bid['image_url']) ?>" alt="<?= e($bid['product_name']) ?>">
                    <?php else: ?>
                      <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f3f4f6; color:#999; font-size:0.8rem; text-align:center; padding: 0.5rem;">No Image</div>
                    <?php endif; ?>
                  </div>
                  <div class="bid-product-info">
                    <div class="bid-product-name"><?= e($bid['product_name']) ?></div>
                    <div class="bid-product-price">Original Price: <strong>₱<?= number_format((float)$bid['price'], 2) ?></strong></div>
                    <span class="bid-status-badge bid-status-<?= $bid['bid_status'] ?>">
                      <?= ucfirst($bid['bid_status']) ?>
                    </span>
                  </div>
                </div>
                
                <!-- Bid Details -->
                <div class="bid-details">
                  <div class="bid-detail-item">
                    <div class="bid-detail-label">Bid Amount</div>
                    <div class="bid-detail-value bid-amount-highlight">₱<?= number_format((float)$bid['bid_amount'], 2) ?></div>
                  </div>
                  
                  <div class="bid-detail-item">
                    <div class="bid-detail-label">Bidder</div>
                    <div class="bid-customer-info">
                      <div class="bid-customer-name"><?= e($bid['first_name'] . ' ' . $bid['last_name']) ?></div>
                      <div style="color: #666; font-size: 0.9rem;">@<?= e($bid['username']) ?></div>
                    </div>
                  </div>
                  
                  <div class="bid-detail-item">
                    <div class="bid-detail-label">Contact Info</div>
                    <div style="font-size: 0.95rem; color: #111;">
                      <div><?= e($bid['email']) ?></div>
                      <div><?= e($bid['contact_number']) ?></div>
                    </div>
                  </div>
                  
                  <div class="bid-detail-item">
                    <div class="bid-detail-label">Bid Date</div>
                    <div style="font-size: 0.95rem; color: #111;">
                      <?php 
                      $date = new DateTime($bid['created_at']);
                      echo $date->format('M d, Y h:i A');
                      ?>
                    </div>
                  </div>
                </div>
                
                <!-- Message (if available) -->
                <?php if (!empty($bid['bid_message'])): ?>
                  <div style="padding: 0 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <div class="bid-detail-label" style="margin-bottom: 0.75rem;">Message from Bidder</div>
                    <div class="bid-message"><?= e($bid['bid_message']) ?></div>
                  </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="bid-actions">
                  <?php if ($bid['bid_status'] === 'pending'): ?>
                    <button class="bid-action-btn bid-approve-btn" onclick="updateBidStatus(<?= $bid['bid_id'] ?>, 'accepted')">
                      ✓ Approve Bid
                    </button>
                    <button class="bid-action-btn bid-reject-btn" onclick="updateBidStatus(<?= $bid['bid_id'] ?>, 'rejected')">
                      ✗ Reject Bid
                    </button>
                  <?php else: ?>
                    <button class="bid-action-btn" style="background-color: #9ca3af; color: white;" disabled>
                      <?= ucfirst($bid['bid_status']) ?>
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Edit Product</h2>
          <button id="closeEditModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <form id="editProductForm" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="edit_product">
          <input type="hidden" id="editProductId" name="product_id">

          <div>
            <label class="text-sm text-gray-600 block mb-1">Product Name</label>
            <input type="text" id="editProductName" name="product_name" class="w-full input-bg" required>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Price</label>
            <input type="number" id="editProductPrice" name="price" step="0.01" class="w-full input-bg" min="0" required>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Quantity</label>
            <input type="number" id="editProductQuantity" name="quantity" class="w-full input-bg" min="0" required>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Description</label>
            <textarea id="editProductDescription" name="description" rows="4" class="w-full input-bg" required></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" id="editProductBidding" name="bidding" value="1">
            <label for="editProductBidding" class="text-sm text-gray-600">Enable bidding for this product</label>
          </div>

          <div>
            <label class="text-sm text-gray-600 block mb-1">Product Image</label>
            <div id="currentImage" class="mb-2">
              <img id="currentImagePreview" src="" alt="Current product image" class="h-32 object-cover rounded">
            </div>
            <input type="file" id="editProductImage" name="picture" accept="image/*" class="w-full">
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current image</p>
          </div>

          <div class="flex space-x-4">
            <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">Save Changes</button>
            <button type="button" id="cancelEditModal" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
          </div>
        </form>
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
const tabBids = document.getElementById('tabReceivedBids');
const myContent = document.getElementById('myProductsContent');
const addContent = document.getElementById('addProductContent');
const soldContent = document.getElementById('soldProductsContent');
const receivedBidsContent = document.getElementById('receivedBidsContent');

// Edit Product Modal Variables
const editProductModal = document.getElementById('editProductModal');
const closeEditModal = document.getElementById('closeEditModal');
const cancelEditModal = document.getElementById('cancelEditModal');
const editProductForm = document.getElementById('editProductForm');

// Edit Product Modal Functions
function openEditModal(productData) {
  document.getElementById('editProductId').value = productData.id;
  document.getElementById('editProductName').value = productData.name;
  document.getElementById('editProductPrice').value = productData.price;
  document.getElementById('editProductQuantity').value = productData.quantity;
  document.getElementById('editProductDescription').value = productData.description;
  
  if (productData.image) {
    document.getElementById('currentImagePreview').src = 'uploads/' + productData.image;
    document.getElementById('currentImage').style.display = 'block';
  } else {
    document.getElementById('currentImage').style.display = 'none';
  }

  // set bidding checkbox
  try {
    var bidEl = document.getElementById('editProductBidding');
    if (bidEl) bidEl.checked = (productData.bidding == 1 || productData.bidding === '1' || productData.bidding === true || productData.bidding === 'true');
  } catch(e) { console.warn('bidding checkbox not found', e); }
  
  editProductModal.classList.remove('hidden');
}

function closeEditProductModal() {
  editProductModal.classList.add('hidden');
  editProductForm.reset();
}

// Event listeners for modal
closeEditModal.addEventListener('click', closeEditProductModal);
cancelEditModal.addEventListener('click', closeEditProductModal);

// Close modal when clicking outside
editProductModal.addEventListener('click', function(e) {
  if (e.target === editProductModal) {
    closeEditProductModal();
  }
});

// Handle edit product form submission
editProductForm.addEventListener('submit', function(e) {
  e.preventDefault();
  console.log('Edit product form submitted');
  
  // Validate form
  const productId = document.getElementById('editProductId').value;
  const productName = document.getElementById('editProductName').value.trim();
  const price = parseFloat(document.getElementById('editProductPrice').value);
  const quantity = parseInt(document.getElementById('editProductQuantity').value);
  const description = document.getElementById('editProductDescription').value.trim();
  
  if (!productId || !productName || price <= 0 || quantity < 0 || !description) {
    alert('Please fill in all fields correctly');
    return;
  }
  
  console.log('Submitting:', {productId, productName, price, quantity, description});
  // Submit the form
  this.submit();
});

// Edit button click handlers
  document.querySelectorAll('.editProductBtn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const productData = {
      id: this.getAttribute('data-product-id'),
      name: this.getAttribute('data-product-name'),
      price: this.getAttribute('data-product-price'),
      quantity: this.getAttribute('data-product-quantity'),
      description: this.getAttribute('data-product-description'),
      image: this.getAttribute('data-product-image'),
      bidding: this.getAttribute('data-product-bidding')
    };
    openEditModal(productData);
  });
});

// Prevent double form submission for Add Product form
const addProductForm = document.getElementById('addProductForm');
if (addProductForm) {
  addProductForm.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('addProductSubmitBtn');
    if (submitBtn && submitBtn.disabled) {
      e.preventDefault();
      return false;
    }
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding Product...';
  });
}

function openProducts() {
  productsSection.style.display = 'block';
  accountSection.style.display = 'none';
  showTab('my');
}


function closeProducts() {
  productsSection.style.display = 'none';
  accountSection.style.display = 'block';
  // Restore original values to account form fields
  if (window.originalValues) {
    Object.keys(window.originalValues).forEach(function(key) {
      var field = accountSection.querySelector('input[name="'+key+'"], textarea[name="'+key+'"], select[name="'+key+'"]');
      if (field) field.value = window.originalValues[key] ?? '';
    });
    accountSection.querySelectorAll('input[name="new_password"], input[name="confirm_password"]').forEach(f => f.value = '');
  }
}

if (productsTab) {
  productsTab.addEventListener('click', function(e){ e.preventDefault(); openProducts(); });
}

function clearTabStyles(){
  [tabMy, tabAdd, tabSold, tabBids].forEach(b=>{ if (b) { b.classList.remove('text-gray-800'); b.classList.add('text-gray-600'); } });
}

function hideContents(){
  [myContent, addContent, soldContent, receivedBidsContent].forEach(c=>{ if (c) c.style.display = 'none'; });
}

function showTab(name){
  hideContents();
  clearTabStyles();
  if (name === 'my') { if (myContent) myContent.style.display = 'block'; if (tabMy) { tabMy.classList.add('text-gray-800'); tabMy.classList.remove('text-gray-600'); } }
  if (name === 'add') { if (addContent) addContent.style.display = 'block'; if (tabAdd) { tabAdd.classList.add('text-gray-800'); tabAdd.classList.remove('text-gray-600'); } }
  if (name === 'sold') { if (soldContent) soldContent.style.display = 'block'; if (tabSold) { tabSold.classList.add('text-gray-800'); tabSold.classList.remove('text-gray-600'); } }
  if (name === 'bids') { if (receivedBidsContent) receivedBidsContent.style.display = 'block'; if (tabBids) { tabBids.classList.add('text-gray-800'); tabBids.classList.remove('text-gray-600'); } }
}

if (tabMy) tabMy.addEventListener('click', function(e){ e.preventDefault(); showTab('my'); });
if (tabAdd) tabAdd.addEventListener('click', function(e){ e.preventDefault(); showTab('add'); });
if (tabSold) tabSold.addEventListener('click', function(e){ e.preventDefault(); showTab('sold'); });
if (tabBids) tabBids.addEventListener('click', function(e){ e.preventDefault(); showTab('bids'); });

// Account Details tab click handler
const navAccount = document.getElementById('navAccount');
if (navAccount) {
  navAccount.addEventListener('click', function(e) {
    e.preventDefault();
    productsSection.style.display = 'none';
    accountSection.style.display = 'block';
    // Restore original values to account form fields from PHP user data
    if (window.userValues) {
      Object.keys(window.userValues).forEach(function(key) {
        var field = accountSection.querySelector('input[name="'+key+'"]');
        if (field) {
          field.value = window.userValues[key] ?? '';
        }
      });
      accountSection.querySelectorAll('input[name="new_password"], input[name="confirm_password"]').forEach(f => f.value = '');
    }
    // Highlight Account Details, remove highlight from Seller Center
    navAccount.classList.add('font-semibold', 'text-gray-800');
    var navSeller = document.getElementById('navSeller');
    if (navSeller) navSeller.classList.remove('font-semibold', 'text-gray-800');
  });
}

// Bid status update function
function updateBidStatus(bidId, status) {
    const statusText = status === 'accepted' ? 'approve' : 'reject';
    if (!confirm('Are you sure you want to ' + statusText + ' this bid?')) {
        return;
    }
    
    fetch('update_bid_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'bid_id=' + bidId + '&bid_status=' + status
    })
    .then(response => response.json())
    .then(data => {
        console.log('Update response:', data);
        if (data.success) {
            // Update the bid card UI immediately
            const bidCard = document.querySelector('[data-bid-id="' + bidId + '"]');
            if (bidCard) {
                // Find the status badge and update it
                const statusBadge = bidCard.querySelector('.bid-status-badge');
                if (statusBadge) {
                    // Remove all status classes
                    statusBadge.classList.remove('bid-status-pending', 'bid-status-accepted', 'bid-status-rejected');
                    // Add new status class
                    statusBadge.classList.add('bid-status-' + status);
                    // Update text
                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
                
                // Hide/update action buttons
                const actionButtons = bidCard.querySelector('.bid-actions');
                if (actionButtons) {
                    actionButtons.innerHTML = '<button class="bid-action-btn" style="background-color: #9ca3af; color: white;" disabled>' + 
                                              (status.charAt(0).toUpperCase() + status.slice(1)) + 
                                              '</button>';
                }
            }
            
            // Show success message AFTER updating UI
            alert('Bid ' + statusText + 'ed successfully!');
            
            // Don't reload - just refresh data without changing tabs
            // This will ensure the database has the update committed
        } else {
            alert('Error: ' + (data.message || 'Failed to update bid status'));
            console.error('Update failed:', data);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
        console.error('Fetch error:', error);
    });
}

// Always show Seller Center tab (My products) by default on page load
window.addEventListener('load', function() {
  // Check if there's a URL parameter to show a specific tab
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  
  openProducts();
  
  if (tab === 'bids') {
    showTab('bids');
  } else {
    showTab('my');
  }
  
  // Highlight Seller Center, remove highlight from Account Details
  var navAccount = document.getElementById('navAccount');
  var navSeller = document.getElementById('navSeller');
  if (navAccount) navAccount.classList.remove('font-semibold', 'text-gray-800');
  if (navSeller) navSeller.classList.add('font-semibold', 'text-gray-800');
});

</script>
</body>
</html>


