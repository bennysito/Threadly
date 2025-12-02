<?php
// product_info.php - MODIFIED WITH DEBUGGING
if (session_status() === PHP_SESSION_NONE) session_start();

// Ensure these models exist and are required
require_once __DIR__ . "/../Back_End/Models/Database.php";
// require_once __DIR__ . "/../Back_End/Models/User.php"; // Required if you have a User model

// Assuming product data is loaded based on URL parameter 'id'
$product_id = intval($_GET['id'] ?? 0);

// --- START: PRODUCT DATA LOADING EXAMPLE ---
$product_seller_id = null;
$product_data = null; 
$product_name = "Product Loading..."; // Default name

if ($product_id > 0) {
    $db = new Database();
    $conn = $db->threadly_connect;
    $sql = "SELECT * FROM products WHERE product_id = ?"; 
    $stmt = $conn->prepare($sql);
    
    // Check for product query failure
    if (!$stmt) {
        echo "<p class='bg-red-200 p-2'>⚠️ DEBUG: Product Query Prepare Failed: " . $conn->error . "</p>";
    }
    
    if ($stmt) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_data = $result->fetch_assoc();
        $stmt->close();
        $db->close_db();
    }
}

if ($product_data) {
    // ⭐ MOST CRITICAL PART: VERIFY THIS COLUMN NAME ⭐
    $product_seller_id = $product_data['seller_id'] ?? null; 
    $product_name = htmlspecialchars($product_data['product_name']);
} else {
    // Fallback/Testing block
    if ($product_id > 0) {
        echo "<p class='bg-yellow-200 p-2'>⚠️ DEBUG: Product ID {$product_id} not found in DB or failed to load.</p>";
    }
    
    // Temporarily using a dummy ID for testing, remove this line later
    $product_seller_id = 101; 
    $product_name = "Sample Product (Test Mode)";
}
// --- END: PRODUCT DATA LOADING EXAMPLE ---


// --------------------------------------------------------------------------------------------------
// ⭐ NEW FUNCTION: Renders the Seller Profile Card (With Debugging) ⭐
// --------------------------------------------------------------------------------------------------

function render_seller_profile_card($seller_id) {
    if (!$seller_id) {
        echo "<p class='bg-red-200 p-2'>⚠️ DEBUG: render_seller_profile_card failed. Seller ID is NULL or 0.</p>";
        return;
    }
    
    echo "<p class='bg-green-200 p-2'>✅ DEBUG: Attempting to fetch data for Seller ID: {$seller_id}</p>";

    $db = new Database();
    $conn = $db->threadly_connect;
    
    // ⭐ CRITICAL QUERY: CHECK YOUR USERS TABLE SCHEMA ⭐
    // Verify your users table is named 'users', the ID column is 'user_id', 
    // and the name is 'username'.
    $sql = "SELECT username, profile_picture FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // Handle database prepare error (e.g., table 'users' doesn't exist)
        echo "<p class='bg-red-200 p-2'>❌ DEBUG: Seller Profile Query PREPARE failed. Check if table 'users' exists: " . $conn->error . "</p>";
        return;
    }
    
    $stmt->bind_param('i', $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();
    $stmt->close();
    $db->close_db();

    if ($seller) {
        echo "<p class='bg-green-200 p-2'>✅ DEBUG: Seller data found for {$seller['username']}. Rendering card.</p>";
        $username = htmlspecialchars($seller['username'] ?? 'Anonymous Seller');
        $profilePic = htmlspecialchars($seller['profile_picture'] ?? 'default_profile.png');
        $dashboardLink = "seller_dashboard.php?view_user=" . $seller_id; 

        // --- Start Card HTML Output ---
        echo "
        <div class='seller-profile-card bg-white p-6 shadow-lg rounded-lg border border-gray-100 mt-8'>
            <h3 class='text-xl font-semibold mb-4 text-gray-700 border-b pb-2'>Sold By</h3>
            
            <div class='flex items-center space-x-4 mb-4'>
                
                <a href='{$dashboardLink}' class='flex-shrink-0 cursor-pointer'>
                    <img src='uploads/{$profilePic}' alt='{$username}' 
                         class='w-16 h-16 object-cover rounded-full border-2 border-amber-500'>
                </a>
                
                <div class='flex-1'>
                    <a href='{$dashboardLink}' class='text-2xl font-bold text-gray-900 hover:text-amber-600 transition-colors'>{$username}</a>
                    <p class='text-sm text-gray-500'>View this seller's products.</p> 
                </div>
            </div>
            
            <a href='{$dashboardLink}' class='inline-block w-full text-center bg-amber-500 text-white font-semibold py-3 rounded-lg 
                       hover:bg-amber-600 transition-colors duration-200 shadow-md'>
                View All {$username}'s Products
            </a>
        </div>
        ";
        // --- End Card HTML Output ---
    } else {
        echo "<p class='bg-red-200 p-2'>❌ DEBUG: Seller Profile Query EXECUTED but returned NO rows. Check if user ID {$seller_id} exists in 'users' table.</p>";
    }
}

// --------------------------------------------------------------------------------------------------
// --- START: Main HTML Structure of product_info.php ---
// --------------------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product: <?= $product_name ?></title>
    </head>
<body>
    <div class="container mx-auto p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-2">
            <h1 class="text-3xl font-bold mb-4"><?= $product_name ?></h1>
            <p class="mt-4 text-lg">Product Description...</p>
        </div>

        <div class="md:col-span-1">
            <section id="seller-details-section">
                <?php 
                render_seller_profile_card($product_seller_id); 
                ?>
            </section>
        </div>
    </div>
</body>
</html>