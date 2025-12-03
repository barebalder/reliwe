<?php
/**
 * PURCHASE PAGE - CLEAN PRODUCT STOREFRONT
 * 
 * Displays products from the database with a simple, clean layout.
 * Products are managed in admin_products.php
 */

require_once 'config/config.php';
require_once 'config/functions.php';

ensure_session();

// Get all active products
$products = $conn->query("
    SELECT id, name, description, price, image, images, stock, category 
    FROM products 
    WHERE status = 'active' 
    ORDER BY category DESC, price ASC
")->fetch_all(MYSQLI_ASSOC);

// Separate devices and accessories
$devices = array_filter($products, fn($p) => $p['category'] === 'device');
$accessories = array_filter($products, fn($p) => $p['category'] === 'accessory');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Reliwe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global_styles.css">
    <link rel="stylesheet" href="css/purchase.css">
</head>
<body>
    <!-- Cart Added Popup -->
    <div class="cart-popup" id="cartPopup">
        <div class="cart-popup-content">
            <span class="cart-popup-icon">✓</span>
            <h3>Added to Cart!</h3>
            <p id="cartPopupProduct">Product has been added to your cart.</p>
            <div class="cart-popup-buttons">
                <button class="cart-popup-btn secondary" onclick="closeCartPopup()">Continue Shopping</button>
                <a href="cart.php" class="cart-popup-btn primary">View Cart</a>
            </div>
        </div>
    </div>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section - Featured Product -->
    <?php if (!empty($devices)): 
        $featured = reset($devices); // First device is featured
        $gallery = !empty($featured['images']) ? json_decode($featured['images'], true) : [];
    ?>
    <section class="shop-hero">
        <div class="hero-grid">
            <!-- Product Image -->
            <div class="hero-image">
                <img src="<?= htmlspecialchars($featured['image']) ?>" 
                     alt="<?= htmlspecialchars($featured['name']) ?>" 
                     id="heroImage"
                     onerror="this.src='Images/produkt1.avif'">
                
                <!-- Gallery Thumbnails (if available) -->
                <?php if (!empty($gallery)): ?>
                <div class="hero-thumbnails">
                    <button class="thumb active" onclick="setHeroImage('<?= htmlspecialchars($featured['image']) ?>', this)">
                        <img src="<?= htmlspecialchars($featured['image']) ?>" alt="Main">
                    </button>
                    <?php foreach (array_slice($gallery, 0, 3) as $img): ?>
                    <button class="thumb" onclick="setHeroImage('<?= htmlspecialchars($img) ?>', this)">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Gallery">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="hero-content">
                <span class="hero-badge">Featured</span>
                <h1 id="heroName"><?= htmlspecialchars($featured['name']) ?></h1>
                <p class="hero-desc" id="heroDesc"><?= htmlspecialchars($featured['description']) ?></p>
                
                <!-- Version Selector (if multiple devices) -->
                <?php if (count($devices) > 1): ?>
                <div class="version-picker">
                    <label>Select Model:</label>
                    <div class="versions">
                        <?php foreach ($devices as $i => $d): ?>
                        <button type="button" 
                                class="version <?= $i === 0 ? 'active' : '' ?>"
                                data-id="<?= $d['id'] ?>"
                                data-name="<?= htmlspecialchars($d['name']) ?>"
                                data-price="<?= $d['price'] ?>"
                                data-image="<?= htmlspecialchars($d['image']) ?>"
                                data-desc="<?= htmlspecialchars($d['description']) ?>"
                                onclick="selectVersion(this)">
                            <span class="version-name"><?= htmlspecialchars(str_replace('Neupulse ', '', $d['name'])) ?></span>
                            <span class="version-price">$<?= number_format($d['price'], 0) ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="hero-price">
                    <span class="price" id="heroPrice">$<?= number_format($featured['price'], 0) ?></span>
                    <span class="stock">✓ In Stock</span>
                </div>
                
                <!-- Add to Cart Button -->
                <div class="hero-form">
                    <button type="button" class="btn-cart" onclick="addToCart(document.getElementById('heroProductId').value, document.getElementById('heroName').textContent)">
                        <span>🛒</span> Add to Cart
                    </button>
                    <input type="hidden" id="heroProductId" value="<?= $featured['id'] ?>">
                    <a href="technology.php" class="btn-learn">Learn More</a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="hero-trust">
                    <span>🚚 Free shipping over $200</span>
                    <span>↩ 30-day returns</span>
                    <span>🛡️ 2-year warranty</span>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- No products message -->
    <section class="shop-hero empty">
        <h1>No Products Available</h1>
        <p>Please check back soon!</p>
    </section>
    <?php endif; ?>
    
    <!-- All Devices Section -->
    <?php if (count($devices) > 0): ?>
    <section class="products-section">
        <div class="section-header">
            <h2>Our Devices</h2>
            <p>Professional-grade vagus nerve stimulation for better wellness</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($devices as $i => $device): ?>
            <div class="product-card <?= $i === count($devices) - 1 ? 'featured' : '' ?>">
                <?php if ($i === count($devices) - 1): ?>
                    <span class="card-badge">Premium</span>
                <?php endif; ?>
                
                <div class="card-image">
                    <img src="<?= htmlspecialchars($device['image']) ?>" 
                         alt="<?= htmlspecialchars($device['name']) ?>"
                         onerror="this.src='Images/produkt1.avif'">
                </div>
                
                <div class="card-content">
                    <h3><?= htmlspecialchars($device['name']) ?></h3>
                    <p><?= htmlspecialchars($device['description']) ?></p>
                    <div class="card-price">$<?= number_format($device['price'], 0) ?></div>
                    
                    <button type="button" class="btn-add" onclick="addToCart(<?= $device['id'] ?>, '<?= addslashes($device['name']) ?>')">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Accessories Section -->
    <?php if (count($accessories) > 0): ?>
    <section class="products-section alt">
        <div class="section-header">
            <h2>Accessories</h2>
            <p>Enhance your experience with premium add-ons</p>
        </div>
        
        <div class="products-grid accessories">
            <?php foreach ($accessories as $accessory): ?>
            <div class="product-card small">
                <div class="card-image">
                    <img src="<?= htmlspecialchars($accessory['image']) ?>" 
                         alt="<?= htmlspecialchars($accessory['name']) ?>"
                         onerror="this.src='Images/produkt1.avif'">
                </div>
                
                <div class="card-content">
                    <h3><?= htmlspecialchars($accessory['name']) ?></h3>
                    <p><?= htmlspecialchars($accessory['description']) ?></p>
                    <div class="card-price">$<?= number_format($accessory['price'], 0) ?></div>
                    
                    <button type="button" class="btn-add" onclick="addToCart(<?= $accessory['id'] ?>, '<?= addslashes($accessory['name']) ?>')">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Why Choose Us Section -->
    <section class="benefits-section">
        <h2>Why Choose Reliwe?</h2>
        <div class="benefits-grid">
            <div class="benefit">
                <span class="benefit-icon">🚚</span>
                <h3>Free Shipping</h3>
                <p>Free worldwide shipping on orders over $200</p>
            </div>
            <div class="benefit">
                <span class="benefit-icon">🛡️</span>
                <h3>2-Year Warranty</h3>
                <p>Comprehensive coverage on all devices</p>
            </div>
            <div class="benefit">
                <span class="benefit-icon">↩</span>
                <h3>30-Day Returns</h3>
                <p>Not satisfied? Full refund, no questions</p>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/purchase.js"></script>
</body>
</html>
