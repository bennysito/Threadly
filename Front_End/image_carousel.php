
<div class="swiper mySwiper">
  <div class="swiper-wrapper">
    <!--di pa ni final-->
    
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>

    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
    <div class="swiper-slide">
      <img src="Images/Jacket.jpg" alt="Character" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
  </div>

  
  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
</div>

<!-- cdn ni siya  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />


<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<style>
 
  .swiper-slide {
    width: 150px !important;   
    height: 200px !important;  
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    overflow: hidden;
    background: #e0e0e0;
  }

  .swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
  }
</style>

<script>
  const totalSlides = 10; 
const swiper = new Swiper(".mySwiper", {
  effect: "coverflow",
  grabCursor: true,
  centeredSlides: true,
  slidesPerView: 'auto',
  loop: false,
  
  initialSlide: Math.floor(totalSlides / 2),
  spaceBetween: 20,
  coverflowEffect: {
    rotate: 50,
    stretch: 20,
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
