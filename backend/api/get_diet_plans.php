<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$status = trim($_GET['status'] ?? '');

if ($status !== '' && !in_array($status, ['active', 'completed', 'cancelled'], true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan status'
    ]);
    exit;
}

if ($status === '') {
    $stmt = $conn->prepare("
        SELECT
            dp.id,
            dp.user_id,
            dp.dietitian_id,
            dp.title,
            dp.overview,
            dp.duration_weeks,
            dp.daily_calories,
            dp.start_date,
            dp.end_date,
            dp.status,
            dp.created_at,
            dp.updated_at
        FROM diet_plans dp
        WHERE dp.user_id = ?
        ORDER BY dp.created_at DESC, dp.id DESC
    ");

    $stmt->bind_param("i", $userId);
} else {
    $stmt = $conn->prepare("
        SELECT
            dp.id,
            dp.user_id,
            dp.dietitian_id,
            dp.title,
            dp.overview,
            dp.duration_weeks,
            dp.daily_calories,
            dp.start_date,
            dp.end_date,
            dp.status,
            dp.created_at,
            dp.updated_at
        FROM diet_plans dp
        WHERE dp.user_id = ?
          AND dp.status = ?
        ORDER BY dp.created_at DESC, dp.id DESC
    ");

    $stmt->bind_param("is", $userId, $status);
}

$stmt->execute();

$result = $stmt->get_result();

$plans = [];

while ($row = $result->fetch_assoc()) {
    $plans[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'plans' => $plans
]);
