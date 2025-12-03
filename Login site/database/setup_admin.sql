-- =====================================================
-- DATABASE ENHANCEMENT SCRIPT FOR ADMIN FEATURES
-- =====================================================
-- This script adds role-based access control and admin features
-- Run this AFTER the initial setup_database.sql
-- =====================================================

USE login_system;

-- =====================================================
-- ADD USER ROLE COLUMN
-- Enables role-based access control (RBAC)
-- =====================================================
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user' AFTER password;

-- =====================================================
-- ADD LAST LOGIN TRACKING
-- Helps monitor account activity and security
-- =====================================================
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME NULL AFTER created_at;

-- =====================================================
-- ADD ACCOUNT STATUS
-- Allows admins to disable accounts without deletion
-- =====================================================
ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active', 'suspended', 'deleted') DEFAULT 'active' AFTER role;

-- =====================================================
-- CREATE INDEX FOR FASTER ROLE QUERIES
-- Improves performance when filtering by role
-- =====================================================
ALTER TABLE users ADD INDEX idx_role (role);
ALTER TABLE users ADD INDEX idx_status (status);

-- =====================================================
-- CREATE ADMIN USER
-- Password: password (default example hash below)
-- CHANGE THIS IN DEVELOPMENT: Generate a new hash for your desired password
-- This creates the first admin account
-- =====================================================
-- The password hash below is a sample for the string 'password'
INSERT INTO users (email, password, role, status) 
VALUES ('admin@reliwe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active')
ON DUPLICATE KEY UPDATE role='admin';

-- =====================================================
-- CREATE ACTIVITY LOG TABLE
-- Tracks important user actions for security auditing
-- =====================================================
CREATE TABLE IF NOT EXISTS activity_log (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User who performed the action (NULL for system actions)
    user_id INT NULL,
    
    -- Action type (login, logout, register, profile_update, etc.)
    action_type VARCHAR(50) NOT NULL,
    
    -- Detailed description of action
    description TEXT,
    
    -- IP address for security tracking
    ip_address VARCHAR(45),
    
    -- User agent string (browser info)
    user_agent TEXT,
    
    -- Timestamp of action
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key to users table
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Index for faster queries by user
    INDEX idx_user_id (user_id),
    
    -- Index for faster queries by action type
    INDEX idx_action_type (action_type),
    
    -- Index for faster queries by date
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CREATE CONTACT MESSAGES TABLE
-- Stores messages from support forms
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    -- Primary key
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- User ID if logged in (NULL for guest submissions)
    user_id INT NULL,
    
    -- Contact information
    name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(20),
    
    -- Message category
    category VARCHAR(50),
    
    -- Message content
    message TEXT NOT NULL,
    
    -- Status tracking
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    
    -- Admin response
    admin_response TEXT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICATION QUERIES
-- Check that all changes were applied successfully
-- =====================================================
DESCRIBE users;
DESCRIBE activity_log;
DESCRIBE contact_messages;

-- Show the new admin user
SELECT id, email, role, status, created_at FROM users WHERE role='admin';

-- =====================================================
-- SUCCESS!
-- Database enhancements complete
-- 
-- IMPORTANT SECURITY NOTES:
-- 1. Change the admin password immediately after first login
-- 2. Never use default passwords in production
-- 3. Regularly review activity_log for suspicious activity
-- 4. Consider implementing two-factor authentication
-- =====================================================
