<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Threadly</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
    }
  </style>
</head>
<body class="bg-gray-100">

<nav class="bg-white border-b shadow-sm px-4 py-2">
  <div class="max-w-7xl mx-auto flex items-center justify-between">

    <a href="#" class="flex items-center">
      <img src="logo.png" alt="Logo" class="h-20 w-auto">
      <span class="text-1xl sm:text-3xl font-semibold">Threadly</span>
    </a>

    <div class="flex-1 mx-4">
      <div class="relative w-full">
        <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </span>
        <input type="search" placeholder="Search"
               class="w-full rounded-full pl-12 pr-4 py-2 border border-gray-300 
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>

    <div class="flex items-center gap-4">
      <a href="#" class="hidden md:inline-flex items-center text-gray-700 font-medium hover:text-blue-500">Products</a>

      <a href="#" class="hidden md:inline-flex items-center text-gray-700 font-medium hover:text-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        Notification
      </a>

      <a href="#" class="hidden md:inline-flex items-center text-gray-700 font-medium hover:text-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
        </svg>
        Help
      </a>

      <div class="relative">
        <button id="profileBtn" class="flex items-center rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
          <img src="avatar_blank.png" class="w-8 h-8 rounded-full" alt="Profile">
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

  <!-- Functionality basta ig click sa avatar-->
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
