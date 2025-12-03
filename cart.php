<?php
/**
 * CART.PHP - SHOPPING CART
 * 
 * Session-based shopping cart with product management.
 */

require_once 'config/config.php';
require_once 'config/functions.php';

ensure_session();

// Initialize cart
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Clean up any malformed cart entries (from old API format)
foreach ($_SESSION['cart'] as $key => $value) {
    if (!is_int($value) && !is_numeric($value)) {
        unset($_SESSION['cart'][$key]);
    }
}

// Handle cart actions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);
    $is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';
    
    switch ($action) {
        case 'add':
            $quantity = intval($_POST['quantity'] ?? 1);
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            break;
            
        case 'update':
            $quantity = intval($_POST['quantity'] ?? 0);
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            break;
            
        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }
    
    // If AJAX request, return JSON response
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart_count' => get_cart_count(),
            'message' => 'Cart updated'
        ]);
        exit();
    }
    
    // Regular request - redirect to prevent form resubmission
    header('Location: cart.php');
    exit();
}

// Get cart items with product details from database
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders) AND status = 'active'");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $item_total = $product['price'] * $quantity;
        $subtotal += $item_total;
        
        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'total' => $item_total
        ];
    }
    $stmt->close();
}

// Calculate shipping (free over $200)
$shipping = $subtotal >= 200 ? 0 : 15;
$total = $subtotal + $shipping;

// Count total items
$cart_count = get_cart_count();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Reliwe</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/cart-page.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="cart-page">
        <!-- Simple header -->
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p><?= $cart_count ?> item<?= $cart_count !== 1 ? 's' : '' ?></p>
        </div>
        
        <!-- Cart Content -->
        <div class="cart-container">
            <!-- Cart Items -->
            <div class="cart-items">
                <?php if (empty($cart_items)): ?>
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added anything yet.</p>
                        <a href="purchase.php" class="shop-btn">Browse Products</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='Images/produkt1.avif'">
                            
                            <div class="item-details">
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                            </div>
                            
                            <div class="item-controls">
                                <!-- Decrease quantity -->
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="quantity" value="<?= $item['quantity'] - 1 ?>">
                                    <button type="submit" class="qty-btn">−</button>
                                </form>
                                
                                <span class="qty-display"><?= $item['quantity'] ?></span>
                                
                                <!-- Increase quantity -->
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
                                    <button type="submit" class="qty-btn">+</button>
                                </form>
                                
                                <!-- Remove item -->
                                <form method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                            
                            <div class="item-total">$<?= number_format($item['total'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
            
            <div class="summary-row">
                <span>Shipping</span>
                <span><?= $shipping === 0 ? 'FREE' : '$' . number_format($shipping, 2) ?></span>
            </div>
            
            <?php if ($subtotal > 0 && $subtotal >= 200): ?>
                <div class="free-shipping">🎉 Free shipping applied!</div>
            <?php elseif ($subtotal > 0): ?>
                <div class="shipping-notice">Add $<?= number_format(200 - $subtotal, 2) ?> more for free shipping</div>
            <?php endif; ?>
            
            <div class="summary-row total">
                <span>Total</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            
            <?php if (!empty($cart_items)): ?>
                <button class="checkout-btn" onclick="alert('Checkout coming soon!')">Proceed to Checkout</button>
            <?php endif; ?>
            
            <a href="purchase.php" class="continue-link">← Continue Shopping</a>
            
            <div class="trust-badges">
                <span>🔒 Secure</span>
                <span>↩ 30-Day Returns</span>
                <span>✓ Warranty</span>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
