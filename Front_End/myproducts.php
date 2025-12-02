<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../Back_End/Models/Database.php";

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<div class=\"p-4 bg-red-50 text-red-700 rounded\">Please log in to view your products.</div>";
    return;
}

$db = new Database();
$conn = $db->threadly_connect;

// Fetch seller's products from database
$sellerProducts = [];
$sql = "SELECT product_id, product_name, description, image_url, price, quantity FROM products WHERE seller_id = ? ORDER BY product_id DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sellerProducts[] = $row;
    }
    $stmt->close();
}

?>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        padding: 1rem 0;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        transition: all 0.25s ease;
        cursor: pointer;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 36px rgba(0,0,0,0.12);
    }
    
    .product-image {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: #f3f4f6;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .product-info {
        padding: 0.9rem;
    }
    
    .product-price {
        font-weight: 700;
        color: #111;
        font-size: 1.05rem;
    }
    
    .product-name {
        margin-top: 0.45rem;
        color: #444;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-desc {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-stock {
        margin-top: 0.8rem;
        padding-top: 0.8rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stock-label {
        font-size: 0.85rem;
        color: #666;
        font-weight: 600;
    }
    
    .stock-input {
        width: 70px;
        padding: 0.4rem 0.6rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    
    .stock-input:focus {
        outline: none;
        border-color: #b45309;
        box-shadow: 0 0 0 2px rgba(180, 83, 9, 0.1);
    }
    
    .edit-btn, .save-btn {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .edit-btn {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .edit-btn:hover {
        background-color: #e5e7eb;
    }
    
    .save-btn {
        background-color: #b45309;
        color: white;
    }
    
    .save-btn:hover {
        background-color: #92400e;
    }
    
    .details-edit-btn {
        padding: 0.4rem 0.8rem;
        background-color: #1f2937;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .details-edit-btn:hover {
        background-color: #111;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 1rem;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
    
    .close-btn:hover {
        color: #111;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #b45309;
        box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .modal-btn-cancel {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .modal-btn-cancel:hover {
        background-color: #e5e7eb;
    }
    
    .modal-btn-save {
        background-color: #b45309;
        color: white;
    }
    
    .modal-btn-save:hover {
        background-color: #92400e;
    }
</style>

<?php if (empty($sellerProducts)): ?>
    <div class="p-4 bg-gray-50 rounded text-center text-gray-600">
        You haven't added any products yet. 
        <a href="#" onclick="showTab('add'); return false;" class="text-amber-600 font-semibold hover:underline">
            Add your first product →
        </a>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($sellerProducts as $prod): ?>
            <div class="product-card">
                <a href="product_info.php?id=<?= intval($prod['product_id']) ?>" class="block">
                    <div class="product-image">
                        <?php if (!empty($prod['image_url'])): ?>
                            <img src="uploads/<?= e($prod['image_url']) ?>" alt="<?= e($prod['product_name'] ?? '') ?>">
                        <?php else: ?>
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f3f4f6; color:#999;">
                                No Image
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-price">₱<?= number_format((float)($prod['price'] ?? 0), 2) ?></div>
                        <p class="product-name"><?= e($prod['product_name'] ?? 'Product') ?></p>
                        <?php if (!empty($prod['description'])): ?>
                            <p class="product-desc"><?= e($prod['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="product-stock" onclick="event.stopPropagation();">
                    <span class="stock-label">Stock:</span>
                    <div id="stock-display-<?= $prod['product_id'] ?>" style="display: flex; gap: 0.5rem; align-items: center;">
                        <span class="text-gray-900 font-semibold"><?= intval($prod['quantity'] ?? 0) ?></span>
                        <button class="edit-btn" onclick="editStock(<?= $prod['product_id'] ?>, <?= intval($prod['quantity'] ?? 0) ?>)">Edit</button>
                    </div>
                    <div id="stock-edit-<?= $prod['product_id'] ?>" style="display: none; gap: 0.5rem; align-items: center;">
                        <input type="number" class="stock-input" id="stock-input-<?= $prod['product_id'] ?>" value="<?= intval($prod['quantity'] ?? 0) ?>" min="0">
                        <button class="save-btn" onclick="saveStock(<?= $prod['product_id'] ?>)">Save</button>
                    </div>
                </div>
                <div style="padding: 0.9rem; text-align: center; border-top: 1px solid #e5e7eb;">
                    <button class="details-edit-btn" onclick="openEditModal(<?= $prod['product_id'] ?>, <?= htmlspecialchars(json_encode([
                        'product_name' => $prod['product_name'],
                        'description' => $prod['description'],
                        'price' => $prod['price'],
                        'quantity' => $prod['quantity'],
                        'image_url' => $prod['image_url']
                    ])) ?>)">Edit Details</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Edit Product Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Product Details</h2>
            <button class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" onsubmit="saveProductDetails(event)">
            <input type="hidden" id="editProductId" value="">
            
            <div class="form-group">
                <label for="editProductName">Product Name</label>
                <input type="text" id="editProductName" name="product_name" required>
            </div>
            
            <div class="form-group">
                <label for="editPrice">Price (₱)</label>
                <input type="number" id="editPrice" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="editQuantity">Quantity</label>
                <input type="number" id="editQuantity" name="quantity" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="editDescription">Description</label>
                <textarea id="editDescription" name="description" required></textarea>
            </div>
            
            <div class="modal-buttons">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editStock(productId, currentStock) {
    document.getElementById('stock-display-' + productId).style.display = 'none';
    const editDiv = document.getElementById('stock-edit-' + productId);
    editDiv.style.display = 'flex';
    document.getElementById('stock-input-' + productId).focus();
}

function saveStock(productId) {
    const quantity = parseInt(document.getElementById('stock-input-' + productId).value) || 0;
    
    // Send AJAX request to update stock
    fetch('update_product_stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            document.getElementById('stock-display-' + productId).querySelector('span').textContent = quantity;
            document.getElementById('stock-display-' + productId).style.display = 'flex';
            document.getElementById('stock-edit-' + productId).style.display = 'none';
        } else {
            alert('Error updating stock: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function openEditModal(productId, productData) {
    document.getElementById('editProductId').value = productId;
    document.getElementById('editProductName').value = productData.product_name;
    document.getElementById('editPrice').value = productData.price;
    document.getElementById('editQuantity').value = productData.quantity;
    document.getElementById('editDescription').value = productData.description;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function saveProductDetails(e) {
    e.preventDefault();
    
    const productId = document.getElementById('editProductId').value;
    const productName = document.getElementById('editProductName').value;
    const price = parseFloat(document.getElementById('editPrice').value);
    const quantity = parseInt(document.getElementById('editQuantity').value);
    const description = document.getElementById('editDescription').value;
    
    // Validate inputs
    if (!productName || price <= 0 || quantity < 0 || !description) {
        alert('Please fill in all fields with valid values');
        return;
    }
    
    // Send AJAX request to update product
    fetch('update_product_details.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + 
              '&product_name=' + encodeURIComponent(productName) + 
              '&price=' + price + 
              '&quantity=' + quantity + 
              '&description=' + encodeURIComponent(description)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product details updated successfully!');
            closeEditModal();
            // Reload the page to see changes
            location.reload();
        } else {
            alert('Error updating product: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>
