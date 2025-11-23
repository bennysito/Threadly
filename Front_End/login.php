<?php
session_start();
require_once "../Back_End/Models/Users.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = new User();

    $loginAccount = $user->login(
        $_POST['username'],
        $_POST['user_password']
    );

    if($loginAccount){
        $_SESSION['username'] = $loginAccount['username'];
        $_SESSION['user_id'] = $loginAccount['id'];

        header("Location: index.php");
        exit;
    } else{
        echo "<script>alert('Logged-in unsuccessfully.')</script>";     
    }
}
?>

<html>
    <head>
        <title>
            Login
        </title>
    </head>
    <body>
        <form action="" method = "POST">
            <input type="text" name="username" id="username"><br>
            <input type="password" name="user_password" id="password"><br>
            <button type="submit">Log-in</button>
        </form>
    </body>
</html>