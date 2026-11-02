<?php
include 'config.php';

// Destroy session
session_destroy();

// Clear cookies
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

// Redirect to login page
redirect('login.php?message=Logged out successfully');
?>
