/**
 * INDEX.JS - HOMEPAGE INTERACTIVE ELEMENTS
 * 
 * Handles all interactive elements on the homepage:
 * - Hero section call-to-action buttons
 * - Navigation to purchase and technology pages
 * - Call-to-action button at bottom of page
 * 
 * All buttons use proper event listeners instead of inline onclick
 */
    
    /**
     * HERO SECTION BUTTONS
     * Primary CTA: Navigate to purchase page
     * Secondary CTA: Navigate to technology page
     */
    
    // "SHOP NOW" button - navigates to purchase page
    const shopNowBtn = document.querySelector('.hero-btn-primary');
    if (shopNowBtn) {
        shopNowBtn.addEventListener('click', () => {
            window.location.href = 'purchase.php';
        });
    }
    
    // "LEARN MORE" button - navigates to technology page
    const learnMoreBtn = document.querySelector('.hero-btn-secondary');
    if (learnMoreBtn) {
        learnMoreBtn.addEventListener('click', () => {
            window.location.href = 'technology.php';
        });
    }
    
    /**
     * BOTTOM CALL-TO-ACTION BUTTON
     * "GET STARTED TODAY" - navigates to purchase page
     */
    const ctaBtn = document.querySelector('.cta-btn');
    if (ctaBtn) {
        ctaBtn.addEventListener('click', () => {
            window.location.href = 'purchase.php';
        });
    }
});
