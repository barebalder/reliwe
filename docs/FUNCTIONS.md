# Functions Reference

Complete API documentation for all helper function libraries.

**Last Updated:** December 2024

---

## Overview

| Library | Functions | Lines | Purpose |
|---------|-----------|-------|----------|
| **`config/functions.php`** | 30+ | 689 | Core utilities (auth, validation, user mgmt, helpers) |
| **`config/admin_functions.php`** | 10+ | 474 | Admin-specific (init, rendering, data fetching) |
| **`config/constants.php`** | 2 | 28 | Shared constants (phone codes, countries) |
---

## Quick Reference

### Authentication & Security
- `log_activity()` — Log user actions for audit trail
- `is_admin()` — Check if current user is admin
- `require_admin()` — Enforce admin-only access
- `email_exists()` — Check if email is registered
- `isStrongPassword()` — Validate password strength
- `rehashPassword()` — Hash password with bcrypt
- `checkLoginRateLimit()` — Prevent brute force attacks
- `verify_user_password()` — **NEW** Centralized password verification

### Input Validation & Formatting
- `sanitize_input()` — Clean user input
- `validate_email()` — **NEW** Clean email validation
- `format_phone_input()` — **NEW** Clean phone number
- `get_client_ip()` — **NEW** Get real client IP

### Database Helpers
- `fetch_single_row()` — **NEW** Simplified single-row queries
- `get_user_count()` — Count users by role
- `get_user_profile()` — Fetch user profile data
- `format_datetime()` — Format timestamps for display

### Admin Functions
- `init_admin_page()` — Initialize admin page with security
- `render_admin_header()` — Render admin navigation
- `render_pagination()` — Render pagination controls
- `get_dashboard_stats()` — Fetch dashboard metrics
- `process_product_action()` — Handle product CRUD operations
- `handle_image_upload()` — Process image uploads
- `get_messages_with_counts()` — Fetch support messages
- `get_activity_log()` — Fetch activity log with filtering

---

## Libraries

- **`config/functions.php`** — Core application functions (authentication, cart, user management, support)
- **`config/admin_functions.php`** — Admin-specific functions (page initialization, rendering, data fetching)
- **`config/constants.php`** — Shared constants (phone codes, countries)

---

## Admin Functions (`config/admin_functions.php`)

### `init_admin_page($conn, $page_title)`

Initializes admin page with security checks and session management.

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$page_title` | string | Page title for header |

**Side effects:**
- Starts session
- Enforces admin access
- Sets page title

```php
init_admin_page($conn, 'User Management');
```

---

### `render_admin_header($page_title)`

Outputs consistent admin page header with navigation.

| Param | Type | Description |
|-------|------|-------------|
| `$page_title` | string | Page title to display |

```php
render_admin_header('Products');
```

---

### `render_pagination($current_page, $total_pages)`

Outputs pagination controls for list views.

| Param | Type | Description |
|-------|------|-------------|
| `$current_page` | int | Current page number (1-indexed) |
| `$total_pages` | int | Total number of pages |

```php
render_pagination($_GET['page'] ?? 1, $total_pages);
```

---

### `get_dashboard_stats($conn)`

Fetches all dashboard statistics in single optimized query.

**Returns:** `array` with keys:
- `total_users` — Total registered users
- `new_users_today` — Users registered today
- `total_products` — Product count
- `pending_messages` — Unresolved support messages
- `recent_activity` — Last 10 activity log entries

```php
$stats = get_dashboard_stats($conn);
echo "Total Users: " . $stats['total_users'];
```

---

### `process_product_action($conn, $post_data, $files_data, $user_id)`

Handles all product CRUD operations (add, edit, delete).

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$post_data` | array | $_POST data |
| `$files_data` | array | $_FILES data |
| `$user_id` | int | Admin user ID for logging |

**Returns:** `array` with keys `success` (bool) and `message` (string)

```php
$result = process_product_action($conn, $_POST, $_FILES, $_SESSION['user_id']);
if ($result['success']) {
    echo $result['message'];
}
```

