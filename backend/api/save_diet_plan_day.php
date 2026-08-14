<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$planId     = intval($_POST['plan_id'] ?? 0);
$dayName    = trim($_POST['day_name'] ?? '');
$breakfast  = trim($_POST['breakfast'] ?? '');
$lunch      = trim($_POST['lunch'] ?? '');
$dinner     = trim($_POST['dinner'] ?? '');
$snacks     = trim($_POST['snacks'] ?? '');

$validDays = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday'
];

if ($planId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan ID'
    ]);
    exit;
}

if (!in_array($dayName, $validDays, true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid day name'
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

$existing = $conn->prepare("
    SELECT id
    FROM diet_plan_days
    WHERE plan_id = ? AND day_name = ?
    LIMIT 1
");

$existing->bind_param("is", $planId, $dayName);
$existing->execute();

$existingResult = $existing->get_result();
$existingDay = $existingResult->fetch_assoc();

$existing->close();

if ($existingDay) {
    $dayId = intval($existingDay['id']);

    $stmt = $conn->prepare("
        UPDATE diet_plan_days
        SET breakfast = ?,
            lunch = ?,
            dinner = ?,
            snacks = ?
        WHERE id = ? AND plan_id = ?
    ");

    $stmt->bind_param(
        "ssssii",
        $breakfast,
        $lunch,
        $dinner,
        $snacks,
        $dayId,
        $planId
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

    $message = 'Diet plan day updated successfully';
} else {
    $stmt = $conn->prepare("
        INSERT INTO diet_plan_days
            (plan_id, day_name, breakfast, lunch, dinner, snacks)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $planId,
        $dayName,
        $breakfast,
        $lunch,
        $dinner,
        $snacks
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

    $dayId = $stmt->insert_id;
    $message = 'Diet plan day added successfully';
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => $message,
    'day' => [
        'id' => $dayId,
        'plan_id' => $planId,
        'day_name' => $dayName,
        'breakfast' => $breakfast,
        'lunch' => $lunch,
        'dinner' => $dinner,
        'snacks' => $snacks
    ]
]);
