<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$stmt = $conn->prepare("
    SELECT id, item_name, added_at
    FROM pantry_items
    WHERE user_id = ?
    ORDER BY added_at DESC, id DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'items' => $items
]);
