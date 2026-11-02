<?php
/**
 * Database Setup Wizard
 * Creates database and tables, handles initial setup
 */

header('Content-Type: text/html; charset=utf-8');

// Check if already set up
$setup_flag = dirname(__DIR__) . '/data/.setup_complete';
$is_setup = file_exists($setup_flag);

if ($is_setup && isset($_GET['action']) !== true) {
    header('Location: ../index.html');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondicherry University Department Portal - Setup Wizard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .setup-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .setup-step {
            margin-bottom: 20px;
        }
        .setup-step h3 {
            color: #2563eb;
            margin-bottom: 10px;
        }
        .success {
            color: #059669;
            font-weight: bold;
        }
        .error {
            color: #dc2626;
            font-weight: bold;
        }
        .loading {
            color: #7c3aed;
        }
        .code {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        button {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>🚀 Pondicherry University Department Portal - Setup Wizard</h1>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="setup-step">
                <h3>Initializing Database...</h3>
                
                <?php
                // Create data directory
                $data_dir = dirname(__DIR__) . '/data';
                if (!is_dir($data_dir)) {
                    mkdir($data_dir, 0755, true);
                    echo '<p class="success">✓ Data directory created</p>';
                } else {
                    echo '<p class="success">✓ Data directory exists</p>';
                }
                
                // Try to connect to MySQL
                $mysql_host = 'localhost';
                $mysql_user = 'root';
                $mysql_pass = '';
                $mysql_db = 'department_portal';
                
                echo '<p>Attempting MySQL connection...</p>';
                
                $conn = @new mysqli($mysql_host, $mysql_user, $mysql_pass);
                
                if ($conn->connect_error) {
                    echo '<p class="error">✗ MySQL Connection Error: ' . htmlspecialchars($conn->connect_error) . '</p>';
                    echo '<p>Note: This is common if MySQL root has password protection. No problem!</p>';
                    echo '<p>Switching to SQLite mode...</p>';
                    
                    // Use SQLite instead
                    use_sqlite($data_dir);
                } else {
                    echo '<p class="success">✓ Connected to MySQL</p>';
                    
                    // Create database
                    $sql = "CREATE DATABASE IF NOT EXISTS " . $mysql_db . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    if ($conn->query($sql)) {
                        echo '<p class="success">✓ Database "' . $mysql_db . '" created/exists</p>';
                    } else {
                        echo '<p class="error">✗ Error creating database: ' . htmlspecialchars($conn->error) . '</p>';
                    }
                    
                    // Select database
                    $conn->select_db($mysql_db);
                    $conn->set_charset('utf8mb4');
                    
                    // Create tables
                    if (create_mysql_tables($conn)) {
                        echo '<p class="success">✓ Database tables created successfully</p>';
                        // Create flag
                        file_put_contents($setup_flag, date('Y-m-d H:i:s'));
                    } else {
                        echo '<p class="error">✗ Error creating tables</p>';
                    }
                    
                    $conn->close();
                }
                ?>
                
                <p style="margin-top: 20px;">
                    <strong>Setup Complete!</strong><br>
                    <a href="../index.html" style="color: #2563eb;">← Go to Home Page</a>
                </p>
            </div>
        <?php else: ?>
            <div class="setup-step">
                <p>Welcome! Let's set up your Pondicherry University Department Portal.</p>
                <p>This wizard will:</p>
                <ul>
                    <li>Create the necessary database</li>
                    <li>Set up database tables</li>
                    <li>Initialize with sample data</li>
                </ul>
                
                <form method="POST" style="margin-top: 30px;">
                    <button type="submit">Start Setup</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php

function use_sqlite($data_dir) {
    global $setup_flag;
    
    $db_file = $data_dir . '/department_portal.db';
    
    try {
        $conn = new PDO('sqlite:' . $db_file);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_THROW);
        
        echo '<p class="success">✓ SQLite database created at ' . basename($db_file) . '</p>';
        
        // Create tables
        $tables_sql = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                fullname VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                department VARCHAR(50),
                phone VARCHAR(20),
                password VARCHAR(255) NOT NULL,
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1
            )",
            
            "CREATE TABLE IF NOT EXISTS login_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                logout_time TIMESTAMP,
                ip_address VARCHAR(45),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
            
            "CREATE TABLE IF NOT EXISTS departments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                manager_id INTEGER,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS activity_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                action VARCHAR(255),
                description TEXT,
                activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ];
        
        foreach ($tables_sql as $sql) {
            $conn->exec($sql);
        }
        
        echo '<p class="success">✓ Database tables created</p>';
        
        // Insert sample departments
        $departments = ['IT', 'HR', 'Finance', 'Sales', 'Marketing'];
        $stmt = $conn->prepare("INSERT OR IGNORE INTO departments (name, description) VALUES (?, ?)");
        
        foreach ($departments as $dept) {
            $stmt->execute([$dept, $dept . ' Department']);
        }
        
        echo '<p class="success">✓ Sample departments added</p>';
        
        // Insert sample users
        $password_hash = password_hash('password123', PASSWORD_BCRYPT);
        $users_sql = "INSERT OR IGNORE INTO users (fullname, email, department, phone, password) VALUES 
            ('John Doe', 'john.doe@example.com', 'IT', '1234567890', ?),
            ('Jane Smith', 'jane.smith@example.com', 'HR', '0987654321', ?)";
        
        $stmt = $conn->prepare($users_sql);
        $stmt->execute([$password_hash, $password_hash]);
        
        echo '<p class="success">✓ Sample users added (email: john.doe@example.com / jane.smith@example.com, password: password123)</p>';
        
        // Create flag
        file_put_contents($GLOBALS['setup_flag'], date('Y-m-d H:i:s'));
        
        // Save config to indicate SQLite is active
        $config_file = dirname(__DIR__) . '/php/config.sqlite.php';
        file_put_contents($config_file, '<?php define("USE_SQLITE", true); define("SQLITE_PATH", "' . $db_file . '"); ?>');
        
        $conn = null;
        
    } catch (PDOException $e) {
        echo '<p class="error">✗ SQLite Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        return false;
    }
    
    return true;
}

function create_mysql_tables($conn) {
    $tables_sql = [
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
    
    foreach ($tables_sql as $sql) {
        if (!$conn->query($sql)) {
            logError("Table creation error: " . $conn->error);
            return false;
        }
    }
    
    // Insert sample data
    $password_hash = password_hash('password123', PASSWORD_BCRYPT);
    
    $departments = ['IT', 'HR', 'Finance', 'Sales', 'Marketing'];
    foreach ($departments as $dept) {
        $sql = "INSERT IGNORE INTO departments (name, description) VALUES ('$dept', '$dept Department')";
        $conn->query($sql);
    }
    
    // Insert sample users
    $sql = "INSERT IGNORE INTO users (fullname, email, department, phone, password) VALUES 
        ('John Doe', 'john.doe@example.com', 'IT', '1234567890', '$password_hash'),
        ('Jane Smith', 'jane.smith@example.com', 'HR', '0987654321', '$password_hash')";
    $conn->query($sql);
    
    return true;
}

function logError($message) {
    error_log($message);
}

?>
