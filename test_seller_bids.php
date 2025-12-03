<?php
require_once 'Back_End/Models/Database.php';
$db = new Database();
$conn = $db->threadly_connect;
?>
<html>
<body>
<pre>
<?php
echo "=== Users Table ===\n";
$users = $conn->query("SELECT id, username FROM users WHERE id IN (20,21,23)");
while ($row = $users->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Username: " . $row['username'] . "\n";
}

echo "\n=== Direct Test for Seller 28 ===\n";
$seller_id = 28;
$result = $conn->query("
    SELECT 
        b.bid_id, 
        b.session_id,
        bs.product_id,
        b.user_id,
        u.username
    FROM bids b
    LEFT JOIN bidding_session bs ON b.session_id = bs.session_id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE bs.product_id IN (SELECT product_id FROM products WHERE seller_id = $seller_id)
");

echo "Found " . $result->num_rows . " bids\n";
while ($row = $result->fetch_assoc()) {
    echo "Bid " . $row['bid_id'] . ": user_id=" . $row['user_id'] . ", username=" . $row['username'] . ", product=" . $row['product_id'] . "\n";
}

echo "\n=== Seller's Products ===\n";
$prods = $conn->query("SELECT product_id, product_name FROM products WHERE seller_id = $seller_id");
echo "Found " . $prods->num_rows . " products\n";
while ($row = $prods->fetch_assoc()) {
    echo "Product " . $row['product_id'] . ": " . $row['product_name'] . "\n";
}
?>
</pre>
</body>
</html>
