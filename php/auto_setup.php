<?php
/**
 * Pondicherry University Department Portal - Database Setup Helper
 * This script helps you set up the database if manual setup fails
 */

// Get current working directory
$project_root = dirname(__DIR__);

// Check if we're being called from web
$is_web = isset($_SERVER['HTTP_HOST']);

if ($is_web) {
    echo "<h2>Pondicherry University Department Portal - Database Setup</h2>";
    echo "<p>This page will help you verify your database connection and create the necessary tables.</p>";
    echo "<hr>";
}

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_name = 'department_portal';

// Try different passwords
$passwords_to_try = ['', 'root', 'password', 'root@123', 'xampp'];

echo ($is_web ? '<h3>' : '') . "Attempting Database Connection..." . ($is_web ? '</h3>' : '') . "\n";

$connection = null;
$successful_password = null;

foreach ($passwords_to_try as $password) {
    $attempt_msg = "Trying with password: " . ($password ? "'$password'" : "'(empty)'") . " ... ";
    echo $attempt_msg;
    
    // Try to connect
    $test_conn = @mysqli_connect($db_host, $db_user, $password);
    
    if ($test_conn && !mysqli_connect_error()) {
        echo "✓ SUCCESS\n";
        $connection = $test_conn;
        $successful_password = $password;
        break;
    } else {
        echo "✗ Failed\n";
    }
}

if (!$connection) {
    echo "\n" . ($is_web ? '<h3 style="color:red;">' : '') . "ERROR: Could not connect to MySQL" . ($is_web ? '</h3>' : '') . "\n";
    echo "Please ensure:\n";
    echo "1. MySQL service is running\n";
    echo "2. You can try manually running XAMPP Control Panel and clicking Start for MySQL\n";
    echo "3. Check if the root password has been set to something other than empty\n";
    echo "\nIf MySQL is running but not connecting, the issue might be:\n";
    echo "- MySQL root account has a password set\n";
    echo "- MySQL is listening on a different port\n";
    echo "- MySQL socket permissions\n";
    
    if ($is_web) {
        echo "\n<p><strong>Manual Setup Instructions:</strong></p>";
        echo "<ol>";
        echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Create a new database named: <code>department_portal</code></li>";
        echo "<li>Use collation: <code>utf8mb4_unicode_ci</code></li>";
        echo "<li>Go to SQL tab and copy-paste content from: <code>database/database.sql</code></li>";
        echo "<li>Click Go</li>";
        echo "</ol>";
    }
    exit(1);
}

// Connection successful
echo "\n✓ Connected to MySQL\n";
echo "  Host: $db_host\n";
echo "  User: $db_user\n";
echo "  Password: " . ($successful_password ? "set to '$successful_password'" : "empty") . "\n";

// Now try to create the database
echo "\n" . ($is_web ? '<h3>' : '') . "Creating Database..." . ($is_web ? '</h3>' : '') . "\n";

$create_db_sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

if (!mysqli_query($connection, $create_db_sql)) {
    echo "✗ Error creating database: " . mysqli_error($connection) . "\n";
    mysqli_close($connection);
    exit(1);
}

echo "✓ Database '$db_name' created or already exists\n";

// Select the database
if (!mysqli_select_db($connection, $db_name)) {
    echo "✗ Error selecting database: " . mysqli_error($connection) . "\n";
    mysqli_close($connection);
    exit(1);
}

echo "✓ Database selected\n";

// Read SQL file
$sql_file = $project_root . '/database/database.sql';

if (!file_exists($sql_file)) {
    echo "\n✗ Error: SQL file not found at: $sql_file\n";
    mysqli_close($connection);
    exit(1);
}

echo "\n" . ($is_web ? '<h3>' : '') . "Importing Database Schema..." . ($is_web ? '</h3>' : '') . "\n";

$sql_content = file_get_contents($sql_file);

// Split by semicolon and execute each statement
$statements = array_filter(
    array_map('trim', explode(';', $sql_content)),
    fn($s) => !empty($s) && !str_starts_with(trim($s), '--')
);

$executed = 0;
$errors = 0;

foreach ($statements as $statement) {
    if (!empty($statement)) {
        if (mysqli_query($connection, $statement)) {
            $executed++;
        } else {
            $errors++;
            echo "✗ Error: " . mysqli_error($connection) . "\n";
            echo "  Statement: " . substr($statement, 0, 50) . "...\n";
        }
    }
}

echo "✓ Executed $executed SQL statements\n";
if ($errors > 0) {
    echo "⚠ $errors errors occurred\n";
}

// Verify tables
$result = mysqli_query($connection, "SHOW TABLES");
$table_count = mysqli_num_rows($result);

echo "\n" . ($is_web ? '<h3>' : '') . "Database Summary" . ($is_web ? '</h3>' : '') . "\n";
echo "Tables created: $table_count\n";

echo "\nTables:\n";
while ($row = mysqli_fetch_array($result)) {
    echo "  - " . $row[0] . "\n";
}

// Check sample data
$users = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as count FROM users"));
echo "\nSample users: " . $users['count'] . "\n";

mysqli_close($connection);

echo "\n" . ($is_web ? '<hr>' : '');
echo "✅ Database setup complete!\n\n";
echo "You can now:\n";
echo "1. Register a new account at: http://localhost:8000/register.html\n";
echo "2. Or login with sample account:\n";
echo "   Email: john.doe@example.com\n";
echo "   Password: password123\n";

if ($is_web) {
    echo "\n<p><a href='http://localhost:8000'>Go to Application</a></p>";
}

?>
