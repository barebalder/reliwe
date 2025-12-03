# Configuration & Setup

Complete setup guide for the Reliwe platform.

---

## Prerequisites

- **PHP** 7.4+
- **MySQL** 5.7+ / MariaDB 10.3+
- **Web Server**: Apache (XAMPP/LAMP/MAMP)

---

## Quick Start

1. Place project in web root (e.g., `/htdocs/Login site/`)
2. Create database and import schema
3. Verify config credentials
4. Start Apache + MySQL
5. Visit `http://localhost/Login%20site/`

---

## Database Setup

### 1. Create Database

```sql
CREATE DATABASE login_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Import Schema

Run in order:
```bash
mysql -u root login_system < database/setup_database.sql
mysql -u root login_system < database/setup_admin.sql
mysql -u root login_system < database/setup_cart_system.sql
mysql -u root login_system < database/setup_profiles.sql
```

Or via phpMyAdmin: Import each file sequentially.

### 3. Default Admin Account

| Field    | Value            |
|----------|------------------|
| Email    | admin@reliwe.com   |
| Password | password         |

**Change immediately after first login.**

Generate new hash:
```php
<?php echo password_hash('YourSecurePassword', PASSWORD_DEFAULT);
```

Update in database:
```sql
UPDATE users SET password='<NEW_HASH>' WHERE email='admin@reliwe.com';
```

---

## Configuration Files

### Database Connection
**Location:** `config/config.php`

```php
$servername = "localhost";    // DB host
$username   = "root";         // DB user
$password   = "";             // DB password (empty for XAMPP)
$dbname     = "login_system"; // DB name
```

### Shared Constants
**Location:** `config/constants.php`

Centralized constants used across multiple pages:

```php
define('PHONE_CODES', [    // 11 country phone codes
    '+45' => '🇩🇰 +45',      // Denmark
    '+46' => '🇸🇪 +46',      // Sweden
    // ... 9 more countries
]);

define('COUNTRIES', [      // 13 supported countries
    'Denmark', 'Sweden', 'Norway', 'Finland',
    'Germany', 'Netherlands', 'Belgium', 'France',
    'United Kingdom', 'United States', 'Canada',
    'Australia', 'Other'
]);
```

**Used by:** `profile.php`, `register.php`

### Production Recommendations

- Move `config/` outside web root
- Use environment variables for credentials
- Enable `display_errors = Off`
- Use HTTPS (SSL certificate)

---

## Database Schema

### Core Tables

| Table              | Purpose                          |
|--------------------|----------------------------------|
| `users`            | User accounts with roles/status  |
| `activity_log`     | Security audit trail             |
| `contact_messages` | Support inquiries                |
| `shopping_carts`   | Persistent cart storage          |
| `orders`           | Completed purchases              |
| `order_items`      | Order line items                 |
| `user_preferences` | User settings storage            |
| `user_profiles`    | Customer personal details        |

### Users Table Structure

```sql
users (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  email         VARCHAR(254) UNIQUE NOT NULL,
  password      VARCHAR(255) NOT NULL,        -- bcrypt hash
  role          ENUM('user','admin') DEFAULT 'user',
  status        ENUM('active','suspended','deleted') DEFAULT 'active',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login    DATETIME NULL
)
```

---

## File Structure

```
/Login site/
├── config/
│   ├── config.php          # Database connection (28 lines)
│   ├── constants.php       # Shared constants (28 lines)
│   ├── functions.php       # Core helpers (689 lines, 30+ functions)
│   └── admin_functions.php # Admin helpers (474 lines)
├── database/
│   ├── setup_database.sql  # Core schema (users, activity_log)
│   ├── setup_admin.sql     # Admin user setup
│   ├── setup_cart_system.sql # Cart & orders
│   ├── setup_products.sql  # Product database
│   └── setup_profiles.sql  # User profiles
├── includes/
│   ├── header.php          # Global navigation
│   ├── footer.php          # Global footer
│   └── product_modal.php   # Product CRUD modal
├── css/                    # 14 modular stylesheets
├── js/                     # 8 JavaScript modules
│   ├── form-formats.js     # Shared format definitions
│   ├── profile.js          # Profile interactions
│   ├── register.js         # Registration interactions
│   └── admin_products.js   # Product management
├── Images/                 # Product images
├── docs/                   # 14 documentation files
└── *.php                   # 16 page files
```

---

## Environment Checklist

### Development
- [ ] XAMPP/MAMP running
- [ ] Database created and imported
- [ ] Config credentials set
- [ ] Admin password changed

### Production
- [ ] HTTPS enabled
- [ ] Error display disabled
- [ ] Config outside web root
- [ ] Strong admin password
- [ ] Database backups configured
- [ ] File permissions secured (644/755)

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Connection failed | Check MySQL running, verify credentials |
| 404 errors | Verify Apache DocumentRoot and folder name |
| Session issues | Check `session.save_path` in php.ini |
| CSS not loading | Clear browser cache, check paths |

---

*Last Updated: December 2025*
