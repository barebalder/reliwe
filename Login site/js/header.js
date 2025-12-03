/**
 * HEADER.JS - NAVIGATION HEADER ANIMATIONS
 * 
 * Controls the show/hide behavior of the site header based on:
 * - Scroll direction (hide on scroll down, show on scroll up)
 * - Mouse position (show when mouse near top of screen)
 * - Page position (always show at top of page)
 * 
 * Used on: All public pages (index, about, technology, purchase, support)
 */

(function() {
    'use strict';
    
    // Track last scroll position to determine scroll direction
    let lastScroll = 0;
    let header = null;
    
    /**
     * Initialize header functionality
     */
    function init() {
        header = document.querySelector('header');
        
        if (!header) {
            console.warn('Header element not found');
            return;
        }
        
        // Show header on page load
        header.classList.remove('header--hidden');
        
        // Setup event listeners
        setupScrollListener();
        setupMouseListener();
        
        console.log('Header initialized');
    }
    
    /**
     * Setup scroll event listener
     */
    function setupScrollListener() {
        window.addEventListener('scroll', function() {
            if (!header) return;
            
            const currentScroll = window.pageYOffset;
            
            // Always show header at top of page
            if (currentScroll <= 0) {
                header.classList.remove('header--hidden');
                lastScroll = currentScroll;
                return;
            }
            
            // Determine scroll direction and show/hide header
            if (currentScroll > lastScroll && currentScroll > 100) {
                // Scrolling down - hide header (only after scrolling 100px)
                header.classList.add('header--hidden');
            } else if (currentScroll < lastScroll) {
                // Scrolling up - show header
                header.classList.remove('header--hidden');
            }
            
            // Update last scroll position
            lastScroll = currentScroll;
        });
    }
    
    /**
     * Setup mouse movement listener
     */
    function setupMouseListener() {
        document.addEventListener('mousemove', function(e) {
            if (!header) return;
            
            // If mouse is within 40px of top of screen, show header
            if (e.clientY < 40) {
                header.classList.remove('header--hidden');
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();

