<?php
/**
 * REGISTER.PHP - USER REGISTRATION
 * 
 * Handles new user account creation with validation and security.
 * Collects credentials and profile information per GDPR requirements.
 */

require_once 'config/config.php';
require_once 'config/functions.php';
require_once 'config/constants.php';

/**
 * VALIDATE STRONG PASSWORD
 * 
 * Checks if password meets security requirements
 */
function isStrongPassword($password) {
    if (strlen($password) < 8) return false;
    if (strlen($password) > 128) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
    
    // Check against common weak passwords
    $commonPasswords = [
        'password', '123456', '123456789', 'qwerty', 'abc123',
        'password123', 'admin', 'letmein', 'welcome', '1234567890'
    ];
    
    if (in_array(strtolower($password), $commonPasswords)) return false;
    
    return true;
}

$error = '';
$success = '';

// Form field values (preserve on error)
$form_data = [
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'zip_code' => '',
    'country' => 'Denmark'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize all form data
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['first_name'] = trim($_POST['first_name'] ?? '');
    $form_data['last_name'] = trim($_POST['last_name'] ?? '');
    
    // Combine phone code with phone number
    $phone_code = trim($_POST['phone_code'] ?? '+45');
    $phone_number = trim($_POST['phone'] ?? '');
    $form_data['phone'] = $phone_number ? $phone_code . ' ' . $phone_number : '';
    
    $form_data['address'] = trim($_POST['address'] ?? '');
    $form_data['city'] = trim($_POST['city'] ?? '');
    $form_data['zip_code'] = trim($_POST['zip_code'] ?? '');
    $form_data['country'] = trim($_POST['country'] ?? 'Denmark');
    
    $pass1 = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';

    // Email validation regex (RFC-compliant)
    $email_regex = '/^(?=.{1,254}$)[A-Za-z0-9._%+-]{1,64}@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';
    
    // Validation
    if (empty($form_data['email']) || empty($pass1) || empty($pass2)) {
        $error = 'Email and password are required.';
    } 
    elseif (empty($form_data['first_name']) || empty($form_data['last_name'])) {
        $error = 'First name and last name are required.';
    }
    elseif (!preg_match($email_regex, $form_data['email'])) {
        $error = 'Please enter a valid email address.';
    }
    elseif ($pass1 !== $pass2) {
        $error = 'Passwords do not match.';
    } 
    elseif (strlen($pass1) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } 
    elseif (!isStrongPassword($pass1)) {
        $error = 'Password must contain uppercase, lowercase, number and special character.';
    }
    else {
        // Check if email already exists
        if (email_exists($conn, $form_data['email'])) {
            $error = 'This email is already registered.';
        } else {
            // Start transaction for atomic operation
            $conn->begin_transaction();
            
            try {
                // 1. Create user account with hashed password
                $hashed = password_hash($pass1, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $form_data['email'], $hashed);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $stmt->close();
                
                // 2. Create user profile with personal information
                $profile_stmt = $conn->prepare("
                    INSERT INTO user_profiles (user_id, first_name, last_name, phone, address, city, zip_code, country)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $profile_stmt->bind_param("isssssss", 
                    $user_id, 
                    $form_data['first_name'], 
                    $form_data['last_name'], 
                    $form_data['phone'], 
                    $form_data['address'], 
                    $form_data['city'], 
                    $form_data['zip_code'], 
                    $form_data['country']
                );
                $profile_stmt->execute();
                $profile_stmt->close();
                
                // 3. Log registration activity
                log_activity($conn, $user_id, 'register', 'New user registered: ' . $form_data['email']);
                
                // Commit transaction
                $conn->commit();
                
                // Redirect to login page
                header('Location: login.php?registered=1');
                exit();
                
            } catch (Exception $e) {
                // Rollback on error
                $conn->rollback();
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Reliwe</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="auth-page">
    <div class="container">
        <h2>Create Account</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="post" id="registerForm">
            
            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="form-section-title">Personal Information</div>
                
                <div class="form-row">
                    <input 
                        type="text" 
                        name="first_name" 
                        placeholder="First Name *" 
                        required
                        value="<?= htmlspecialchars($form_data['first_name']) ?>">
                    <input 
                        type="text" 
                        name="last_name" 
                        placeholder="Last Name *" 
                        required
                        value="<?= htmlspecialchars($form_data['last_name']) ?>">
                </div>
                
                <div class="phone-input-group" style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <select name="phone_code" id="phone_code" style="width: 120px; flex-shrink: 0;">
                        <?php foreach (PHONE_CODES as $code => $label): ?>
                            <option value="<?= $code ?>" <?= $code === '+45' ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone"
                        placeholder="12 34 56 78"
                        style="flex: 1;"
                        value="<?= htmlspecialchars($form_data['phone']) ?>">
                </div>
            </div>
            
            <!-- Address Section -->
            <div class="form-section">
                <div class="form-section-title">Address <span class="optional-label">(optional)</span></div>
                
                <input 
                    type="text" 
                    name="address" 
                    placeholder="Street Address"
                    value="<?= htmlspecialchars($form_data['address']) ?>">
                
                <select name="country" id="country">
                    <?php foreach (COUNTRIES as $country): ?>
                        <option value="<?= $country ?>" <?= $form_data['country'] === $country ? 'selected' : '' ?>><?= $country ?></option>
                    <?php endforeach; ?>
                    <option value="Netherlands" <?= $form_data['country'] === 'Netherlands' ? 'selected' : '' ?>>🇳🇱 Netherlands</option>
                    <option value="Belgium" <?= $form_data['country'] === 'Belgium' ? 'selected' : '' ?>>🇧🇪 Belgium</option>
                    <option value="France" <?= $form_data['country'] === 'France' ? 'selected' : '' ?>>🇫🇷 France</option>
                    <option value="United Kingdom" <?= $form_data['country'] === 'United Kingdom' ? 'selected' : '' ?>>🇬🇧 United Kingdom</option>
                    <option value="United States" <?= $form_data['country'] === 'United States' ? 'selected' : '' ?>>🇺🇸 United States</option>
                    <option value="Canada" <?= $form_data['country'] === 'Canada' ? 'selected' : '' ?>>🇨🇦 Canada</option>
                    <option value="Australia" <?= $form_data['country'] === 'Australia' ? 'selected' : '' ?>>🇦🇺 Australia</option>
                    <option value="Other" <?= $form_data['country'] === 'Other' ? 'selected' : '' ?>>🌍 Other</option>
                </select>
                
                <div class="form-row">
                    <input 
                        type="text" 
                        name="city" 
                        placeholder="City"
                        value="<?= htmlspecialchars($form_data['city']) ?>">
                    <input 
                        type="text" 
                        name="zip_code" 
                        id="zip_code"
                        placeholder="Postal Code"
                        value="<?= htmlspecialchars($form_data['zip_code']) ?>">
                </div>
                <small id="zip-hint" style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin-top: -10px; display: block;"></small>
            </div>
            
            <!-- Account Section -->
            <div class="form-section">
                <div class="form-section-title">Account Details</div>
                
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    placeholder="Email Address *" 
                    required 
                    autocomplete="off"
                    value="<?= htmlspecialchars($form_data['email']) ?>">

                <div id="email-error" class="error" style="display:none; margin: -10px 0 15px; font-size: 0.9em;"></div>

                <input type="password" id="password" name="password" placeholder="Password *" required minlength="8">

                <div class="strength-meter">
                    <div id="strength-bar"></div>
                </div>
                <div id="strength-text"></div>

                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password *" required>

                <div id="match-text"></div>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="link">Already have an account? <a href="login.php">Log in here</a></div>
        <div class="link"><a href="index.php">← Back to homepage</a></div>
    </div>

    <script src="js/global.js"></script>
    <script src="js/register.js"></script>
</body>
</html>