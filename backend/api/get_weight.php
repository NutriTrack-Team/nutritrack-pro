<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$logDate = trim($_GET['log_date'] ?? '');

if ($logDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

if ($logDate !== '') {
    $stmt = $conn->prepare("
        SELECT id, weight, log_date, bmi, created_at
        FROM weight_logs
        WHERE user_id = ? AND log_date = ?
        ORDER BY id DESC
    ");

    $stmt->bind_param("is", $userId, $logDate);
} else {
    $stmt = $conn->prepare("
        SELECT id, weight, log_date, bmi, created_at
        FROM weight_logs
        WHERE user_id = ?
        ORDER BY log_date DESC, id DESC
    ");

    $stmt->bind_param("i", $userId);
}

$stmt->execute();

$result = $stmt->get_result();

$weights = [];

while ($row = $result->fetch_assoc()) {
    $weights[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'weights' => $weights
]);
