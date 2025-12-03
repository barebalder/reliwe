<?php
/**
 * HEADER NAVIGATION COMPONENT
 * 
 * Main navigation header for all public pages.
 * Uses PHP session for cart count - no JavaScript API needed.
 */

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get cart count from session
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<!-- HEADER -->
<header>
    <div class="logo">
        <a href="index.php">Reliwe</a>
    </div>
    
    <nav>
        <a href="purchase.php">PURCHASE</a>
        <a href="technology.php">TECHNOLOGY</a>
        <a href="about.php">ABOUT US</a>
        <a href="support.php">SUPPORT</a>
        <?php if ($is_logged_in): ?>
            <a href="dashboard.php">DASHBOARD</a>
        <?php else: ?>
            <a href="login.php">LOGIN</a>
        <?php endif; ?>
    </nav>
    
    <div class="header-actions">
        <a href="cart.php" class="cart-btn" style="text-decoration: none;">
            <span class="cart-icon">🛒</span>
            <?php if ($cart_count > 0): ?>
                <span class="cart-badge" style="opacity: 1; transform: scale(1);"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>

<script src="js/header.js"></script>
