<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$itemId   = intval($_POST['id'] ?? 0);
$itemName = trim($_POST['item_name'] ?? '');

if ($itemId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid pantry item ID'
    ]);
    exit;
}

if ($itemName === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Item name is required'
    ]);
    exit;
}

if (strlen($itemName) > 100) {
    echo json_encode([
        'success' => false,
        'error' => 'Item name must not exceed 100 characters'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE pantry_items
    SET item_name = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("sii", $itemName, $itemId, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Pantry item not found or unchanged'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Pantry item updated successfully',
    'item' => [
        'id' => $itemId,
        'item_name' => $itemName
    ]
]);
