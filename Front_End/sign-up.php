<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
require_once "../Back_End/Models/Users.php";

$signup_error = '';
$signup_success = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = new User();

    $registerAccount = $user->register(
        $_POST['first_name'],
        $_POST['last_name'],     
        $_POST['username'],
        $_POST['user_password'],    
        $_POST['email'],
        $_POST['contact_number']     
    );

    if($registerAccount){
        $signup_success = "Account created successfully. You can now log in.";
    }else{
        $signup_error = "Account creation failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Threadly Sign Up</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
  .chewy-font { font-family: 'Chewy', cursive; font-size: 3rem; font-weight: 400; }
</style>
</head>
<body class="flex items-center justify-center min-h-screen">

<div class="bg-white shadow-lg rounded-lg p-10 w-full max-w-md">
    <!-- Logo & Heading -->
    <div class="flex items-center justify-center mb-6 space-x-4">
        <div class="text-center">
            <span class="chewy-font text-4xl">Threadly</span>
            <p class="text-gray-500 mt-1">Create your account</p>
        </div>
    </div>

    <!-- Messages -->
    <?php if($signup_error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
            <?= $signup_error ?>
        </div>
    <?php endif; ?>
    <?php if($signup_success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
            <?= $signup_success ?>
        </div>
    <?php endif; ?>

    <!-- Signup Form -->
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700 mb-1" for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="username">Username</label>
            <input type="text" id="username" name="username" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="user_password">Password</label>
            <input type="password" id="user_password" name="user_password" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="email">Email</label>
            <input type="email" id="email" name="email" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="contact_number">Contact Number</label>
            <input type="number" id="contact_number" name="contact_number" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition">
            Create Account
        </button>
    </form>

    <!-- OR separator -->
    <div class="flex items-center my-4">
        <hr class="flex-1 border-gray-300">
        <span class="mx-2 text-gray-400 text-sm">OR</span>
        <hr class="flex-1 border-gray-300">
    </div>

    <!-- Social signup buttons -->
    <div class="flex flex-col space-y-3">
        <button class="flex items-center justify-center w-full py-2 border rounded-lg bg-white hover:bg-gray-100 transition">
            <img src="Images/google_icon.png" alt="Google" class="h-5 w-5 mr-2">
            <span class="text-gray-700 font-medium">Sign up with Google</span>
        </button>
        <button class="flex items-center justify-center w-full py-2 border rounded-lg bg-blue-600 hover:bg-blue-700 transition">
            <img src="Images/facebook_icon.png" alt="Facebook" class="h-5 w-5 mr-2">
            <span class="text-white font-medium">Sign up with Facebook</span>
        </button>
    </div>

    <!-- Login link -->
    <p class="mt-4 text-gray-500 text-sm text-center">
        Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Log in</a>
    </p>
</div>

</body>
</html>
