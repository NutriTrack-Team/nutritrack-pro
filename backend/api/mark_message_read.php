<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$messageId = intval($_POST['id'] ?? 0);

if ($messageId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid message ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE id = ?
      AND receiver_id = ?
      AND receiver_type = 'user'
");

$stmt->bind_param("ii", $messageId, $userId);

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
        'error' => 'Message not found or already read'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Message marked as read',
    'message_id' => $messageId
]);
