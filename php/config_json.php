<?php
/**
 * Database Mock/Fallback Configuration
 * Provides MySQL-like interface using JSON files
 */

if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1800);
    ini_set('session.cookie_httponly', true);
    ini_set('session.cookie_secure', false);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Use JSON-based mock database for demo
class JSONDatabase {
    private $db_path;
    private $data = [];
    
    public function __construct($path) {
        $this->db_path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $this->load();
    }
    
    private function load() {
        $files = ['users', 'login_history', 'departments', 'activity_log'];
        foreach ($files as $file) {
            $file_path = $this->db_path . '/' . $file . '.json';
            if (file_exists($file_path)) {
                $this->data[$file] = json_decode(file_get_contents($file_path), true) ?? [];
            } else {
                $this->data[$file] = [];
            }
        }
    }
    
    private function save() {
        foreach ($this->data as $table => $records) {
            file_put_contents(
                $this->db_path . '/' . $table . '.json',
                json_encode($records, JSON_PRETTY_PRINT)
            );
        }
    }
    
    public function query($sql) {
        // Very basic SQL parsing for demo purposes
        if (stripos($sql, 'CREATE TABLE') !== false) {
            return true;
        }
        if (stripos($sql, 'INSERT') !== false) {
            return true;
        }
        return new JSONQueryResult($this->data, $sql);
    }
    
    public function prepare($sql) {
        return new JSONPreparedStatement($this, $sql);
    }
    
    public function real_escape_string($str) {
        return $str;
    }
    
    public function select_db($db) {
        return true;
    }
    
    public function set_charset($charset) {
        return true;
    }
    
    public function close() {
        $this->save();
    }
    
    public $connect_error = false;
    
    public function addUser($fullname, $email, $department, $phone, $password) {
        $user = [
            'id' => count($this->data['users']) + 1,
            'fullname' => $fullname,
            'email' => $email,
            'department' => $department,
            'phone' => $phone,
            'password' => $password,
            'registration_date' => date('Y-m-d H:i:s'),
            'last_updated' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ];
        $this->data['users'][] = $user;
        $this->save();
        return true;
    }
    
    public function getUserByEmail($email) {
        foreach ($this->data['users'] as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function getDepartments() {
        return $this->data['departments'];
    }
    
    public function initSampleData() {
        $this->data['departments'] = [
            ['id' => 1, 'name' => 'IT', 'description' => 'IT Department'],
            ['id' => 2, 'name' => 'HR', 'description' => 'HR Department'],
            ['id' => 3, 'name' => 'Finance', 'description' => 'Finance Department'],
            ['id' => 4, 'name' => 'Sales', 'description' => 'Sales Department'],
            ['id' => 5, 'name' => 'Marketing', 'description' => 'Marketing Department']
        ];
        
        $password_hash = password_hash('password123', PASSWORD_BCRYPT);
        
        $this->data['users'] = [
            [
                'id' => 1,
                'fullname' => 'John Doe',
                'email' => 'john.doe@example.com',
                'department' => 'IT',
                'phone' => '1234567890',
                'password' => $password_hash,
                'registration_date' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'fullname' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'department' => 'HR',
                'phone' => '0987654321',
                'password' => $password_hash,
                'registration_date' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]
        ];
        
        $this->data['login_history'] = [];
        $this->data['activity_log'] = [];
        $this->save();
    }
}

class JSONPreparedStatement {
    private $db;
    private $sql;
    
    public function __construct($db, $sql) {
        $this->db = $db;
        $this->sql = $sql;
    }
    
    public function execute($params = []) {
        return true;
    }
}

class JSONQueryResult {
    private $data;
    
    public function __construct($data, $sql) {
        $this->data = $data;
    }
}

// Initialize database
$data_dir = dirname(__DIR__) . '/data';
$conn = new JSONDatabase($data_dir);

// Initialize sample data if empty
if (empty($conn->getDepartments())) {
    $conn->initSampleData();
}

// Utility functions
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function logError($error) {
    $log_dir = dirname(__DIR__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    error_log($error);
}

function redirect($location) {
    header('Location: ' . $location);
    exit();
}

function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return array('message' => $message, 'type' => $type);
    }
    return null;
}
?>
