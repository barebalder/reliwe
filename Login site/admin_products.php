<?php
/**
 * ADMIN_PRODUCTS.PHP - PRODUCT MANAGEMENT
 * 
 * Admin interface for managing products with CRUD operations.
 */

require_once 'config/admin_functions.php';

// Initialize admin page (handles auth, session, includes)
$admin_profile = init_admin_page($conn);

// Process form submissions (add/edit/delete)
$result = ['success' => '', 'error' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = process_product_action($conn, $_POST, $_FILES, $_SESSION['user_id']);
}

// Fetch all products
$products = $conn->query("SELECT * FROM products ORDER BY category, name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Reliwe Admin</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
    <link rel="stylesheet" href="css/admin_products.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <?php render_admin_header('Product Management', 'Manage products, images and inventory', 'admin.php', 
            '<button class="btn-add" onclick="openAddModal()">+ Add Product</button>'); ?>
        
        <?php render_alert($result['success']); ?>
        <?php render_alert($result['error'], 'error'); ?>
        
        <div class="admin-section">
            <div class="section-header">
                <h2 class="section-title">All Products (<?= count($products) ?>)</h2>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <h3>No products yet</h3>
                    <p>Click "+ Add Product" to create your first product.</p>
                </div>
            <?php else: ?>
                <table class="admin-table products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): 
                            $gallery_count = !empty($p['images']) ? count(json_decode($p['images'], true) ?: []) : 0;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="product-thumb" alt="" onerror="this.src='Images/produkt1.avif'">
                                <?php if ($gallery_count > 0): ?>
                                    <span class="gallery-count">+<?= $gallery_count ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="product-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
                            </td>
                            <td class="price">$<?= number_format($p['price'], 2) ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td><span class="badge badge-<?= $p['category'] ?>"><?= ucfirst($p['category']) ?></span></td>
                            <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td class="actions">
                                <button class="btn btn-secondary" onclick='editProduct(<?= json_encode($p) ?>)'>Edit</button>
                                <button class="btn btn-danger" onclick="deleteProduct(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/product_modal.php'; ?>
    <script src="js/admin_products.js"></script>
</body>
</html>
