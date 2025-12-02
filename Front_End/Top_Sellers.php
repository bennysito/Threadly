<?php
// --- Configuration and Static Data Definition ---

// Define 12 static sellers with easily editable names and unique image source paths.
// To change a seller, just edit the 'name' and 'image_src' for that specific entry.
$sellers = [
    [
        'name' => 'Alexander',
        'image_src' => 'Profile/alex.png', 
    ],
    [
        'name' => 'Sam Mariscal', // Seller 2 Name
        'image_src' => 'Profile/sams.png', // Replace with your image path
    ],
    [
        'name' => 'Francis.', // Seller 3 Name
        'image_src' => 'Profile/Francis.jpg', // Replace with your image path
    ],
    [
        'name' => 'James.', // Seller 4 Name
        'image_src' => 'Profile/benny.jpg', // Replace with your image path
    ],
    [
        'name' => 'Naomi', // Seller 5 Name
        'image_src' => 'Profile/Naomi.jpg', // Replace with your image path
    ],
    [
        'name' => 'Renzo', // Seller 6 Name
        'image_src' => 'Profile/reo.png', // Replace with your image path
    ],
    [
        'name' => 'Maria', // Seller 7 Name
        'image_src' => 'Profile/maria.png', // Replace with your image path
    ],
    [
        'name' => 'Daven', // Seller 8 Name
        'image_src' => 'Profile/daven.jpg', // Replace with your image path
    ],
    [
        'name' => 'Walter', // Seller 9 Name
        'image_src' => 'Profile/sammego.jpg', // Replace with your image path
    ],
    [
        'name' => 'kenny.', // Seller 10 Name
        'image_src' => 'Profile/kenny.jpg', // Replace with your image path
    ],
    [
        'name' => 'Mambo', // Seller 11 Name
        'image_src' => 'Profile/nomnom.jpg', // Replace with your image path
    ],
    [
        'name' => 'Diwata', // Seller 12 Name
        'image_src' => 'Profile/diwata.jpg', // Replace with your image path
    ],
];


// Check if a session has been started (from the original request)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Tailwind CSS is assumed to be loaded and customized for the dark theme -->
<!-- Accent color: Green/Emerald (e.g., #000000ff) -->

<div class="top-sellers container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-white-900">
    <h2 class="text-3xl font-bold text-white mb-10 text-center">Top Sellers of the Week</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
        <?php foreach($sellers as $seller): ?>
            <a href="#" class="flex flex-col items-center group">
                <!-- Profile Image -->
                <div class="w-24 h-24 rounded-full border-4 border-black-400 flex items-center justify-center overflow-hidden shadow-xl bg-gray-800 transform group-hover:scale-105 transition duration-300">
                    
                    <!-- INSTRUCTIONS: The 'src' attribute uses $seller['image_src']. 
                         Edit the paths in the $sellers array at the top of the file. -->
                    <img
                        src="<?= htmlspecialchars($seller['image_src']) ?>" 
                        alt="<?= htmlspecialchars($seller['name']) ?>'s Profile Image"
                        class="w-full h-full object-cover"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='https://placehold.co/96x96/4B5563/E5E7EB?text=IMG';"
                    >
                </div>
                <!-- Seller Name -->
                <div class="mt-3 text-sm text-gray-200 font-semibold text-center group-hover:text-emerald-400 transition">
                    <?= htmlspecialchars($seller['name']) ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>