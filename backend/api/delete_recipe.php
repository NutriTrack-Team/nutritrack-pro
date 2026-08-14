<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$recipeId = intval($_POST['id'] ?? 0);

if ($recipeId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid recipe ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM recipes
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("ii", $recipeId, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Recipe not found'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Recipe deleted successfully',
    'recipe_id' => $recipeId
]);
