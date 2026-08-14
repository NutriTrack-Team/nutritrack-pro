<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$planId = intval($_POST['id'] ?? 0);

if ($planId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid diet plan ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM diet_plans
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("ii", $planId, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Diet plan not found'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Diet plan deleted successfully',
    'plan_id' => $planId
]);
