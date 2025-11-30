<?php
// Make sure this file can find your general Database class
require_once "Database.php"; 

function getWishlistItems($user_id) {
    $db = new Database();
    $conn = $db->get_connection();
    $items = [];

    // SQL Query to fetch the item details
    $sql = "
        SELECT 
            wi.wishlist_item_id, 
            pv.stock_quantity,
            pv.price,
            pv.size,
            p.product_name,
            p.main_image_url, 
            p.original_price
        FROM 
            wishlist w
        JOIN 
            wishlist_item wi ON w.wishlist_id = wi.wishlist_id
        JOIN 
            product_variants pv ON wi.variant_id = pv.variant_id 
        JOIN 
            products p ON pv.product_id = p.product_id 
        WHERE 
            w.user_id = ?
        ORDER BY 
            wi.added_at DESC;
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        $stmt->close();
    } 
    
    $db->close_db();
    return $items;
}

// You can add other functions here, like:
function deleteWishlistItemFromDB($item_id, $user_id) {
    // ... logic for your delete_wishlist_item.php
}

?>