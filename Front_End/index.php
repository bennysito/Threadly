<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: white;
        }
        /* Style rule for the main headline font (labeled chewy in the link, but Oswald in the CSS) */
        .font-chewy { 
            font-family: 'Oswald', sans-serif;
        }

        /* Logo scale */
        .logo-scale {
            display: inline-block;
            transform: scale(1.5);
            transform-origin: left;
            margin-left: -30px;
            will-change: transform;
        }

        /* Seamless swipable carousel */
        #hero-carousel {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            touch-action: pan-y;
        }
        .carousel-slide {
            min-width: 100%;
            flex-shrink: 0;
        }

        /* Grok-style black arrows below - hover amber */
        .arrow-container {
            position: relative;
            z-index: 10;
        }
        .grok-arrow {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin: 0 8px;
            cursor: pointer;
            border: 1px solid #e5e7eb;
        }
        .grok-arrow svg {
            width: 24px;
            height: 24px;
            color: black;
            transition: color 0.3s ease;
        }
        .grok-arrow:hover {
            background-color: #f59e0b;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: scale(1.1);
            border-color: #f59e0b;
        }
        .grok-arrow:hover svg {
            color: white;
        }

        /* Responsive text sizing */
        .hero-headline { 
            font-size: clamp(4rem, 10vw, 6rem); 
            line-height: 0.9; 
        }
        .hero-caption { 
            font-size: clamp(1.125rem, 3vw, 1.5rem); 
        }
    </style>
</head>

