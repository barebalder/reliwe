# Technical Reference - Reliwe Platform

Complete technical documentation for developers.

**Last Updated:** December 2024

---

## Table of Contents

1. [Setup & Configuration](#setup--configuration)
2. [Database Schema](#database-schema)
3. [Project Architecture](#project-architecture)
4. [Function Reference](#function-reference)
5. [File Structure](#file-structure)
6. [Security Implementation](#security-implementation)

---

## Setup & Configuration

### Prerequisites
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+
- Apache (XAMPP/LAMP/MAMP)

### Installation

```bash
# 1. Place project in web root
/Applications/XAMPP/xamppfiles/htdocs/Login site/

# 2. Create database
mysql -u root -e "CREATE DATABASE login_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# 3. Import schema (in order)
cd database/
mysql -u root login_system < setup_database.sql
mysql -u root login_system < setup_admin.sql
mysql -u root login_system < setup_cart_system.sql
mysql -u root login_system < setup_profiles.sql

# 4. Configure
# Edit config/config.php with your credentials

# 5. Start server
# XAMPP Control Panel → Start Apache + MySQL

# 6. Access
open http://localhost/Login%20site/
```

### Configuration Files

#### `config/config.php` - Database Connection
```php
$servername = "localhost";
$username   = "root";
$password   = "";             // Empty for XAMPP default
$dbname     = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);
```

#### `config/constants.php` - Shared Constants
```php
define('PHONE_CODES', [
    '+45' => '🇩🇰 +45',  // Denmark
    '+46' => '🇸🇪 +46',  // Sweden
    // ... 11 total country codes
]);

define('COUNTRIES', [
    'Denmark', 'Sweden', 'Norway', 'Finland',
    // ... 13 total countries
]);
```

**Usage:**
```php
require_once 'config/constants.php';

foreach (PHONE_CODES as $code => $display) {
    echo "<option value='$code'>$display</option>";
}
```

---

## Database Schema

### Tables Overview

| Table | Rows (est.) | Purpose |
|-------|-------------|---------|
| `users` | 100-10,000 | User accounts |
| `user_profiles` | 100-10,000 | Extended user data |
| `products` | 10-1,000 | Product catalog |
| `shopping_carts` | 50-5,000 | Persistent carts |
| `orders` | 100-10,000 | Purchase history |
| `order_items` | 200-50,000 | Order line items |
| `contact_messages` | 50-5,000 | Support tickets |
| `activity_log` | 1,000-100,000 | Audit trail |

### Table Structures

#### `users`
```sql
CREATE TABLE users (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    email        VARCHAR(254) UNIQUE NOT NULL,
    password     VARCHAR(255) NOT NULL,        -- bcrypt hash
    role         ENUM('user','admin') DEFAULT 'user',
    status       ENUM('active','suspended','deleted') DEFAULT 'active',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login   DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
);
```

#### `user_profiles`
```sql
CREATE TABLE user_profiles (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT UNIQUE NOT NULL,
    first_name  VARCHAR(100),
    last_name   VARCHAR(100),
    phone_code  VARCHAR(10),
    phone       VARCHAR(20),
    address     VARCHAR(255),
    city        VARCHAR(100),
    country     VARCHAR(100),
    zip_code    VARCHAR(20),
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### `products`
```sql
CREATE TABLE products (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT DEFAULT 0,
    images      TEXT,                          -- JSON array
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);
```

#### `activity_log`
```sql
CREATE TABLE activity_log (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    user_id       INT NULL,
    action_type   VARCHAR(50) NOT NULL,
    description   TEXT,
    ip_address    VARCHAR(45),
    user_agent    TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at)
);
```

---

## Project Architecture

### Directory Structure

```
/Login site/
│
├── config/                      # Configuration & Libraries
│   ├── config.php               # Database connection
│   ├── constants.php            # Shared constants
│   ├── functions.php            # Core helpers (689 lines, 30+ functions)
│   └── admin_functions.php      # Admin helpers (474 lines, 10+ functions)
│
├── database/                    # SQL Schema
│   ├── setup_database.sql       # Core tables (users, activity_log)
│   ├── setup_admin.sql          # Admin user creation
│   ├── setup_cart_system.sql    # Cart & orders
│   └── setup_profiles.sql       # User profiles
│
├── includes/                    # Reusable Components
│   ├── header.php               # Global navigation (85 lines)
│   ├── footer.php               # Global footer (45 lines)
│   └── product_modal.php        # Product CRUD modal (80 lines)
│
├── css/                         # Stylesheets (14 files)
│   ├── global_styles.css        # Base styles
│   ├── index.css                # Landing page
│   ├── cart-page.css            # Shopping cart
│   ├── admin_common.css         # Admin shared styles
│   └── ...                      # Page-specific styles
│
├── js/                          # JavaScript (8 files)
│   ├── global.js                # Shared utilities
│   ├── form-formats.js          # Phone/zip formats (shared)
│   ├── profile.js               # Profile page (49 lines)
│   ├── register.js              # Registration (49 lines)
│   ├── purchase.js              # Cart interactions
│   ├── admin.js                 # Admin dashboard
│   ├── admin_products.js        # Product management (210 lines)
│   └── admin_users.js           # User management
│
├── Images/                      # Product images (AVIF, WebP, PNG)
├── docs/                        # Documentation (this folder)
│
└── *.php                        # Page Files (16 files)
    ├── index.php                # Landing page
    ├── about.php                # Company info
    ├── technology.php           # Product details
    ├── purchase.php             # Browse products
    ├── support.php              # Contact form
    ├── login.php                # Authentication
    ├── register.php             # Account creation
    ├── logout.php               # Session cleanup
    ├── dashboard.php            # User hub
    ├── profile.php              # Profile management
    ├── cart.php                 # Shopping cart
    ├── admin.php                # Admin dashboard
    ├── admin_users.php          # User management
    ├── admin_products.php       # Product CRUD (96 lines, optimized)
    ├── admin_messages.php       # Support messages
    ├── admin_activity.php       # Activity log
    └── admin_settings.php       # System settings
```

### Data Flow

#### User Registration Flow
```
register.php (form)
    ↓ POST
Validate input (server-side)
    ↓
Hash password (bcrypt)
    ↓
Insert into users table
    ↓
Create user_profiles entry
    ↓
Log activity (registration)
    ↓
Redirect to dashboard.php
```

#### Login Flow
```
login.php (form)
    ↓ POST
Check rate limit (checkLoginRateLimit)
    ↓
Verify credentials (verify_user_password)
    ↓
Create session ($_SESSION['user_id'])
    ↓
Log activity (login)
    ↓
Redirect to dashboard.php (or admin.php if admin)
```

#### Cart to Checkout Flow
```
Session-based cart (no login required)
    ↓
User adds products (cart.php)
    ↓
Cart stored in $_SESSION['cart']
    ↓
User logs in/registers
    ↓
Optional: Sync cart to database (shopping_carts table)
    ↓
Checkout (purchase.php)
    ↓
Create order (orders + order_items tables)
    ↓
Clear cart
```

---

## Function Reference

### Core Functions (`config/functions.php`)

#### Authentication & Security

**`log_activity($conn, $user_id, $action_type, $description)`**
Records user actions for audit trail.
```php
log_activity($conn, $_SESSION['user_id'], 'profile_update', 'Email changed');
```

**`is_admin()`**
Checks if current user has admin role.
```php
if (is_admin()) {
    // Show admin controls
}
```

**`require_admin()`**
Enforces admin-only access. Redirects if not admin.
```php
require_admin(); // Place at top of admin pages
```

**`checkLoginRateLimit($conn, $email, $ip)`**
Prevents brute force (5 attempts/email, 10/IP per hour).
```php
if (!checkLoginRateLimit($conn, $email, get_client_ip())) {
    $error = 'Too many failed attempts. Try later.';
}
```

**`verify_user_password($conn, $email, $password)`**
Centralized password verification.
```php
$user = verify_user_password($conn, $email, $password);
if ($user) {
    $_SESSION['user_id'] = $user['id'];
}
```

#### Validation & Formatting

**`sanitize_input($data)`**
Cleans user input (trim, strip slashes, htmlspecialchars).
```php
$clean_email = sanitize_input($_POST['email']);
```

**`validate_email($email)`**
Validates email format.
```php
if (!validate_email($email)) {
    $error = 'Invalid email';
}
```

**`isStrongPassword($password)`**
Validates password strength (8+ chars, mixed case, numbers, symbols).
```php
if (!isStrongPassword($password)) {
    $error = 'Password too weak';
}
```

**`format_phone_input($phone)`**
Removes non-numeric characters.
```php
$clean_phone = format_phone_input('+45 12 34 56 78');
// Result: '12345678'
```

**`get_client_ip()`**
Gets real client IP (proxy-aware).
```php
$ip = get_client_ip();
```

#### Database Helpers

**`fetch_single_row($stmt)`**
Simplified single-row query.
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$user = fetch_single_row($stmt);
```

**`get_user_profile($conn, $user_id)`**
Fetches user profile data.
```php
$profile = get_user_profile($conn, $_SESSION['user_id']);
echo $profile['first_name'];
```

**`format_datetime($datetime, $format = 'd/m/Y H:i')`**
Formats timestamps for display.
```php
echo format_datetime($user['created_at']); // "03/12/2025 14:30"
```

### Admin Functions (`config/admin_functions.php`)

**`init_admin_page($conn, $page_title)`**
Initializes admin page with security checks.
```php
init_admin_page($conn, 'User Management');
// Starts session, checks admin role, sets page title
```

**`render_admin_header($page_title)`**
Outputs consistent admin header with navigation.
```php
render_admin_header('Products');
```

**`render_pagination($current_page, $total_pages)`**
Outputs pagination controls.
```php
render_pagination($_GET['page'] ?? 1, $total_pages);
```

**`get_dashboard_stats($conn)`**
Fetches dashboard metrics in one query.
```php
$stats = get_dashboard_stats($conn);
echo "Total Users: " . $stats['total_users'];
echo "New Today: " . $stats['new_users_today'];
```

**`process_product_action($conn, $post_data, $files_data, $user_id)`**
Handles product CRUD (add, edit, delete).
```php
$result = process_product_action($conn, $_POST, $_FILES, $_SESSION['user_id']);
if ($result['success']) {
    $success = $result['message'];
}
```

**`handle_image_upload($file, $upload_dir = '../Images/')`**
Processes single image upload (max 5MB, JPG/PNG/GIF/AVIF/WEBP).
```php
$filename = handle_image_upload($_FILES['product_image']);
```

**`get_messages_with_counts($conn, $status = null, $page = 1, $per_page = 20)`**
Fetches support messages with status counts.
```php
$data = get_messages_with_counts($conn, 'new', 1);
foreach ($data['messages'] as $msg) {
    echo $msg['subject'];
}
```

---

## File Structure

### Page Templates

#### Public Page Pattern
```php
<?php
/**
 * PAGE_NAME.PHP - Description
 */

// Optional: Session start if needed
// session_start();

// Include dependencies
require_once 'config/config.php';
require_once 'config/functions.php';

// Page logic here

// Include header
include 'includes/header.php';
?>

<!-- HTML content -->

<?php include 'includes/footer.php'; ?>
```

#### User Page Pattern
```php
<?php
/**
 * PAGE_NAME.PHP - Description
 */

session_start();
require_once 'config/config.php';
require_once 'config/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Page logic

include 'includes/header.php';
?>

<!-- HTML content -->

<?php include 'includes/footer.php'; ?>
```

#### Admin Page Pattern
```php
<?php
/**
 * ADMIN_PAGE_NAME.PHP - Description
 */

require_once 'config/config.php';
require_once 'config/functions.php';
require_once 'config/admin_functions.php';

// Initialize admin page (handles auth, session, etc.)
init_admin_page($conn, 'Page Title');

// Page logic

render_admin_header('Page Title');
?>

<!-- HTML content -->

<?php include 'includes/footer.php'; ?>
```

### CSS Organization

```
css/
├── global_styles.css         # Base: reset, typography, utilities
├── [page].css                # Page-specific styles
└── admin_*.css               # Admin panel styles
```

**Naming Convention:**
- Global: `global_styles.css`
- Page-specific: `[pagename].css` (e.g., `index.css`, `profile.css`)
- Admin shared: `admin_common.css`
- Admin page: `admin_[section].css` (e.g., `admin_users.css`)

### JavaScript Organization

```
js/
├── global.js                 # Shared utilities
├── form-formats.js           # Shared format definitions
├── [page].js                 # Page-specific interactions
└── admin_*.js                # Admin-specific scripts
```

**Module Pattern:**
```javascript
// form-formats.js - Shared data
const phoneFormats = { ... };
const zipFormats = { ... };

// profile.js - Uses shared data
function updatePhonePlaceholder() {
    const code = phoneCodeSelect.value;
    const placeholder = phoneFormats[code];
    phoneInput.placeholder = placeholder;
}
```

---

## Security Implementation

### Password Security

```php
// Hashing (registration)
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Verification (login)
if (password_verify($password, $user['password'])) {
    // Login successful
}

// Strength validation
function isStrongPassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,128}$/', $password);
}
```

### SQL Injection Prevention

```php
// ✅ CORRECT: Prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// ❌ WRONG: String concatenation
$query = "SELECT * FROM users WHERE email = '$email'"; // VULNERABLE!
```

### XSS Protection

```php
// ✅ CORRECT: Escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ❌ WRONG: Raw output
echo $user_input; // VULNERABLE!
```

### Rate Limiting

```php
function checkLoginRateLimit($conn, $email, $ip) {
    // Check both email (5 attempts) and IP (10 attempts) in one query
    $stmt = $conn->prepare(
        "SELECT 
            SUM(CASE WHEN description LIKE ? THEN 1 ELSE 0 END) as email_attempts,
            SUM(CASE WHEN ip_address = ? THEN 1 ELSE 0 END) as ip_attempts
         FROM activity_log 
         WHERE action_type = 'failed_login' 
         AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
    );
    
    $email_pattern = '%' . $email . '%';
    $stmt->bind_param("ss", $email_pattern, $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return ($row['email_attempts'] < 5 && $row['ip_attempts'] < 10);
}
```

### Session Security

```php
// Secure session configuration
session_start([
    'cookie_httponly' => true,      // Prevent JavaScript access
    'cookie_samesite' => 'Strict',  // CSRF protection
    'use_strict_mode' => true       // Reject uninitialized session IDs
]);

// Session regeneration after login
session_regenerate_id(true);
```

### Input Validation

```php
// Server-side validation (never trust client)
if (empty($_POST['email'])) {
    $error = 'Email required';
} elseif (!validate_email($_POST['email'])) {
    $error = 'Invalid email format';
} elseif (email_exists($conn, $_POST['email'])) {
    $error = 'Email already registered';
}
```

---

## Development Guidelines

### Code Standards

**PHP:**
- Use prepared statements for ALL database queries
- Sanitize input with `sanitize_input()` or `htmlspecialchars()`
- Validate on server-side (client validation is UX, not security)
- Log important actions with `log_activity()`
- Use meaningful variable names
- Comment complex logic

**JavaScript:**
- Use `const` and `let` (not `var`)
- Add event listeners in DOMContentLoaded
- Provide user feedback for actions
- Handle errors gracefully
- Use shared resources (form-formats.js)

**CSS:**
- Use semantic class names (`.user-dashboard`, not `.box1`)
- Mobile-first responsive design
- Consistent spacing (use CSS variables)
- Avoid `!important` unless necessary

### File Naming

- PHP pages: `lowercase.php` (e.g., `admin_products.php`)
- CSS: `lowercase.css` (e.g., `admin_common.css`)
- JavaScript: `lowercase.js` (e.g., `admin_products.js`)
- Images: `descriptive-name.ext` (e.g., `product-image-1.avif`)

### Adding New Features

1. **Database**: Update schema in `database/` folder
2. **Backend**: Add functions to `config/functions.php`
3. **Page**: Create `newpage.php` following template pattern
4. **Style**: Create `newpage.css`
5. **Script**: Create `newpage.js` if needed
6. **Navigation**: Update `includes/header.php`
7. **Security**: Add activity logging
8. **Testing**: Test all user flows

---

## Troubleshooting

### Common Issues

**Database Connection Failed**
- Check MySQL is running
- Verify credentials in `config/config.php`
- Check database exists

**Session Issues**
- Check PHP session directory is writable
- Verify `session_start()` is called before output
- Check for conflicting session configs

**CSS/JS Not Loading**
- Clear browser cache
- Check file paths (case-sensitive on Linux/Mac)
- Verify files exist in correct directories

**Admin Access Denied**
- Check user has `role = 'admin'` in database
- Verify session is active (`$_SESSION['user_id']` set)
- Check `is_admin()` function logic

---

*Technical Reference - Reliwe Platform - December 2024*
