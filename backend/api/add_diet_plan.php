<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$dietitianId   = intval($_POST['dietitian_id'] ?? 0);
$title         = trim($_POST['title'] ?? '');
$overview      = trim($_POST['overview'] ?? '');
$durationWeeks = intval($_POST['duration_weeks'] ?? 0);
$dailyCalories = intval($_POST['daily_calories'] ?? 0);
$startDate     = trim($_POST['start_date'] ?? '');
$endDate       = trim($_POST['end_date'] ?? '');

if ($dietitianId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Valid dietitian ID is required'
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

$stmt = $conn->prepare("
    SELECT id
    FROM dietitians
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $dietitianId);
$stmt->execute();

$result = $stmt->get_result();

if (!$result->fetch_assoc()) {
    echo json_encode([
        'success' => false,
        'error' => 'Dietitian not found'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();

if ($endDate === '') {
    $stmt = $conn->prepare("
        INSERT INTO diet_plans
            (user_id, dietitian_id, title, overview, duration_weeks, daily_calories, start_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iississ",
        $userId,
        $dietitianId,
        $title,
        $overview,
        $durationWeeks,
        $dailyCalories,
        $startDate
    );
} else {
    $stmt = $conn->prepare("
        INSERT INTO diet_plans
            (user_id, dietitian_id, title, overview, duration_weeks, daily_calories, start_date, end_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iississs",
        $userId,
        $dietitianId,
        $title,
        $overview,
        $durationWeeks,
        $dailyCalories,
        $startDate,
        $endDate
    );
}

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'error' => $stmt->error
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$planId = $stmt->insert_id;

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Diet plan added successfully',
    'plan' => [
        'id' => $planId,
        'user_id' => $userId,
        'dietitian_id' => $dietitianId,
        'title' => $title,
        'overview' => $overview,
        'duration_weeks' => $durationWeeks,
        'daily_calories' => $dailyCalories,
        'start_date' => $startDate,
        'end_date' => $endDate !== '' ? $endDate : null,
        'status' => 'active'
    ]
]);
