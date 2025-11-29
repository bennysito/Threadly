<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Log-in credentials not found.');</script>";
    exit;
}

$db = new Database();
$stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (isset($userData['role']) && $userData['role'] === 'seller') {
    $sellerStatus = 'approved';
} else {
    // If not a seller, check the verify_seller table for pending/rejected requests
    $stmt_status = $conn->prepare("SELECT status FROM verify_seller WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
    $stmt_status->bind_param("i", $_SESSION['user_id']);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();
    
    if ($result_status->num_rows > 0) {
        $row_status = $result_status->fetch_assoc();
        $sellerStatus = $row_status['status']; // 'pending', 'rejected', or 'approved'
    } else {
        $sellerStatus = 'none'; // No form submitted yet
    }
    $stmt_status->close();
}

$db->close_db();

// Handle POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Re-connect to database since it was closed after the status check
    $db_post = new Database();

    $userAccess = new User();

    $upload_dir = "C:/xampp/htdocs/Threadly/Front_End/uploads/";
    
    $path1 = "C:/xampp/htdocs/Threadly/Front_End/uploads/" . basename($_FILES["idIdentifier1"]["name"]);
    $path2 = "C:/xampp/htdocs/Threadly/Front_End/uploads/" . basename($_FILES["idIdentifier2"]["name"]);

    $checkbox = isset($_POST["checkbox"]) ? 1 : 0;  

    if (move_uploaded_file($_FILES["idIdentifier1"]["tmp_name"], $path1) &&
        move_uploaded_file($_FILES["idIdentifier2"]["tmp_name"], $path2)) 
    {
        $authentication = $userAccess->authenticate_seller(
            $_SESSION['user_id'],
            $_POST["date"],
            $_POST["contact_number"],
            $_POST["address"],
            $path1,
            $path2,
            $checkbox
        );

        if (!$authentication) {
            echo "<script>alert('Unable to verify as seller due to database error.');</script>";
            // Set status to rejected to allow resubmission if DB error occurred
            $sellerStatus = 'rejected'; 
        } else {
            // Success! Change status to pending to hide the form immediately
            echo "<script>alert('Successfully requested to verify as seller. Please wait for this form to be approved.');</script>";
            $sellerStatus = 'pending';
        }
    } else {
        echo "<script>alert('Error uploading files. Check permissions or file sizes.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php include "nav_bar.php"; ?>


<div class="max-w-2xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Become a Seller</h1>

    <p class="text-gray-600 mb-1"><strong>Name:</strong> 
        <?= htmlspecialchars($userData['first_name'] . " " . $userData['last_name']); ?>
    </p>

    <p class="text-gray-600 mb-6"><strong>Email:</strong> 
        <?= htmlspecialchars($userData['email']); ?>
    </p>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">

        <!-- Date -->
        <div>
            <label class="font-medium">Birthdate</label>
            <input type="date" name="date" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <!-- Address -->
        <div>
            <label class="font-medium">Complete Address</label>
            <input type="text" name="address" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <!-- Contact Number FIXED -->
        <div>
            <label class="font-medium">Contact Number</label>
            <input type="text" name="contact_number" maxlength="11" class="w-full mt-1 p-2 border rounded-lg" placeholder="09XXXXXXXXX" required>
        </div>

        <!-- ID Upload -->
        <div>
            <label class="font-medium">Valid ID (Front)</label>
            <input type="file" name="idIdentifier1" class="w-full mt-1" required>
        </div>

        <div>
            <label class="font-medium">Valid ID (Back)</label>
            <input type="file" name="idIdentifier2" class="w-full mt-1" required>
        </div>

        <!-- Checkbox -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="checkbox" id="checkboxTerms" required>
            <p class="text-gray-700">I agree to the <a href="#" class="text-blue-600 underline">Terms & Conditions</a>.</p>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg font-bold">
            Submit Verification
        </button>

    </form>
</div>

<br><br><br>

</body>
</html>
