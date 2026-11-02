<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    redirect('login.php');
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_department = $_SESSION['user_department'];

// Get user details from demo mode
$user = getDemoUserByEmail($user_email);
if (!$user) {
    redirect('login.php');
}

// Get login history from demo mode
$login_history_file = DEMO_DATA_DIR . '/login_history.json';
$login_history = file_exists($login_history_file) ? json_decode(file_get_contents($login_history_file), true) : [];

// Filter for current user
$user_login_history = array_filter($login_history, function($entry) use ($user_id) {
    return $entry['user_id'] == $user_id;
});

// Sort by login_time descending and limit to 5
usort($user_login_history, function($a, $b) {
    return strtotime($b['login_time']) - strtotime($a['login_time']);
});
$user_login_history = array_slice($user_login_history, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../about.html">About Us</a></li>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="dashboard-section">
        <div class="container">
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <p>This is your personal dashboard. Manage your profile and view your information.</p>
            </div>

            <div class="user-info-grid">
                <!-- User Profile Card -->
                <div class="info-card">
                    <h3>Profile Information</h3>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <p><strong>Registered:</strong> <?php echo date('M d, Y', strtotime($user['registration_date'])); ?></p>
                </div>

                <!-- Department Info Card -->
                <div class="info-card">
                    <h3>Department Information</h3>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
                    <p>You are currently assigned to the <strong><?php echo htmlspecialchars($user['department']); ?></strong> department.</p>
                    <p>For department-specific inquiries, please contact your department manager.</p>
                </div>

                <!-- Account Status Card -->
                <div class="info-card">
                    <h3>Account Status</h3>
                    <p><strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">Active</span></p>
                    <p>Your account is active and in good standing.</p>
                    <p>Last login: <?php echo isset($login_history[1]) ? date('M d, Y H:i:s', strtotime($login_history[1]['login_time'])) : 'First login'; ?></p>
                </div>

                <!-- Quick Actions Card -->
                <div class="info-card">
                    <h3>Quick Actions</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="edit_profile.php" style="color: #2563eb; text-decoration: none;">✎ Edit Profile</a></li>
                        <li><a href="change_password.php" style="color: #2563eb; text-decoration: none;">🔒 Change Password</a></li>
                        <li><a href="view_history.php" style="color: #2563eb; text-decoration: none;">📋 View Login History</a></li>
                        <li><a href="logout.php" style="color: #ef4444; text-decoration: none;">⊗ Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Login History Section -->
            <div style="margin-top: 2rem;">
                <div class="info-card">
                    <h3>Recent Login History</h3>
                    <?php if (count($user_login_history) > 0): ?>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e5e7eb;">
                                    <th style="text-align: left; padding: 0.5rem 0;">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_login_history as $entry): ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 0.75rem 0;">
                                            <?php echo date('M d, Y H:i:s', strtotime($entry['login_time'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No login history available.</p>
                    <?php endif; ?>
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
