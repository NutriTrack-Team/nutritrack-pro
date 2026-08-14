<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$receiverId   = intval($_POST['receiver_id'] ?? 0);
$receiverType = trim($_POST['receiver_type'] ?? '');
$message      = trim($_POST['message'] ?? '');

$validReceiverTypes = [
    'user',
    'dietitian'
];

if ($receiverId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid receiver ID'
    ]);
    exit;
}

if (!in_array($receiverType, $validReceiverTypes, true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid receiver type'
    ]);
    exit;
}

if ($message === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Message is required'
    ]);
    exit;
}

if (strlen($message) > 5000) {
    echo json_encode([
        'success' => false,
        'error' => 'Message must not exceed 5000 characters'
    ]);
    exit;
}

if ($receiverType === 'user') {
    $check = $conn->prepare("
        SELECT id
        FROM users
        WHERE id = ?
        LIMIT 1
    ");
} else {
    $check = $conn->prepare("
        SELECT id
        FROM dietitians
        WHERE id = ?
        LIMIT 1
    ");
}

$check->bind_param("i", $receiverId);
$check->execute();

$result = $check->get_result();

if (!$result->fetch_assoc()) {
    echo json_encode([
        'success' => false,
        'error' => 'Receiver not found'
    ]);

    $check->close();
    closeDBConnection($conn);
    exit;
}

$check->close();

$stmt = $conn->prepare("
    INSERT INTO messages
        (sender_id, sender_type, receiver_id, receiver_type, message)
    VALUES (?, 'user', ?, ?, ?)
");

$stmt->bind_param(
    "iiss",
    $userId,
    $receiverId,
    $receiverType,
    $message
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

$messageId = $stmt->insert_id;

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Message sent successfully',
    'data' => [
        'id' => $messageId,
        'receiver_id' => $receiverId,
        'receiver_type' => $receiverType,
        'message' => $message
    ]
]);
