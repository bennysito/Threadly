<?php 

require_once __DIR__ . "/Database.php";

class User {

    private $conn; // mysqli connection

    public function __construct() {
        $db = new Database();
        $this->conn = $db->get_connection();
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

        // Try to find user by username, email, or contact_number
        $stmt = $this->conn->prepare(
            "SELECT id, username, user_password, first_name, last_name 
             FROM users 
             WHERE username = ? OR email = ? OR contact_number = ? 
             LIMIT 1"
        );

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("sss", $username, $username, $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            return false; // No user found
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify password - handle both hashed and plain text for migration purposes
        $storedPassword = $user["user_password"];
        
        // First try password_verify (for hashed passwords)
        if (password_verify($password, $storedPassword)) {
            return $user;  // Login success with hashed password
        }
        
        // Fallback: check if password matches plain text (for old accounts without hashing)
        if ($password === $storedPassword) {
            // Auto-upgrade to hashed password
            $this->upgradePasswordHash($user['id'], $password);
            return $user;  // Login success with plain text password (will be upgraded)
        }

        return false; // Wrong password
    }

    /* ===========================
        UPGRADE PASSWORD HASH
    ============================ */
    private function upgradePasswordHash($user_id, $plainPassword) {
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("UPDATE users SET user_password = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $hashed, $user_id);
            $stmt->execute();
            $stmt->close();
            error_log("Password upgraded to hash for user ID: $user_id");
        }
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
        // Redirection is handled by the calling script
    }
}
?>