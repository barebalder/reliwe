<?php
/**
 * CONFIG.PHP - DATABASE CONNECTION CONFIGURATION
 * 
 * This file establishes the connection to the MySQL database.
 * It's included at the top of every PHP file that needs database access.
 * 
 * Security Note:
 * - In production, move this file outside the web root directory
 * - Use environment variables for sensitive credentials
 * - Never commit real credentials to version control
 * 
 * Database Requirements:
 * - MySQL/MariaDB server running on localhost
 * - Database name: login_system
 * - Tables: users, activity_log, contact_messages (see setup_admin.sql)
 */

// ========================================
// DATABASE CONNECTION CREDENTIALS
// Update these values to match your setup
// ========================================

$servername = "localhost";        // Database host (localhost for local development)
$username   = "root";             // MySQL username (default: 'root' for XAMPP)
$password   = "";                 // MySQL password (default: empty for XAMPP)
$dbname     = "login_system";     // Database name (must match imported schema)

// ========================================
// CREATE DATABASE CONNECTION
// Uses mysqli (MySQL Improved) extension
// ========================================

// Create new mysqli connection object
$conn = new mysqli($servername, $username, $password, $dbname);

// ========================================
// ERROR HANDLING
// Check if connection was successful
// ========================================

// If connection failed, stop execution and display error
if ($conn->connect_error) {
    // In production, log this error instead of displaying it
    // Never show detailed error messages to users in production
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful - $conn object is now available for database queries
// No need to return anything - $conn is available in global scope
?>