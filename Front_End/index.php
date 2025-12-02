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

        <p class="ml-2 text-3xl font-semibold italic custom-inter mb-6">BIDDING DEALS</p>
        <div>
            <?php require "Bidding_Swipe.php"; ?>
        </div>

        <div>
            <p class="ml-2 text-2xl italic font-semibold custom-inter mb-6">TOP SELLERS</p>
            <?php require "Top_Sellers.php"; ?>
        </div>

        <div class="mb-12">
            <p class="text-2xl italic font-semibold custom-inter text-center">DAILY DISCOVER</p>
            <hr class="flex-1 border-t-2 border-black mb-2">
            <?php require "Daily_Discover.php"; ?>
        </div>

    </div>

    <script>
        // NAVBAR DROPDOWN (profile)
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        if(profileBtn) {
            profileBtn.addEventListener('click', () => {
                profileDropdown.classList.toggle('hidden');
            });
        }

        // MOBILE MENU
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
        
        // The panel JS listeners are now handled directly within each panel's included file.
    </script>

</body>
</html>