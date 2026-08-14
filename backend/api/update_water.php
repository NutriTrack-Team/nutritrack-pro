<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$glasses = intval($_POST['glasses'] ?? 0);
$logDate = trim($_POST['log_date'] ?? date('Y-m-d'));

if ($glasses < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Glasses cannot be negative'
    ]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO water_logs (user_id, glasses, log_date)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE glasses = VALUES(glasses)
");

$stmt->bind_param("iis", $userId, $glasses, $logDate);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'error' => $stmt->error
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Water intake saved successfully',
    'water' => [
        'glasses' => $glasses,
        'log_date' => $logDate
    ]
]);
