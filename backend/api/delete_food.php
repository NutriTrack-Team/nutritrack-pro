<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$id = intval($_POST['id'] ?? 0);

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/
if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid food log ID'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Delete food log (user-safe)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    DELETE FROM food_logs 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $id, $userId);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'error'   => $stmt->error
    ]);
    exit;
}

$affected = $stmt->affected_rows;
$stmt->close();
closeDBConnection($conn);

/*
|--------------------------------------------------------------------------
| Final response
|--------------------------------------------------------------------------
*/
echo json_encode([
    'success' => true,
    'deleted' => $affected > 0
]);
