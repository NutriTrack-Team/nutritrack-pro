<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '',
        'passing_institute' => $_POST['passing_institute'] ?? '',
        'degrees' => $_POST['degrees'] ?? '',
        'experience' => $_POST['experience'] ?? 0,
        'specialization' => $_POST['specialization'] ?? '',
        'profile_picture' => $_POST['profile_picture'] ?? 'default-dietitian.jpg'
    ];
    
    $result = AuthController::signupDietitian($data);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
