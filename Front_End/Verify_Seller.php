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

// Handle POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userAccess = new User();

    $path1 = "uploads/" . basename($_FILES["idIdentifier1"]["name"]);
    $path2 = "uploads/" . basename($_FILES["idIdentifier2"]["name"]);

    $checkbox = isset($_POST["checkbox"]) ? 1 : 0;

    move_uploaded_file($_FILES["idIdentifier1"]["tmp_name"], $path1);
    move_uploaded_file($_FILES["idIdentifier2"]["tmp_name"], $path2);

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
        echo "<script>alert('Unable to verify as seller.');</script>";
    } else {
        echo "<script>alert('Successfully requested to verify as seller.');</script>";
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
