/**
 * SUPPORT.JS - FAQ ACCORDION FUNCTIONALITY
 * 
 * Handles the interactive FAQ accordion on the support page.
 * When a question is clicked:
 * - Closes all other open FAQ items
 * - Toggles the clicked FAQ item open/closed
 * 
 * This creates a clean, one-at-a-time FAQ experience.
 */

document.addEventListener('DOMContentLoaded', () => {
    
    /**
     * FAQ ACCORDION
     * Add click handlers to all FAQ questions
     */
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            // Get parent FAQ item
            const faqItem = this.parentElement;
            
            // Close all other FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current FAQ item (open if closed, close if open)
            faqItem.classList.toggle('active');
        });
    });
});
