<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireRole('admin');

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();

$stmt->close();
closeDBConnection($conn);

echo json_encode(['success' => $ok]);
