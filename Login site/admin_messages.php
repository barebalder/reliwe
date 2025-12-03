<?php
/**
 * ADMIN_MESSAGES.PHP - MESSAGE MANAGEMENT
 * 
 * Admin interface for viewing and managing support inquiries.
 */

require_once 'config/admin_functions.php';

// Initialize admin page
$admin_profile = init_admin_page($conn);

$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $message_id = (int)$_POST['message_id'];
    $new_status = $_POST['status'];
    
    if (update_message_status($conn, $message_id, $new_status, $_SESSION['user_id'])) {
        $success = "Message status updated successfully";
    }
}

// Get filter and messages
$filter_status = $_GET['status'] ?? 'all';
$data = get_messages_with_counts($conn, $filter_status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Panel</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
    <link rel="stylesheet" href="css/admin_messages.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <?php render_admin_header('Contact Messages', 'Manage Purchase Inquiries & Support Requests'); ?>
        
        <?php render_alert($success); ?>
        
        <div class="filter-tabs">
            <?php foreach (['all', 'new', 'in_progress', 'resolved', 'closed'] as $status): ?>
                <a href="?status=<?= $status ?>" class="filter-tab <?= $filter_status === $status ? 'active' : '' ?>">
                    <?= ucwords(str_replace('_', ' ', $status)) ?> (<?= $data['counts'][$status] ?>)
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="admin-section">
            <?php if (empty($data['messages'])): ?>
                <div class="empty-state">
                    <h3>No messages found</h3>
                    <p>There are no <?= $filter_status === 'all' ? '' : $filter_status ?> messages at the moment.</p>
                </div>
            <?php else: ?>
                <div class="messages-list">
                <?php foreach ($data['messages'] as $msg): ?>
                    <div class="message-card <?= $msg['status'] ?>">
                        <div class="message-header">
                            <div>
                                <div class="message-sender"><?= htmlspecialchars($msg['name']) ?></div>
                                <div class="message-email">📧 <?= htmlspecialchars($msg['email']) ?></div>
                                <?php if ($msg['phone']): ?>
                                    <div class="message-phone">📞 <?= htmlspecialchars($msg['phone']) ?></div>
                                <?php endif; ?>
                                <?php if ($msg['user_email']): ?>
                                    <div class="message-phone">👤 Registered User: <?= htmlspecialchars($msg['user_email']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="message-meta">
                                <div><?= format_datetime($msg['created_at']) ?></div>
                                <div>ID: #<?= $msg['id'] ?></div>
                            </div>
                        </div>
                        
                        <?php if ($msg['category']): ?>
                            <div class="message-category">📦 <?= htmlspecialchars($msg['category']) ?></div>
                        <?php endif; ?>
                        
                        <div class="message-body"><?= htmlspecialchars($msg['message']) ?></div>
                        
                        <form method="POST" class="message-actions">
                            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                            <select name="status" class="status-select">
                                <?php foreach (['new', 'in_progress', 'resolved', 'closed'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $msg['status'] === $status ? 'selected' : '' ?>>
                                        <?= ucwords(str_replace('_', ' ', $status)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
