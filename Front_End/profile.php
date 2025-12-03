<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php";

// Ensure logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$db = new Database();
$conn = $db->get_connection();
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, username, email, contact_number, role FROM users WHERE id = ?");
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Check if user is a seller
$isSeller = ($user['role'] === 'seller');

$original_user = $user;

$success_msg = '';
$error_msg = '';

// Handle Save Changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password && $new_password !== $confirm_password) {
        $error_msg = "New password and confirm password do not match!";
    } else {

        // Generate dynamic query
        $query = "UPDATE users SET first_name=?, last_name=?, username=?, email=?, contact_number=?";
        $params = [$first_name, $last_name, $username, $email, $contact_number];
        $types = "sssss";

        if ($new_password) {
            $query .= ", user_password=?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE id=?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success_msg = "Profile updated successfully!";

            // Refresh user array
            $user = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'email' => $email,
                'contact_number' => $contact_number
            ];
            $original_user = $user;

        } else {
            $error_msg = "Update failed: " . $stmt->error;
        }
    }
}

// Safe HTML output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<meta charset="utf-8" />
<title>Profile Details — Threadly</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
    font-family: 'Inter', sans-serif;
}
.font-chewy {
    font-family: 'Chewy', cursive;
}
.input-bg {
    background-color: #F5F5F5;
    border-radius: 6px;
    padding: .65rem .75rem;
    border: 1px solid #bfb5b5ff;
    transition: all 0.2s ease;
}
.input-bg:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    background-color: #fff;
}
</style>

<script>
let editing = false;
const originalValues = <?= json_encode($original_user) ?>;

function toggleEdit() {
    editing = !editing;

    document.querySelectorAll('.editable').forEach(el => {
        el.readOnly = !editing;
        el.classList.toggle('bg-white', editing);
        el.classList.toggle('bg-gray-100', !editing);
    });

    document.getElementById('passwordFields').style.display = editing ? 'block' : 'none';

    const btn = document.getElementById('editBtn');
    btn.textContent = editing ? 'Cancel' : 'Edit Details';

    if (!editing) {
        Object.keys(originalValues).forEach(key => {
            const field = document.querySelector(`[name=${key}]`);
            if (field) field.value = originalValues[key] ?? '';
        });
        document.querySelectorAll('[name=new_password], [name=confirm_password]')
            .forEach(f => f.value = '');
    }
}
</script>
</head>

<body class="bg-gray-100 min-h-screen">

<?php include 'nav_bar.php'; ?>

    <?php require "wishlist_panel.php"; ?>
    <?php require "notification_panel.php"; ?> 
    <?php require "add_to_bag.php"; ?> 
    <?php require "messages_panel.php"; ?> 

<main class="max-w-6xl mx-auto px-6 py-10">
  <div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-6">
      <nav class="text-sm text-gray-600 space-x-6">
        <a class="font-semibold text-gray-800" href="#">Account Details</a>
        <a href="#" class="hover:underline">My Reviews</a>
        <a href="#" class="hover:underline">My Wishlist</a>
        <a href="#" class="hover:underline">My Orders</a>
        <?php if ($isSeller): ?>
          <a href="seller_dashboard.php" class="hover:underline">Seller Center</a>
        <?php else: ?>
          <a href="Verify_Seller.php" class="hover:underline">Become a Seller</a>
        <?php endif; ?>
      </nav>
    </div>

    <?php if ($success_msg): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= e($success_msg) ?></div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= e($error_msg) ?></div>
    <?php endif; ?>

    <form method="POST" id="profileForm">

      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Email Address</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" class="w-full input-bg editable bg-gray-100" readonly>
      </div>

      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Contact Number</label>
        <input type="text" name="contact_number" value="<?= e($user['contact_number']) ?>" class="w-full input-bg editable bg-gray-100" readonly>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="text-sm text-gray-600 block mb-2">First Name</label>
          <input type="text" name="first_name" value="<?= e($user['first_name']) ?>" class="w-full input-bg editable bg-gray-100" readonly>
        </div>
        <div>
          <label class="text-sm text-gray-600 block mb-2">Last Name</label>
          <input type="text" name="last_name" value="<?= e($user['last_name']) ?>" class="w-full input-bg editable bg-gray-100" readonly>
        </div>
      </div>

      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Username</label>
        <input type="text" name="username" value="<?= e($user['username']) ?>" class="w-full input-bg editable bg-gray-100" readonly>
      </div>

      <div id="passwordFields" style="display:none;">
        <div class="mb-4">
          <label class="text-sm text-gray-600 block mb-2">New Password</label>
          <input type="password" name="new_password" class="w-full input-bg editable">
        </div>
        <div class="mb-4">
          <label class="text-sm text-gray-600 block mb-2">Confirm Password</label>
          <input type="password" name="confirm_password" class="w-full input-bg editable">
        </div>
      </div>

      <div class="flex space-x-4">
        <button type="button" id="editBtn" onclick="toggleEdit()" class="px-4 py-2 border border-black bg-white text-black rounded hover:bg-black hover:text-white">
          Edit Details
        </button>
        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-white hover:text-black hover:border-black border">
          Save Changes
        </button>
      </div>

    </form>
  </div>
</main>

<script>
// Prevent saving unless editing mode is active
// FIXED: Only target the profile form by ID, not all forms on the page
document.getElementById('profileForm').addEventListener('submit', function(e) {
    if (!editing) e.preventDefault();
});
</script>

<script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Navbar and Panel Toggles ---
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            if(profileBtn) {
                profileBtn.addEventListener('click', () => {
                    profileDropdown.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>