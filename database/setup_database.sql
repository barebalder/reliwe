-- =====================================================
-- DATABASE SETUP SCRIPT FOR Reliwe
-- =====================================================
-- This script creates the necessary database and table
-- structure for the Reliwe user authentication system.
--
-- Run this in phpMyAdmin or MySQL command line
-- =====================================================

-- Create the database
-- DROP DATABASE IF EXISTS login_system; -- Uncomment to reset database
CREATE DATABASE IF NOT EXISTS login_system;
USE login_system;

-- =====================================================
-- USERS TABLE
-- Stores user account information
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    -- Primary key: Unique user identifier
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Email address: Used for login (must be unique)
    email VARCHAR(254) NOT NULL UNIQUE,
    
    -- Password: Stored as bcrypt hash (255 chars for future-proofing)
    password VARCHAR(255) NOT NULL,
    
    -- Account creation timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Index on email for faster login queries
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- OPTIONAL: Add sample user for testing
-- Password is: Test123!
-- =====================================================
-- INSERT INTO users (email, password) VALUES 
-- ('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =====================================================
-- VERIFICATION
-- Check that table was created successfully
-- =====================================================
SHOW TABLES;
DESCRIBE users;

-- =====================================================
-- SUCCESS!
-- Database setup is complete
-- Update config.php with your database credentials
-- =====================================================
