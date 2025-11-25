<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php 
require_once "../Back_End/Models/Users.php";

if(!isset($_SESSION['user_id'])){
    echo "<script><alert>Log-in credentials not found.</alert></script>";
}

$db = new Database();

$stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if($_SERVER["REQUEST_METHOD"] == "POST"){
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

    if(!$authentication){
        echo "<script><alert>Unable to verify as seller.</alert></script>";
    } else{
        echo "<script><alert>Successfully requested to verify as seller.</alert></script>";
    }
}


?>

<html>
    <head>
        <title>Seller Authentication</title>
    </head>
    <body>
        <form method = "POST" enctype="multipart/form-data">
        <div id="sellerForms">
            <p id="sellerName">Name of the Seller fetch from Database</p><br>
            <p id="sellerName">Email of the Seller fetch from Database</p><br>
            <input type="date" name="date" id="sellerDate"><br>
            <input type="text" name="address" id="address"><br>
            <input type="number" name="" id="sellerContactNumber"><br>  
            <input type="file" name="idIdentifier1" id="idIdentifier1"><br>
            <input type="file" name="idIdentifier1" id="idIdentifier2"><br>
            <input type="checkbox" name="checkbox" id="checkboxTerms"><p>I agree to the Terms and condition.</p>
            <button type="submit">Request</button>
            <!--Route dayon sa subscription plans kamo na bahala ana -->
        </div>
        </forms>
    </body>
</html>