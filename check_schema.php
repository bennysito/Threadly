<?php
require_once 'Back_End/Models/Database.php';

$db = new Database();
$conn = $db->threadly_connect;

$result = $conn->query('DESCRIBE products');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}
?>
