<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php

<?php
$daily_products = [];
require_once __DIR__ . "/../Back_End/Models/Search.php";
$search = new Search();
$daily_products = $search->getRecent(12);

?>

<link rel="stylesheet" href="CSS/Daily_discover.css">

<div class="daily-grid">
    <?php foreach($daily_products as $p): 
        $id = $p['id'] ?? '';
        $link = $id ? "product_info.php?id=" . urlencode($id) : "product_info.php?name=" . urlencode($p['name']);
    ?>
    <a href="<?= $link ?>" style="text-decoration: none; color: inherit;">
    <div class="daily-card">
        <div class="daily-img-wrapper">
            
              <img src="Images/<?= htmlspecialchars($p['image'] ?? 'panti.png') ?>" 
                  alt="<?= htmlspecialchars($p['name']) ?>" 
                  class="daily-img daily-img-main">


              <img src="Images/<?= htmlspecialchars($p['hover_image'] ?? $p['image'] ?? 'panti.png') ?>" 
                  alt="<?= htmlspecialchars($p['name']) ?> hover" 
                  class="daily-img daily-img-hover">
        </div>

        <div class="daily-info">
            <div class="daily-price-row">
                 <div class="daily-price">₱<?= number_format($p['price'] ?? 0) ?></div>
                <svg class="daily-heart" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <p class="daily-name"><?= htmlspecialchars($p['name']) ?></p>
        </div>
    </div>
    </a>
    <?php endforeach; ?>
</div>