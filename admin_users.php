<?php
/**
 * ADMIN_USERS.PHP - USER MANAGEMENT
 * 
 * Admin interface for managing user accounts.
 */

require_once 'config/admin_functions.php';

// Initialize admin page
$admin_profile = init_admin_page($conn);

/**
 * GET ALL USERS WITH PAGINATION
 * 
 * Fetches users from database with pagination support.
 */
function get_all_users($conn, $page = 1, $per_page = 20) {
    $offset = ($page - 1) * $per_page;
    
    // Get total user count for pagination
    $total_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE status != 'deleted'");
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_count = $total_result->fetch_assoc()['count'];
    $total_stmt->close();
    
    $total_pages = ceil($total_count / $per_page);
    
    // Fetch users for current page
    $stmt = $conn->prepare("
        SELECT id, email, role, status, created_at, last_login 
        FROM users 
        WHERE status != 'deleted'
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    $stmt->close();
    
    return [
        'users' => $users,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_count' => $total_count
    ];
}

/**
 * UPDATE USER ROLE
 * 
 * Changes a user's role (user/admin).
 */
function update_user_role($conn, $user_id, $new_role) {
    if (!in_array($new_role, ['user', 'admin'])) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * UPDATE USER STATUS
 * 
 * Changes a user's account status (active/suspended/deleted).
 */
function update_user_status($conn, $user_id, $new_status) {
    if (!in_array($new_status, ['active', 'suspended', 'deleted'])) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Handle AJAX actions (role/status updates)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $response = ['success' => false, 'message' => 'Invalid request'];
    
    // Prevent self-modification
    if ($user_id === $_SESSION['user_id']) {
        $response['message'] = 'Cannot modify your own account';
        echo json_encode($response);
        exit();
    }
    
    if ($_POST['action'] === 'change_role' && isset($_POST['new_role'])) {
        if (update_user_role($conn, $user_id, $_POST['new_role'])) {
            log_activity($conn, $_SESSION['user_id'], 'user_role_change', "Changed user #$user_id role to {$_POST['new_role']}");
            $response = ['success' => true, 'message' => 'Role updated successfully'];
        } else {
            $response['message'] = 'Failed to update role';
        }
    }
    elseif ($_POST['action'] === 'change_status' && isset($_POST['new_status'])) {
        if (update_user_status($conn, $user_id, $_POST['new_status'])) {
            log_activity($conn, $_SESSION['user_id'], 'user_status_change', "Changed user #$user_id status to {$_POST['new_status']}");
            $response = ['success' => true, 'message' => 'Status updated successfully'];
        } else {
            $response['message'] = 'Failed to update status';
        }
    }
    
    echo json_encode($response);
    exit();
}

// Get pagination and users
$page = max(1, (int)($_GET['page'] ?? 1));
$user_data = get_all_users($conn, $page, 20);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
    <link rel="stylesheet" href="css/admin_users.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <?php render_admin_header('User Management', 'Manage user accounts, roles and status'); ?>
        
        <div id="alert" class="alert hidden"></div>
        
        <div class="admin-section">
            <div class="section-header">
                <h2 class="section-title">All Users (<?= $user_data['total_count'] ?>)</h2>
            </div>
            
            <table class="admin-table users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user_data['users'] as $user): ?>
                        <tr id="user-row-<?= $user['id'] ?>">
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="role-badge <?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span></td>
                            <td><span class="status-badge <?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span></td>
                            <td><?= format_datetime($user['created_at']) ?></td>
                            <td><?= format_datetime($user['last_login']) ?></td>
                            <td>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <select class="action-select role-select" data-user-id="<?= $user['id'] ?>">
                                        <option value="">Change Role</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <select class="action-select status-select" data-user-id="<?= $user['id'] ?>">
                                        <option value="">Change Status</option>
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspend</option>
                                    </select>
                                <?php else: ?>
                                    <span class="text-muted text-italic">Your account</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php render_pagination($page, $user_data['total_pages'], 'admin_users.php'); ?>
        </div>
    </div>

    <script src="js/admin_users.js"></script>
</body>
</html>
