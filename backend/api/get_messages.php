<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$otherId   = intval($_GET['other_id'] ?? 0);
$otherType = trim($_GET['other_type'] ?? '');

if ($otherId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid other user ID'
    ]);
    exit;
}

if (!in_array($otherType, ['user', 'dietitian'], true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid other user type'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id,
        sender_id,
        sender_type,
        receiver_id,
        receiver_type,
        message,
        is_read,
        sent_at
    FROM messages
    WHERE
        (
            sender_id = ?
            AND sender_type = 'user'
            AND receiver_id = ?
            AND receiver_type = ?
        )
        OR
        (
            sender_id = ?
            AND sender_type = ?
            AND receiver_id = ?
            AND receiver_type = 'user'
        )
    ORDER BY sent_at ASC, id ASC
");

$stmt->bind_param(
    "iisisis",
    $userId,
    $otherId,
    $otherType,
    $otherId,
    $otherType,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'other_id' => $otherId,
    'other_type' => $otherType,
    'messages' => $messages
]);
