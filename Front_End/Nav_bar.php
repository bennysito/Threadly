<?php 
// nav_bar.php
require_once "../Back_End/Models/Users.php";

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $user = new User();
    $user->logoutUser(); 
    header("location: index.php");
    exit;
}
?>
<style>

    .chewy-font-v2{
        font-family: 'Chewy', cursive;
        font-size: 2rem;
        font-weight: 400;
    }

    /* Ensure the logo class used in markup receives the Chewy font as well */
    .chewy-font{
        font-family: 'Chewy', cursive;
        font-size: 2rem;
        font-weight: 400;
    }
</style>
<nav class="bg-white  ">
    <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-2">

        <a href="index.php" class="flex items-center flex-shrink-0">
            <img src="Images/Threadly_logo.png" alt="Logo" class="h-16 w-auto ml-5/6 logo-scale">
            <span class="chewy-font ml-6">Threadly</span>
        </a>

        <div class="flex-1 min-w-0 ml-20">
            <form action="search.php" method="get" class="w-full">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </span>
                    <input type="search" name="q" placeholder="Search"
                            class="w-full max-w-xs sm:max-w-md rounded-full pl-10 pr-10 py-2 border border-gray-300 
                                 focus:outline-none focus:ring-2 focus:ring-amber-500 min-w-0">
                </div>
            </form>
        </div>

        <div class="hidden md:flex items-center gap-6 ml-4">
            
            <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center hover:text-amber-500 gap-1 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </a>
            
            <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center hover:text-amber-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                </svg>
            </a>
            
            <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center hover:text-amber-500 ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </a>
            
            <a id="openWishlistBtn" href="javascript:void(0);" class="flex items-center text-gray-700 hover:text-amber-500 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>
            
            <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center text-gray-700 hover:text-amber-500 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0
                        1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442
                        -.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0
                        9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
            </a>
            
        </div>

        <div class="flex items-center gap-2">
            <button id="mobileBtn" class="md:hidden flex items-center p-2 border rounded ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>


<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<div class="relative">
    <button id="profileBtn" class="flex items-center text-gray-700 hover:text-amber-500 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
    </button>

    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-10">
        <?php if ($isLoggedIn): ?>

            <?php if (!empty($_SESSION['first_name'])): ?>
                <div class="px-4 py-2 chewy-font-v2">
                    Welcome 
                    <span class="font-bold italic uppercase text-yellow-500">
                        <?= htmlspecialchars($_SESSION['first_name']) . '!'; ?>
                    </span>
                </div>
            <?php endif; ?>

            <a href="profile.php" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span>Profile</span>
            </a>

            <a href="my_bids.php" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166m4.024-.166L18 9m-18 0a48.002 48.002 0 0 1 4.024-.166m0 0L6 9m12 0v-1.5m0 1.5c1.355 0 2.697.056 4.024.166M18 9l4.24.005M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>My Bids</span>
            </a>

            <a id="openWishlistDropdownBtn" href="javascript:void(0);" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                <span>Wishlist</span>
            </a>

            <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <span>Orders</span>
            </a>

            <a href="Verify_Seller.php" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                <span>become a seller </span>
            </a>

            <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M12 5a.75.75 0 0 0-.643.363L8.145 10.7 3.408 7.621a.75.75 0 0 0-1.15.74l1.5 10A.75.75 0 0 0 4.5 19h15a.75.75 0 0 0 .742-.639l1.5-10a.75.75 0 0 0-1.15-.74L15.855 10.7l-3.212-5.336A.75.75 0 0 0 12 5Z"/>
                </svg>
                <span>Plans & Pricing</span>
            </a>

            <hr class="my-1 border-gray-200">

            <a href="?action=logout" class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
                </svg>
                <span>Logout</span>
            </a>

        <?php else: ?>

            <div class="bg-white p-6 rounded-xl w-46 mx-auto flex flex-col gap-4 shadow-lg">
                <p class="chewy-font-v2">Welcome!</p>
                <a href="login.php" class="block text-white bg-black px-4 py-2 rounded-full text-center font-semibold hover:bg-amber-600 transition-colors duration-200">
                    LOG IN
                </a>
                <a href="sign-up.php" class="block text-black border border-black px-4 py-2 rounded-full text-center font-semibold hover:bg-black hover:text-white transition-colors duration-200">
                    SIGN UP
                </a>
            </div>

        <?php endif; ?>

    </div>

</div>
        </div>

    </div>
    
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t px-4 py-2 space-y-1">
        <a href="#" class="block text-gray-700 hover:text-blue-500">Products</a>
        <a href="#" class="block text-gray-700 hover:text-blue-500">Notification</a>
        <a href="#" class="block text-gray-700 hover:text-blue-500">Help</a>
    </div>
</nav>

<script>
    // Grab the profile button and the dropdown
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    // Toggle dropdown on click
    if (profileBtn) {
        profileBtn.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });
    }

    // Optional: close the dropdown if user clicks outside
    document.addEventListener('click', (event) => {
        if (profileDropdown && profileBtn && !profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.add('hidden');
        }
    });
</script>