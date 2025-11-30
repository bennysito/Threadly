<?php 

require_once __DIR__ . "/Database.php";

class User {

    private $conn; // mysqli connection

    public function __construct() {
        $db = new Database();
        $this->conn = $db->get_connection();  // <-- FIX: Use mysqli connection
    }

    /* ===========================
       REGISTER
    ============================ */
    public function register($first_name, $last_name, $username, $password, $email, $contact_number) {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (first_name, last_name, username, user_password, email, contact_number) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ssssss", $first_name, $last_name, $username, $hashed, $email, $contact_number);
        $success = $stmt->execute();

        if ($success) {
            error_log("Registration successful for user: $username");
        } else {
            error_log("Registration failed: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }


    /* ===========================
       LOGIN
    ============================ */
    public function login($username, $password) {

        $stmt = $this->conn->prepare(
            "SELECT id, username, user_password, first_name, last_name 
             FROM users WHERE username = ? LIMIT 1"
        );

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            return false; // No user found
        }

        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["user_password"])) {
            return $user;  // Login success
        }

        return false; // Wrong password
    }


    /* ===========================
       SELLER AUTHENTICATION
    ============================ */
    public function authenticate_seller(
        $user_id, $birthdate, $contact_number, $address, 
        $id_front, $id_back, $agree_terms
    ) {

        $stmt = $this->conn->prepare(
            "INSERT INTO verify_seller 
            (user_id, birthdate, contact_number, address, id_front, id_back, agree_terms)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("isssssi",
            $user_id, $birthdate, $contact_number, $address,
            $id_front, $id_back, $agree_terms
        );

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }


    /* ===========================
       LOGOUT
    ============================ */
    public function logoutUser() {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>
