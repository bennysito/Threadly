<?php
// Debug: Check if the edit is actually being submitted
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../Back_End/Models/Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    error_log("=== EDIT PRODUCT DEBUG ===");
    error_log("POST data: " . json_encode($_POST));
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $pname = trim($_POST['product_name'] ?? '');
    $pprice = floatval($_POST['price'] ?? 0);
    $pquantity = intval($_POST['quantity'] ?? 0);
    $pdescription = trim($_POST['description'] ?? '');
    
    error_log("Parsed values - ID: $product_id, Name: $pname, Price: $pprice, Qty: $pquantity");
    
    $user_id = $_SESSION['user_id'] ?? 0;
    
    // Check database
    $db = new Database();
    $conn = $db->threadly_connect;
    
    $verifyStmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND seller_id = ?");
    $verifyStmt->bind_param('ii', $product_id, $user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    
    error_log("Verify result rows: " . $verifyResult->num_rows);
    if ($verifyResult->num_rows > 0) {
        $row = $verifyResult->fetch_assoc();
        error_log("Found product: " . json_encode($row));
    }
    
    $verifyStmt->close();
}
?>
