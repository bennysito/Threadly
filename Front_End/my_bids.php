<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/../Back_End/Models/Bidding.php";

$user_id = $_SESSION['user_id'];
$bidding = new Bidding();
$bids = $bidding->getUserBids($user_id);
$bidding->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids - Threadly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-accepted { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-withdrawn { background-color: #f3f4f6; color: #374151; }
    </style>
</head>
<body class="bg-gray-50 font-['Inter']">

    <?php require "Nav_bar.php"; ?>
    <?php require "wishlist_panel.php"; ?>

    <div class="max-w-6xl mx-auto px-4 py-8">
        
        <h1 class="text-4xl font-bold text-gray-900 mb-8">My Bids</h1>

        <?php if (empty($bids)): ?>
            <div class="text-center py-20">
                <p class="text-2xl text-gray-600 mb-4">You haven't placed any bids yet.</p>
                <a href="index.php" class="text-white bg-black px-6 py-3 rounded-full font-semibold hover:bg-gray-800 transition">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($bids as $bid): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="flex flex-col md:flex-row">
                            
                            <!-- Product Image -->
                            <div class="md:w-24 flex-shrink-0">
                                <img src="<?= htmlspecialchars($bid['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($bid['product_name']) ?>"
                                     class="w-full h-24 object-cover">
                            </div>

                            <!-- Bid Details -->
                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        <a href="product_info.php?id=<?= $bid['product_id'] ?>" class="hover:text-amber-600">
                                            <?= htmlspecialchars($bid['product_name']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        Original Price: <span class="font-semibold text-gray-900">₱<?= number_format((float)$bid['price'], 2) ?></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Bid Amount -->
                            <div class="p-6 bg-gray-50 flex flex-col justify-center items-center min-w-[200px]">
                                <p class="text-gray-600 text-sm mb-2">Your Bid Amount</p>
                                <p class="text-3xl font-bold text-amber-600">₱<?= number_format((float)$bid['bid_amount'], 2) ?></p>
                            </div>

                            <!-- Status -->
                            <div class="p-6 flex flex-col justify-center items-center min-w-[180px]">
                                <span class="status-badge status-<?= $bid['bid_status'] ?>">
                                    <?= ucfirst($bid['bid_status']) ?>
                                </span>
                                <p class="text-xs text-gray-500 mt-3">
                                    <?= date('M d, Y', strtotime($bid['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
