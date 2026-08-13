<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$itemName = trim($_POST['item_name'] ?? '');

if ($itemName === '') {
    echo json_encode(['success' => false, 'error' => 'Item name is required']);
    exit;
}

if (strlen($itemName) > 100) {
    echo json_encode(['success' => false, 'error' => 'Item name must not exceed 100 characters']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO pantry_items (user_id, item_name)
    VALUES (?, ?)
");

$stmt->bind_param("is", $userId, $itemName);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$itemId = $stmt->insert_id;

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Pantry item added successfully',
    'item' => [
        'id' => $itemId,
        'item_name' => $itemName
    ]
]);
