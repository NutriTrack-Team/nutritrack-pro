<?php
// Session Management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check user role
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function isDietitian() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'dietitian';
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    header('Location: /nutritrack-pro/index.php');
    exit();
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /nutritrack-pro/index.php');
        exit();
    }
}

// Redirect if not specific role
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header('Location: /nutritrack-pro/index.php');
        exit();
    }
}
?>
