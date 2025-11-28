<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DIRI IMPLEMENT ANG LOGIC SA ADMIN_VERIFY PARA MAKA OPEN SIYA ANI NA TAB OR FILE

require_once "../Back_End/Models/Database.php";

$db = new Database();
$conn = $db->get_connection();

// --- 2. Fetch Pending Requests ---
$sql = "
    SELECT 
        sv.id AS request_id, 
        u.first_name, 
        u.last_name, 
        u.email,
        sv.address, 
        sv.contact_number, 
        sv.id_front, 
        sv.id_back, 
        sv.submitted_at
    FROM verify_seller sv
    JOIN users u ON sv.user_id = u.id
    WHERE sv.status = 'pending'
    ORDER BY sv.submitted_at ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$pendingRequests = [];
while ($row = $result->fetch_assoc()) {
    $pendingRequests[] = $row;
}
$stmt->close();
$db->close_db();


function get_web_path($local_path) {
    
    $base_path = "C:\xampp\htdocs";
    $web_root = "";

    $web_path = str_ireplace($base_path, $web_root, $local_path);
    return str_replace('\\', '/', $web_path);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>⚡ TEMP Seller Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        
        .id-image {
            max-width: 100%;
            height: auto;
            max-height: 150px; 
            border-radius: 4px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body class="bg-gray-800 text-white p-6">

    <h1 class="text-3xl font-bold mb-6 text-yellow-400">Temporary Approver sa Sellers Approval</h1>
    <p class="mb-4 text-gray-400">Approve or Reject the Filled out Forms of the Users that are applying to be a seller.</p>

    <?php if (empty($pendingRequests)): ?>
        <p class="text-lg text-green-400">🎉 All pending verifications have been processed!</p>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($pendingRequests as $request): ?>
                
                <?php 
                    $web_path_front = get_web_path($request['id_front']);
                    $web_path_back = get_web_path($request['id_back']);
                ?>

                <div class="bg-gray-700 p-5 rounded-lg shadow-lg border border-gray-600">
                    <div class="flex justify-between items-start mb-3 border-b border-gray-600 pb-2">
                        <div class="font-mono text-xs text-gray-400">
                             Request ID: #<?= htmlspecialchars($request['request_id']); ?> 
                             <br>
                             Requested: <?= date('Y-m-d H:i', strtotime($request['submitted_at'])); ?>
                        </div>
                        <div class="flex space-x-2">
                             <a href="admin_seller_approval.php?action=approve&id=<?= htmlspecialchars($request['request_id']); ?>" 
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-sm transition duration-150"
                                onclick="return confirm('Are you sure you want to APPROVE this request?')">
                                ✅ Approve
                             </a>
                             <a href="admin_seller_approval.php?action=reject&id=<?= htmlspecialchars($request['request_id']); ?>" 
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm transition duration-150"
                                onclick="return confirm('Are you sure you want to REJECT this request?')">
                                ❌ Reject
                             </a>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-semibold text-white mb-2">
                        <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                    </h2>
                    
                    <p class="text-sm text-gray-300 mb-1">
                        Email: <?= htmlspecialchars($request['email']); ?> | 
                    </p>
                    <p class="text-sm text-gray-300 mb-1">
                        Contact: <?= htmlspecialchars($request['contact_number']); ?>
                    </p>
                    <p class="text-sm text-gray-300">
                        Address: <?= htmlspecialchars($request['address']); ?>
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-sm font-medium mb-2">ID Front</p>
                            <a href="<?= htmlspecialchars($web_path_front); ?>" target="_blank" class="block hover:opacity-75 transition">
                                <img src="<?= htmlspecialchars($web_path_front); ?>" alt="ID Front Image" class="id-image">
                            </a>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium mb-2">ID Back</p>
                            <a href="<?= htmlspecialchars($web_path_back); ?>" target="_blank" class="block hover:opacity-75 transition">
                                <img src="<?= htmlspecialchars($web_path_back); ?>" alt="ID Back Image" class="id-image">
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>