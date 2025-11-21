<?php
$products = [
    ['name' => 'Red Jacket', 'image' => 'Images/jacket_hoodie.png', 'top' => true, 'price' => 1200],
    ['name' => 'Blue Hoodie', 'image' => 'Images/jacket_hoodie.png', 'top' => true, 'price' => 950],
    ['name' => 'White Sneakers', 'image' => 'Images/jacket_hoodie.png', 'top' => true, 'price' => 1800],
];

?>
<!--static sa karon kay wapay seller database-->
<div class="swiper biddingSwiper">
  <div class="swiper-wrapper">
    <?php foreach($products as $prod): ?>
      <div class="swiper-slide flex flex-col items-start">
       
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 w-64">
         
          <div class="relative w-full h-40 bg-gray-100 rounded">
            <img src="<?= $prod['image'] ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="w-full h-full object-contain">

            <?php if($prod['top']): ?>
            <div class="absolute top-2 left-2 px-3 py-1 bg-black text-white text-xs font-bold rounded">
              TOP
            </div>
            <?php endif; ?>

            
            <div class="absolute inset-0 bg-black bg-opacity-0 flex items-center justify-center hover:bg-opacity-20 transition-opacity rounded">
              <span class="text-white text-sm font-semibold opacity-0 hover:opacity-100"><?= htmlspecialchars($prod['name']) ?></span>
            </div>
          </div>

          
          <div class="mt-3">
            <div class="text-sm text-gray-700"><?= htmlspecialchars($prod['name'] ?: 'Text') ?></div>
            <div class="mt-1 text-lg font-bold text-gray-900">₱<?= number_format($prod['price'] ?? 0, 2) ?></div>
          
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  
  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<style>
.biddingSwiper{padding-left:0;padding-right:0}
.biddingSwiper .swiper-wrapper{padding-right:0}
.biddingSwiper .swiper-slide {
  width: 16rem; 
  display: flex;
  justify-content: center;
  align-items: flex-start;
  background-color: transparent;
  box-sizing: border-box;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const swiper = new Swiper(".biddingSwiper", {
    slidesPerView: 'auto',
    spaceBetween: 20,
    loop: false,
    grabCursor: true,
    autoHeight: false, 
    centeredSlides: false,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
});
</script>
