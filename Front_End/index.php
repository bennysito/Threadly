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
<link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
<link href="CSS/homepage.css" rel="stylesheet">
</head>

<!--Nav-->

<div>
  <?php require "Nav_bar.php"; ?>
</div>



 <div class="container mx-auto py-8">
  
  <!-- CATEGORIES-->
  <p class="ml-2 text-3xl font-semibold italic custom-inter mt-0">CATEGORIES</p>
  <div>
    <?php require "Category_carousel.php"; ?>
  </div>

  <!-- BIDDING DEAL-->
  <p class="ml-2 text-3xl font-semibold italic custom-inter mb-6 ">BIDDING DEALS</p>
  <div class="">
    <?php require "Bidding_Swipe.php"; ?>
  </div>

  <div>
    <p class="ml-2 text-2xl italic font-semibold custom-inter mb-6">TOP SELLERS</p>
    <?php require "Top_Sellers.php";?>
  </div>

  <!--Daily Discover-->
  <div class="mb-12">
   
    <p class="text-2xl italic font-semibold custom-inter text-center ">DAILY DISCOVER</p>
    
   
    <hr class="flex-1 border-t-2 border-black mb-2">
    <?php require "Daily_Discover.php"; ?>  
  </div>
</div>


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