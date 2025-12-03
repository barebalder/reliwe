<?php
/**
 * LOGIN.PHP - USER AUTHENTICATION
 * 
 * Handles user login with session management.
 * Features email/password validation, rate limiting, and secure authentication.
 */

require_once 'config/config.php';
require_once 'config/functions.php';

ensure_session();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

/**
 * CHECK LOGIN RATE LIMITING
 * 
 * Prevents brute force attacks by tracking failed attempts.
 */
function checkLoginRateLimit($conn, $email, $ip) {
    $stmt = $conn->prepare(
        "SELECT 
            SUM(CASE WHEN description LIKE ? THEN 1 ELSE 0 END) as email_attempts,
            SUM(CASE WHEN ip_address = ? THEN 1 ELSE 0 END) as ip_attempts
         FROM activity_log 
         WHERE action_type = 'failed_login' 
         AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
    );
    
    $email_pattern = '%' . $email . '%';
    $stmt->bind_param("ss", $email_pattern, $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return ($row['email_attempts'] < 5 && $row['ip_attempts'] < 10);
}

$error = '';
$success = '';
$email = '';

// Check if user was redirected from successful registration
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = 'Registration successful! Please log in with your credentials.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Email validation regex (same as register.php)
    $email_regex = '/^(?=.{1,254}$)[A-Za-z0-9._%+-]{1,64}@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } 
    elseif (!preg_match($email_regex, $email)) {
        $error = 'Please enter a valid email address.';
    }
    else {
        // Basic rate limiting to prevent brute force
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (!checkLoginRateLimit($conn, $email, $ip)) {
            $error = 'Too many failed attempts. Please try again later.';
        } else {
        // Query now includes role and status for access control
        $stmt = $conn->prepare("SELECT id, password, role, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                $error = 'Your account has been suspended. Please contact support.';
            }
            elseif (password_verify($password, $user['password'])) {
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['role']; // Store role for admin checks
                
                // Update last login timestamp
                $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Log the login activity
                log_activity($conn, $user['id'], 'login', 'User logged in successfully');
                
                // Set JavaScript flag for cart sync
                echo "<script>window.userLoggedIn = true;</script>";
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = 'Invalid email or password.';
                // Log failed login attempt for rate limiting
                log_activity($conn, null, 'failed_login', 'Failed login for ' . $email . ' from IP ' . $ip);
            }
        } else {
            $error = 'Invalid email or password.';
            // Log failed login attempt for rate limiting
            log_activity($conn, null, 'failed_login', 'Failed login for ' . $email . ' from IP ' . $ip);
        }
        $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
</head>
<body class="auth-page">
    <div class="container">
        <h2>Welcome Back</h2>

        <?php 
        render_html_alert($error, 'error');
        render_html_alert($success, 'success');
        ?>

        <form action="" method="post" id="loginForm">
            <input 
                type="email" 
                name="email" 
                id="email" 
                placeholder="Email address" 
                required 
                autocomplete="email"
                value="<?= htmlspecialchars($email) ?>">

            <div id="email-error" class="error" style="display:none; margin: -10px 0 15px; font-size: 0.9em;"></div>

            <input type="password" id="password" name="password" placeholder="Password" required>

            <button type="submit">Sign In</button>
        </form>

        <div class="link">Don't have an account? <a href="register.php">Sign up here</a></div>
        <div class="link"><a href="index.php">← Back to homepage</a></div>
    </div>

    <!-- Global JavaScript -->
    <script src="js/global.js"></script>
</body>
</html>