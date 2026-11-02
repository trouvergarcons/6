<?php
// Database setup script
// This script creates the database and imports the SQL file

// Try different connection approaches
$connection_attempts = [
    // Standard XAMPP setup
    ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
    // Sometimes XAMPP uses 127.0.0.1
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
    // Try with common XAMPP passwords
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'password'],
];

$conn = null;
$successful_attempt = null;

echo "Attempting database connection...\n";

foreach ($connection_attempts as $attempt) {
    $test_conn = @new mysqli($attempt['host'], $attempt['user'], $attempt['pass']);
    
    if (!$test_conn->connect_error) {
        $conn = $test_conn;
        $successful_attempt = $attempt;
        echo "✓ Connected with: host={$attempt['host']}, user={$attempt['user']}\n";
        break;
    } else {
        echo "✗ Failed: " . $test_conn->connect_error . "\n";
    }
}

if (!$conn) {
    echo "\nFailed to connect to MySQL. Please check:\n";
    echo "1. MySQL service is running (check XAMPP Control Panel)\n";
    echo "2. MySQL credentials in php/config.php\n";
    exit(1);
}

echo "\nCreating database...\n";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS department_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
if ($conn->query($sql) === TRUE) {
    echo "✓ Database 'department_portal' created or already exists\n";
} else {
    echo "✗ Error creating database: " . $conn->error . "\n";
    exit(1);
}

// Select database
if (!$conn->select_db("department_portal")) {
    echo "✗ Error selecting database: " . $conn->error . "\n";
    exit(1);
}

echo "✓ Database selected\n\n";

// Read and execute SQL file
$sql_file = __DIR__ . '/database.sql';
if (!file_exists($sql_file)) {
    echo "✗ SQL file not found at: $sql_file\n";
    exit(1);
}

echo "Importing SQL schema...\n";

$sql = file_get_contents($sql_file);

// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

$executed = 0;
foreach ($statements as $statement) {
    if (!empty($statement)) {
        if ($conn->query($statement) === TRUE) {
            $executed++;
        } else {
            echo "✗ Error executing statement: " . $conn->error . "\n";
            echo "   Statement: " . substr($statement, 0, 50) . "...\n";
        }
    }
}

echo "✓ Successfully executed $executed SQL statements\n";

// Verify tables were created
$result = $conn->query("SHOW TABLES");
$table_count = $result->num_rows;
echo "\n✓ Database setup complete!\n";
echo "   Tables created: $table_count\n";

// List tables
echo "\n   Tables:\n";
while ($row = $result->fetch_array()) {
    echo "   - " . $row[0] . "\n";
}

// Check sample data
$users_result = $conn->query("SELECT COUNT(*) as count FROM users");
$users_row = $users_result->fetch_assoc();
echo "\n   Sample users in database: " . $users_row['count'] . "\n";

echo "\n✅ Database is ready!\n";
echo "\nYou can now:\n";
echo "1. Go to http://localhost:8000/register.html to register a new account\n";
echo "2. Or login with sample account:\n";
echo "   Email: john.doe@example.com\n";
echo "   Password: password123\n";

$conn->close();
?>
