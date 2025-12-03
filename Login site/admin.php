<?php
/**
 * ADMIN.PHP - ADMIN CONTROL PANEL
 * 
 * Main admin dashboard with system overview and management tools.
 */

require_once 'config/admin_functions.php';

// Initialize admin page (handles auth, session, includes)
$admin_profile = init_admin_page($conn);
$admin_name = $admin_profile['first_name'] ?? 'Admin';

/**
 * GET PENDING CONTACT MESSAGES
 * 
 * Fetches unresolved contact form messages.
 */
function get_contact_messages($conn, $status = 'new') {
    $stmt = $conn->prepare("
        SELECT m.*, u.email as user_email 
        FROM contact_messages m 
        LEFT JOIN users u ON m.user_id = u.id 
        WHERE m.status = ? 
        ORDER BY m.created_at DESC
    ");
    
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    $stmt->close();
    return $messages;
}

// Fetch all dashboard statistics efficiently
$stats = get_dashboard_stats($conn);
$recent_activity = get_recent_activity($conn, 10);
$pending_messages = get_contact_messages($conn, 'new');
$pending_count = count($pending_messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Reliwe</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="admin-branding">
                <a href="index.php" class="admin-logo">Reliwe</a>
                <span class="admin-badge">ADMIN</span>
            </div>
            <nav class="admin-nav">
                <a href="index.php">Home</a>
                <a href="purchase.php">Shop</a>
                <a href="dashboard.php">My Dashboard</a>
            </nav>
            <div class="admin-info">
                <div class="admin-email">Welcome, <?= htmlspecialchars($admin_name) ?></div>
                <a href="logout.php" class="admin-btn admin-logout">Logout</a>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?= $stats['users']['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">New Today</div>
                <div class="stat-value"><?= $stats['users']['today'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Now</div>
                <div class="stat-value"><?= $stats['users']['active'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Support Requests</div>
                <div class="stat-value"><?= $pending_count ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Products</div>
                <div class="stat-value"><?= $stats['products']['active'] ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-section">
            <div class="section-header">
                <h2 class="section-title">Quick Actions</h2>
            </div>
            <div class="quick-actions">
                <a href="admin_products.php" class="action-btn">Manage Products</a>
                <a href="admin_users.php" class="action-btn">Manage Users</a>
                <a href="admin_activity.php" class="action-btn">View Activity Log</a>
                <a href="admin_messages.php" class="action-btn">Support Messages</a>
                <a href="admin_settings.php" class="action-btn">System Settings</a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="admin-section">
            <div class="section-header">
                <h2 class="section-title">Recent Activity</h2>
                <a href="admin_activity.php" class="admin-btn admin-btn-primary">View All</a>
            </div>
            
            <?php if (empty($recent_activity)): ?>
                <p class="empty-state">No recent activity</p>
            <?php else: ?>
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>User</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activity as $activity): ?>
                            <tr>
                                <td>
                                    <span class="activity-type <?= $activity['action_type'] ?>">
                                        <?= htmlspecialchars($activity['action_type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($activity['email'] ?? 'System') ?></td>
                                <td><?= htmlspecialchars($activity['description']) ?></td>
                                <td><?= format_datetime($activity['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Pending Support Messages -->
        <?php if ($pending_count > 0): ?>
            <div class="admin-section">
                <div class="section-header">
                    <h2 class="section-title">Pending Support Messages (<?= $pending_count ?>)</h2>
                    <a href="admin_messages.php" class="admin-btn admin-btn-primary">View All</a>
                </div>
                
                <?php foreach (array_slice($pending_messages, 0, 3) as $msg): ?>
                    <div class="message-preview">
                        <div class="message-header">
                            <span class="message-from">
                                <?= htmlspecialchars($msg['name']) ?> (<?= htmlspecialchars($msg['email']) ?>)
                            </span>
                            <span class="message-time"><?= format_datetime($msg['created_at']) ?></span>
                        </div>
                        <div class="message-text">
                            <?= htmlspecialchars(substr($msg['message'], 0, 150)) ?>
                            <?= strlen($msg['message']) > 150 ? '...' : '' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
