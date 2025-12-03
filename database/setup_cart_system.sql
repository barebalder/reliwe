-- =====================================================
-- CART SYSTEM DATABASE EXTENSION
-- =====================================================
-- This script extends the existing login_system database
-- to support persistent shopping cart and user preferences
--
-- Run this AFTER setup_database.sql and setup_admin.sql
-- =====================================================

USE login_system;

-- =====================================================
-- SHOPPING CART TABLE
-- Stores persistent cart data for logged-in users
-- =====================================================
CREATE TABLE IF NOT EXISTS shopping_carts (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User relationship (NULL for guest carts)
    user_id INT NULL,
    
    -- Session ID for guest users
    session_id VARCHAR(128) NULL,
    
    -- Product information
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    
    -- Timestamps
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key to users table
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER PREFERENCES TABLE
-- Stores user settings and preferences
-- =====================================================
CREATE TABLE IF NOT EXISTS user_preferences (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User relationship
    user_id INT NOT NULL,
    
    -- Preference data
    preference_key VARCHAR(100) NOT NULL,
    preference_value TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key and unique constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preference (user_id, preference_key),
    
    -- Index for performance
    INDEX idx_user_pref (user_id, preference_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER SESSIONS TABLE
-- Enhanced session management with metadata
-- =====================================================
CREATE TABLE IF NOT EXISTS user_sessions (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User relationship (NULL for guest sessions)
    user_id INT NULL,
    
    -- Session data
    session_id VARCHAR(128) NOT NULL UNIQUE,
    session_data TEXT,
    
    -- Browser and location info
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER HISTORY TABLE
-- Stores completed purchases for user tracking
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User relationship
    user_id INT NOT NULL,
    
    -- Order information
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_user_orders (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER ITEMS TABLE
-- Stores individual items within each order
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Order relationship
    order_id INT NOT NULL,
    
    -- Product information (snapshot at time of purchase)
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    product_image VARCHAR(255),
    quantity INT NOT NULL,
    
    -- Foreign key
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    
    -- Index
    INDEX idx_order_items (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CLEANUP EXPIRED SESSIONS PROCEDURE
-- Automatically removes old session data
-- =====================================================
DELIMITER //
CREATE PROCEDURE CleanupExpiredSessions()
BEGIN
    DELETE FROM user_sessions 
    WHERE expires_at < NOW() 
    OR last_activity < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //
DELIMITER ;

-- =====================================================
-- SAMPLE DATA FOR TESTING
-- =====================================================

-- Sample user preferences
INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value) VALUES
(1, 'theme', 'dark'),
(1, 'notifications', 'true'),
(1, 'newsletter', 'true');

-- Sample cart items (for user_id 1 if exists)
INSERT IGNORE INTO shopping_carts (user_id, product_id, product_name, product_price, product_image, quantity) VALUES
(1, 'pulsetto-light', 'Pulsetto Light', 199.00, 'Images/produkt1.avif', 1);

-- =====================================================
-- USER PRIVACY SETTINGS TABLE (GDPR Compliance)
-- Stores GDPR consent and privacy preferences
-- =====================================================
CREATE TABLE IF NOT EXISTS user_privacy_settings (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User relationship
    user_id INT NOT NULL,
    
    -- GDPR consent data (JSON format)
    consent_data JSON NOT NULL,
    
    -- Privacy preferences
    data_processing_consent BOOLEAN DEFAULT FALSE,
    marketing_consent BOOLEAN DEFAULT FALSE,
    analytics_consent BOOLEAN DEFAULT FALSE,
    
    -- Data retention preferences
    data_retention_days INT DEFAULT 365,
    
    -- Consent version tracking
    consent_version VARCHAR(10) DEFAULT '1.0',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Unique constraint - one privacy setting per user
    UNIQUE KEY unique_user_privacy (user_id),
    
    -- Indexes
    INDEX idx_consent_date (created_at),
    INDEX idx_marketing_consent (marketing_consent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICATION
-- Check that all tables were created successfully
-- =====================================================
SHOW TABLES;

-- Show table structures
DESCRIBE shopping_carts;
DESCRIBE user_preferences;
DESCRIBE user_sessions;
DESCRIBE orders;
DESCRIBE order_items;

-- =====================================================
-- SUCCESS!
-- Cart system database is ready
-- Next: Run the PHP integration functions
-- =====================================================