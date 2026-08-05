<?php
require_once '../config/session.php';
require_once '../config/database.php';

requireRole('admin');

if (!isset($_POST['dietitian_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$dietitianId = (int) $_POST['dietitian_id'];

$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM dietitians WHERE id = ?");
$stmt->bind_param("i", $dietitianId);
$success = $stmt->execute();

closeDBConnection($conn);
echo json_encode(['success' => $success]);
