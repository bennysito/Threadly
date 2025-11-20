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
        error_log("✅ Registration successful for user: $username");
    } else {
        error_log("❌ Registration failed: " . $databaseStatement->error);
    }

    $databaseStatement->close();
    return $database_insert;
}

public function login($username, $user_password){
   $databaseStatement = $this->db->prepare("SELECT id, username, user_password FROM users WHERE username = ? LIMIT 1");

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
}
?>