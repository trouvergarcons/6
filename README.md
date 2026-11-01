# Department Portal - Complete Web Application

A dynamic department management system built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- **Home Page**: Welcome page with features overview
- **User Registration**: Create new user accounts with validation
- **User Login**: Secure login with session management
- **Dashboard**: Personalized dashboard for logged-in users
- **About Us**: Information about the department portal
- **Database Integration**: MySQL database with user management
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Security**: Password hashing, input validation, SQL injection prevention

## Project Structure

```
department-portal/
├── index.html              # Home page
├── login.html              # Login page
├── register.html           # Registration page
├── about.html              # About Us page
├── css/
│   └── style.css           # Main stylesheet
├── js/
│   └── script.js           # Client-side validation and functionality
├── php/
│   ├── config.php          # Database configuration
│   ├── login.php           # Login processing
│   ├── register.php        # Registration processing
│   ├── dashboard.php       # User dashboard
│   └── logout.php          # Logout functionality
└── database/
    └── database.sql        # Database schema and sample data
```

## Requirements

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web Server (Apache, Nginx, or built-in PHP server)
- Modern web browser

## Installation & Setup

### Step 1: Download and Extract Files
Extract all files to your web server's root directory (typically `htdocs` for XAMPP).

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database (or the script will create it)
3. Go to the SQL tab and paste the contents of `database/database.sql`
4. Click "Go" to execute the SQL script

### Step 3: Configure Database Connection
Edit `php/config.php` if needed:
- `DB_HOST`: localhost (default)
- `DB_USER`: root (default for XAMPP)
- `DB_PASS`: (leave empty for XAMPP default)
- `DB_NAME`: department_portal

### Step 4: Start Your Web Server
If using XAMPP:
1. Start Apache and MySQL from XAMPP Control Panel
2. Navigate to http://localhost/path-to-project/index.html

If using built-in PHP server:
```bash
cd path-to-project
php -S localhost:8000
```

## Usage

### For New Users
1. Click "Register" link
2. Fill in all required fields:
   - Full Name (minimum 3 characters)
   - Email (must be valid)
   - Department (select from dropdown)
   - Phone Number (minimum 10 digits)
   - Password (minimum 6 characters)
   - Confirm Password
3. Accept terms and conditions
4. Click "Register"
5. You'll be redirected to login page

### For Existing Users
1. Click "Login" link
2. Enter your email and password
3. Optionally check "Remember me" to save email
4. Click "Login"
5. Access your personalized dashboard

### Dashboard Features
- View profile information
- Check account status
- View login history
- Quick access to profile settings (to be implemented)

## Database Schema

### Users Table
- `id`: Unique user identifier
- `fullname`: User's full name
- `email`: User's email (unique)
- `department`: Department assignment
- `phone`: Contact phone number
- `password`: Hashed password
- `registration_date`: Account creation date
- `is_active`: Account status

### Login History Table
Tracks all user login attempts with timestamp and IP address

### Departments Table
Contains department information and descriptions

### Activity Log Table
Audit trail of user activities

## Security Features

1. **Password Hashing**: Uses bcrypt for secure password storage
2. **Input Validation**: Client-side and server-side validation
3. **SQL Injection Prevention**: Uses prepared statements
4. **XSS Prevention**: HTML encoding of user inputs
5. **Session Management**: Secure session handling
6. **Remember Me**: Secure cookie implementation

## Test Credentials (After Setup)

If sample data is inserted:
- Email: john.doe@example.com
- Password: password123

OR

- Email: jane.smith@example.com
- Password: password123

## Troubleshooting

### Database Connection Error
- Ensure MySQL server is running
- Check database credentials in `php/config.php`
- Verify database name matches

### "User not found" during login
- Ensure you've registered an account first
- Check that email is entered correctly

### Password hash not working
- Ensure PHP version 5.5+ (for password_hash function)
- Or update PHP to a newer version

### 404 Error on login
- Ensure all PHP files are in the `php/` directory
- Check server supports PHP

## Future Enhancements

- User profile editing
- Change password functionality
- View detailed login history
- Department-wise user listing
- Admin panel for user management
- Email verification
- Password reset functionality
- User roles and permissions
- File upload capabilities
- Search and filter features

## File Permissions

Ensure the following permissions:
- `php/` directory: readable and writable
- `database/` directory: readable
- All `.html`, `.css`, `.js` files: readable

## Support & Contact

For issues or questions:
- Email: info@departmentportal.com
- Department Portal Support Team

## License

This project is provided as-is for educational and commercial use.

## Version History

- v1.0 (Initial Release)
  - Basic authentication system
  - User registration and login
  - Dashboard functionality
  - Database integration
  - Responsive design

---

**Last Updated**: 2024
**Created for**: Complete Department Management System
