<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$query = $_GET['q'] ?? '';
// Capture the sort parameter, default to 'relevance'
$sort = $_GET['sort'] ?? 'relevance'; 
$products = [];
$wishlistProductIds = []; // 1. Initialize empty array for wishlist IDs

// =========================================================================
// 1. PHP LOGIC TO FETCH WISHLIST IDs
// =========================================================================
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    // Adjust path as needed. Assuming Database.php is in Back_End/Models/
    $dbPath = __DIR__ . '/../Back_End/Models/Database.php';
    
    if (file_exists($dbPath)) {
        require_once $dbPath;
        try {
            $db = new Database();
            $conn = $db->threadly_connect;
            
            $stmt = $conn->prepare("
                SELECT wi.product_id 
                FROM wishlist w
                JOIN wishlist_item wi ON w.wishlist_id = wi.wishlist_id
                WHERE w.user_id = ?
            ");
            
            if ($stmt) {
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $wishlistProductIds[] = (int) $row['product_id'];
                }
                $stmt->close();
            }
            $db->close_db();
        } catch (Exception $e) {
            // Handle DB error silently or log it
            error_log("Wishlist fetch error: " . $e->getMessage());
        }
    }
}
// =========================================================================


if ($query !== '') {
    // NOTE: This path assumes search_results.php is in the root and Search_db.php is in Back_End/Models/
    require_once __DIR__ . '/../Back_End/Models/Search_db.php';
    try {
        $search = new Search();
        // Pass the sort parameter to the search method (needs implementation in Search_db.php)
        $products = $search->search($query, 50, $sort); 
    } catch (Exception $e) {
        // Display any database or execution errors
        echo "<div class='p-4 bg-red-100 text-red-800 mb-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Function to display the current sort option name
function getSortDisplayName($current_sort) {
    switch ($current_sort) {
        case 'price_asc':
            return 'Price, low to high';
        case 'price_desc':
            return 'Price, high to low';
        case 'relevance':
        default:
            return 'Relevance';
    }
}

// Function to generate the URL for a new sort option
function getSortUrl($query, $new_sort) {
    return 'search_results.php?q=' . urlencode($query) . '&sort=' . urlencode($new_sort);
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
<style>
    /* Styling to match the bold, distressed look of the SEARCH RESULTS text */
    .chewy-font {
        font-family: 'Chewy', cursive;
        letter-spacing: 0.05em;
    }
    /* Simple utility to show the dropdown on hover/focus */
    .dropdown-container:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
    }
    /* 4. Add the CSS for the active heart icon */
    .heart.active {
        fill: #ef4444 !important; /* Tailwind's red-500 */
        color: #ef4444 !important;
    }
</style>
</head>
<body class="bg-gray-50">
<?php 
    include 'nav_bar.php'; // This includes the main navigation and search bar 
    // Ensure wishlist_panel.php is included BEFORE the content for the global JS function
    include 'wishlist_panel.php'; 
?>

<main class="max-w-7xl mx-auto p-6">
  
    <?php if ($query === ''): ?>
        <p class="text-center text-gray-600">Please enter a search term.</p>
    <?php else: ?>
        <div class="flex justify-between items-center mb-6 pt-2 border-t">
            <div class="flex items-center space-x-4">
                <div class="flex items-center text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v5.586a1 1 0 01-1.54.843l-3.21-1.605A1 1 0 018 18.586V14a1 1 0 00-.293-.707L3.293 6.707A1 1 0 013 6V4z"></path></svg>
                    <span class="font-medium">Filter</span>
                </div>
                
                <div class="relative dropdown-container">
                    <button class="flex items-center text-gray-700 hover:text-gray-900 transition focus:outline-none">
                        Sort By: 
                        <span class="font-bold ml-1 capitalize"><?= getSortDisplayName($sort) ?></span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg opacity-0 invisible transition duration-150 z-10">
                        <a href="<?= getSortUrl($query, 'relevance') ?>" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= ($sort === 'relevance' ? 'font-bold bg-gray-50' : '') ?>">
                            Relevance
                        </a>
                        <a href="<?= getSortUrl($query, 'price_asc') ?>" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= ($sort === 'price_asc' ? 'font-bold bg-gray-50' : '') ?>">
                            Price, low to high
                        </a>
                        <a href="<?= getSortUrl($query, 'price_desc') ?>" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= ($sort === 'price_desc' ? 'font-bold bg-gray-50' : '') ?>">
                            Price, high to low
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600"><?= count($products) ?> products</span>
                <button class="p-1 text-gray-400 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                </button>
                <button class="p-1 text-gray-700">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM13 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM13 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z"></path></svg>
                </button>
            </div>
        </div>
        <?php if (empty($products)): ?>
            <div class="p-8 bg-white rounded border text-center text-gray-600">No products found matching "<?= htmlspecialchars($query) ?>".</div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                <?php foreach ($products as $p):
                    $id = $p['id'] ?? $p['product_id'] ?? 0; // Use product_id if 'id' isn't set
                    $name = $p['name'] ?? 'Product';
                    $image = $p['image'] ?? 'panti.png';
                    $price = isset($p['price']) ? number_format((float)$p['price'], 2) : '0.00';
                    $category = $p['category'] ?? '';
                    $availability = $p['availability'] ?? '';
                    $link = !empty($id) ? "product_info.php?id=$id" : "#";
                    
                    // Check if this specific product ID is in the user's wishlist
                    $isLiked = in_array((int)$id, $wishlistProductIds);
                ?>
                <a href="<?= $link ?>" class="block bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition group">
                    <div class="relative">
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            <img src="<?= htmlspecialchars($image) ?>" 
                                alt="<?= htmlspecialchars($name) ?>" 
                                class="w-full h-full object-cover"
                                onerror="this.src='Images/panti.png'">
                        </div>
                        
                        <button onclick="event.preventDefault(); event.stopPropagation(); window.toggleWishlist(<?= (int)$id ?>);" 
                            class="absolute top-2 right-2 p-1 bg-white rounded-full shadow-md transition">
                            <svg class="heart w-5 h-5 <?= $isLiked ? 'active' : '' ?>" 
                                 data-product-id="<?= (int)$id ?>" 
                                 fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 truncate"><?= htmlspecialchars($name) ?></h3>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Category: <?= htmlspecialchars($category) ?></span>
                            <span class="font-bold text-lg text-gray-900">₱<?= $price ?></span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($availability) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>