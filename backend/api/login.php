<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Start session
session_start();

// Include AuthController
require_once __DIR__ . '/../controllers/AuthController.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect input safely
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate input
    if (empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Try login
    try {
        $result = AuthController::login($email, $password, $role);

        // Ensure $result is an array with 'success'
        if (!is_array($result) || !isset($result['success'])) {
            throw new Exception("Invalid response from AuthController");
        }

        // On success, store session info and send redirect URL
        if ($result['success']) {
            $_SESSION['user_id'] = $_SESSION['user_id'] ?? null;
            $_SESSION['role'] = $_SESSION['role'] ?? $role;
            $_SESSION['name'] = $_SESSION['name'] ?? 'User';

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $result['redirect'] ?? '/nutritrack-pro/pages/user/dashboard.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid email or password'
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
