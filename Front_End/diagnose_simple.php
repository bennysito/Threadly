<?php
// Simple bid check
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Direct MySQLi connection
$conn = new mysqli("localhost", "root", "", "threadly");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Bids Diagnostic</h1>\n";

// 1. Check table structure
echo "<h2>1. Bids Table Columns:</h2>\n";
$result = $conn->query("DESCRIBE bids");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
echo "</pre>";

// 2. Count bids
echo "<h2>2. Total Bids:</h2>\n";
$result = $conn->query("SELECT COUNT(*) as cnt FROM bids");
$row = $result->fetch_assoc();
echo "Total: " . $row['cnt'] . " bids\n";

// 3. Show all bids with details
echo "<h2>3. All Bids (with product/user info):</h2>\n";
echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>\n";
echo "<tr><th>bid_id</th><th>user_id</th><th>product_id</th><th>session_id</th><th>bid_amount</th><th>bid_status</th><th>created_at</th></tr>\n";

$result = $conn->query("SELECT * FROM bids");
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['bid_id'] . "</td>";
    echo "<td>" . $row['user_id'] . "</td>";
    echo "<td>" . ($row['product_id'] ?? '-') . "</td>";
    echo "<td>" . ($row['session_id'] ?? '-') . "</td>";
    echo "<td>₱" . number_format($row['bid_amount'], 2) . "</td>";
    echo "<td>" . $row['bid_status'] . "</td>";
    echo "<td>" . $row['created_at'] . "</td>";
    echo "</tr>\n";
}
echo "</table>\n";

// 4. Check session info if exists
echo "<h2>4. Bidding Sessions:</h2>\n";
$result = $conn->query("SELECT * FROM bidding_session LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>\n";
    echo "<tr><th>session_id</th><th>product_id</th><th>status</th><th>start_time</th><th>end_time</th></tr>\n";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['session_id'] . "</td>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['start_time'] . "</td>";
        echo "<td>" . $row['end_time'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "No bidding sessions found\n";
}

$conn->close();
?>