---

### `handle_image_upload($file, $upload_dir = '../Images/')`

Processes single image upload with validation.

| Param | Type | Description |
|-------|------|-------------|
| `$file` | array | Single file from $_FILES |
| `$upload_dir` | string | Upload directory path |

**Returns:** `string|false` — Filename on success, false on failure

**Validation:**
- Allowed types: JPG, JPEG, PNG, GIF, AVIF, WEBP
- Max size: 5MB

---

### `handle_multiple_uploads($files, $upload_dir = '../Images/')`

Processes multiple image uploads.

**Returns:** `array` — Array of uploaded filenames

```php
$images = handle_multiple_uploads($_FILES['product_images']);
```

---

### `get_messages_with_counts($conn, $status = null, $page = 1, $per_page = 20)`

Fetches support messages with status counts in optimized query.

**Returns:** `array` with keys:
- `messages` — Message records
- `counts` — Status counts (new, pending, resolved)
- `total_pages` — Pagination info
- `current_page` — Current page number

```php
$data = get_messages_with_counts($conn, 'new', 1);
echo "New messages: " . $data['counts']['new'];
```

---

### `get_activity_log($conn, $action_type = null, $page = 1, $per_page = 50)`

Fetches activity log with optional filtering and pagination.

| Param | Type | Description |
|-------|------|-------------|
| `$action_type` | string\|null | Filter by action type (login, product_add, etc.) |
| `$page` | int | Page number |
| `$per_page` | int | Results per page |

**Returns:** `array` with keys `activities`, `total_pages`, `current_page`

---

### `get_action_types($conn)`

Returns array of unique action types from activity log.

**Returns:** `array` — Action type strings

```php
$types = get_action_types($conn);
// ['login', 'logout', 'product_add', 'product_edit', ...]
```

---

## Core Functions (`config/functions.php`)

## Activity & Logging

### `log_activity($conn, $user_id, $action_type, $description)`

Records user actions for security auditing.

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$user_id` | int\|null | User ID (null for system/guest) |
| `$action_type` | string | Action category (login, logout, etc.) |
| `$description` | string | Detailed description |

**Returns:** `bool` — Success status

```php
log_activity($conn, $_SESSION['user_id'], 'profile_update', 'Email changed');
```







---

## Authentication & Access Control

### `is_admin()`

Checks if current session user has admin role.

**Returns:** `bool`




php
if (is_admin()) {
    // Show admin controls
}




### `require_admin()`

Enforces admin-only page access. Redirects non-admins to dashboard.

php
require_admin(); // Place at top of admin pages




### `email_exists($conn, $email)`

Checks if email is already registered.

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$email` | string | Email to check |

**Returns:** `bool`





---




## Password Security

### `isStrongPassword($password)`

Validates password meets security requirements:
- 8-128 characters
- Uppercase + lowercase
- Numbers + special characters
- Not in common password list

**Returns:** `bool`




### `rehashPassword($password)`

Hashes password with current PHP defaults (bcrypt).

**Returns:** `string` — Password hash






### `checkLoginRateLimit($conn, $email, $ip)`

