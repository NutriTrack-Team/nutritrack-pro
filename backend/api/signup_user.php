<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'age' => $_POST['age'] ?? 0,
        'gender' => $_POST['gender'] ?? '',
        'height' => $_POST['height'] ?? 0,
        'weight' => $_POST['weight'] ?? 0,
        'target_weight' => $_POST['target_weight'] ?? 0,
        'activity_level' => $_POST['activity_level'] ?? '',
        'medical_conditions' => $_POST['medical_conditions'] ?? '',
        'dietary_preference' => $_POST['dietary_preference'] ?? '',
        'allergies' => $_POST['allergies'] ?? '',
        'auto_assign_dietitian' => isset($_POST['auto_assign_dietitian'])
    ];
    
    $result = AuthController::signupUser($data);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
