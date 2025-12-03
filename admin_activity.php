<?php
/**
 * ADMIN_ACTIVITY.PHP - ACTIVITY LOG
 * 
 * Displays system activity log with filtering and pagination.
 */

require_once 'config/admin_functions.php';

// Initialize admin page
$admin_profile = init_admin_page($conn);

// Get pagination and filter parameters
$page = max(1, (int)($_GET['page'] ?? 1));
$filter_type = $_GET['type'] ?? '';

// Fetch activity log with pagination
$data = get_activity_log($conn, $page, 25, $filter_type);

// Get action types for filter
$action_types = get_action_types($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - Admin Panel</title>
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/admin_common.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <?php render_admin_header('Activity Log', 'View all system activity and user actions'); ?>

        <div class="admin-section">
            <!-- Filter Controls -->
            <div class="filter-controls">
                <form method="GET" class="filter-form">
                    <label for="type">Filter by type:</label>
                    <select name="type" id="type">
                        <option value="">All Types</option>
                        <?php foreach ($action_types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= $filter_type === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($filter_type): ?>
                        <a href="admin_activity.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <p class="results-summary">
                Showing <?= count($data['activities']) ?> of <?= $data['total_count'] ?> activities
                <?= $filter_type ? " (filtered by: {$filter_type})" : '' ?>
            </p>

            <?php if (empty($data['activities'])): ?>
                <div class="empty-state">
                    <h3>No activity records found</h3>
                    <p>There are no activities matching your filter.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['activities'] as $activity): ?>
                            <tr>
                                <td><?= $activity['id'] ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($activity['action_type']) ?>">
                                        <?= htmlspecialchars($activity['action_type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($activity['email'] ?? 'System') ?></td>
                                <td><?= htmlspecialchars($activity['description']) ?></td>
                                <td><code><?= htmlspecialchars($activity['ip_address'] ?? '-') ?></code></td>
                                <td><?= format_datetime($activity['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php render_pagination($page, $data['total_pages'], 'admin_activity.php', $filter_type ? ['type' => $filter_type] : []); ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
