<?php

$products = [
    ['name' => 'Red Jacket',          'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 1200],
    ['name' => 'Blue Hoodie',         'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 950],
    ['name' => 'White Sneakers',      'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 1800],
    ['name' => 'Black Cargo Pants',   'image' => 'Images/jacket_hoodie.png', 'top' => false, 'price' => 890],
    ['name' => 'Denim Overshirt',     'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 1499],
    ['name' => 'Leather Boots',       'image' => 'Images/jacket_hoodie.png', 'top' => false, 'price' => 2390],
    ['name' => 'Knit Beanie',         'image' => 'Images/jacket_hoodie.png', 'top' => false, 'price' => 390],
    ['name' => 'Puffer Vest',         'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 1690],
    ['name' => 'Flannel Shirt',       'image' => 'Images/jacket_hoodie.png', 'top' => false, 'price' => 799],
    ['name' => 'Wool Coat',           'image' => 'Images/jacket_hoodie.png', 'top' => true,  'price' => 3290],
    ['name' => 'Track Jacket',        'image' => 'Images/jacket_hoodie.png', 'top' => false, 'price' => 1190],
];
?>

<div class="bidding-swiper-container my-8">
  <div class="swiper biddingSwiper">
    <div class="swiper-wrapper">
      <?php foreach($products as $prod): ?>
        <div class="swiper-slide">
          <div class="card-hover group">
            <div class="relative bg-white rounded-xl overflow-hidden border border-gray-100">
              <div class="aspect-square bg-gray-50 p-8">
                <img src="<?= $prod['image'] ?>" 
                     alt="<?= htmlspecialchars($prod['name']) ?>" 
                     class="w-full h-full object-contain drop-shadow-sm">
                
                <?php if($prod['top']): ?>
                  <div class="absolute top-3 left-3 px-3 py-1 bg-black text-white text-xs font-bold rounded-full">
                    TOP
                  </div>
                <?php endif; ?>
              </div>

              <div class="p-4">
                <h3 class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($prod['name']) ?></h3>
                <p class="text-lg font-bold text-gray-900 mt-1">₱<?= number_format($prod['price']) ?></p>
              </div>
            </div>
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
  padding: 0 40px;
  max-width: 1400px;
  margin-left: auto;
  margin-right: auto;
}

.biddingSwiper .swiper-slide {
  width: 260px !important;
  height: auto;
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