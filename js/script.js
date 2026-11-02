// Form Validation Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize forms if they exist
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegisterForm()) {
                e.preventDefault();
            }
        });
    }
});

// Validate Login Form
function validateLoginForm() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email) {
        alert('Please enter your email address');
        return false;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        return false;
    }

    if (!password) {
        alert('Please enter your password');
        return false;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }

    return true;
}

// Validate Registration Form
function validateRegisterForm() {
    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const department = document.getElementById('department').value;
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const agree = document.getElementById('agree').checked;

    if (!fullname) {
        alert('Please enter your full name');
        return false;
    }

    if (fullname.length < 3) {
        alert('Full name must be at least 3 characters long');
        return false;
    }

    if (!email) {
        alert('Please enter your email address');
        return false;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address');
        return false;
    }

    if (!department) {
        alert('Please select a department');
        return false;
    }

    if (!phone) {
        alert('Please enter your phone number');
        return false;
    }

    if (!isValidPhone(phone)) {
        alert('Please enter a valid phone number');
        return false;
    }

    if (!password) {
        alert('Please enter a password');
        return false;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }

    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }

    if (!agree) {
        alert('Please agree to the terms and conditions');
        return false;
    }

    return true;
}

// Email Validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone Validation (simple check for digits)
function isValidPhone(phone) {
    const phoneRegex = /^\d{10,}$/;
    return phoneRegex.test(phone.replace(/[\s\-()]/g, ''));
}

// Remove Alert Messages after 5 seconds
function removeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
}

// Call removeAlerts on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', removeAlerts);
} else {
    removeAlerts();
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Add loading indicator
function showLoading() {
    document.body.style.opacity = '0.6';
    document.body.style.pointerEvents = 'none';
}

function hideLoading() {
    document.body.style.opacity = '1';
    document.body.style.pointerEvents = 'auto';
}

// Log user action
function logAction(action) {
    console.log('User action: ' + action + ' at ' + new Date().toLocaleString());
}

// Store user preferences in localStorage
function setUserPreference(key, value) {
    localStorage.setItem(key, value);
}

function getUserPreference(key) {
    return localStorage.getItem(key);
}

// Check if user is logged in
function isLoggedIn() {
    return getUserPreference('user_logged_in') === 'true';
}

// Display toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        z-index: 9999;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease-in-out;
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Log page views
function logPageView(page) {
    console.log('Page viewed: ' + page + ' at ' + new Date().toLocaleTimeString());
}

// Initialize on page load
window.addEventListener('load', function() {
    logPageView(document.title);
});