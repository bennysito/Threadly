<?php
session_start();
$_POST['product_id'] = 1;
$_SESSION['user_id'] = 1;

require_once __DIR__ . "/../Back_End/Models/Database.php";

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);

$db = new Database();
$conn = $db->threadly_connect;

// Check wishlist
$sql = "SELECT * FROM wishlist WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
echo "Wishlist records: " . $result->num_rows . "\n";
while ($row = $result->fetch_assoc()) {
    echo "Wishlist ID: " . $row['wishlist_id'] . "\n";
}
$stmt->close();

// Check wishlist_item
$sql2 = "SELECT * FROM wishlist_item WHERE product_id = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param('i', $product_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
echo "Wishlist items for product $product_id: " . $result2->num_rows . "\n";
while ($row = $result2->fetch_assoc()) {
    print_r($row);
}
$stmt2->close();

$db->close_db();
?>
