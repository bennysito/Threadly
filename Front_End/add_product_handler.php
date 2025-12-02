<?php
// add_product_handler.php
// Handles product form POST, uploads image, inserts into DB, redirects with flash message

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";
require_once __DIR__ . "/../Back_End/Models/Categories.php";

// Ensure logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "You must be logged in to add a product.";
    header("Location: login.php");
    exit;
}

// Only process POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: seller_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->threadly_connect;

// Collect form data
$pname = trim($_POST['product_name'] ?? '');
$pcategory = trim($_POST['category'] ?? '');
$pdescription = trim($_POST['description'] ?? '');
$pquantity = intval($_POST['quantity'] ?? 0);
$pprice = floatval($_POST['price'] ?? 0);

// Validate inputs
if ($pname === '' || $pdescription === '' || $pquantity < 0 || $pprice <= 0) {
    $_SESSION['flash_error'] = "Please fill in all product fields with valid values.";
    header("Location: seller_dashboard.php");
    exit;
}

// Validate image upload
if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash_error'] = "Please provide a valid product image.";
    header("Location: seller_dashboard.php");
    exit;
}

// Save uploaded image
$uploadsDir = __DIR__ . '/uploads/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['picture']['name']));
$filename = time() . '_' . $safeName;
$target = $uploadsDir . $filename;

if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
    $_SESSION['flash_error'] = "Failed to save uploaded image.";
    header("Location: seller_dashboard.php");
    exit;
}

// Detect products table columns dynamically
$existingCols = [];
$resCols = $conn->query("SHOW COLUMNS FROM products");
if ($resCols) {
    while ($r = $resCols->fetch_assoc()) {
        $existingCols[] = $r['Field'];
    }
}

$pick = function(array $candidates) use ($existingCols) {
    foreach ($candidates as $c) {
        if (in_array($c, $existingCols)) {
            return $c;
        }
    }
    return null;
};

// Detect column names
$nameCol = $pick(['product_name', 'name', 'title']);
$descCol = $pick(['description', 'product_description', 'details']);
$priceCol = $pick(['price', 'product_price', 'amount']);
$categoryCol = $pick(['category', 'category_id', 'product_category']);
$quantityCol = $pick(['quantity', 'stock', 'availability']);
$imageCol = $pick(['image', 'product_image', 'image_url', 'img']);
$sellerCol = $pick(['seller_id', 'user_id', 'owner_id']);

// Build dynamic INSERT
$cols = [];
$params = [];
$types = '';

if ($nameCol) {
    $cols[] = "`$nameCol`";
    $params[] = $pname;
    $types .= 's';
}
if ($descCol) {
    $cols[] = "`$descCol`";
    $params[] = $pdescription;
    $types .= 's';
}
if ($priceCol) {
    $cols[] = "`$priceCol`";
    $params[] = $pprice;
    $types .= 'd';
}

// Handle category mapping (ID vs name)
if ($categoryCol && $pcategory !== '') {
    $categoryModel = new Category();
    $categories = $categoryModel->getAllCategories();
    
    $expectsId = preg_match('/\b(id|_id)$/i', $categoryCol) || stripos($categoryCol, 'category_id') !== false;
    $catId = null;
    $catName = null;

    // Try to parse as numeric ID first
    if (is_numeric($pcategory)) {
        $catId = intval($pcategory);
    }

    // If not numeric, try to match by name
    if ($catId === null && $pcategory !== '') {
        foreach ($categories as $c) {
            $nameCandidate = $c['name'] ?? $c['category'] ?? null;
            if ($nameCandidate !== null && strcasecmp($nameCandidate, $pcategory) === 0) {
                $catId = $c['id'] ?? $c['category_id'] ?? null;
                $catName = $nameCandidate;
                break;
            }
        }
    }

    // Validate the category ID exists
    if ($catId !== null) {
        $validCat = false;
        foreach ($categories as $c) {
            $cid = $c['id'] ?? $c['category_id'] ?? null;
            if ($cid !== null && intval($cid) === intval($catId)) {
                $validCat = true;
                break;
            }
        }

        if (!$validCat) {
            $_SESSION['flash_error'] = "Invalid category selected.";
            header("Location: seller_dashboard.php");
            exit;
        }

        // Add to insert
        if ($expectsId) {
            $cols[] = "`$categoryCol`";
            $params[] = $catId;
            $types .= 'i';
        } else {
            // Fetch name if not already set
            if ($catName === null) {
                foreach ($categories as $c) {
                    $cid = $c['id'] ?? $c['category_id'] ?? null;
                    if ($cid !== null && intval($cid) === intval($catId)) {
                        $catName = $c['name'] ?? $c['category'] ?? null;
                        break;
                    }
                }
            }
            if ($catName !== null) {
                $cols[] = "`$categoryCol`";
                $params[] = $catName;
                $types .= 's';
            }
        }
    }
}

if ($quantityCol) {
    $cols[] = "`$quantityCol`";
    $params[] = $pquantity;
    $types .= 'i';
}
if ($imageCol) {
    $cols[] = "`$imageCol`";
    $params[] = $filename;
    $types .= 's';
}
if ($sellerCol) {
    $cols[] = "`$sellerCol`";
    $params[] = $user_id;
    $types .= 'i';
}

// Validate we have columns to insert
if (empty($cols)) {
    $_SESSION['flash_error'] = "Products table does not contain expected columns.";
    header("Location: seller_dashboard.php");
    exit;
}

// Prepare and execute INSERT
$placeholders = rtrim(str_repeat('?,', count($cols)), ',');
$sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES ($placeholders)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['flash_error'] = "Database prepare failed: " . $conn->error;
    header("Location: seller_dashboard.php");
    exit;
}

// Bind parameters
$bind_names = [$types];
for ($i = 0; $i < count($params); $i++) {
    $bind_names[] = &$params[$i];
}

// Helper to bind by reference
$refValues = function($arr) {
    $refs = [];
    foreach ($arr as $k => $v) {
        $refs[$k] = &$arr[$k];
    }
    return $refs;
};

call_user_func_array([$stmt, 'bind_param'], $refValues($bind_names));

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Product added successfully!";
    $stmt->close();
    $db->close_db();
    header("Location: seller_dashboard.php");
    exit;
} else {
    $_SESSION['flash_error'] = "Failed to insert product: " . $stmt->error;
    $stmt->close();
    $db->close_db();
    header("Location: seller_dashboard.php");
    exit;
}

?>
