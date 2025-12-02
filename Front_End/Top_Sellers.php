<?php
// --- Configuration and Randomization Logic ---

// 1. Define a pool of diverse potential names
$all_names = [
    'Jona', 'Mark', 'Liza', 'Ethan', 'Chloe', 'Ryan', 'Sofia', 'Elias', 'Mila', 'Leo',
    'Amelia', 'Liam', 'Olivia', 'Noah', 'Harper', 'Henry', 'Evelyn', 'Alexander', 'Ella', 'Daniel'
];

// 2. Define 12 unique image source placeholders.
// You can replace these empty strings with your actual 12 image URLs/paths.
$image_sources = [
    'Images/', // Image for Seller 1
    '', // Image for Seller 2
    '', // Image for Seller 3
    '', // Image for Seller 4
    '', // Image for Seller 5
    '', // Image for Seller 6
    '', // Image for Seller 7
    '', // Image for Seller 8
    '', // Image for Seller 9
    '', // Image for Seller 10
    '', // Image for Seller 11
    '', // Image for Seller 12
];

// 3. Set the desired number of sellers to display
$count = 12;

// 4. Shuffle the names array randomly and select the first $count names
shuffle($all_names);
$random_sellers = array_slice($all_names, 0, $count);

// 5. Structure the final sellers data, merging names and unique image paths
$sellers = [];
for ($i = 0; $i < $count; $i++) {
    $sellers[] = [
        'name' => $random_sellers[$i],
        // Assign the unique image source from the defined array
        'image_src' => $image_sources[$i],
    ];
}

// Check if a session has been started (from the original request)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Tailwind CSS is assumed to be loaded and customized for the dark theme -->
<!-- Accent color: Green/Emerald (e.g., #34D399) -->

<div class="top-sellers container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold text-white mb-10 text-center">Top Sellers of the Week</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
        <?php foreach($sellers as $seller): ?>
            <div class="flex flex-col items-center">
                <!-- Profile Image: Placeholder for easy insertion -->
                <div class="w-24 h-24 rounded-full border-4 border-emerald-400 flex items-center justify-center overflow-hidden shadow-xl bg-gray-800 transform hover:scale-105 transition duration-300">
                    
                    <!-- 
                        INSTRUCTIONS: The 'src' attribute now uses $seller['image_src'], which is defined 
                        in the $image_sources array at the top of the file. 
                        Replace the empty strings in $image_sources array with your 12 unique image links.
                    -->
                    <img
                        src="<?= htmlspecialchars($seller['image_src']) ?>" 
                        alt="<?= htmlspecialchars($seller['name']) ?>'s Profile Image"
                        class="w-full h-full object-cover"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='https://placehold.co/96x96/4B5563/E5E7EB?text=IMG';"
                    >
                </div>
                <!-- Seller Name -->
                <div class="mt-3 text-sm text-gray-200 font-semibold text-center hover:text-emerald-400 transition"><?= htmlspecialchars($seller['name']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>