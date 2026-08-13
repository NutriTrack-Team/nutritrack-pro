<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$itemId = intval($_POST['id'] ?? 0);

if ($itemId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid pantry item ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM pantry_items
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("ii", $itemId, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Pantry item not found'
    ]);
    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Pantry item removed successfully'
]);
