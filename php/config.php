<?php
// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1800);
    ini_set('session.cookie_httponly', true);
    ini_set('session.cookie_secure', false);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Demo mode - using JSON file storage
define('DEMO_DATA_DIR', dirname(__DIR__) . '/data');

// Initialize demo data directory
if (!is_dir(DEMO_DATA_DIR)) {
    @mkdir(DEMO_DATA_DIR, 0755, true);
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
        @mkdir($log_dir, 0755, true);
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

// Initialize demo users
function initDemoUsers() {
    $data_file = DEMO_DATA_DIR . '/users.json';
    if (!file_exists($data_file)) {
        $demo_users = [
            [
                'id' => 1,
                'fullname' => 'John Doe',
                'email' => 'john.doe@example.com',
                'department' => 'IT',
                'phone' => '1234567890',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
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
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'registration_date' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]
        ];
        @file_put_contents($data_file, json_encode($demo_users, JSON_PRETTY_PRINT));
    }
}

// Get demo user by email
function getDemoUserByEmail($email) {
    initDemoUsers();
    $data_file = DEMO_DATA_DIR . '/users.json';
    if (file_exists($data_file)) {
        $users = @json_decode(file_get_contents($data_file), true);
        if (is_array($users)) {
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    return $user;
                }
            }
        }
    }
    return null;
}

// Add demo user
function addDemoUser($fullname, $email, $department, $phone, $password) {
    initDemoUsers();
    $data_file = DEMO_DATA_DIR . '/users.json';
    $users = @json_decode(file_get_contents($data_file), true) ?? [];
    
    // Check if email exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return false;
        }
    }
    
    // Add new user
    $new_user = [
        'id' => (count($users) > 0 ? max(array_column($users, 'id')) : 0) + 1,
        'fullname' => $fullname,
        'email' => $email,
        'department' => $department,
        'phone' => $phone,
        'password' => $password,
        'registration_date' => date('Y-m-d H:i:s'),
        'last_updated' => date('Y-m-d H:i:s'),
        'is_active' => 1
    ];
    $users[] = $new_user;
    @file_put_contents($data_file, json_encode($users, JSON_PRETTY_PRINT));
    return true;
}

// Initialize demo data
initDemoUsers();
?>

