<?php
/**
 * FUNCTIONS.PHP - Shared Helper Functions
 * Includes: Session, Auth, Logging, Sanitization, Formatting
 */

// === ACTIVITY LOGGING ===

/* Log user actions to database for audit trail */
function log_activity($conn, $user_id, $action_type, $description) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare(
        "INSERT INTO activity_log (user_id, action_type, description, ip_address, user_agent) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issss", $user_id, $action_type, $description, $ip_address, $user_agent);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// === ADMIN ACCESS CONTROL ===

/* Check if user has admin role */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/* Enforce admin access - redirect if not authorized */
function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        header('Location: dashboard.php');
        exit();
    }
}

// === USER MANAGEMENT ===

/* Count active users, optionally filter by role */
function get_user_count($conn, $role = null) {
    if ($role) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = ? AND status = 'active'");
        $stmt->bind_param("s", $role);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return (int)$row['count'];
}

/* Get recent activity logs with user email */
function get_recent_activity($conn, $limit = 10) {
    $stmt = $conn->prepare("
        SELECT a.*, u.email 
        FROM activity_log a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
    return $activities;
}

// === INPUT SANITIZATION ===

/* Clean user input to prevent XSS attacks */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/* Check if email already exists in database */
function email_exists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

// === DATA FORMATTING ===

/* Format datetime for display */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '-';
    $date = new DateTime($datetime);
    return $date->format($format);
}

/* Format price with currency symbol */
function format_price($price, $currency = '$') {
    return $currency . number_format($price, 2);
}

// === SESSION MANAGEMENT ===

/* Start session if not already started */
function ensure_session() {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

/* Require user authentication */
function require_login() {
    ensure_session();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// === SHOPPING CART ===

/* Get total cart item count */
function get_cart_count() {
    ensure_session();
    return array_sum($_SESSION['cart'] ?? []);
}

// === UI HELPERS ===

/* Display styled alert message */
function render_html_alert($message, $type = 'success') {
    if (empty($message)) return;
    $class = $type === 'error' ? 'error' : 'success';
    echo '<div class="' . $class . '">' . htmlspecialchars($message) . '</div>';
}
?>
