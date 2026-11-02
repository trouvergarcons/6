<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT fullname, email, department, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = isset($_POST['fullname']) ? sanitizeInput($_POST['fullname']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department']) : '';
    
    if (empty($fullname)) {
        $error = 'Full name is required';
    } elseif (strlen($fullname) < 3) {
        $error = 'Full name must be at least 3 characters';
    } elseif (empty($phone)) {
        $error = 'Phone is required';
    } elseif (!preg_match('/^\d{10,}$/', preg_replace('/[\s\-()]/','', $phone))) {
        $error = 'Invalid phone number';
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ?, department = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $phone, $department, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Profile updated successfully!';
            $_SESSION['user_name'] = $fullname;
            $_SESSION['user_department'] = $department;
            $user['fullname'] = $fullname;
            $user['phone'] = $phone;
            $user['department'] = $department;
        } else {
            $error = 'Failed to update profile';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="edit_profile.php" class="active">Edit Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form">
                <h2>Edit Profile</h2>

                <?php
                if (!empty($success)) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
                }
                if (!empty($error)) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($error) . '</div>';
                }
                ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($user['fullname']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" disabled placeholder="Your email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Email cannot be changed</p>
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="IT" <?php echo $user['department'] == 'IT' ? 'selected' : ''; ?>>Information Technology</option>
                            <option value="HR" <?php echo $user['department'] == 'HR' ? 'selected' : ''; ?>>Human Resources</option>
                            <option value="Finance" <?php echo $user['department'] == 'Finance' ? 'selected' : ''; ?>>Finance</option>
                            <option value="Sales" <?php echo $user['department'] == 'Sales' ? 'selected' : ''; ?>>Sales</option>
                            <option value="Marketing" <?php echo $user['department'] == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Update Profile</button>
                </form>

                <div class="auth-footer">
                    <p><a href="dashboard.php">← Back to Dashboard</a></p>
                    <p style="margin-top: 1rem;"><a href="change_password.php">Change Password</a></p>
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
