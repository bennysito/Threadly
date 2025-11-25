
<?php 
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
<nav class="bg-white  shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-2">

    <!-- Logo kaotng Threaldy-->
    <a  href="https://samtu43.github.io/Sam_chanel/" class="flex items-center flex-shrink-0">
      <img src="Images/Threadly_logo.png" alt="Logo" class="h-16 w-auto ml-5/6 logo-scale">
      <span class="chewy-font ml-6">Threadly</span>
    </a>

    <!-- Search bar -->
    <div class="flex-1 min-w-0 ml-20">
      <div class="relative">
        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </span>
        <input type="search" placeholder="Search"
               class="w-full max-w-xs sm:max-w-md rounded-full pl-10 pr-10 py-2 border border-gray-300 
                      focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-0">
      </div>
    </div>

    <!-- Products,Notification,ug Help -->
    <div class="hidden md:flex items-center gap-6 ml-4">
      
    
      <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center  hover:text-amber-500 gap-1 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
      <!--Hi, text balhin ngari -->
      
      <!--message icon-->
        <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center hover:text-amber-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
</svg>
</a>
<!--cart icon-->
      <a href="https://samtu43.github.io/Sam_chanel/" class="flex items-center hover:text-amber-500 "><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
</svg>
</a>
<!--heart icon-->
 <a href="https://samtu43.github.io/Sam_chanel/"class="flex items-center text-gray-700 hover:text-amber-500 font-medium"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
</svg>
</a>
<!-- question mark icon-->
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

    <!-- Hamburger nga button ,katong 3 lines -->
    <div class="flex items-center gap-2">
      <button id="mobileBtn" class="md:hidden flex items-center p-2 border rounded ">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>


<?php
 // Start the session

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']); // Replace 'user_id' with your session key from login
?>

<div class="relative">
    <button id="profileBtn" class="flex items-center text-gray-700 hover:text-amber-500 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
</svg>

    </button>

    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-10">
        <?php if ($isLoggedIn): ?>
            <!-- Logged-in user dropdown -->
             <div>
              <?php if (isset($_SESSION['first_name']) && !empty($_SESSION['first_name'])): ?>
  <div class="hidden sm:flex sm:items-center sm:ml-4">
    <div style="line-height:1;">
      <div class="text-sm italic font-medium text-gray-800">Welcome  <span class="text-sm font-bold italic" style="color:#F59E0B; text-transform:uppercase;"><?= htmlspecialchars($_SESSION['first_name']); ?></span></div>
      
    </div>
  </div>
<?php endif; ?>
             </div>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Reviews</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wishlist</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Orders</a>
            <a href="Verify_Seller.php" class="block px-4 py-2 hover:bg-gray-100">Become a seller</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Plans & Pricing</a>
            <hr class="my-1 border-gray-200">
            <a href="?action=logout" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        <?php else: ?>
            <!-- Guest dropdown -->
            <a href="login.php" class="block px-4 py-2 hover:bg-gray-100">Login</a>
            <a href="sign-up.php" class="block px-4 py-2 hover:bg-gray-100">Create Account</a>
        <?php endif; ?>
    </div>
</div>
    </div>

  </div>
  
<!--Table para sa categories-->
<!--
<p class="ml-2 ">Categories</p>
  <div class="grid grid-cols-10 grid-rows-2 gap-4 p-4">

  <div class="border border-gray-300 h-20 "></div>
  <div class="border border-gray-300 h-20 "></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>
  <div class="border border-gray-300 h-20 h-20"></div>

 
  </div>
  </div>
-->
  <!-- para mobile-->
  <div id="mobileMenu" class="hidden md:hidden bg-white border-t px-4 py-2 space-y-1">
    <a href="#" class="block text-gray-700 hover:text-blue-500">Products</a>
    <a href="#" class="block text-gray-700 hover:text-blue-500">Notification</a>
    <a href="#" class="block text-gray-700 hover:text-blue-500">Help</a>
  </div>
</nav>