<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$logDate = trim($_GET['log_date'] ?? date('Y-m-d'));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, glasses, log_date, created_at, updated_at
    FROM water_logs
    WHERE user_id = ? AND log_date = ?
    LIMIT 1
");

$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();
closeDBConnection($conn);

if (!$row) {
    echo json_encode([
        'success' => true,
        'water' => [
            'glasses' => 0,
            'log_date' => $logDate
        ]
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'water' => $row
]);
