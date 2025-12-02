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
            <div class="category-thumb carousel-container" data-category="<?= htmlspecialchars($cat['name']); ?>">
              <?php if(strtolower($cat['name']) === 'new in'): ?>
                <!-- Image carousel for "New In" category -->
                <div class="image-carousel">
                  <img src="Images/ac-tivewear.jpg" class="carousel-image active">
                  <img src="Images/dresx.jpg"  class="carousel-image">
                  <img src="Images/pznts.jpg"  class="carousel-image">
                  <img src="Images/wats.jpg"  class="carousel-image">
                  <img src="Images/pol-o.jpg" class="carousel-image">
                  
                  <!-- Carousel dots -->
                  <div class="carousel-dots">
                    <div class="carousel-dot active"></div>
                    <div class="carousel-dot"></div>
                    <div class="carousel-dot"></div>
                    <div class="carousel-dot"></div>
                    <div class="carousel-dot"></div>
                  </div>
                </div>
              <?php else: ?>
                <!-- Regular single image for other categories -->
                <img src="<?= htmlspecialchars($cat['image']); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
              <?php endif; ?>
            </div>
            <div class="category-label"><?= htmlspecialchars($cat['name']); ?></div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Remove navigation buttons -->
    <div class="swiper-pagination"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <style>
    .category-swiper-wrapper { 
      width: 100%; 
      padding: 22px 10px; 
      box-sizing: border-box; 
    }

    .swiper { 
      overflow: hidden; /* Changed from visible to hidden to prevent overflow */
    }
    .swiper-wrapper { align-items: flex-start; }
    .swiper-slide { 
      width: auto !important;
      flex: 1 1 auto; /* Allow equal distribution */
      max-width: 180px; /* Prevent slides from getting too wide */
      height: auto; 
      display: flex; 
      justify-content: center; 
    }

    .category-item { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      text-decoration: none; 
      color: inherit; 
    }

    /* Carousel container */
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
      position: relative;
    }

    /* Image carousel styles */
    .image-carousel {
      width: 100%;
      height: 100%;
      position: relative;
    }

    .carousel-image {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 600ms ease;
      -webkit-transition: opacity 600ms ease;
    }

    .carousel-image.active {
      opacity: 1;
    }

    /* Regular single image for non-carousel categories */
    .category-thumb > img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }

    /* Carousel dots */
    .carousel-dots {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      bottom: 8px;
      display: flex;
      gap: 6px;
      z-index: 5;
    }

    .carousel-dot {
      width: 6px;
      height: 6px;
      border-radius: 999px;
      background: rgba(255,255,255,0.6);
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
      opacity: 0.9;
      transition: all 0.3s ease;
    }

    .carousel-dot.active {
      transform: scale(1.3);
      background: rgba(255,255,255,0.95);
    }

    .category-item:hover .carousel-image { transform: scale(1.05); }
    .category-item:hover .category-thumb > img { transform: scale(1.05); }

    .category-label { 
      margin-top: 10px; 
      font-size: 14px; 
      color: #111827; 
      text-align: center; 
    }

    /* Hide navigation buttons */
    .categorySwiper .swiper-button-prev,
    .categorySwiper .swiper-button-next {
      display: none !important;
    }

    /* Pagination dots styling */
    .swiper-pagination {
      position: relative !important;
      margin-top: 20px;
    }

    .swiper-pagination-bullet {
      background: #9CA3AF;
      opacity: 0.5;
    }

    .swiper-pagination-bullet-active {
      background: #FBBF24;
      opacity: 1;
    }

    /* Responsive breakpoints */
    @media (min-width: 1200px) {
      .category-thumb { width: 150px; height: 190px; border-radius: 34px; }
      .category-label { font-size: 15px; }
      .carousel-dot { width: 7px; height: 7px; }
    }

    @media (max-width: 640px) {
      .category-thumb { width: 120px; height: 150px; border-radius: 24px; }
      .category-label { font-size: 13px; }
      .carousel-dot { width: 5px; height: 5px; }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Calculate how many categories to show per view based on total
      const totalCategories = <?= count($categories); ?>;
      const categoriesPerRow = Math.ceil(totalCategories / 2);

      // Initialize Swiper
      new Swiper('.categorySwiper', {
        slidesPerView: categoriesPerRow,
        spaceBetween: 18,
        centeredSlides: false,
        loop: false,
        grabCursor: true,
        grid: { rows: 2, fill: 'row' },
        pagination: { 
          el: '.swiper-pagination', 
          clickable: true 
        },
        breakpoints: {
          320: { 
            slidesPerView: 2, 
            spaceBetween: 8, 
            grid: { rows: 2 } 
          },
          640: { 
            slidesPerView: 3, 
            spaceBetween: 12, 
            grid: { rows: 2 } 
          },
          768: {
            slidesPerView: Math.min(4, categoriesPerRow),
            spaceBetween: 15,
            grid: { rows: 2 }
          },
          1024: { 
            slidesPerView: Math.min(5, categoriesPerRow), 
            spaceBetween: 18, 
            grid: { rows: 2 } 
          },
          1280: {
            slidesPerView: categoriesPerRow,
            spaceBetween: 18,
            grid: { rows: 2 }
          }
        },
        keyboard: { enabled: true },
      });

      // Initialize image carousels for each category
      const SWITCH_MS = 1000; // 1 second per image
      const carouselContainers = document.querySelectorAll('.image-carousel');

      carouselContainers.forEach(container => {
        const images = Array.from(container.querySelectorAll('.carousel-image'));
        const dots = Array.from(container.querySelectorAll('.carousel-dot'));

        // Only initialize carousel if there are multiple images
        if (images.length <= 1) return;

        let currentIdx = 0;
        let intervalId;

        function nextImage() {
          images[currentIdx].classList.remove('active');
          if (dots[currentIdx]) dots[currentIdx].classList.remove('active');

          currentIdx = (currentIdx + 1) % images.length;

          images[currentIdx].classList.add('active');
          if (dots[currentIdx]) dots[currentIdx].classList.add('active');
        }

        function startCarousel() {
          intervalId = setInterval(nextImage, SWITCH_MS);
        }

        function stopCarousel() {
          clearInterval(intervalId);
        }

        // Start the carousel
        startCarousel();

        // Pause on hover
        container.closest('.category-thumb').addEventListener('mouseenter', stopCarousel);
        container.closest('.category-thumb').addEventListener('mouseleave', startCarousel);

        // Preload images
        images.forEach(img => {
          const preload = new Image();
          preload.src = img.src;
        });
      });
    });
  </script>
</div>