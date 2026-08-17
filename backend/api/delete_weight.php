<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$weightId = intval($_POST['id'] ?? 0);

if ($weightId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid weight log ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM weight_logs
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("ii", $weightId, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Weight log not found'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Weight log deleted successfully',
    'weight_id' => $weightId
]);
