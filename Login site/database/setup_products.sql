-- =====================================================
-- PRODUCTS TABLE SETUP
-- =====================================================
-- Creates products table for the Reliwe store
-- Run after setup_database.sql and setup_admin.sql
-- =====================================================

USE login_system;

-- =====================================================
-- PRODUCTS TABLE
-- Stores product catalog information
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'Images/produkt1.avif',
    stock INT DEFAULT 100,
    category VARCHAR(50) DEFAULT 'device',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT PRODUCTS
-- Neupulse device variants
-- =====================================================
INSERT INTO products (name, description, price, image, category) VALUES
('Neupulse 435i', 'Perfect for beginners. Includes device, USB-C cable, and quick start guide. 1-year warranty.', 199.00, 'Images/produkt1.avif', 'device'),
('Neupulse 435i', 'Advanced features with premium build. Includes carrying case, extra electrodes, and 2-year warranty.', 349.00, 'Images/produkt2.webp', 'device'),
('Neupulse 435x', 'Professional grade with all accessories. Priority support and 3-year warranty included.', 499.00, 'Images/produkt3.webp', 'device'),
('Electrode Gel Pack', 'Replacement conductive gel for optimal performance. 3-month supply.', 29.00, 'Images/produkt4.webp', 'accessory'),
('Replacement Electrodes', 'Set of 4 premium replacement electrode pads.', 49.00, 'Images/produkt1.avif', 'accessory'),
('Travel Case', 'Hard-shell protective case for your Neupulse 435i.', 39.00, 'Images/produkt2.webp', 'accessory');

-- =====================================================
-- CART TABLE (SESSION-BASED)
-- Stores cart items linked to user sessions
-- =====================================================
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL,
    user_id INT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    UNIQUE KEY unique_cart_item (session_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDERS TABLE (UPDATED)
-- Links to products table via order_items
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER ITEMS TABLE
-- Line items for each order
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICATION
-- =====================================================
SELECT 'Products table created with ' AS status, COUNT(*) AS product_count FROM products;
