/**
 * ADMIN.JS - ADMIN DASHBOARD FUNCTIONALITY
 * 
 * Handles interactive elements on the admin dashboard:
 * - Logout confirmation
 * - Admin panel navigation
 */

document.addEventListener('DOMContentLoaded', () => {
    
    /**
     * LOGOUT BUTTON
     * Shows confirmation dialog before logging out from admin panel
     */
    const logoutBtn = document.querySelector('.admin-btn[href="logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            // Show confirmation dialog
            if (!confirm('Logout from admin panel?')) {
                // Prevent logout if user cancels
                e.preventDefault();
            }
        });
    }
});
