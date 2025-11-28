<?php 

require_once __DIR__ . "/Database.php";

class User{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

public function register($first_name, $last_name, $username, $password, $email, $contact_number){
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $databaseStatement = $this->db->prepare("INSERT INTO users (first_name, last_name, username, user_password, email, contact_number) VALUES (?,?,?,?,?,?)");

    if (!$databaseStatement) {
        error_log("Prepare failed: " . $this->db->threadly_connect->error);
        return false;
    }

    $databaseStatement->bind_param("ssssss", $first_name, $last_name, $username, $hashed , $email, $contact_number);
    $database_insert = $databaseStatement->execute();

    if ($database_insert) {
        error_log(" Registration successful for user: $username");
    } else {
        error_log(" Registration failed: " . $databaseStatement->error);
    }

    $databaseStatement->close();
    return $database_insert;
}

public function login($username, $user_password){
   $databaseStatement = $this->db->prepare("SELECT id, username, user_password, first_name, last_name FROM users WHERE username = ? LIMIT 1");

    if (!$databaseStatement) {
        error_log("Prepare failed: " . $this->db->threadly_connect->error);
        return false;
    }

    $databaseStatement->bind_param("s", $username);
    $databaseStatement->execute();

    $usernameLocated = $databaseStatement->get_result();

    if($usernameLocated->num_rows === 1){
        $user = $usernameLocated->fetch_assoc();

        if(password_verify($user_password, $user['user_password'])){
            return $user;
        }
        return False;
    }
}

    public function authenticate_seller($user_id, $birthdate, $contact_number, $address, $id_front, $id_back, $agree_terms) {
        $db = new Database();

        $stmt = $db->prepare(
            "INSERT INTO verify_seller(user_id, birthdate, contact_number, address, id_front, id_back, agree_terms) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("isssssi", $user_id, $birthdate, $contact_number, $address, $id_front, $id_back, $agree_terms);

        $result = $stmt->execute();
        $stmt->close();
        $db->close_db();

        return $result;
    }

    public function logoutUser(){
    $_SESSION = [];
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
    }
}
?>