-- =====================================================
-- USER PROFILES TABLE SETUP
-- =====================================================
-- Creates user_profiles table for extended customer data
-- Run this AFTER setup_database.sql
-- =====================================================

USE login_system;

-- =====================================================
-- USER PROFILES TABLE
-- Stores personal information separate from auth data
-- =====================================================
CREATE TABLE IF NOT EXISTS user_profiles (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Foreign key to users table
    user_id INT NOT NULL UNIQUE,
    
    -- Personal Information
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    
    -- Address Information
    address VARCHAR(255),
    city VARCHAR(100),
    zip_code VARCHAR(20),
    country VARCHAR(100),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_city (city),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICATION
-- =====================================================
SELECT 'User profiles table created' AS status;
DESCRIBE user_profiles;
