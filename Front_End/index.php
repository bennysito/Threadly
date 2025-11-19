<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Threadly</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
</style>
</head>
<body class="bg-gray-100">

<nav class="bg-white border-b shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-2">

    <!-- Logo kaotng Threaldy-->
    <a href="#" class="flex items-center flex-shrink-0">
      <img src="Images//logo.png" alt="Logo" class="h-16 w-auto">
      <span class="text-xl sm:text-3xl font-semibold ml-2">Threadly</span>
    </a>

    <!-- Search bar -->
    <div class="flex-1 min-w-0">
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
      <a href="#" class="text-gray-700 hover:text-blue-500 font-medium">Products</a>
      <a href="#" class="flex items-center text-gray-700 hover:text-blue-500 gap-1 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        Notification
      </a>
      <a href="#" class="flex items-center text-gray-700 hover:text-blue-500 gap-1 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0
                   1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442
                   -.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0
                   9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
        </svg>
        Help
      </a>
    </div>

    <!-- Hamburger nga button ,katong 3 lines -->
    <div class="flex items-center gap-2">
      <button id="mobileBtn" class="md:hidden flex items-center p-2 border rounded">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>

      <div class="relative">
        <button id="profileBtn" class="flex items-center rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
          <img src="Images/avatar_blank.png" class="w-8 h-8 rounded-full" alt="Profile">
        </button>
        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-10">
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Reviews</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wishlist</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Orders</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Plans&Pricing</a>
          <hr class="my-1 border-gray-200">
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        </div>
      </div>
    </div>

  </div>

  <!-- para mobile-->
  <div id="mobileMenu" class="hidden md:hidden bg-white border-t px-4 py-2 space-y-1">
    <a href="#" class="block text-gray-700 hover:text-blue-500">Products</a>
    <a href="#" class="block text-gray-700 hover:text-blue-500">Notification</a>
    <a href="#" class="block text-gray-700 hover:text-blue-500">Help</a>
  </div>
</nav>

<script>
  const profileBtn = document.getElementById('profileBtn');
  const profileDropdown = document.getElementById('profileDropdown');
  profileBtn.addEventListener('click', () => {
    profileDropdown.classList.toggle('hidden');
  });

  const mobileBtn = document.getElementById('mobileBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  mobileBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
</script>

</body>
</html>
