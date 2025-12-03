<?php
/**
 * DASHBOARD.PHP - USER DASHBOARD
 * 
 * Main user dashboard after successful login.
 * Only accessible to authenticated users.
 */

require_once 'config/config.php';
require_once 'config/functions.php';

// Redirect if not logged in
require_login();

// Get user data
$user_email = $_SESSION['user_email'] ?? 'User';
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding and responsive viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Page title -->
    <title>Reliwe Dashboard</title>
    
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
</head>
<body>
    <!-- Main Dashboard Container -->
    <div class="reliwe-dashboard">
        <!-- Dashboard Header -->
        <header class="reliwe-header">
            <div class="header-content">
                <a href="index.php" class="logo-link">
                    <h1>Reliwe</h1>
                </a>
                
                <nav class="dashboard-nav">
                    <a href="purchase.php" class="shop-link">Shop</a>
                    <?php if ($is_admin): ?>
                        <a href="admin.php" class="admin-link">Admin Panel</a>
                    <?php endif; ?>
                </nav>
                
                <div class="user-section">
                    <span class="user-email">Welcome, <?= htmlspecialchars($user_email) ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="reliwe-main">
            <div class="dashboard-title">
                <h2>DASHBOARD</h2>
            </div>

            <div class="dashboard-content">
                <!-- Main Action Buttons -->
                <div class="main-buttons">
                    <a href="#" class="dashboard-btn" id="orders-btn" onclick="showComingSoon(event)">
                        <span class="btn-icon">📦</span>
                        <span class="btn-text">MY ORDERS</span>
                    </a>
                    
                    <a href="#" class="dashboard-btn" id="progress-btn" onclick="showComingSoon(event)">
                        <span class="btn-icon">📊</span>
                        <span class="btn-text">MY PROGRESS</span>
                    </a>
                    
                    <a href="support.php" class="dashboard-btn" id="support-btn">
                        <span class="btn-icon">💬</span>
                        <span class="btn-text">GET SUPPORT</span>
                    </a>
                </div>

                <!-- Account Management -->
                <div class="account-section">
                    <div class="account-card">
                        <h3>MY ACCOUNT</h3>
                        
                        <div class="profile-circle">
                            <span class="profile-initial"><?= strtoupper(substr($user_email, 0, 1)) ?></span>
                        </div>
                        
                        <p class="account-email"><?= htmlspecialchars($user_email) ?></p>
                        
                        <a href="profile.php" class="edit-btn">Edit Information</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/global.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>