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

// Store original values for JS revert
$original_user = $user;

// Handle form submission
$success_msg = '';
$error_msg = '';
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
        // Update user data
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

        $stmt = $db->prepare($query);
        if (!$stmt) die("Prepare failed: " . $db->error);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success_msg = "Profile updated successfully!";
            // Refresh $user to reflect updated values
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
.input-bg { 
    background-color: #F5F5F5; 
    border-radius: 6px; 
    padding: .65rem .75rem; 
    border: 1px solid #bfb5b5ff; /* visible border */
    transition: all 0.2s ease;
}

.input-bg:focus { 
    outline: none; 
    border-color: #3B82F6; /* Tailwind blue-500 */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); /* subtle glow */
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
        // Revert values to original
        Object.keys(originalValues).forEach(key => {
            const field = document.querySelector(`[name=${key}]`);
            if (field) field.value = originalValues[key] ?? '';
        });
        document.querySelectorAll('[name=new_password], [name=confirm_password]').forEach(f => f.value = '');
    }
}
</script>
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

    <?php if ($success_msg): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= e($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= e($error_msg) ?></div>
    <?php endif; ?>

    <form method="POST">
      <!-- Email -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Email Address</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Contact Number -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Contact Number</label>
        <input type="text" name="contact_number" value="<?= e($user['contact_number'] ?? '') ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Name row -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="text-sm text-gray-600 block mb-2">First Name</label>
          <input type="text" name="first_name" value="<?= e($user['first_name']) ?>" class="w-full input-bg editable" readonly>
        </div>

        <div>
          <label class="text-sm text-gray-600 block mb-2">Last Name</label>
          <input type="text" name="last_name" value="<?= e($user['last_name']) ?>" class="w-full input-bg editable" readonly>
        </div>
      </div>

      <!-- Username -->
      <div class="mb-4">
        <label class="text-sm text-gray-600 block mb-2">Username</label>
        <input type="text" name="username" value="<?= e($user['username']) ?>" class="w-full input-bg editable" readonly>
      </div>

      <!-- Password fields -->
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
        <button type="button" id="editBtn" onclick="toggleEdit()" class="px-4 py-2 border border-black bg-white border-color-black text-black rounded hover:bg-black hover:text-white">Edit Details</button>
        <button type="submit" id="saveBtn" class="px-4 py-2 border border-whte bg-amber-600 text-white rounded hover:bg-white hover:text-black hover:border-black" style="display:inline-block;">Save Changes</button>
      </div>
    </form>
  </div>
</main>

<script>
// Hide Save button unless editing
document.querySelector('form').addEventListener('submit', function(e) {
    if (!editing) e.preventDefault();
});
</script>
</body>
</html>
