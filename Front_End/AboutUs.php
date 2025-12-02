<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Ukay Ta Bai</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Custom styles for the dark theme */
        :root {
            --color-dark-bg: #111827; /* Tailwind Gray-900 for main background */
            --color-card-bg: #1F2937; /* Tailwind Gray-800 for cards */
            --color-accent: #34D399; /* Tailwind Emerald-400/500 for green accents */
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
            background-color: #10B981; /* Tailwind Green-500 */
        }
    </style>
</head>
<body class="bg-dark-primary text-gray-200 font-sans">

    <!-- Header / Navigation Bar -->
    <header class="bg-card shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold text-accent">UKAY TA BAI</a>
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a href="#" class="hover:text-accent transition duration-150">Home</a>
                <a href="#" class="text-accent border-b-2 border-accent pb-1">About Us</a>
                <a href="#" class="hover:text-accent transition duration-150">Products</a>
                <a href="#" class="btn-accent px-4 py-2 rounded-lg font-semibold shadow-md">Contact Us</a>
            </nav>
        </div>
    </header>

    <!-- Hero Banner Section -->
    <section class="relative h-96 flex items-center justify-center overflow-hidden">
        <!-- IMAGE PLACEHOLDER: Hero Background Image -->
        <!-- Replace 'src' below with your desired banner image URL (e.g., a dark, atmospheric market photo) -->
        <img
            src="https://placehold.co/1920x400/222222/ffffff?text=HERO+BACKGROUND+IMAGE"
            alt="Ukay Ta Bai Banner"
            class="absolute inset-0 w-full h-full object-cover opacity-30"
            onerror="this.onerror=null; this.src='https://placehold.co/1920x400/222222/ffffff?text=Fallback';"
        >
        <div class="relative z-10 text-center">
            <h1 class="text-6xl font-extrabold text-white mb-4">About Us</h1>
            <p class="text-sm text-gray-400">Home / <span class="text-accent">About Us</span></p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-20 md:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Left Column: Image -->
            <div>
                <!-- IMAGE PLACEHOLDER: Team/Community Image -->
                <!-- Replace 'src' below with your team or community photo URL -->
                <img
                    src="https://placehold.co/600x400/34D399/1F2937?text=IMAGE+OF+OUR+TEAM/COMMUNITY"
                    alt="Image of our community"
                    class="w-full h-auto rounded-xl shadow-2xl transition duration-300 hover:shadow-accent/30"
                    onerror="this.onerror=null; this.src='https://placehold.co/600x400/374151/ffffff?text=Image+Missing';"
                >
            </div>
            
            <!-- Right Column: Text -->
            <div class="md:pl-8">
                <p class="text-accent text-sm font-semibold uppercase mb-2 tracking-widest">Our Story</p>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                    We Connect You To <span class="text-accent">Sustainable Fashion</span>
                </h2>
                <p class="text-gray-400 mb-8 leading-relaxed">
                    Ukay Ta Bai was founded on the inspiration drawn from platforms like **Threadly**, recognizing the need for a dedicated, high-quality community marketplace for pre-loved goods. We believe in extending the life cycle of clothing while providing accessible and unique finds.
                </p>
                <button class="btn-accent px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-accent/50 transition duration-300">
                    Learn More
                </button>
            </div>
        </div>
    </section>

    <!-- Our Impact & Inspiration Section -->
    <section class="py-20 md:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-10">
            <!-- Left: Title and List -->
            <div class="lg:col-span-2">
                <h3 class="text-3xl font-bold text-white mb-4">Our Impact</h3>
                <p class="text-accent text-xl font-semibold mb-6">Inspired by Threadly & Giants</p>
                <ul class="space-y-3 text-gray-400 list-disc list-inside">
                    <li>Curated Collections & Authenticity Checks</li>
                    <li>Secure Payment Processing</li>
                    <li>Efficient Nationwide Logistics</li>
                    <li>Transparent Seller Rating System</li>
                </ul>
            </div>

            <!-- Right: Inspiration Cards -->
            <div class="lg:col-span-3 grid sm:grid-cols-3 gap-6">
                
                <!-- Card 1: Zalora -->
                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="shopping-bag" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Zalora</h4>
                    <p class="text-sm text-gray-400">Seamless platform experience, high-fashion standards.</p>
                </div>

                <!-- Card 2: Shopee -->
                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="truck" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Shopee</h4>
                    <p class="text-sm text-gray-400">Trustworthy platform ideas, easy transactions.</p>
                </div>

                <!-- Card 3: Lazada -->
                <div class="bg-card p-6 rounded-xl shadow-lg border border-gray-700 hover:border-accent transition duration-300">
                    <span data-lucide="package" class="text-accent w-8 h-8 mb-4"></span>
                    <h4 class="text-xl font-bold text-white mb-1">Lazada</h4>
                    <p class="text-sm text-gray-400">Reliable logistics and extensive regional reach.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Discovery/CTA Banner Section -->
    <section class="relative py-20 my-20 overflow-hidden">
        <!-- IMAGE PLACEHOLDER: Middle Banner Image -->
        <!-- Replace 'src' below with your desired banner image URL (e.g., people shopping at a thrift store) -->
        <img
            src="https://placehold.co/1920x400/1F2937/34D399?text=Discover+Unique+Finds+-+Image+Background"
            alt="People discovering unique finds"
            class="absolute inset-0 w-full h-full object-cover opacity-60"
            onerror="this.onerror=null; this.src='https://placehold.co/1920x400/1F2937/ffffff?text=Fallback+Banner';"
        >
        <div class="absolute inset-0 bg-dark-primary opacity-50"></div>
        <div class="relative z-10 text-center max-w-xl mx-auto px-4">
            <h3 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Discover Unique Finds & Support Local
            </h3>
            <p class="text-gray-300 mb-8">
                Every purchase supports a community and champions sustainability.
            </p>
            <button class="btn-accent px-8 py-4 rounded-xl font-semibold text-lg shadow-xl hover:shadow-accent/70 transition duration-300">
                Shop Now
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-card pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10 border-t border-gray-700 pt-8">
            
            <!-- Column 1: Logo and About -->
            <div class="col-span-1 md:col-span-2">
                <span class="text-3xl font-bold text-accent">UKAY TA BAI</span>
                <p class="mt-4 text-sm text-gray-400 max-w-xs">
                    Curating the best in pre-loved fashion, inspired by global platforms and driven by local community.
                </p>
                <div class="flex space-x-4 mt-6">
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="facebook" class="w-5 h-5"></span></a>
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="instagram" class="w-5 h-5"></span></a>
                    <a href="#" class="text-gray-400 hover:text-accent transition duration-150"><span data-lucide="twitter" class="w-5 h-5"></span></a>
                </div>
            </div>

            <!-- Column 2: Our Store -->
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Our Store</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-accent transition duration-150">Home</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">About</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">Shop</a></li>
                    <li><a href="#" class="hover:text-accent transition duration-150">Contact</a></li>
                </ul>
            </div>

            <!-- Column 3: Get In Touch -->
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-center space-x-2">
                        <span data-lucide="map-pin" class="w-4 h-4 text-accent"></span>
                        <span>2468 Old Balanga Rd, Cebu City</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span data-lucide="phone" class="w-4 h-4 text-accent"></span>
                        <span>+63 900 876 5432</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span data-lucide="mail" class="w-4 h-4 text-accent"></span>
                        <span>support@ukaytba.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 text-center text-xs text-gray-500 border-t border-gray-800 pt-6">
            Copyright © 2025 Ukay Ta Bai. All rights reserved.
        </div>
    </footer>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>