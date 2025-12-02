<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
require_once __DIR__ . "/../Back_End/Models/Categories.php";

$categoryObj = new Category();
$categories = $categoryObj->getAllCategories();
?>

    <?php require "wishlist_panel.php"; ?>

<div class="category-swiper-wrapper">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <div class="swiper categorySwiper">
    <div class="swiper-wrapper">
      <?php foreach($categories as $cat): ?>
        <div class="swiper-slide">
          <a class="category-item" href="category_products.php?category=<?= urlencode($cat['name']); ?>">
            <div class="category-thumb">
              <img src="<?= htmlspecialchars($cat['image']); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
            </div>
            <div class="category-label"><?= htmlspecialchars($cat['name']); ?></div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-pagination"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <style>
   
    .category-swiper-wrapper { width: 100%; padding: 22px 10px; box-sizing: border-box; }

   
    .swiper { overflow: visible; }
    .swiper-wrapper { align-items: flex-start; }
    .swiper-slide { width: 160px !important; height: auto; display: flex; justify-content: center; }

    .category-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: inherit; }

    
    .category-thumb {
      width: 140px;
      height: 170px;            
      border-radius: 28px;      
      overflow: hidden;
      background: #ffffff;      
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      border: none;
      object-fit: contain;
    }

    .category-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
    .category-item:hover .category-thumb img { transform: scale(1.05); }

    .category-label { margin-top: 10px; font-size: 14px; color: #111827; text-align: center; }

    
   
    .categorySwiper .swiper-button-prev,
    .categorySwiper .swiper-button-next {
      background: white;
      border-radius: 50%;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
      width: 44px !important;
      height: 44px !important;
      margin-top: -22px;
    }

    .categorySwiper .swiper-button-prev:after,
    .categorySwiper .swiper-button-next:after {
      font-size: 18px;
      font-weight: bold;
      color: #FBBF24; 
    }

    
    @media (min-width: 1200px) {
      .swiper-slide { width: 180px !important; }
      .category-thumb { width: 150px; height: 190px; border-radius: 34px; }
      .category-label { font-size: 15px; }
    }

    @media (max-width: 640px) {
      .swiper-slide { width: 130px !important; }
      .category-thumb { width: 120px; height: 150px; border-radius: 24px; }
      .category-label { font-size: 13px; }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      new Swiper('.categorySwiper', {
        slidesPerView: 5,
        spaceBetween: 18,
        centeredSlides: false,
        loop: false,
        grabCursor: true,
        grid: { rows: 2, fill: 'row' },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: {
          320: { slidesPerView: 2, spaceBetween: 8, grid: { rows: 2 } },
          640: { slidesPerView: 3, spaceBetween: 12, grid: { rows: 2 } },
          1024: { slidesPerView: 5, spaceBetween: 18, grid: { rows: 2 } }
        },
        keyboard: { enabled: true },
      });
    });
  </script>
</div>
