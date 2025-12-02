<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$bagItems = $_SESSION['shopping_bag'] ?? [];
$hasItems = !empty($bagItems);
$totalSubtotal = 0;

foreach ($bagItems as $item) {
    $totalSubtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
}

echo json_encode([
    'success' => true,
    'hasItems' => $hasItems,
    'items' => $bagItems,
    'subtotal' => $totalSubtotal
]);