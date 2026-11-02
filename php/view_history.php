<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get detailed login history
$stmt = $conn->prepare("SELECT id, login_time, logout_time, ip_address FROM login_history WHERE user_id = ? ORDER BY login_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$login_history = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total logins
$total_logins = count($login_history);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login History - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_history.php" class="active">Login History</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="dashboard-section">
        <div class="container">
            <div class="welcome-box">
                <h2>Login History</h2>
                <p>Track your account access activity</p>
            </div>

            <div style="background: white; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Total Logins:</strong> <?php echo $total_logins; ?></p>
                </div>

                <?php if (count($login_history) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                    <th style="text-align: left; padding: 1rem; font-weight: 600;">Login Time</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600;">Logout Time</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600;">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($login_history as $entry): ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 1rem;">
                                            <?php echo date('M d, Y H:i:s', strtotime($entry['login_time'])); ?>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php echo $entry['logout_time'] ? date('M d, Y H:i:s', strtotime($entry['logout_time'])) : 'Still logged in'; ?>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php echo $entry['ip_address'] ? htmlspecialchars($entry['ip_address']) : 'N/A'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No login history available.</p>
                <?php endif; ?>

                <div style="margin-top: 2rem;">
                    <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
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
