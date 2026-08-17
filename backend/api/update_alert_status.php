<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$alertId = intval($_POST['id'] ?? 0);
$status  = trim($_POST['status'] ?? '');

$validStatuses = [
    'unseen',
    'active',
    'dismissed'
];

if ($alertId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid alert ID'
    ]);
    exit;
}

if (!in_array($status, $validStatuses, true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid alert status'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE alerts
    SET status = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("sii", $status, $alertId, $userId);

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
        'error' => 'Alert not found or status unchanged'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Alert status updated successfully',
    'alert_id' => $alertId,
    'status' => $status
]);
