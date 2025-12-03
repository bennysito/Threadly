<?php
/**
 * add_sample_products.php
 * Adds sample clothing products to the database
 * Run this file once, then delete it for security
 */

require_once __DIR__ . "/Models/Database.php";

$db = new Database();
$conn = $db->threadly_connect;

// Sample products data
$products = [
    [
        'name' => 'Classic Black Hoodie',
        'description' => 'Comfortable and stylish black hoodie perfect for any season. Made from premium cotton blend.',
        'price' => 1299.99,
        'quantity' => 15,
        'category' => 'Hoodies',
        'image' => 'baggy_pants.png',
        'seller_id' => 1,
        'bidding' => 1
    ],
    [
        'name' => 'Premium White T-Shirt',
        'description' => 'High-quality white cotton t-shirt. Classic design that goes with everything.',
        'price' => 499.99,
        'quantity' => 25,
        'category' => 'Shirts',
        'image' => 'baggy_pants.png',
        'seller_id' => 1,
        'bidding' => 0
    ],
    [
        'name' => 'Vintage Denim Jacket',
        'description' => 'Stylish vintage-inspired denim jacket with authentic distressing. Perfect for casual wear.',
        'price' => 2499.99,
        'quantity' => 8,
        'category' => 'Jackets',
        'image' => 'baggy_pants.png',
        'seller_id' => 1,
        'bidding' => 1
    ],
    [
        'name' => 'Comfortable Cotton Pants',
        'description' => 'Relaxed fit cotton pants ideal for everyday comfort. Available in multiple colors.',
        'price' => 1099.99,
        'quantity' => 20,
        'category' => 'Pants',
        'image' => 'baggy_pants.png',
        'seller_id' => 2,
        'bidding' => 0
    ],
    [
        'name' => 'Casual Sneakers',
        'description' => 'Modern casual sneakers with comfortable cushioning. Great for all-day wear.',
        'price' => 1799.99,
        'quantity' => 12,
        'category' => 'Shoes',
        'image' => 'baggy_pants.png',
        'seller_id' => 2,
        'bidding' => 1
    ],
    [
        'name' => 'Elegant Summer Dress',
        'description' => 'Light and elegant summer dress perfect for warm weather. Breathable fabric.',
        'price' => 1599.99,
        'quantity' => 10,
        'category' => 'Dresses',
        'image' => 'baggy_pants.png',
        'seller_id' => 2,
        'bidding' => 0
    ],
    [
        'name' => 'Cozy Winter Beanie',
        'description' => 'Warm and cozy beanie for cold winter days. One size fits all.',
        'price' => 399.99,
        'quantity' => 30,
        'category' => 'Accessories',
        'image' => 'baggy_pants.png',
        'seller_id' => 3,
        'bidding' => 0
    ],
    [
        'name' => 'Stylish Leather Belt',
        'description' => 'Premium leather belt with quality buckle. Classic design suitable for any outfit.',
        'price' => 799.99,
        'quantity' => 18,
        'category' => 'Accessories',
        'image' => 'baggy_pants.png',
        'seller_id' => 3,
        'bidding' => 1
    ],
    [
        'name' => 'Athletic Performance Shorts',
        'description' => 'Breathable athletic shorts perfect for workouts and sports activities.',
        'price' => 899.99,
        'quantity' => 16,
        'category' => 'Shorts',
        'image' => 'baggy_pants.png',
        'seller_id' => 3,
        'bidding' => 0
    ],
    [
        'name' => 'Fashion Sunglasses',
        'description' => 'Trendy UV protection sunglasses. Perfect accessory for sunny days.',
        'price' => 1199.99,
        'quantity' => 22,
        'category' => 'Accessories',
        'image' => 'baggy_pants.png',
        'seller_id' => 1,
        'bidding' => 1
    ]
];

// Insert products
$inserted = 0;
$failed = 0;

foreach ($products as $product) {
    $stmt = $conn->prepare("
        INSERT INTO products (product_name, description, price, quantity, category, image_url, seller_id, bidding)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt) {
        $stmt->bind_param(
            'ssdiisii',
            $product['name'],
            $product['description'],
            $product['price'],
            $product['quantity'],
            $product['category'],
            $product['image'],
            $product['seller_id'],
            $product['bidding']
        );
        
        if ($stmt->execute()) {
            $inserted++;
            echo "✓ Added: " . $product['name'] . "<br>";
        } else {
            $failed++;
            echo "✗ Failed: " . $product['name'] . " - " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        $failed++;
        echo "✗ Prepare failed for: " . $product['name'] . "<br>";
    }
}

echo "<hr>";
echo "<strong>Summary:</strong><br>";
echo "Inserted: $inserted products<br>";
echo "Failed: $failed products<br>";
echo "<br><strong style='color: green;'>Sample products added successfully!</strong><br>";
echo "<strong style='color: red;'>Please delete this file (add_sample_products.php) for security!</strong>";

$db->close_db();
?>
