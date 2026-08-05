<?php
require_once '../config/session.php';
require_once '../config/database.php';

requireRole('admin');

if (!isset($_POST['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$userId = (int) $_POST['user_id'];

$conn = getDBConnection();

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$success = $stmt->execute();

closeDBConnection($conn);

echo json_encode(['success' => $success]);
