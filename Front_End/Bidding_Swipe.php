<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Database.php";

// Fetch products with bidding enabled
$products = [];
$db = new Database();
$conn = $db->threadly_connect;

// Check if bidding column exists
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'bidding'");
$hasBiddingColumn = ($colRes && $colRes->num_rows > 0);

if ($hasBiddingColumn) {
    // Fetch products where bidding is enabled
    $sql = "SELECT product_id, product_name, price, image_url, bidding 
            FROM products 
            WHERE bidding = 1 AND quantity > 0
            ORDER BY product_id DESC
            LIMIT 20";
} else {
    // Fallback: fetch recent products if bidding column doesn't exist yet
    $sql = "SELECT product_id, product_name, price, image_url 
            FROM products 
            WHERE quantity > 0
            ORDER BY product_id DESC
            LIMIT 20";
}

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['product_id'],
            'name' => $row['product_name'],
            'price' => $row['price'],
            'image' => $row['image_url'] ? 'uploads/' . $row['image_url'] : 'Images/jacket_hoodie.png',
            'top' => false  // Can be customized based on business logic
        ];
    }
}

// If no products with bidding enabled, show a fallback message or placeholder
if (empty($products)) {
    $products = [
        ['id' => 0, 'name' => 'No Bidding Products', 'price' => 0, 'image' => 'Images/jacket_hoodie.png', 'top' => false],
    ];
}
?>

<div class="bidding-swiper-container my-8">
  <div class="swiper biddingSwiper">
    <div class="swiper-wrapper">
      <?php foreach($products as $prod): ?>
        <div class="swiper-slide">
          <div class="card-hover group">
            <a href="product_info.php?id=<?= htmlspecialchars($prod['id']) ?>" class="relative bg-white rounded-xl overflow-hidden border border-gray-100 block hover:shadow-md transition">
              <div class="aspect-square bg-gray-50">
                <img src="<?= htmlspecialchars($prod['image']) ?>" 
                     alt="<?= htmlspecialchars($prod['name']) ?>" 
                     class="w-full h-full object-contain drop-shadow-sm">
                
                <div class="absolute top-3 left-3 px-3 py-1 bg-amber-600 text-white text-xs font-bold rounded-full">
                  BIDDING
                </div>
              </div>

              <div class="p-4">
                <h3 class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($prod['name']) ?></h3>
                <p class="text-lg font-bold text-amber-600 mt-1">₱<?= number_format($prod['price'], 2) ?></p>
                <p class="text-xs text-gray-500 mt-2">Click to place bid</p>
              </div>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Nav arrows-->
    <div class="swiper-button-next !text-blue-600 !w-10 !h-10"></div>
    <div class="swiper-button-prev !text-blue-600 !w-10 !h-10"></div>
  </div>
</div>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.bidding-swiper-container {
  padding: 0 40px 0 40px;
  max-width: 1400px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 0;
}

.biddingSwiper .swiper-slide {
  width: 260px !important;
  height: auto;
}

.biddingSwiper .swiper-wrapper {
  align-items: flex-start; 
}

.card-hover {
  transition: transform 0.25s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
}


.biddingSwiper .swiper-button-next,
.biddingSwiper .swiper-button-prev {
  background: white;
  border-radius: 50%;
  box-shadow: 0 2px 10px rgba(0,0,0,0.15);
  width: 44px !important;
  height: 44px !important;
  margin-top: -22px;
}

.biddingSwiper .swiper-button-next:after,
.biddingSwiper .swiper-button-prev:after {
  font-size: 18px;
  font-weight: bold;
  color: #FBBF24; 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.biddingSwiper', {
    slidesPerView: 2,
    spaceBetween: 20,
    loop: false,
    grabCursor: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      640: { slidesPerView: 3 },
      768: { slidesPerView: 4 },
      1024: { slidesPerView: 5 },
    }
  });
});
</script>