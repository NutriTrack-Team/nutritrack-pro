<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = getCurrentUserId();
$role = getCurrentUserRole();
$about = $_POST['about'] ?? '';

$conn = getDBConnection();

if ($role === 'user') {
    $stmt = $conn->prepare("UPDATE users SET about = ? WHERE id = ?");
    $stmt->bind_param("si", $about, $userId);
} elseif ($role === 'dietitian') {
    $stmt = $conn->prepare("UPDATE dietitians SET about = ? WHERE id = ?");
    $stmt->bind_param("si", $about, $userId);
} else {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$success = $stmt->execute();
$stmt->close();
closeDBConnection($conn);

echo json_encode(['success' => $success]);
?>
