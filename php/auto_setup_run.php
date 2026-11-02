<?php
/**
 * Auto-Setup Script - Runs database initialization without user interaction
 */

header('Content-Type: text/html; charset=utf-8');

// Create data directory
$data_dir = dirname(__DIR__) . '/data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

$setup_flag = $data_dir . '/.setup_complete';
$output = "";

// If already set up, show success
if (file_exists($setup_flag)) {
    echo "<!DOCTYPE html><html><head><title>Setup Complete</title><style>body{font-family:Arial;padding:20px;background:#f3f4f6;}div{max-width:600px;margin:100px auto;padding:40px;background:white;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);}h1{color:#059669;}.success{color:#059669;}.info{color:#2563eb;}</style></head><body><div><h1>✓ Setup Complete!</h1><p class='success'>Your Pondicherry University Department Portal is ready to use.</p><p class='info'>Test users created:</p><ul><li>Email: john.doe@example.com (Password: password123)</li><li>Email: jane.smith@example.com (Password: password123)</li></ul><p><a href='../index.html' style='color:#2563eb;text-decoration:none;'>← Go to Home Page</a></p></div></body></html>";
    exit;
}

// Try MySQL connection
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = '';
$mysql_db = 'department_portal';

$conn = @new mysqli($mysql_host, $mysql_user, $mysql_pass);

if ($conn && !$conn->connect_error) {
    // MySQL available - create database
    $output .= "✓ Connected to MySQL\n";
    
    $sql = "CREATE DATABASE IF NOT EXISTS " . $mysql_db . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        $output .= "✓ Database created/exists\n";
        $conn->select_db($mysql_db);
        $conn->set_charset('utf8mb4');
        
        // Create tables
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                fullname VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                department VARCHAR(50),
                phone VARCHAR(20),
                password VARCHAR(255) NOT NULL,
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1,
                INDEX idx_email (email),
                INDEX idx_department (department)
            )",
            "CREATE TABLE IF NOT EXISTS login_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                logout_time TIMESTAMP NULL,
                ip_address VARCHAR(45),
                FOREIGN KEY (user_id) REFERENCES users(id),
                INDEX idx_user_id (user_id),
                INDEX idx_login_time (login_time)
            )",
            "CREATE TABLE IF NOT EXISTS departments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                manager_id INT,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_name (name)
            )",
            "CREATE TABLE IF NOT EXISTS activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(255),
                description TEXT,
                activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                INDEX idx_user_id (user_id),
                INDEX idx_activity_date (activity_date)
            )"
        ];
        
        foreach ($tables as $table_sql) {
            if (!$conn->query($table_sql)) {
                $output .= "✗ Error creating table\n";
            }
        }
        
        $output .= "✓ Tables created\n";
        
        // Insert sample data
        $password_hash = password_hash('password123', PASSWORD_BCRYPT);
        
        $depts_sql = "INSERT IGNORE INTO departments (name, description) VALUES
            ('IT', 'IT Department'),
            ('HR', 'HR Department'),
            ('Finance', 'Finance Department'),
            ('Sales', 'Sales Department'),
            ('Marketing', 'Marketing Department')";
        $conn->query($depts_sql);
        
        $users_sql = "INSERT IGNORE INTO users (fullname, email, department, phone, password) VALUES
            ('John Doe', 'john.doe@example.com', 'IT', '1234567890', '" . $conn->real_escape_string($password_hash) . "'),
            ('Jane Smith', 'jane.smith@example.com', 'HR', '0987654321', '" . $conn->real_escape_string($password_hash) . "')";
        $conn->query($users_sql);
        
        $output .= "✓ Sample data inserted\n";
        $output .= "✓ Setup complete with MySQL!\n";
        
        $conn->close();
        $setup_success = true;
    } else {
        $output .= "✗ Error creating database: " . $conn->error . "\n";
    }
} else {
    // MySQL failed
    $output .= "MySQL connection failed\n";
    $output .= "Please check MySQL/MariaDB service is running\n";
    $setup_success = false;
}

// Create setup flag if successful
if (isset($setup_success) && $setup_success) {
    file_put_contents($setup_flag, date('Y-m-d H:i:s'));
    $setup_message = "✓ Setup Successful!";
    $setup_class = "success";
} else {
    $setup_message = "⚠ Setup encountered issues";
    $setup_class = "warning";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pondicherry University Department Portal - Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f3f4f6;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2563eb;
        }
        .success {
            color: #059669;
            font-weight: bold;
            margin: 20px 0;
            font-size: 18px;
        }
        .warning {
            color: #dc2626;
            font-weight: bold;
            margin: 20px 0;
            font-size: 18px;
        }
        .output {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            margin: 15px 0;
            font-size: 14px;
        }
        .info {
            background: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        a {
            color: #2563eb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .test-users {
            background: #ecfdf5;
            border-left: 4px solid #059669;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Pondicherry University Department Portal Setup</h1>
        
        <div class="<?php echo $setup_class; ?>">
            <?php echo $setup_message; ?>
        </div>
        
        <div class="output"><?php echo htmlspecialchars($output); ?></div>
        
        <div class="test-users">
            <strong>Test Users:</strong>
            <ul>
                <li><strong>Email:</strong> john.doe@example.com</li>
                <li><strong>Password:</strong> password123</li>
            </ul>
            <ul>
                <li><strong>Email:</strong> jane.smith@example.com</li>
                <li><strong>Password:</strong> password123</li>
            </ul>
        </div>
        
        <div class="info">
            <strong>Next Steps:</strong>
            <ol>
                <li>Go back to the home page</li>
                <li>Click "Login"</li>
                <li>Use one of the test user credentials above</li>
                <li>Explore the dashboard!</li>
            </ol>
        </div>
        
        <p><a href="../index.html">← Go to Home Page</a></p>
    </div>
</body>
</html>
