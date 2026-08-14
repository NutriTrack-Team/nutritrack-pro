<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$recipeId    = intval($_POST['id'] ?? 0);
$recipeName  = trim($_POST['recipe_name'] ?? '');
$ingredients = trim($_POST['ingredients'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$calories    = intval($_POST['calories'] ?? 0);

if ($recipeId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid recipe ID'
    ]);
    exit;
}

if ($recipeName === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Recipe name is required'
    ]);
    exit;
}

if (strlen($recipeName) > 200) {
    echo json_encode([
        'success' => false,
        'error' => 'Recipe name must not exceed 200 characters'
    ]);
    exit;
}

if ($ingredients === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Ingredients are required'
    ]);
    exit;
}

if ($calories < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Calories cannot be negative'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE recipes
    SET recipe_name = ?,
        ingredients = ?,
        instructions = ?,
        calories = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "sssiii",
    $recipeName,
    $ingredients,
    $instructions,
    $calories,
    $recipeId,
    $userId
);

$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Recipe not found or unchanged'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Recipe updated successfully',
    'recipe' => [
        'id' => $recipeId,
        'recipe_name' => $recipeName,
        'ingredients' => $ingredients,
        'instructions' => $instructions,
        'calories' => $calories
    ]
]);
