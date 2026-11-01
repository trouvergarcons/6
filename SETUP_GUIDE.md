# Department Portal - Setup Guide

## Quick Start Guide

This guide will help you set up and run the Department Portal web application on your local machine or server.

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Configuration](#configuration)
5. [Running the Application](#running-the-application)
6. [Troubleshooting](#troubleshooting)
7. [First Time Usage](#first-time-usage)

---

## System Requirements

### Software
- **PHP**: 7.0 or higher (7.4+ recommended)
- **MySQL**: 5.7 or higher (8.0+ recommended)
- **Web Server**: Apache or Nginx (Apache 2.4+ recommended)
- **Browser**: Modern browser supporting HTML5/CSS3

### Recommended Setup
- **XAMPP**: Includes Apache, MySQL, and PHP
- **WAMP**: Windows alternative
- **LAMP Stack**: For Linux servers
- **Docker**: For containerized setup

---

## Installation Steps

### Option 1: Using XAMPP (Windows/Mac/Linux)

#### Step 1: Download and Install XAMPP
1. Visit [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download XAMPP for your operating system
3. Run the installer and complete installation
4. Choose default options (Apache, MySQL, PHP)

#### Step 2: Extract Project Files
1. Locate your XAMPP htdocs folder:
   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

2. Create a new folder: `department-portal`
3. Extract all project files into this folder

#### Step 3: Start XAMPP Services
1. Open XAMPP Control Panel
2. Click "Start" button for Apache
3. Click "Start" button for MySQL
4. Wait for both to show green "Running" status

### Option 2: Using Built-in PHP Server

#### Step 1: Ensure PHP is Installed
```bash
php -v
```

#### Step 2: Run Development Server
```bash
cd C:\Users\natur\OneDrive\Desktop\6
php -S localhost:8000
```

---

## Database Setup

### Step 1: Create Database Using phpMyAdmin

#### If Using XAMPP:
1. Open browser and go to `http://localhost/phpmyadmin`
2. Login with default credentials:
   - Username: `root`
   - Password: (leave empty)

#### Step 2: Create Database
1. Click "New" in left sidebar
2. Enter database name: `department_portal`
3. Choose charset: `utf8mb4_unicode_ci`
4. Click "Create"

#### Step 3: Import SQL File
1. Select the newly created `department_portal` database
2. Click "Import" tab
3. Click "Choose File" and select `database/database.sql`
4. Click "Go"

### Step 2: Verify Database Created Successfully

After import, you should see these tables:
- `users` - User accounts
- `departments` - Department information
- `login_history` - Login records
- `activity_log` - User activity tracking

---

## Configuration

### Edit Database Configuration

1. Open `php/config.php` with a text editor
2. Verify these settings match your installation:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'department_portal');
```

### Adjust for Your Setup:

- **DB_HOST**: 
  - Local: `localhost`
  - Remote Server: Your server IP/domain

- **DB_USER**: 
  - Default for XAMPP: `root`
  - Adjust if you created a different user

- **DB_PASS**: 
  - XAMPP default: Leave empty (`''`)
  - Production: Set a strong password

- **DB_NAME**: 
  - Should be: `department_portal`

---

## Running the Application

### Via XAMPP with Apache:

1. Open web browser
2. Go to: `http://localhost/department-portal/index.html`
3. You should see the Department Portal homepage

### Via Built-in PHP Server:

1. Start server: `php -S localhost:8000`
2. Open browser
3. Go to: `http://localhost:8000/index.html`

### Via Remote Server:

1. Upload all files to server
2. Go to: `http://your-domain.com/department-portal/index.html`

---

## First Time Usage

### Create Your First Account

1. On homepage, click **"Register"**
2. Fill in registration form:
   - **Full Name**: Your Name (minimum 3 characters)
   - **Email**: your.email@example.com
   - **Department**: Select one from dropdown
   - **Phone**: 10+ digit phone number
   - **Password**: Minimum 6 characters
   - **Confirm Password**: Re-enter password
   - **Terms**: Check agreement box
3. Click **"Register"**
4. You'll be redirected to login page

### Login with Your Account

1. Click **"Login"** button
2. Enter your email and password
3. Click **"Login"**
4. Access your personalized dashboard

### Dashboard Features

After login, you can:
- View your profile information
- Check account status
- View login history
- Edit your profile
- Change your password
- Logout

---

## Testing with Sample Data

If database import included sample users:

**Test Account 1:**
- Email: `john.doe@example.com`
- Password: `password123`

**Test Account 2:**
- Email: `jane.smith@example.com`
- Password: `password123`

---

## File Permissions

For proper operation, ensure:

```
department-portal/
├── php/          (Readable and Writable)
├── css/          (Readable)
├── js/           (Readable)
└── database/     (Readable)
```

**Set Permissions (Linux/Mac):**
```bash
chmod 755 department-portal/
chmod 755 department-portal/php/
chmod 644 department-portal/php/*.php
```

---

## Troubleshooting

### Issue 1: "Connection refused" Error

**Problem**: Cannot connect to database

**Solution**:
1. Ensure MySQL service is running
   - XAMPP: Check MySQL shows "Running"
   - Linux: `sudo service mysql start`
2. Check database credentials in `php/config.php`
3. Verify database exists in phpMyAdmin

### Issue 2: "User not found" During Login

**Problem**: Cannot login with registered account

**Solution**:
1. Verify user exists in database
   - Go to phpMyAdmin → database → users table
   - Check if your email is listed
2. Ensure correct email and password
3. Re-register if account is missing

### Issue 3: Blank Page or 500 Error

**Problem**: Page displays blank or server error

**Solution**:
1. Check PHP error logs
   - XAMPP: `xampp/php/logs/`
2. Verify PHP version: `php -v`
3. Ensure all files extracted properly
4. Check file permissions

### Issue 4: CSS/JavaScript Not Loading

**Problem**: Page displays without styling

**Solution**:
1. Verify relative paths are correct
2. Check browser console for 404 errors
3. Ensure CSS and JS folders exist
4. Clear browser cache

### Issue 5: Registration Fails

**Problem**: Cannot register new account

**Solution**:
1. Check all field validations:
   - Full name ≥ 3 characters
   - Valid email format
   - Phone ≥ 10 digits
   - Password ≥ 6 characters
2. Verify email not already registered
3. Check database permissions

---

## Security Checklist

Before deploying to production:

- [ ] Change default database password
- [ ] Update security headers in `.htaccess`
- [ ] Disable debug mode
- [ ] Set up HTTPS/SSL certificate
- [ ] Regularly backup database
- [ ] Keep PHP and MySQL updated
- [ ] Review user permissions
- [ ] Set up firewall rules
- [ ] Monitor error logs
- [ ] Test input validation

---

## Performance Optimization

### Database Optimization
```sql
-- Add indexes for faster queries
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_department (department);
```

### Caching
- Enable browser caching in `.htaccess`
- Consider implementing server-side caching
- Use CDN for static files on production

### Code Optimization
- Minimize CSS and JavaScript
- Compress images
- Remove unused code
- Use lazy loading for images

---

## Support & Help

### Common Resources
- PHP Documentation: https://www.php.net/
- MySQL Documentation: https://dev.mysql.com/doc/
- HTML/CSS Reference: https://developer.mozilla.org/

### Getting Help
- Check README.md for feature overview
- Review troubleshooting section above
- Check database structure in phpMyAdmin
- Enable error logging for debugging

---

## Next Steps

After successful setup:

1. **Customize**: Update colors, text, and logo
2. **Add Features**: Implement email verification, password reset
3. **Enhance Security**: Add two-factor authentication
4. **Deploy**: Move to production server
5. **Monitor**: Set up error logging and monitoring

---

**Setup Completed!** 🎉

Your Department Portal is now ready to use. Start by registering an account and exploring the dashboard.

For detailed information about features, see [README.md](README.md)
