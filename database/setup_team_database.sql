-- =====================================================
-- TEAM DATABASE SETUP
-- =====================================================
-- 
-- This script sets up the database and user permissions
-- for team collaboration on the Reliwe project.
-- 
-- IMPORTANT: Run this file FIRST before any other setup files
-- 
-- Usage:
--   mysql -u root -p < setup_team_database.sql
-- 
-- What this script does:
-- 1. Creates the project database (project_db)
-- 2. Creates a team user account (teamuser)
-- 3. Grants full permissions for the database
-- 4. Allows remote connections for team collaboration
-- 
-- Security Note:
-- - The '%' wildcard allows connections from ANY IP address
-- - Change 'password123' to something stronger for production
-- - Only grants permissions for project_db (not entire MySQL server)
-- =====================================================

-- 1. Create the project database (if it doesn't exist yet)
CREATE DATABASE IF NOT EXISTS project_db;

-- 2. Create the user for your teammates
-- The '%' symbol is the wildcard. It allows connection from ANY IP address.
-- Change 'password123' to something stronger if you want.
CREATE USER IF NOT EXISTS 'teamuser'@'%' IDENTIFIED BY 'password123';

-- 3. Grant full permissions ONLY for this specific database
-- This ensures teammates can't accidentally delete other databases on your computer.
GRANT ALL PRIVILEGES ON project_db.* TO 'teamuser'@'%';

-- 4. Refresh permissions to apply changes immediately
FLUSH PRIVILEGES;

-- Display success message
SELECT 'Team database setup complete!' AS Status,
       'Database: project_db' AS Database_Name,
       'Username: teamuser' AS User,
       'Password: password123' AS Password,
       'Connection: localhost (or your IP for remote access)' AS Connection;
