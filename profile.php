<?php
/**
 * PROFILE.PHP - USER PROFILE MANAGEMENT
 * 
 * Allows users to view and update their profile information.
 */

require_once 'config/config.php';
require_once 'config/functions.php';
require_once 'config/constants.php';

// Redirect if not logged in
require_login();

/**
 * GET USER PROFILE
 * 
 * Retrieves profile information for a user
 */
function get_user_profile($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();
    
    return $profile;
}

/**
 * UPDATE USER PROFILE
 * 
 * Updates or creates user profile information
 */
function update_user_profile($conn, $user_id, $data) {
    // Extract and sanitize data
    $first_name = sanitize_input($data['first_name'] ?? '');
    $last_name = sanitize_input($data['last_name'] ?? '');
    $phone = sanitize_input($data['phone'] ?? '');
    $address = sanitize_input($data['address'] ?? '');
    $city = sanitize_input($data['city'] ?? '');
    $zip_code = sanitize_input($data['zip_code'] ?? '');
    $country = sanitize_input($data['country'] ?? '');
    
    // Check if profile exists
    $check_stmt = $conn->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    $exists = $check_stmt->num_rows > 0;
    $check_stmt->close();
    
    if ($exists) {
        // Update existing profile
        $stmt = $conn->prepare("
            UPDATE user_profiles 
            SET first_name = ?, last_name = ?, phone = ?, 
                address = ?, city = ?, zip_code = ?, country = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?
        ");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $phone, $address, $city, $zip_code, $country, $user_id);
    } else {
        // Create new profile
        $stmt = $conn->prepare("
            INSERT INTO user_profiles (user_id, first_name, last_name, phone, address, city, zip_code, country)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssss", $user_id, $first_name, $last_name, $phone, $address, $city, $zip_code, $country);
    }
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

$error = '';
$success = '';
$email = '';

// Get current user information
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$email = $user['email'];

// Get user profile data
$profile = get_user_profile($conn, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Combine phone code with phone number
        $phone_code = trim($_POST['phone_code'] ?? '+45');
        $phone_number = trim($_POST['phone'] ?? '');
        $full_phone = $phone_number ? $phone_code . ' ' . $phone_number : '';
        
        $profile_data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $full_phone,
            'address' => $_POST['address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'zip_code' => $_POST['zip_code'] ?? '',
            'country' => $_POST['country'] ?? ''
        ];
        
        if (update_user_profile($conn, $_SESSION['user_id'], $profile_data)) {
            $success = 'Profile updated successfully!';
            // Refresh profile data
            $profile = get_user_profile($conn, $_SESSION['user_id']);
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }

    if (isset($_POST['update_email'])) {
        $new_email = trim($_POST['email'] ?? '');
        $password = $_POST['current_password'] ?? '';

        // Email validation regex
        $email_regex = '/^(?=.{1,254}$)[A-Za-z0-9._%+-]{1,64}@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';
        
        if (empty($new_email) || empty($password)) {
            $error = 'All fields are required.';
        } 
        elseif (!preg_match($email_regex, $new_email)) {
            $error = 'Please enter a valid email address.';
        }
        else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_user = $result->fetch_assoc();
            $stmt->close();

            if (password_verify($password, $current_user['password'])) {
                // Check if new email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $new_email, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = 'This email is already in use by another account.';
                } else {
                    // Update email
                    $update_stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $new_email, $_SESSION['user_id']);

                    if ($update_stmt->execute()) {
                        $_SESSION['user_email'] = $new_email;
                        $email = $new_email;
                        $success = 'Email updated successfully!';
                    } else {
                        $error = 'Failed to update email. Please try again.';
                    }
                    $update_stmt->close();
                }
                $stmt->close();
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }

    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password_change'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All fields are required.';
        } 
        elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } 
        elseif (strlen($new_password) < 8) {
            $error = 'New password must be at least 8 characters long.';
        } 
        else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_user = $result->fetch_assoc();
            $stmt->close();

            if (password_verify($current_password, $current_user['password'])) {
                $hashed_new = password_hash($new_password, PASSWORD_DEFAULT);

                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_new, $_SESSION['user_id']);

                if ($update_stmt->execute()) {
                    $success = 'Password updated successfully!';
                } else {
                    $error = 'Failed to update password. Please try again.';
                }
                $update_stmt->close();
            } else {
                $error = 'Current password is incorrect.';
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
    <title>Profile Settings - Reliwe</title>
    <!-- Global stylesheet for entire site -->
    <link rel="stylesheet" href="css/global_styles.css">
</head>
<body class="profile-page">
    <div class="profile-container">
        <header class="profile-header">
            <div class="profile-header-content">
                <h1>Profile Settings</h1>
                <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
            </div>
        </header>

        <?php 
        render_html_alert($error, 'error');
        render_html_alert($success, 'success');
        ?>

        <div class="profile-sections">
            <!-- Email Update Section -->
            <div class="profile-card">
                <h3>Update Email Address</h3>
                <form action="" method="post" id="emailForm">
                    <input type="hidden" name="update_email" value="1">
                    
                    <div class="form-group">
                        <label for="email">New Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="Enter new email address" 
                            required 
                            autocomplete="email"
                            value="<?= htmlspecialchars($email) ?>">
                        <div id="email-error" class="error" style="display:none; margin: 5px 0 0; font-size: 0.9em;"></div>
                    </div>

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" placeholder="Enter current password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Email</button>
                </form>
            </div>

            <!-- Password Update Section -->
            <div class="profile-card" id="security">
                <h3>Change Password</h3>
                <form action="" method="post" id="passwordForm">
                    <input type="hidden" name="update_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password_change">Current Password</label>
                        <input type="password" name="current_password_change" id="current_password_change" placeholder="Enter current password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required minlength="8">
                        <div class="strength-meter">
                            <div id="strength-bar-new"></div>
                        </div>
                        <div id="strength-text-new"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                        <div id="match-text-new"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>

            <!-- Personal Details Section -->
            <div class="profile-card">
                <h3>Personal Details</h3>
                <form action="" method="post">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-row" style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" placeholder="First Name">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" placeholder="Last Name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div style="display: flex; gap: 10px;">
                            <select name="phone_code" id="phone_code" style="width: 110px; padding: 12px 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem;">
                                <?php
                                // Extract current phone code from stored phone
                                $current_phone = $profile['phone'] ?? '';
                                $current_code = '+45';
                                $phone_number = $current_phone;
                                
                                foreach (PHONE_CODES as $code => $label) {
                                    if (strpos($current_phone, $code) === 0) {
                                        $current_code = $code;
                                        $phone_number = trim(substr($current_phone, strlen($code)));
                                        break;
                                    }
                                }
                                
                                foreach (PHONE_CODES as $code => $label): ?>
                                    <option value="<?= $code ?>" <?= $current_code === $code ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($phone_number) ?>" placeholder="12 34 56 78" style="flex: 1;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" placeholder="Street Address">
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <select name="country" id="country" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                            <?php
                            $selected_country = $profile['country'] ?? 'Denmark';
                            foreach (COUNTRIES as $country): ?>
                                <option value="<?= $country ?>" <?= $selected_country === $country ? 'selected' : '' ?>><?= $country ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row" style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" placeholder="City">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="zip_code">Postal / Zip Code</label>
                            <input type="text" name="zip_code" id="zip_code" value="<?= htmlspecialchars($profile['zip_code'] ?? '') ?>" placeholder="e.g. 7400">
                            <small id="zip-hint" style="color: #666; font-size: 0.8rem;"></small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Details</button>
                </form>
            </div>

            <!-- Account Information -->
            <div class="profile-card">
                <h3>Account Information</h3>
                <div class="info-display">
                    <div class="info-row">
                        <span class="info-label">Current Email:</span>
                        <span class="info-value"><?= htmlspecialchars($email) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">User ID:</span>
                        <span class="info-value">#<?= $_SESSION['user_id'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/global.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>