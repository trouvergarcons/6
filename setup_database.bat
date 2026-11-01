@echo off
REM Database Setup Script for Department Portal
REM This script creates and initializes the database

echo ============================================
echo Department Portal Database Setup
echo ============================================
echo.

REM Check if MySQL is installed
if not exist "C:\xampp\mysql\bin\mysql.exe" (
    echo Error: MySQL not found at C:\xampp\mysql\bin\mysql.exe
    echo Please ensure XAMPP is installed correctly.
    pause
    exit /b 1
)

echo Attempting to connect to MySQL...
echo.

REM Try to create database using init.sql file
echo Creating database and tables...
"C:\xampp\mysql\bin\mysql.exe" --user=root < "C:\Users\natur\OneDrive\Desktop\6\database\init.sql"

if errorlevel 1 (
    echo.
    echo Error: Could not connect to MySQL.
    echo.
    echo Possible solutions:
    echo 1. Open XAMPP Control Panel
    echo 2. Click "Start" button for MySQL
    echo 3. Wait for MySQL to show "Running" status
    echo 4. Run this batch file again
    echo.
    echo If you still get errors, the MySQL root user may have a password.
    echo Please update: php/config.php with the correct password.
    echo.
    pause
    exit /b 1
)

echo.
echo ============================================
echo Database setup complete!
echo ============================================
echo.
echo You can now access the application at:
echo http://localhost:8000
echo.
echo Test Login Credentials:
echo Email: john.doe@example.com
echo Password: password123
echo.
pause
