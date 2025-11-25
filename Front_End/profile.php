<?php
// profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php"; // adjust path if needed

// Ensure logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database(); // your Database class that returns mysqli-like object
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $db->prepare("SELECT first_name, last_name, username, email, contact_number FROM users WHERE id = ?");
if (!$stmt) die("Prepare failed: " . $db->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Helper for safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Profile Details — Threadly</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .input-bg { background-color: #F5F5F5; border-radius: 6px; padding: .65rem .75rem; border: 1px solid transparent; }
    .input-bg:focus { outline: none; border-color: rgba(0,0,0,0.06); box-shadow: none; background-color: #F5F5F5; }
  </style>
</head>

<body class="bg-gray-100 min-h-screen">

  <?php include 'nav_bar.php'; ?>

  <main class="max-w-6xl mx-auto px-6 py-10">
    <div class="bg-white rounded-xl shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <nav class="text-sm text-gray-600 space-x-6">
          <a class="font-semibold text-gray-800" href="#">Account Details</a>
          <a href="#" class="hover:underline">My Reviews</a>
          <a href="#" class="hover:underline">My Wishlist</a>
          <a href="#" class="hover:underline">My Orders</a>
          <a href="Verify_Seller.php" class="hover:underline">Become a Seller</a>
        </nav>
      </div>
          <!-- Email -->
          <div>
            <label class="text-sm text-gray-600 block mb-2">Email Address</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>" class="w-full input-bg" readonly>
          </div>

          <!-- Contact Number -->
          <div>
            <label class="text-sm text-gray-600 block mb-2">Contact Number</label>
            <input type="text" name="contact_number" value="<?= e($user['contact_number'] ?? '') ?>" class="w-full input-bg" readonly>
          </div>

          <!-- Name row -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-gray-600 block mb-2">First Name</label>
              <input type="text" name="first_name" value="<?= e($user['first_name']) ?>" class="w-full input-bg" readonly>
            </div>

            <div>
              <label class="text-sm text-gray-600 block mb-2">Last Name</label>
              <input type="text" name="last_name" value="<?= e($user['last_name']) ?>" class="w-full input-bg" readonly>
            </div>
          </div>

          <!-- Username -->
          <div>
            <label class="text-sm text-gray-600 block mb-2">Username</label>
            <input type="text" name="username" value="<?= e($user['username']) ?>" class="w-full input-bg" readonly>
          </div>
        </form>
      </div>
    </div>
  </main>

</body>
</html>
