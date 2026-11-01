# Department Portal - Configuration Documentation

## Overview

This document explains how to configure and customize the Department Portal application for different environments and use cases.

---

## Table of Contents

1. [Database Configuration](#database-configuration)
2. [Application Settings](#application-settings)
3. [Security Configuration](#security-configuration)
4. [Email Configuration](#email-configuration)
5. [Environment Variables](#environment-variables)
6. [Customization Guide](#customization-guide)
7. [Advanced Configuration](#advanced-configuration)

---

## Database Configuration

### File Location
`php/config.php`

### Database Connection Settings

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password
define('DB_NAME', 'department_portal'); // Database name
```

### Configuration Options

#### For Local Development (XAMPP/WAMP)
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for XAMPP default
define('DB_NAME', 'department_portal');
```

#### For Remote Server
```php
define('DB_HOST', 'your.remote.server.com');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_strong_password');
define('DB_NAME', 'department_portal');
```

#### For Different MySQL Port
```php
define('DB_HOST', 'localhost:3307');  // Custom port
```

#### For Database Replication
```php
define('DB_HOST', 'primary-db.example.com');  // Primary connection
$backup_conn = new mysqli('backup-db.example.com', DB_USER, DB_PASS, DB_NAME);
```

### Connection Parameters

- **Charset**: UTF-8 (for international characters)
- **Timezone**: Set in PHP configuration
- **Persistent Connections**: Recommended for high-traffic sites

---

## Application Settings

### Session Configuration

Located in `php/config.php`:

```php
// Session timeout (in seconds)
ini_set('session.gc_maxlifetime', 1800);  // 30 minutes

// Cookie settings
ini_set('session.cookie_httponly', true);  // Prevent JavaScript access
ini_set('session.cookie_secure', false);   // Set true for HTTPS only
ini_set('session.cookie_samesite', 'Strict');  // CSRF protection
```

### Password Requirements

Current settings in validation functions:

```php
// Minimum password length
$min_password_length = 6;

// Can be increased to:
$min_password_length = 12;  // For higher security
```

### Form Validation Settings

Phone number validation (regex):
```php
// Current: 10+ digits
/^\d{10,}$/

// For specific formats:
/^(\+\d{1,2}\s?)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/  // US format
```

---

## Security Configuration

### HTTPS/SSL Configuration

Add to `php/config.php`:

```php
// Force HTTPS
if ($_SERVER['HTTPS'] != 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

### Add to `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### CORS Configuration

Add to `php/config.php`:

```php
// Allow specific domains
header('Access-Control-Allow-Origin: https://trusted-domain.com');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

### Security Headers

Already configured in `.htaccess`:

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
```

---

## Email Configuration

### Setup Email Notifications (Future Feature)

Add to `php/config.php`:

```php
// Email settings
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'your-email@gmail.com');
define('MAIL_PASS', 'your-app-password');
define('MAIL_FROM', 'noreply@departmentportal.com');
define('MAIL_FROM_NAME', 'Department Portal');

// For PHPMailer library
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = MAIL_HOST;
$mail->SMTPAuth = true;
$mail->Username = MAIL_USER;
$mail->Password = MAIL_PASS;
$mail->SMTPSecure = 'tls';
$mail->Port = MAIL_PORT;
$mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
```

---

## Environment Variables

### Create `.env` File

Create `.env` in project root:

```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=department_portal

APP_DEBUG=false
APP_ENV=development

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=your-email@gmail.com
MAIL_PASS=your-password
```

### Load Environment Variables

Add to `php/config.php`:

```php
// Load .env file
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Use environment variables
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));
```

---

## Customization Guide

### Change Application Name

1. **Update HTML files** - Search and replace:
   ```html
   <!-- Old -->
   <title>Department Portal</title>
   <div class="logo">Department Portal</div>
   
   <!-- New -->
   <title>Your Company Portal</title>
   <div class="logo">Your Company Portal</div>
   ```

2. **Update CSS** - Modify colors:
   ```css
   :root {
       --primary-color: #2563eb;      /* Blue */
       --secondary-color: #1e40af;    /* Dark Blue */
       --text-color: #1f2937;         /* Dark Gray */
   }
   ```

### Change Departments

Edit `database/database.sql`:

```sql
-- Modify existing departments
INSERT INTO departments (name, description) VALUES
('Engineering', 'Software development and infrastructure'),
('Design', 'UI/UX and graphic design'),
('Operations', 'Day-to-day operations management');
```

Or update in `php/register.php`:

```php
<select id="department" name="department" required>
    <option value="">Select Department</option>
    <option value="Engineering">Engineering</option>
    <option value="Design">Design</option>
    <option value="Operations">Operations</option>
</select>
```

### Customize CSS Colors

Update `:root` variables in `css/style.css`:

```css
:root {
    --primary-color: #6366f1;        /* Indigo */
    --secondary-color: #4f46e5;      /* Darker Indigo */
    --text-color: #0f172a;           /* Slate */
    --light-bg: #f8fafc;             /* Lighter Slate */
}
```

### Add Custom Logo

1. Replace application title with logo:
   ```html
   <div class="logo">
       <img src="../images/logo.png" alt="Logo" style="height: 40px;">
   </div>
   ```

2. Create `images/` folder and add logo file

### Modify Default Page Content

Edit HTML files directly:
- `index.html` - Home page content
- `about.html` - About Us page
- `login.html` - Login page
- `register.html` - Registration page

---

## Advanced Configuration

### Enable Error Logging

Add to `php/config.php`:

```php
// Error logging
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Create logs directory if not exists
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
```

### Database Backup Configuration

Add to `php/config.php`:

```php
function backupDatabase() {
    global $conn;
    $backup_file = __DIR__ . '/backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Create backup directory if not exists
    if (!is_dir(__DIR__ . '/backups')) {
        mkdir(__DIR__ . '/backups', 0755, true);
    }
    
    // Using mysqldump (requires shell access)
    $command = "mysqldump -u " . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " > " . $backup_file;
    exec($command);
    
    return $backup_file;
}
```

### Rate Limiting

Add to `php/config.php`:

```php
function checkRateLimit($user_id, $action, $max_attempts = 5, $time_window = 60) {
    global $conn;
    
    $time = time() - $time_window;
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM activity_log 
                           WHERE user_id = ? AND action = ? AND activity_date > FROM_UNIXTIME(?)");
    $stmt->bind_param("isi", $user_id, $action, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['attempts'] < $max_attempts;
}
```

### Two-Factor Authentication (Future)

```php
function generateTwoFactorCode() {
    return random_int(100000, 999999);
}

function verifyTwoFactorCode($stored_code, $submitted_code) {
    return hash_equals($stored_code, $submitted_code);
}
```

### Custom Authentication Provider

Implement OAuth/OpenID Connect:

```php
// Example: Google OAuth
session_start();
$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/department-portal/php/auth_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
}
```

---

## Testing Configuration

### Local Testing

```php
// Add to top of php/config.php for testing
if ($_SERVER['HTTP_HOST'] == 'localhost:8000' || $_SERVER['HTTP_HOST'] == 'localhost') {
    define('APP_DEBUG', true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    define('APP_DEBUG', false);
    error_reporting(0);
    ini_set('display_errors', 0);
}
```

### Database Testing

Test connection:
```php
// Create test_connection.php
<?php
include 'php/config.php';
echo $conn->ping() ? 'Database connected!' : 'Connection failed!';
?>
```

---

## Production Deployment Checklist

- [ ] Set `APP_DEBUG = false`
- [ ] Update database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set strong database password
- [ ] Configure email settings
- [ ] Set up error logging
- [ ] Enable database backups
- [ ] Update security headers
- [ ] Set file permissions correctly
- [ ] Remove sample/test data
- [ ] Update `php/config.php` for production
- [ ] Test all functionality
- [ ] Monitor error logs
- [ ] Set up regular backups

---

## Support & Resources

- [PHP Configuration Documentation](https://www.php.net/manual/en/ini.php)
- [MySQL Configuration](https://dev.mysql.com/doc/mysql-configuration-reference/5.7/en/)
- [OWASP Security Guidelines](https://owasp.org/)

---

For questions or issues, refer to the main [README.md](README.md) or [SETUP_GUIDE.md](SETUP_GUIDE.md).
