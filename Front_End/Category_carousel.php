<?php
require_once __DIR__ . "/../Back_End/Models/Categories.php";

$categoryObj = new Category();
$categories = $categoryObj->getAllCategories();
$totalSlides = count($categories);
?>

<div class="swiper categorySwiper">
  <div class="swiper-wrapper">
    <?php foreach($categories as $cat): ?>
      <div class="swiper-slide">
        <a href="category_products.php?category=<?= urlencode($cat['name']); ?>">
          <div class="relative w-full h-full">
  <img src="<?= $cat['image']; ?>" 
       alt="<?= htmlspecialchars($cat['name']); ?>" 
       class="w-full h-full object-cover rounded-lg shadow-lg">
  <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity rounded-lg">
    <span class="text-white text-lg font-semibold"><?= htmlspecialchars($cat['name']); ?></span>
  </div>
</div>

        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<style>
.categorySwiper{padding-left:0;padding-right:0}
.categorySwiper .swiper-wrapper{padding-right:0}
.categorySwiper .swiper-slide {
  width: 190px !important;
  height: 200px !important;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 10px;
  overflow: hidden;
  background: #e0e0e0;
  box-sizing: border-box;
}
.categorySwiper .swiper-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}
</style>

<script>
const categorySwiper = new Swiper(".categorySwiper", {
  effect: "coverflow",
  grabCursor: true, 
  centeredSlides: true,
  slidesPerView: 'auto',   
  loop: true,               
  spaceBetween: 10,         
  coverflowEffect: {
    rotate: 10,
    stretch: 0,
    depth: 200,
    modifier: 1,
    slideShadows: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});
</script>
