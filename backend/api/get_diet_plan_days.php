<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$planId = intval($_GET['plan_id'] ?? 0);

if ($planId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan ID'
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT id
    FROM diet_plans
    WHERE id = ? AND user_id = ?
    LIMIT 1
");

$check->bind_param("ii", $planId, $userId);
$check->execute();

$checkResult = $check->get_result();

if (!$checkResult->fetch_assoc()) {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan not found'
    ]);

    $check->close();
    closeDBConnection($conn);
    exit;
}

$check->close();

$stmt = $conn->prepare("
    SELECT
        id,
        plan_id,
        day_name,
        breakfast,
        breakfast_completed,
        lunch,
        lunch_completed,
        dinner,
        dinner_completed,
        snacks,
        snacks_completed
    FROM diet_plan_days
    WHERE plan_id = ?
    ORDER BY FIELD(
        day_name,
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    )
");

$stmt->bind_param("i", $planId);
$stmt->execute();

$result = $stmt->get_result();

$days = [];

while ($row = $result->fetch_assoc()) {
    $days[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'plan_id' => $planId,
    'days' => $days
]);
