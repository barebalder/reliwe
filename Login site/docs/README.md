# Reliwe Platform - Documentation

Complete e-commerce platform for wellness technology products.

**Last Updated:** December 2024

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| **README.md** (this file) | Quick start & overview |
| **[UX_BACHELOR_REPORT.md](UX_BACHELOR_REPORT.md)** | Complete Academic Bachelor's Thesis ⭐ |
| **[5_ELEMENTS_UX.md](5_ELEMENTS_UX.md)** | UX design framework analysis |
| **[TECHNICAL_REFERENCE.md](TECHNICAL_REFERENCE.md)** | Complete technical documentation |
| **[FUNCTIONS.md](FUNCTIONS.md)** | API reference for all functions |
| **[CONFIG.md](CONFIG.md)** | Setup and configuration guide |
| **[DEFENSE_GUIDE.md](DEFENSE_GUIDE.md)** | Exam preparation guide |

---

## Quick Start

```bash
# 1. Start XAMPP (Apache + MySQL)

# 2. Create database
mysql -u root -e "CREATE DATABASE login_system"

# 3. Import schema
cd database/
mysql -u root login_system < setup_database.sql
mysql -u root login_system < setup_admin.sql

# 4. Visit
open http://localhost/Login%20site/
```

**Default Admin:**  
Email: `admin@reliwe.com` | Password: `password`

---

## Project Structure

```
/Login site/
├── config/              # Configuration & helpers
│   ├── config.php           # Database connection
│   ├── constants.php        # Shared constants (phone codes, countries)
│   ├── functions.php        # 30+ core functions (689 lines)
│   └── admin_functions.php  # Admin helpers (474 lines)
│
├── database/            # SQL schema (4 setup files)
├── includes/            # Reusable components
│   ├── header.php           # Navigation
│   ├── footer.php           # Footer
│   └── product_modal.php    # Product CRUD modal
│
├── css/                 # 14 stylesheets
├── js/                  # 8 JavaScript modules
│   ├── form-formats.js      # Shared phone/zip formats
│   └── ...
│
├── docs/                # Documentation (this folder)
└── *.php                # 16 page files
```

---

## Features

### Public
- Landing, About, Technology, Purchase, Support pages
- Session-based shopping cart

### User Area
- Registration & login with validation
- Dashboard, profile management
- Order history

### Admin Panel
- Dashboard with statistics
- User management (view, suspend, delete)
- Product management (CRUD, multi-image upload)
- Support messages handling
- Activity log monitoring

---

## Technology Stack

```
Backend:   PHP 7.4+ with MySQLi
Database:  MySQL 5.7+ / MariaDB 10.3+
Frontend:  HTML5, CSS3, Vanilla JavaScript
Server:    Apache (XAMPP)
Security:  Bcrypt, Prepared Statements, XSS Protection, Rate Limiting
```

---

## Database Schema

| Table | Purpose |
|-------|---------|
| `users` | Accounts (email, password, role, status) |
| `user_profiles` | Personal details (name, phone, address) |
| `products` | Product catalog |
| `shopping_carts` | Persistent cart storage |
| `orders` + `order_items` | Purchase history |
| `contact_messages` | Support inquiries |
| `activity_log` | Security audit trail |

---

## Key Files

| File | Purpose | Lines |
|------|---------|-------|
| `config/functions.php` | Core helper functions | 689 |
| `config/admin_functions.php` | Admin helpers | 474 |
| `admin_products.php` | Product management | 96 |
| `profile.php` | User profile management | 356 |
| `register.php` | Account creation | 275 |

---

## Security

- ✅ Bcrypt password hashing
- ✅ 100% prepared statements (SQL injection prevention)
- ✅ XSS protection (htmlspecialchars on output)
- ✅ Login rate limiting (5/email, 10/IP per hour)
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Activity logging (audit trail)

---

## For Exam Preparation

**Priority Reading Order:**
1. **[UX_BACHELOR_REPORT.md](UX_BACHELOR_REPORT.md)** — Complete bachelor's thesis (primary document)
2. **[5_ELEMENTS_UX.md](5_ELEMENTS_UX.md)** — UX design analysis (strategy → surface)
3. **[DEFENSE_GUIDE.md](DEFENSE_GUIDE.md)** — Talking points & Q&A preparation
4. **[TECHNICAL_REFERENCE.md](TECHNICAL_REFERENCE.md)** — Deep technical details
5. **[FUNCTIONS.md](FUNCTIONS.md)** — Function API reference

---

*Academic Project — Bachelor's Degree in UX Design — December 2024*
