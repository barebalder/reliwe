<?php
/**
 * ADMIN_SETTINGS.PHP - SYSTEM SETTINGS
 * 
 * Admin interface for system configuration.
 */

require_once 'config/admin_functions.php';

// Initialize admin page
$admin_profile = init_admin_page($conn);

$success = '';

// Default settings (in production, stored in database)
$settings = [
    'site_name' => 'Reliwe',
    'contact_email' => 'support@reliwe.com',
    'support_phone' => '+45 12 34 56 78',
    'session_timeout' => 60,
    'min_password_length' => 8,
    'require_strong_password' => true,
    'max_login_attempts' => 5,
    'lockout_duration' => 60,
    'maintenance_mode' => false
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = 'Settings saved successfully! (Note: In this MVP version, settings are not persisted)';
    log_activity($conn, $_SESSION['user_id'], 'settings_update', 'Admin updated system settings');
}

// Get system stats
$stats = get_dashboard_stats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Panel</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
    <link rel="stylesheet" href="css/admin_settings.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <?php render_admin_header('System Settings', 'Configure system-wide settings'); ?>
        
        <?php render_alert($success); ?>
        
        <div class="admin-section">
            <div class="info-box">
                <strong>ℹ️ MVP Notice</strong>
                This is a demonstration of the settings interface. In a production environment, these settings would be stored in the database and applied system-wide.
            </div>
            
            <form method="POST">
                <div class="settings-grid">
                    
                    <!-- General Settings -->
                    <div class="settings-card">
                        <h3>🏢 General Settings</h3>
                        <div class="setting-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>">
                        </div>
                        <div class="setting-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>">
                            <p class="hint">Used for system notifications and support</p>
                        </div>
                        <div class="setting-group">
                            <label for="support_phone">Support Phone</label>
                            <input type="text" id="support_phone" name="support_phone" value="<?= htmlspecialchars($settings['support_phone']) ?>">
                        </div>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Maintenance Mode</div>
                                <div class="toggle-description">Disable site access for non-admins</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="maintenance_mode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Security Settings -->
                    <div class="settings-card">
                        <h3>🔒 Security Settings</h3>
                        <div class="setting-group">
                            <label for="session_timeout">Session Timeout (minutes)</label>
                            <input type="number" id="session_timeout" name="session_timeout" value="<?= $settings['session_timeout'] ?>" min="5" max="1440">
                            <p class="hint">Auto-logout after inactivity</p>
                        </div>
                        <div class="setting-group">
                            <label for="min_password_length">Minimum Password Length</label>
                            <input type="number" id="min_password_length" name="min_password_length" value="<?= $settings['min_password_length'] ?>" min="6" max="32">
                        </div>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Require Strong Passwords</div>
                                <div class="toggle-description">Uppercase, lowercase, number & special char</div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="require_strong_password" <?= $settings['require_strong_password'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-group">
                            <label for="max_login_attempts">Max Login Attempts</label>
                            <input type="number" id="max_login_attempts" name="max_login_attempts" value="<?= $settings['max_login_attempts'] ?>" min="3" max="10">
                            <p class="hint">Before account lockout</p>
                        </div>
                        <div class="setting-group">
                            <label for="lockout_duration">Lockout Duration (minutes)</label>
                            <input type="number" id="lockout_duration" name="lockout_duration" value="<?= $settings['lockout_duration'] ?>" min="5" max="1440">
                        </div>
                    </div>
                    
                    <!-- Database Info -->
                    <div class="settings-card">
                        <h3>🗄️ Database Information</h3>
                        <div class="setting-group">
                            <label>Database Host</label>
                            <input type="text" value="<?= htmlspecialchars($db_host ?? 'localhost') ?>" readonly class="input-readonly">
                        </div>
                        <div class="setting-group">
                            <label>Database Name</label>
                            <input type="text" value="<?= htmlspecialchars($db_name ?? 'login_system') ?>" readonly class="input-readonly">
                        </div>
                        <div class="setting-group">
                            <label>PHP Version</label>
                            <input type="text" value="<?= phpversion() ?>" readonly class="input-readonly">
                        </div>
                        <div class="setting-group">
                            <label>Server Software</label>
                            <input type="text" value="<?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?>" readonly class="input-readonly">
                        </div>
                    </div>
                    
                    <!-- System Statistics -->
                    <div class="settings-card">
                        <h3>📊 System Statistics</h3>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Total Users</div>
                                <div class="toggle-description">Registered accounts</div>
                            </div>
                            <strong class="stat-value"><?= $stats['users']['total'] ?></strong>
                        </div>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Administrators</div>
                                <div class="toggle-description">Admin accounts</div>
                            </div>
                            <strong class="stat-value"><?= $stats['users']['admins'] ?></strong>
                        </div>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Activity Logs</div>
                                <div class="toggle-description">Total entries</div>
                            </div>
                            <strong class="stat-value"><?= $stats['activity_count'] ?></strong>
                        </div>
                        <div class="toggle-setting">
                            <div>
                                <div class="toggle-label">Support Messages</div>
                                <div class="toggle-description">Pending</div>
                            </div>
                            <strong class="stat-value"><?= $stats['pending_messages'] ?></strong>
                        </div>
                    </div>
                    
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">Save All Settings</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