Prevents brute force attacks. Allows 5 attempts per email per hour, 10 per IP.
**Optimized to use single query instead of two separate queries (50% faster).**

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$email` | string | Email attempting login |
| `$ip` | string | IP address |

**Returns:** `bool` — True if attempt allowed

```php
if (checkLoginRateLimit($conn, $email, get_client_ip())) {
    // Allow login attempt
}
```

---

## New Helper Functions (January 2025)

### `validate_email($email)`

Validates email format using PHP's built-in filter.

**Returns:** `bool` — True if valid email

```php
if (!validate_email($email)) {
    $error = 'Invalid email format';
}
```

---

### `get_client_ip()`

Retrieves the real IP address of the client, even behind proxies.

**Returns:** `string` — IP address

```php
$ip = get_client_ip();
log_activity($conn, $user_id, 'login', "Login from IP: $ip");
```

---

### `verify_user_password($conn, $email, $password)`

Centralized password verification helper. Checks if provided password matches stored hash.

| Param | Type | Description |
|-------|------|-------------|
| `$conn` | mysqli | Database connection |
| `$email` | string | User email |
| `$password` | string | Plain text password to verify |

**Returns:** `array|false` — User data array on success, false on failure

```php
$user = verify_user_password($conn, $email, $password);
if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
}
```

---

### `format_phone_input($phone)`

Removes all non-numeric characters from phone number input.

| Param | Type | Description |
|-------|------|-------------|
| `$phone` | string | Raw phone number input |

**Returns:** `string` — Cleaned phone number (digits only)

```php
$clean_phone = format_phone_input('+45 12 34 56 78');
// Result: '12345678'
```

---

### `fetch_single_row($stmt)`

Helper to fetch a single row from a prepared statement.

| Param | Type | Description |
|-------|------|-------------|
| `$stmt` | mysqli_stmt | Prepared statement to execute |

**Returns:** `array|null` — Associative array or null if no results

```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$user = fetch_single_row($stmt);
```

---

## User Management

### `get_user_count($conn, $role = null)`

Returns count of active users, optionally filtered by role.

```php
$total = get_user_count($conn);
$admins = get_user_count($conn, 'admin');
```

### `get_all_users($conn, $page = 1, $per_page = 20)`

Fetches paginated user list.

**Returns:** `array` with keys: `users`, `total_pages`, `current_page`, `total_count`

### `update_user_role($conn, $user_id, $new_role)`

Changes user role. Validates against allowed values.

| Param | Type | Values |
|-------|------|--------|
| `$new_role` | string | 'user', 'admin' |

### `update_user_status($conn, $user_id, $new_status)`

Changes account status.

| Param | Type | Values |
|-------|------|--------|
| `$new_status` | string | 'active', 'suspended', 'deleted' |

---

## Cart Functions

### `sync_cart_to_database($conn, $user_id, $cookie_data)`

Transfers cookie cart to database on login.

### `load_user_cart($conn, $user_id)`

Retrieves user's saved cart as JSON.

**Returns:** `string` — JSON cart data

### `update_cart_item($conn, $user_id, $product_id, $name, $price, $image, $qty)`

Updates or removes cart item. Set `$qty = 0` to remove.

### `clear_user_cart($conn, $user_id)`

Removes all items from user's cart (e.g., after checkout).

### `create_order_from_cart($conn, $user_id, $total, $shipping)`

Converts cart to order with transaction safety.

**Returns:** `string|false` — Order number or false on failure

---

## User Preferences

### `save_user_preference($conn, $user_id, $key, $value)`

Stores key-value preference. Updates if exists.

### `get_user_preference($conn, $user_id, $key, $default = '')`

Retrieves preference value with fallback default.

---

## Support System

### `get_contact_messages($conn, $status = 'new')`

Fetches support messages filtered by status.

### `submit_contact_message($conn, $name, $email, $phone, $category, $message, $user_id = null)`

Saves new support inquiry.

---

## Activity Monitoring

### `get_recent_activity($conn, $limit = 10)`

Fetches recent activity log entries with user email.

**Returns:** `array` — Activity records

---

## Utilities

### `sanitize_input($data)`

Cleans user input: trims whitespace, strips slashes, escapes HTML.

**Returns:** `string` — Sanitized data

### `format_datetime($datetime, $format = 'd/m/Y H:i')`

Formats database datetime for display.

**Returns:** `string` — Formatted date or '-' if empty

---

## Usage Pattern

```php
<?php
require_once 'config/config.php';
require_once 'config/functions.php';

// Check admin access
require_admin();

// Get users with pagination
$result = get_all_users($conn, $_GET['page'] ?? 1);

// Log admin action
log_activity($conn, $_SESSION['user_id'], 'admin_view', 'Viewed user list');
```

---

*Last Updated: December 2025*
