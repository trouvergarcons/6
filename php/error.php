<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Pondicherry University Department Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">Pondicherry University Department Portal</div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../about.html">About Us</a></li>
            </ul>
        </div>
    </nav>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-form" style="text-align: center;">
                <h2 style="color: #ef4444;">⚠️ Error</h2>
                
                <div class="alert alert-error">
                    <p style="font-size: 1rem; margin: 0;">
                        <?php
                        $error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'An error occurred. Please try again.';
                        echo $error_message;
                        ?>
                    </p>
                </div>

                <div style="margin-top: 2rem;">
                    <p>Possible solutions:</p>
                    <ul style="list-style: none; text-align: left; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                        <li>✓ Check your internet connection</li>
                        <li>✓ Verify all form fields are filled correctly</li>
                        <li>✓ Clear browser cache and cookies</li>
                        <li>✓ Try a different browser</li>
                        <li>✓ Contact administrator if problem persists</li>
                    </ul>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="../index.html" class="btn btn-primary">Go to Home</a>
                    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-left: 0.5rem;">Go Back</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Pondicherry University Department Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
