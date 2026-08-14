<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$dayId       = intval($_POST['day_id'] ?? 0);
$meal        = trim($_POST['meal'] ?? '');
$completed   = intval($_POST['completed'] ?? 0);

$validMeals = [
    'breakfast',
    'lunch',
    'dinner',
    'snacks'
];

if ($dayId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan day ID'
    ]);
    exit;
}

if (!in_array($meal, $validMeals, true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid meal type'
    ]);
    exit;
}

if ($completed !== 0 && $completed !== 1) {
    echo json_encode([
        'success' => false,
        'error' => 'Completed must be 0 or 1'
    ]);
    exit;
}

$column = $meal . '_completed';

$stmt = $conn->prepare("
    SELECT d.id
    FROM diet_plan_days d
    INNER JOIN diet_plans p ON p.id = d.plan_id
    WHERE d.id = ? AND p.user_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $dayId, $userId);
$stmt->execute();

$result = $stmt->get_result();

if (!$result->fetch_assoc()) {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan day not found'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();

$stmt = $conn->prepare("
    UPDATE diet_plan_days
    SET $column = ?
    WHERE id = ?
");

$stmt->bind_param("ii", $completed, $dayId);

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
    'message' => ucfirst($meal) . ' completion status updated successfully',
    'day_id' => $dayId,
    'meal' => $meal,
    'completed' => $completed
]);
