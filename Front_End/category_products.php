<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categoryName = $_GET['category'] ?? 'All Products';
$products = [];
$wishlistProductIds = [];

// Path to your real file
$searchFile = __DIR__ . '/../Back_End/Models/Search_db.php';

if (file_exists($searchFile)) {
    require_once $searchFile;

    if (class_exists('Search')) {
        try {
            $search = new Search();

            if ($categoryName === 'All Products' || $categoryName === '' || $categoryName === null) {
                $products = $search->getRecent(24);
            } else {
                $products = $search->getByCategory($categoryName, 24);
            }
        } catch (Throwable $e) {
            error_log("Search error: " . $e->getMessage());
        }
    }
}

// Get user's wishlist items if logged in
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../Back_End/Models/Database.php';
    $db = new Database();
    $conn = $db->threadly_connect;
    
    $stmt = $conn->prepare("
        SELECT DISTINCT wi.product_id 
        FROM wishlist w
        JOIN wishlist_item wi ON w.wishlist_id = wi.wishlist_id
        WHERE w.user_id = ?
    ");
    
    if ($stmt) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $wishlistProductIds[] = (int)$row['product_id'];
        }
        
        $stmt->close();
    }
    
    $db->close_db();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoryName) ?> - Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="CSS/category_products.css">
    
    <style>
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 2rem; 
            padding: 2rem 1rem; 
        }
        .card { 
            background: white; 
            border-radius: 16px; 
            overflow: hidden; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
        }
        .img-container { 
            position: relative; 
            overflow: hidden; 
            aspect-ratio: 1; 
        }
        .main-img, .hover-img { 
            width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;
            transition: opacity 0.4s ease;
        }
        .hover-img { opacity: 0; }
        .card:hover .hover-img { opacity: 1; }
        .card:hover .main-img { opacity: 0; }
        .info { padding: 1rem; }
        .price { font-weight: 700; font-size: 1.3rem; color: #111; }
        .name { margin-top: 0.5rem; color: #444; font-size: 0.95rem; 
                 display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .heart { width: 28px; height: 28px; stroke: #666; transition: all 0.2s; cursor: pointer; }
        .heart:hover { stroke: #ef4444; fill: #ef4444; }
        .heart.active { stroke: #ef4444; fill: #ef4444; }
    </style>
</head>
<body class="bg-white">

    <?php require "Nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?>

    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-10">
            <?= htmlspecialchars($categoryName) ?>
            <span class="text-lg font-normal text-gray-600">(<?= count($products) ?> items)</span>
        </h1>

        <?php if (empty($products)): ?>
            <div class="text-center py-20">
                <p class="text-2xl text-gray-600">No products found in this category yet.</p>
                <p class="text-gray-500 mt-4">Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $p): ?>
                    <a href="product_info.php?id=<?= (int)($p['id'] ?? 0) ?>" class="block">
                        <div class="card">
                            <div class="img-container">
                                <img src="<?= htmlspecialchars($p['image'] ?? 'placeholder.jpg') ?>" 
                                     alt="<?= htmlspecialchars($p['name'] ?? '') ?>" class="main-img">
                                <img src="<?= htmlspecialchars($p['hover_image'] ?? $p['image'] ?? 'placeholder.jpg') ?>" 
                                     alt="hover" class="hover-img">
                            </div>
                            <div class="info">
                                <div class="flex justify-between items-center">
                                    <div class="price">₱<?= number_format((float)($p['price'] ?? 0), 2) ?></div>
                                    <svg class="heart <?= in_array((int)($p['id'] ?? 0), $wishlistProductIds) ? 'active' : '' ?>" 
                                         data-product-id="<?= (int)($p['id'] ?? 0) ?>"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </div>
                                <p class="name"><?= htmlspecialchars($p['name'] ?? 'Product') ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

<script>
    const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        if(profileBtn) {
            profileBtn.addEventListener('click', () => {
                profileDropdown.classList.toggle('hidden');
            });
        }
document.querySelectorAll('.heart').forEach(heart => {
    heart.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = this.getAttribute('data-product-id');
        
        // Optimistically update UI
        this.classList.toggle('active');
        
        // Send request to server
        const formData = new FormData();
        formData.append('product_id', productId);
        
        fetch('toggle_wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert on error
                heart.classList.toggle('active');
                alert(data.message || 'Error updating wishlist');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert on error
            heart.classList.toggle('active');
        });
    });
});
</script>