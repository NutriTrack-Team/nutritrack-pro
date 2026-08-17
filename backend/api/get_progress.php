<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$logDate = trim($_GET['date'] ?? date('Y-m-d'));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id,
        date,
        calories_consumed,
        calories_target,
        protein_consumed,
        carbs_consumed,
        fats_consumed,
        water_glasses
    FROM progress_tracker
    WHERE user_id = ? AND date = ?
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
        'progress' => [
            'date' => $logDate,
            'calories_consumed' => 0,
            'calories_target' => 0,
            'protein_consumed' => 0,
            'carbs_consumed' => 0,
            'fats_consumed' => 0,
            'water_glasses' => 0
        ]
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'progress' => $row
]);
