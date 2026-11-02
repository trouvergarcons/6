<?php
include 'config.php';

// Initialize error messages
$fullname_error = '';
$email_error = '';
$department_error = '';
$phone_error = '';
$password_error = '';
$confirm_password_error = '';
$general_error = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $fullname = isset($_POST['fullname']) ? sanitizeInput($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validation
    if (empty($fullname)) {
        $fullname_error = 'Full name is required';
    } elseif (strlen($fullname) < 3) {
        $fullname_error = 'Full name must be at least 3 characters';
    }
    
    if (empty($email)) {
        $email_error = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $email_error = 'Invalid email format';
    } else {
        // Check if email already exists using demo mode
        if (getDemoUserByEmail($email)) {
            $email_error = 'Email already registered';
        }
    }
    
    if (empty($department)) {
        $department_error = 'Department is required';
    }
    
    if (empty($phone)) {
        $phone_error = 'Phone number is required';
    } elseif (!preg_match('/^\d{10,}$/', preg_replace('/[\s\-()]/','', $phone))) {
        $phone_error = 'Invalid phone number';
    }
    
    if (empty($password)) {
        $password_error = 'Password is required';
    } elseif (strlen($password) < 6) {
        $password_error = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $confirm_password_error = 'Passwords do not match';
    }
    
    // If no errors, register user
    if (empty($fullname_error) && empty($email_error) && empty($department_error) && 
        empty($phone_error) && empty($password_error) && empty($confirm_password_error)) {
        
        // Hash password
        $hashed_password = hashPassword($password);
        
        // Use demo mode to add user
        if (addDemoUser($fullname, $email, $department, $phone, $hashed_password)) {
            $success_message = 'Registration successful! Please login.';
            // Redirect to login page after 3 seconds
            header("refresh:3;url=login.php");
        } else {
            $general_error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../about.html">About Us</a></li>
                <li><a href="../login.html">Login</a></li>
                <li><a href="../register.html" class="active">Register</a></li>
            </ul>
        </div>
    </nav>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form">
                <h2>User Registration</h2>

                <?php
                // Display success or error messages
                if (!empty($success_message)) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
                }
                if (!empty($general_error)) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($general_error) . '</div>';
                }
                ?>

                <form id="registerForm" method="POST" action="">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
                        <?php if (!empty($fullname_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($fullname_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <?php if (!empty($email_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($email_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="IT" <?php echo (isset($_POST['department']) && $_POST['department'] == 'IT') ? 'selected' : ''; ?>>Information Technology</option>
                            <option value="HR" <?php echo (isset($_POST['department']) && $_POST['department'] == 'HR') ? 'selected' : ''; ?>>Human Resources</option>
                            <option value="Finance" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Finance') ? 'selected' : ''; ?>>Finance</option>
                            <option value="Sales" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Sales') ? 'selected' : ''; ?>>Sales</option>
                            <option value="Marketing" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                        </select>
                        <?php if (!empty($department_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($department_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        <?php if (!empty($phone_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($phone_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter a strong password">
                        <?php if (!empty($password_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($password_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                        <?php if (!empty($confirm_password_error)) echo '<span style="color: #dc2626; font-size: 0.875rem;">' . htmlspecialchars($confirm_password_error) . '</span>'; ?>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" id="agree" name="agree" required>
                        <label for="agree">I agree to the terms and conditions</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Register</button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
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
