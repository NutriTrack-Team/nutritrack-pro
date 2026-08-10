<?php
require_once '../config/session.php';
require_once '../config/database.php';

requireRole('user');

$userId = getCurrentUserId();
$conn = getDBConnection();
$today = date('Y-m-d');

// last added food for today
$sql = "
    SELECT id 
    FROM food_logs 
    WHERE user_id = ? AND log_date = ?
    ORDER BY created_at DESC 
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userId, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $deleteId = $row['id'];

    $del = $conn->prepare("DELETE FROM food_logs WHERE id = ?");
    $del->bind_param("i", $deleteId);
    $del->execute();

    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No food to undo'
    ]);
}
