<?php
/**
 * about.php
 * This is the "About Us" page template for Ukay Ta Bai.
 * It is set up to be included within a larger application structure
 * (e.g., requires 'header.php' and 'footer.php').
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Dynamic/Reusable Header and Footer Logic ---
// Note: You would replace these lines with your actual include paths.
// require_once 'includes/header.php'; 
// require_once 'includes/footer.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Ukay Ta Bai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        <?php 
        // Define CSS variables using PHP for potential dynamic changes, though they are static here
        $darkBg = '#ffffffff';
        $cardBg = '#000000ff';
        $accentColor = '#ffffffff';
        $accentHover = '#10B981'; // Tailwind Green-500
        ?>

        /* Custom styles for the dark theme */
        :root {
            --color-dark-bg: <?= $darkBg ?>;
            --color-card-bg: <?= $cardBg ?>;
            --color-accent: <?= $accentColor ?>;
        }
        .bg-dark-primary { background-color: var(--color-dark-bg); }
        .bg-card { background-color: var(--color-card-bg); }
        .text-accent { color: var(--color-accent); }
        .border-accent { border-color: var(--color-accent); }
        .btn-accent {
            background-color: var(--color-accent);
            color: var(--color-dark-bg);
            transition: all 0.2s;
        }
        .btn-accent:hover {
            background-color: <?= $accentHover ?>;
        }
    </style>
</head>
<body class="bg-white">

    <?php require "Nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?> 


    <section class="relative h-96 flex items-center justify-center overflow-hidden">
        <img
            src="uploads/thrift.jpg"
            alt="Ukay Ta Bai Banner"
            class="absolute inset-0 w-full h-full object-cover opacity-60"
            onerror="this.onerror=null; this.src='https://placehold.co/1920x400/222222/ffffff?text=Fallback';"
        >
        <div class="relative z-10 text-center">
            <h1 class="text-6xl font-extrabold text-white mb-4">About Us</h1>
            <p class="text-sm text-gray-400">Home / <span class="text-accent">About Us</span></p>
        </div>
    </section>

    <section class="py-20 md:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <img
                    src="uploads/group-pic.jpg?text=IMAGE+OF+OUR+TEAM/COMMUNITY"
                    alt="Image of our community"
                    class="w-full h-auto rounded-xl shadow-2xl transition duration-300 hover:shadow-accent/30"
                    onerror="this.onerror=null; this.src='uploads/group-pic.jpg';"
                >
            </div>
            
            <div class="md:pl-8">
                <p class="text-accent text-sm font-semibold uppercase mb-2 tracking-widest">Our Story</p>
                <h2 class="text-4xl md:text-5xl font-bold text-black mb-6 leading-tight">
                    We Connect You To <span class="text-cardBg">Sustainable Fashion</span>
                </h2>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Threadly was founded on the inspiration drawn from platforms like UTB (UkayTaBai), recognizing the need for a dedicated, high-quality community marketplace for pre-loved goods. We believe in extending the life cycle of clothing while providing accessible and unique finds.
                </p>
                <button class="btn-cardBg px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-cardBg/50 transition duration-300">
                    Learn More
                </button>
            </div>
        </div>
    </section>

    <section class="py-20 md:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-10">
            <div class="lg:col-span-2">
                <h3 class="text-3xl font-bold text-white mb-4">Our Impact</h3>
                <p class="text-cardBg text-xl font-semibold mb-6">Inspired by UTB (UkayTaBai)</p>
                <ul class="space-y-3 text-gray-600 list-disc list-inside">
                    <li>Curated Collections & Authenticity Checks</li>
                    <li>Secure Payment Processing</li>
                    <li>Efficient Nationwide Logistics</li>
                    <li>Transparent Seller Rating System</li>
                </ul>
            </div>

            <div class="lg:col-span-3 grid sm:grid-cols-3 gap-6">
                
                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="shopping-bag" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Zalora</h4>
                    <p class="text-sm text-gray-400">Seamless platform experience, high-fashion standards.</p>
                </div>

                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="truck" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Shopee</h4>
                    <p class="text-sm text-gray-400">Trustworthy platform ideas, easy transactions.</p>
                </div>

                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="package" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Lazada</h4>
                    <p class="text-sm text-gray-400">Reliable logistics and extensive regional reach.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative py-20 my-20 overflow-hidden">
        <img
            src="uploads/thrift store.jpg?text=Discover+Unique+Finds+-+Image+Background"
            alt="People discovering unique finds"
            class="absolute inset-0 w-full h-full object-cover opacity-150"
            onerror="this.onerror=null; this.src='https://placehold.co/1920x400/1F2937/ffffff?text=Fallback+Banner';"
        >
        <div class="absolute inset-0 bg-dark-primary opacity-50"></div>
        <div class="relative z-10 text-center max-w-xl mx-auto px-4">
            <h3 class="text-4xl md:text-5xl font-bold text-black mb-4">
                Discover Unique Finds & Support Local
            </h3>
            <p class="text-gray-600 mb-8">
                Every purchase supports a community and champions sustainability.
            </p>
            <button class="btn-cardBg px-8 py-4 rounded-xl font-semibold text-lg shadow-xl hover:shadow-cardBg/70 transition duration-300">
                Shop Now
            </button>
        </div>
    </section>

    <footer class="bg-card pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10 border-t border-gray-700 pt-8">
            
            <div class="col-span-1 md:col-span-2">
                <span class="text-3xl font-bold text-accent">Threadly</span>
                <p class="mt-4 text-sm text-gray-400 max-w-xs">
                    Curating the best in pre-loved fashion, inspired by global platforms and driven by local community.
                </p>
                <div class="flex space-x-4 mt-6">
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="facebook" class="w-5 h-5"></span></a>
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="instagram" class="w-5 h-5"></span></a>
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="twitter" class="w-5 h-5"></span></a>
                </div>
            </div>

            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Our Store</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-accent transition duration-150">Home</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">About</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">Shop</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">Contact</a></li>
                </ul>
            </div>

            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-center space-x-2">
                        <span data-lucide="map-pin" class="w-4 h-4 text-accent"></span>
                        <span>Quiot Qyda 3 Cebu City</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span data-lucide="phone" class="w-4 h-4 text-accent"></span>
                        <span>+63 900 876 5432</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span data-lucide="mail" class="w-4 h-4 text-accent"></span>
                        <span>support@Threadly.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 text-center text-xs text-gray-500 border-t border-gray-800 pt-6">
            Copyright &copy; 2025 Threadly. All rights reserved.
        </div>
    </footer>
    
    <script>
        lucide.createIcons();
    </script>
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
        });
    </script>
</body>
</html>