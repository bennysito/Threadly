<?php
session_start();
require_once "../Back_End/Models/Users.php";

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
        echo "<script>alert('Account created successfully.')</script>";
    }else{
        echo "<script>alert('Account created unsuccessfully.')</script>";        
    }
}

?>

<html>
    <head>
        <title> Create an Account </title>
    </head>
    <body>
        <form method = "POST" action = "sign-up.php">
            <input type="text" name="first_name" id="" required><br>
            <input type="text" name="last_name" id="" required><br>
            <input type="text" name="username" id="" required><br>
            <input type="password" name="user_password" id="" required><br>
            <input type="text" name="email" id="" required><br>
            <input type="number" name="contact_number" id="" required><br>
            <button type="submit" id="createAccount">Create Account</button>       
        </form>      
    </body>
</html>