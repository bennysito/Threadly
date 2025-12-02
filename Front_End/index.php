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
    <div class="container mx-auto py-8">    

        <p class="ml-2 text-3xl font-semibold italic custom-inter mt-0">CATEGORIES</p>
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
            <p class="ml-2 text-3xl italic font-semibold custom-inter mb-6">TOP SELLERS</p>
            <?php require "Top_Sellers.php"; ?>
        </div>

        <hr class="flex-1 border-t-2 border-gray-200 my-8">

        <div class="mb-12">
            <p class="text-3xl italic font-semibold custom-inter text-left">DAILY DISCOVER</p>
            <hr class="flex-1 border-t-2 border-black mb-2">
            <?php require "Daily_Discover.php"; ?>
        </div>

    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar and panels JS
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
            
            // Swipable Carousel Logic
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
        });
    </script>

</body>
</html>