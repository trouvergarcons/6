<?php
include 'config.php';

// Set session manually for testing
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'john.doe@example.com';
$_SESSION['user_name'] = 'John Doe';
$_SESSION['user_department'] = 'IT';

// Redirect to dashboard
redirect('dashboard.php');
?>
