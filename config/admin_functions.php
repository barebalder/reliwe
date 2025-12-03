<?php
/**
 * ADMIN_FUNCTIONS.PHP - Admin-Specific Helpers
 * Centralized admin functionality for better maintainability
 */

require_once __DIR__ . '/functions.php';

/* Render admin page header with navigation */
function render_admin_header($title, $subtitle, $back_url = 'admin.php', $action_button = '') {
    ?>
    <div class="admin-header">
        <div class="header-title">
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($subtitle) ?></p>
        </div>
        <div class="header-actions">
            <?= $action_button ?>
            <a href="<?= htmlspecialchars($back_url) ?>" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
    <?php
}

/* Render alert message box */
function render_alert($message, $type = 'success') {
    if (empty($message)) return;
    ?>
    <div class="alert alert-<?= $type === 'error' ? 'error' : 'success' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
}

/* Render pagination controls */
function render_pagination($current_page, $total_pages, $base_url = '', $extra_params = []) {
    if ($total_pages <= 1) return;
    
    $query = $extra_params ? '&' . http_build_query($extra_params) : '';
    ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="<?= $base_url ?>?page=1<?= $query ?>" class="page-btn">« First</a>
            <a href="<?= $base_url ?>?page=<?= $current_page - 1 ?><?= $query ?>" class="page-btn">‹ Prev</a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <a href="<?= $base_url ?>?page=<?= $i ?><?= $query ?>" 
               class="page-btn <?= $i === $current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>?page=<?= $current_page + 1 ?><?= $query ?>" class="page-btn">Next ›</a>
            <a href="<?= $base_url ?>?page=<?= $total_pages ?><?= $query ?>" class="page-btn">Last »</a>
        <?php endif; ?>
    </div>
    <?php
}

/* Fetch dashboard statistics in one query batch */
function get_dashboard_stats($conn) {
    $stats = [];
    
    // User counts
    $user_result = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 ELSE 0 END) as active
        FROM users WHERE status = 'active'
    ");
    $stats['users'] = $user_result->fetch_assoc();
    
    // Product counts
    $product_result = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
        FROM products
    ");
    $stats['products'] = $product_result->fetch_assoc();
    
    // Message and activity counts
    $message_result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'");
    $stats['pending_messages'] = $message_result->fetch_assoc()['count'];
    $activity_result = $conn->query("SELECT COUNT(*) as count FROM activity_log");
    $stats['activity_count'] = $activity_result->fetch_assoc()['count'];
    
    return $stats;
}

/* Fetch messages with status counts */
function get_messages_with_counts($conn, $filter_status = 'all') {
    // Get status counts
    $count_result = $conn->query("
        SELECT status, COUNT(*) as count
        FROM contact_messages
        GROUP BY status
    ");
    
    $counts = ['all' => 0, 'new' => 0, 'in_progress' => 0, 'resolved' => 0, 'closed' => 0];
    while ($row = $count_result->fetch_assoc()) {
        $counts[$row['status']] = $row['count'];
        $counts['all'] += $row['count'];
    }
    
    // Fetch messages
    if ($filter_status === 'all') {
        $messages = $conn->query("
            SELECT m.*, u.email as user_email 
            FROM contact_messages m 
            LEFT JOIN users u ON m.user_id = u.id 
            ORDER BY m.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
    } else {
        $stmt = $conn->prepare("
            SELECT m.*, u.email as user_email 
            FROM contact_messages m 
            LEFT JOIN users u ON m.user_id = u.id 
            WHERE m.status = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->bind_param("s", $filter_status);
        $stmt->execute();
        $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    
    return ['messages' => $messages, 'counts' => $counts];
}

/* Handle image upload with validation */
function handle_image_upload($file, $prefix = 'upload') {
    $upload_dir = __DIR__ . '/../Images/';
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK || $file['size'] === 0) return null;
    
    // Validate type and size
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed) || $file['size'] > 10 * 1024 * 1024) return null;
    
    // Verify image
    if (getimagesize($file['tmp_name']) === false) return null;
    
    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    
    if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        return 'Images/' . $filename;
    }
    return null;
}

/* Process multiple image uploads */
function handle_multiple_uploads($files, $prefix = 'gallery') {
    $uploaded = [];
    
    if (!isset($files['name']) || !is_array($files['name'])) return $uploaded;
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            $path = handle_image_upload($file, $prefix);
            if ($path) $uploaded[] = $path;
        }
    }
    return $uploaded;
}

/* Update message status with logging */
function update_message_status($conn, $message_id, $new_status, $admin_id) {
    $stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $message_id);
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        log_activity($conn, $admin_id, 'message_status_update', "Updated message #$message_id to $new_status");
    }
    return $success;
}

/* Get activity log with pagination and filtering */
function get_activity_log($conn, $page = 1, $per_page = 25, $filter_type = '') {
    $offset = ($page - 1) * $per_page;
    
    // Count total
    if ($filter_type) {
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM activity_log WHERE action_type = ?");
        $count_stmt->bind_param("s", $filter_type);
    } else {
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM activity_log");
    }
    $count_stmt->execute();
    $total_count = $count_stmt->get_result()->fetch_assoc()['count'];
    $count_stmt->close();
    $total_pages = ceil($total_count / $per_page);
    
    // Fetch activities
    if ($filter_type) {
        $stmt = $conn->prepare("
            SELECT a.*, u.email 
            FROM activity_log a 
            LEFT JOIN users u ON a.user_id = u.id 
            WHERE a.action_type = ?
            ORDER BY a.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sii", $filter_type, $per_page, $offset);
    } else {
        $stmt = $conn->prepare("
            SELECT a.*, u.email 
            FROM activity_log a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $per_page, $offset);
    }
    
    $stmt->execute();
    $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return [
        'activities' => $activities,
        'total_pages' => $total_pages,
        'total_count' => $total_count
    ];
}

/* Get unique action types for filtering */
function get_action_types($conn) {
    $result = $conn->query("SELECT DISTINCT action_type FROM activity_log ORDER BY action_type");
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row['action_type'];
    }
    return $types;
}

/* Initialize admin page - handles auth and returns profile */
function init_admin_page(&$conn) {
    ensure_session();
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/functions.php';
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    require_admin();
    
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    return $profile ?: [];
}
?>
