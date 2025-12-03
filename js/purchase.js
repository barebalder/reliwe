/**
 * PURCHASE PAGE INTERACTIONS
 * Handles version selection, gallery, and cart functionality
 */

// Version selector functionality
function selectVersion(btn) {
    document.querySelectorAll('.version').forEach(v => v.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('heroName').textContent = btn.dataset.name;
    document.getElementById('heroDesc').textContent = btn.dataset.desc;
    document.getElementById('heroPrice').textContent = '$' + parseInt(btn.dataset.price);
    document.getElementById('heroImage').src = btn.dataset.image;
    document.getElementById('heroProductId').value = btn.dataset.id;
}

// Gallery thumbnail functionality
function setHeroImage(src, btn) {
    document.getElementById('heroImage').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
}

// Add to cart with AJAX
function addToCart(productId, productName) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add&product_id=' + productId + '&quantity=1&ajax=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge in header
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = data.cart_count;
                badge.style.opacity = '1';
                badge.style.transform = 'scale(1)';
            } else {
                // Create badge if doesn't exist
                const cartBtn = document.querySelector('.cart-btn');
                if (cartBtn) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = data.cart_count;
                    newBadge.style.opacity = '1';
                    newBadge.style.transform = 'scale(1)';
                    cartBtn.appendChild(newBadge);
                }
            }
            // Show popup
            showCartPopup(productName);
        }
    })
    .catch(err => {
        console.error('Cart error:', err);
        // Fallback: redirect to cart
        window.location.href = 'cart.php?add=' + productId;
    });
}

// Show cart popup
function showCartPopup(productName) {
    document.getElementById('cartPopupProduct').textContent = productName + ' has been added to your cart.';
    document.getElementById('cartPopup').classList.add('show');
}

// Close cart popup
function closeCartPopup() {
    document.getElementById('cartPopup').classList.remove('show');
}

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close popup on backdrop click
    const cartPopup = document.getElementById('cartPopup');
    if (cartPopup) {
        cartPopup.addEventListener('click', function(e) {
            if (e.target === this) closeCartPopup();
        });
    }
    
    // Close popup on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCartPopup();
    });
});
