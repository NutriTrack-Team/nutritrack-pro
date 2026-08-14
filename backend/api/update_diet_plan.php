<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$planId        = intval($_POST['id'] ?? 0);
$title         = trim($_POST['title'] ?? '');
$overview      = trim($_POST['overview'] ?? '');
$durationWeeks = intval($_POST['duration_weeks'] ?? 0);
$dailyCalories = intval($_POST['daily_calories'] ?? 0);
$startDate     = trim($_POST['start_date'] ?? '');
$endDate       = trim($_POST['end_date'] ?? '');
$status        = trim($_POST['status'] ?? '');

if ($planId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan ID'
    ]);
    exit;
}

if ($title === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan title is required'
    ]);
    exit;
}

if (strlen($title) > 200) {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan title must not exceed 200 characters'
    ]);
    exit;
}

if ($durationWeeks <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Duration must be greater than zero'
    ]);
    exit;
}

if ($dailyCalories <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Daily calories must be greater than zero'
    ]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid start date format'
    ]);
    exit;
}

if ($endDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid end date format'
    ]);
    exit;
}

if (!in_array($status, ['active', 'completed', 'cancelled'], true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan status'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE diet_plans
    SET title = ?,
        overview = ?,
        duration_weeks = ?,
        daily_calories = ?,
        start_date = ?,
        end_date = NULLIF(?, ''),
        status = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "ssiisssii",
    $title,
    $overview,
    $durationWeeks,
    $dailyCalories,
    $startDate,
    $endDate,
    $status,
    $planId,
    $userId
);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'error' => $stmt->error
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan not found or unchanged'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Diet plan updated successfully',
    'plan' => [
        'id' => $planId,
        'title' => $title,
        'overview' => $overview,
        'duration_weeks' => $durationWeeks,
        'daily_calories' => $dailyCalories,
        'start_date' => $startDate,
        'end_date' => $endDate !== '' ? $endDate : null,
        'status' => $status
    ]
]);