<body class="bg-white">

    <?php require "nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?> 

    <div class="relative w-full overflow-hidden mb-8">
        <div id="hero-carousel" class="cursor-grab active:cursor-grabbing">

            <div class="carousel-slide bg-white relative">
                <div class="container mx-auto flex items-center justify-between p-4 sm:p-8">
                    <div class="w-1/2 pr-4 lg:pr-12">
                        <p class="text-lg sm:text-xl font-medium mb-4">
                            The Platform Where Pre-Loved Clothing Offers Quality, Affordability, and **Sustainability**.
                        </p>
                        <h1 class="hero-headline font-chewy font-extrabold text-black">
                            Fashion For <br> Every Occasion
                        </h1>
                        <a href="#" class="mt-6 inline-block bg-black hover:bg-gray-800 text-white text-xl font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                            Shop All Styles
                        </a>
                    </div>
                    <div class="w-1/2 flex justify-end">
                        <img src="Images/Lady1.png" alt="Stylish woman posing with clothing" class="max-w-full h-auto object-cover max-h-96">
                    </div>
                </div>
            </div>

            <div class="carousel-slide bg-amber-50 relative">
                <div class="container mx-auto flex items-center justify-between p-4 sm:p-8">
                    <div class="w-1/2 pr-4 lg:pr-12">
                        <p class="text-lg sm:text-xl font-medium mb-4 text-amber-900">
                            Premium knits, jackets & boots all pre-loved and perfect for the **cold season**.
                        </p>
                        <h1 class="hero-headline font-chewy font-extrabold text-amber-900">
                            Winter <br> Cozy Vibes
                        </h1>
                        <a href="#" class="mt-6 inline-block bg-amber-500 hover:bg-amber-600 text-white text-xl font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                            Find Winter Gear
                        </a>
                    </div>
                    <div class="w-1/2 flex justify-end">
                        <img src="Images/man3.png" alt="Man wearing winter apparel" class="max-w-full h-auto object-cover max-h-96">
                    </div>
                </div>
            </div>

            <div class="carousel-slide bg-teal-50 relative">
                <div class="container mx-auto flex items-center justify-between p-4 sm:p-8">
                    <div class="w-1/2 pr-4 lg:pr-12">
                        <p class="text-lg sm:text-xl font-medium mb-4 text-teal-800">
                            Flowy dresses, light tops & sandals **fresh finds** for sunny days ahead.
                        </p>
                        <h1 class="hero-headline font-chewy font-extrabold text-teal-900">
                            Ready for <br> Summer Days
                        </h1>
                        <a href="#" class="mt-6 inline-block bg-teal-600 hover:bg-teal-700 text-white text-xl font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                            Shop Summer Wear
                        </a>
                    </div>
                    <div class="w-1/2 flex justify-end">
                        <img src="Images/Lady1.png" alt="Light summer clothing display" class="max-w-full h-auto object-cover max-h-96">
                    </div>
                </div>
            </div>

            <div class="carousel-slide bg-purple-50 relative">
                <div class="container mx-auto flex items-center justify-between p-4 sm:p-8">
                    <div class="w-1/2 pr-4 lg:pr-12">
                        <p class="text-lg sm:text-xl font-medium mb-4 text-purple-800">
                            Bags, jewelry & shoes that instantly **elevate** any pre-loved outfit.
                        </p>
                        <h1 class="hero-headline font-chewy font-extrabold text-purple-900">
                            The Perfect <br> Accessories
                        </h1>
                        <a href="#" class="mt-6 inline-block bg-purple-600 hover:bg-purple-700 text-white text-xl font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                            Discover Accessories
                        </a>
                    </div>
                    <div class="w-1/2 flex justify-end">
                        <img src="Images/Lady1.png" alt="Collection of pre-loved accessories" class="max-w-full h-auto object-cover max-h-96">
                    </div>
                </div>
            </div>

            <div class="carousel-slide bg-rose-50 relative">
                <div class="container mx-auto flex items-center justify-between p-4 sm:p-8">
                    <div class="w-1/2 pr-4 lg:pr-12">
                        <p class="text-lg sm:text-xl font-medium mb-4 text-rose-800">
                            **Sustainable fashion** that feels good on your wallet and the planet.
                        </p>
                        <h1 class="hero-headline font-chewy font-extrabold text-rose-900">
                            Shop Smart <br> Live Green
                        </h1>
                        <a href="#" class="mt-6 inline-block bg-rose-600 hover:bg-rose-700 text-white text-xl font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                            Read Our Pledge
                        </a>
                    </div>
                    <div class="w-1/2 flex justify-end">
                        <img src="Images/Lady1.png" alt="Sustainable shopping bags and plants" class="max-w-full h-auto object-cover max-h-96">
                    </div>
                </div>
            </div>

        </div>

        <div class="arrow-container flex justify-center mt-6 pb-8">
            <button id="prevBtn" class="grok-arrow">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button id="nextBtn" class="grok-arrow">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

    </div>

    <div class="container mx-auto pt-0 pb-8 px-4 sm:px-6 lg:px-8"> 
        
        <p class="ml-2 text-3xl font-semibold italic mt-0 mb-6">CATEGORIES</p>
        <div>
            <?php require "Category_carousel.php"; ?>
        </div>
        
        <hr class="flex-1 border-t-2 border-gray-200 my-8">

        <p class="ml-2 text-3xl font-semibold italic mb-6">BIDDING DEALS</p>
        <div>
            <?php require "Bidding_Swipe.php"; ?>
        </div>

        <hr class="flex-1 border-t-2 border-gray-200 my-8">

        <div>
            <p class="ml-2 text-2xl italic font-semibold mb-6">TOP SELLERS</p>
            <?php require "Top_Sellers.php"; ?>
        </div>

        <hr class="flex-1 border-t-2 border-gray-200 my-8">

        <div class="mb-12">
            <p class="text-2xl italic font-semibold text-left">DAILY DISCOVER</p>
            <hr class="flex-1 border-t-2 border-black mb-6">
            <?php require "Daily_Discover.php"; ?>
        </div>

    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Navbar and Panel Toggles ---
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            if(profileBtn) {
                profileBtn.addEventListener('click', () => {
                    profileDropdown.classList.toggle('hidden');
                });
            }

            const mobileBtn = document.getElementById('mobileBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            if(mobileBtn) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            document.addEventListener('click', (event) => {
                if(profileDropdown && profileBtn && !profileBtn.contains(event.target) && !profileDropdown.contains(event.target)){
                    profileDropdown.classList.add('hidden');
                }
            });
            
            // --- Swipable Carousel Logic ---
            const carousel = document.getElementById('hero-carousel');
            const slides = document.querySelectorAll('.carousel-slide');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentIndex = 0;
            const totalSlides = slides.length;
            let startX = 0;
            let isDragging = false;
            
            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            }

            // Button events
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    currentIndex = (currentIndex + 1) % totalSlides;
                    updateCarousel();
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                    updateCarousel();
                });
            }

            // Touch swipe for mobile
            if (carousel) {
                carousel.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                    isDragging = true;
                    carousel.style.transition = 'none';
                }, { passive: true });

                carousel.addEventListener('touchmove', (e) => {
                    if (!isDragging) return;
                    const currentX = e.touches[0].clientX;
                    const diff = currentX - startX;
                    const baseOffset = -currentIndex * 100;
                    const dragOffset = (diff / carousel.offsetWidth) * 100;
                    carousel.style.transform = `translateX(${baseOffset + dragOffset}%)`;
                }, { passive: true });

                carousel.addEventListener('touchend', (e) => {
                    if (!isDragging) return;
                    const endX = e.changedTouches[0].clientX;
                    const diff = startX - endX;
                    if (Math.abs(diff) > 50) { // Swipe threshold
                        if (diff > 0) {
                            currentIndex = (currentIndex + 1) % totalSlides;
                        } else {
                            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                        }
                    }
                    updateCarousel();
                    carousel.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    isDragging = false;
                });

                // Mouse drag for desktop
                carousel.addEventListener('mousedown', (e) => {
                    startX = e.clientX;
                    isDragging = true;
                    carousel.style.transition = 'none';
                    carousel.style.cursor = 'grabbing';
                    e.preventDefault();
                });

                carousel.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    const currentX = e.clientX;
                    const diff = currentX - startX;
                    const baseOffset = -currentIndex * 100;
                    const dragOffset = (diff / carousel.offsetWidth) * 100;
                    carousel.style.transform = `translateX(${baseOffset + dragOffset}%)`;
                });

                carousel.addEventListener('mouseup', (e) => {
                    if (!isDragging) return;
                    const endX = e.clientX;
                    const diff = startX - endX;
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) {
                            currentIndex = (currentIndex + 1) % totalSlides;
                        } else {
                            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                        }
                    }
                    updateCarousel();
                    carousel.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    carousel.style.cursor = 'grab';
                    isDragging = false;
                });

                carousel.addEventListener('mouseleave', () => {
                    if (isDragging) {
                        updateCarousel();
                        carousel.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                        carousel.style.cursor = 'grab';
                        isDragging = false;
                    }
                });
            }
        });
    </script>

</body>
</html>