<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$stmt = $conn->prepare("
    SELECT id, recipe_name, ingredients, instructions, calories, created_at
    FROM recipes
    WHERE user_id = ?
    ORDER BY created_at DESC, id DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$recipes = [];

while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'recipes' => $recipes
]);
