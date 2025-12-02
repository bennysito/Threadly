<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Users.php";

$login_error = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = new User();

    $loginAccount = $user->login(
        $_POST['username'],
        $_POST['user_password']
    );

    if($loginAccount){
        $_SESSION['username'] = $loginAccount['username'];
        $_SESSION['user_id'] = $loginAccount['id'];
        $_SESSION['first_name'] = $loginAccount['first_name'];
        $_SESSION['last_name'] = $loginAccount['last_name'];

        header("Location: index.php");
        exit;
    } else{
        $login_error = "Login unsuccessful. Please check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Threadly Login</title>
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
            <p class="text-gray-500 mt-1">Login to your account</p>
        </div>
    </div>

    <!-- Error Message -->
    <?php if($login_error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
            <?= $login_error ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" class="space-y-4">
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

        <!-- Forgot Password -->
        <div class="text-right">
            <a href="forgot_password.php" class="text-sm text-blue-500 hover:underline">Forgot password?</a>
        </div>

        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition">
            Log In
        </button>
    </form>

    <!-- OR separator -->
    <div class="flex items-center my-4">
        <hr class="flex-1 border-gray-300">
        <span class="mx-2 text-gray-400 text-sm">OR</span>
        <hr class="flex-1 border-gray-300">
    </div>

    <!-- Social login buttons -->
    <div class="flex flex-col space-y-3">
        <button class="flex items-center justify-center w-full py-2 border rounded-lg bg-white hover:bg-gray-100 transition">
            <img src="Images/google_icon.png" alt="Google" class="h-5 w-5 mr-2">
            <span class="text-gray-700 font-medium">Login with Google</span>
        </button>
        <button class="flex items-center justify-center w-full py-2 border rounded-lg bg-blue-600 hover:bg-blue-700 transition">
            <img src="Images/facebook_icon.png" alt="Facebook" class="h-5 w-5 mr-2">
            <span class="text-white font-medium">Login with Facebook</span>
        </button>
    </div>

    <!-- Signup link -->
    <p class="mt-4 text-gray-500 text-sm text-center">
        Don't have an account? <a href="sign-up.php" class="text-blue-500 hover:underline">Sign up</a>
    </p>
</div>

</body>
</html>