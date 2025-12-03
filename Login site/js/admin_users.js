/**
 * ADMIN_USERS.JS - USER MANAGEMENT INTERFACE FUNCTIONALITY
 * 
 * Handles all interactive elements for the admin user management page:
 * - AJAX requests for role changes
 * - AJAX requests for status changes
 * - Alert message display
 * - User confirmations
 * 
 * Uses fetch API for async updates without page reload
 */

// Event delegation for role and status selects
document.addEventListener('DOMContentLoaded', () => {
    // Event delegation for role selects
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('role-select')) {
            const userId = e.target.dataset.userId;
            const newRole = e.target.value;
            changeRole(userId, newRole);
        }
        
        if (e.target.classList.contains('status-select')) {
            const userId = e.target.dataset.userId;
            const newStatus = e.target.value;
            changeStatus(userId, newStatus);
        }
    });
});

/**
 * CHANGE USER ROLE
 * Sends AJAX request to update user role
 * 
 * @param {number} userId - ID of user to update
 * @param {string} newRole - New role (user/admin)
 */
function changeRole(userId, newRole) {
    // Don't proceed if no role selected
    if (!newRole) return;
    
    // Ask for confirmation before changing role
    if (!confirm(`Change this user's role to ${newRole}?`)) {
        // Reset select dropdown if cancelled
        event.target.value = '';
        return;
    }
    
    // Send AJAX request to server
    fetch('admin_users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=change_role&user_id=${userId}&new_role=${newRole}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            // Reload page to show updated data
            setTimeout(() => location.reload(), 1000);
        } else {
            // Show error message
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        // Handle network or parsing errors
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

/**
 * CHANGE USER STATUS
 * Sends AJAX request to update user status
 * 
 * @param {number} userId - ID of user to update
 * @param {string} newStatus - New status (active/suspended)
 */
function changeStatus(userId, newStatus) {
    // Don't proceed if no status selected
    if (!newStatus) return;
    
    // Determine action verb for confirmation message
    const action = newStatus === 'suspended' ? 'suspend' : 'activate';
    
    // Ask for confirmation before changing status
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
        // Reset select dropdown if cancelled
        event.target.value = '';
        return;
    }
    
    // Send AJAX request to server
    fetch('admin_users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=change_status&user_id=${userId}&new_status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            // Reload page to show updated data
            setTimeout(() => location.reload(), 1000);
        } else {
            // Show error message
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        // Handle network or parsing errors
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

/**
 * SHOW ALERT MESSAGE
 * Displays success or error message to user
 * 
 * @param {string} type - 'success' or 'error'
 * @param {string} message - Message to display
 */
function showAlert(type, message) {
    const alert = document.getElementById('alert');
    if (!alert) return;
    
    // Set alert type (changes color via CSS class)
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alert.classList.add('hidden');
    }, 5000);
}
