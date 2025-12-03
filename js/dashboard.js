/**
 * DASHBOARD.JS - USER DASHBOARD FUNCTIONALITY
 * 
 * Handles all interactive elements on the user dashboard:
 * - Logout confirmation
 * - Coming soon alerts
 * - Dynamic data initialization
 */

/**
 * COMING SOON ALERT
 * Shows a friendly message for features under development
 */
function showComingSoon(event) {
    event.preventDefault();
    alert('🚀 Coming Soon!\n\nGood things take time. This feature is currently under development.');
}

document.addEventListener('DOMContentLoaded', () => {
    
    /**
     * LOGOUT BUTTON
     * Shows confirmation dialog before logging out
     */
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Ask for confirmation
            if (confirm('Are you sure you want to logout?')) {
                // Redirect to logout script
                window.location.href = 'logout.php';
            }
        });
    }
    
    /**
     * INITIALIZE DASHBOARD
     * Set up dynamic data (dates, times, user info)
     */
    initializeDashboard();
});

/**
 * initializeDashboard()
 * Populates dashboard with current date/time
 */
function initializeDashboard() {
    const now = new Date();
    const currentDate = now.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });

    // Update dashboard elements with current date
    const memberSince = document.getElementById('member-since');
    const lastLogin = document.getElementById('last-login');

    if (memberSince) memberSince.textContent = currentDate;
    if (lastLogin) lastLogin.textContent = 'Today';
}
