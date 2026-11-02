<?php
include 'config.php';

// Initialize variables
$email_error = '';
$password_error = '';
$general_error = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validation
    if (empty($email)) {
        $email_error = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $email_error = 'Invalid email format';
    }
    
    if (empty($password)) {
        $password_error = 'Password is required';
    }
    
    // If no errors, process login
    if (empty($email_error) && empty($password_error)) {
        
        // Demo mode - check against demo users
        $user = getDemoUserByEmail($email);
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Password is correct, set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_department'] = $user['department'];
            $_SESSION['logged_in'] = true;
            
            // Remember me functionality
            if ($remember) {
                setcookie("user_email", $email, time() + (86400 * 30), "/");
            }
            
            // Log login activity
            $login_history_file = DEMO_DATA_DIR . '/login_history.json';
            $login_history = file_exists($login_history_file) ? @json_decode(file_get_contents($login_history_file), true) : [];
            if (!is_array($login_history)) $login_history = [];
            $login_history[] = [
                'user_id' => $user['id'],
                'login_time' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ];
            @file_put_contents($login_history_file, json_encode($login_history, JSON_PRETTY_PRINT));
            
            // Redirect to dashboard using meta refresh
            header('refresh:0;url=dashboard.php');
            exit;
        } else {
            $general_error = 'Invalid email or password';
        }
    }
}

// Check if user wants to be remembered
$remembered_email = '';
if (isset($_COOKIE['user_email'])) {
    $remembered_email = $_COOKIE['user_email'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../about.html">About Us</a></li>
                <li><a href="../login.html" class="active">Login</a></li>
                <li><a href="../register.html">Register</a></li>
            </ul>
        </div>
    </nav>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form">
                <h2>User Login</h2>

                <?php
                // Display error messages
                if (!empty($general_error)) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($general_error) . '</div>';
                }
                if (!empty($email_error)) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($email_error) . '</div>';
                }
                if (!empty($password_error)) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($password_error) . '</div>';
                }
                if (!empty($success_message)) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
                }
                ?>

                <form id="loginForm" method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($remembered_email); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>

                    <div class="form-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Login</button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="../register.html">Register here</a></p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Pondicherry University Department Portal. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
