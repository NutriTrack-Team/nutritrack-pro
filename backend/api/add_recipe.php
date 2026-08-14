<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$recipeName  = trim($_POST['recipe_name'] ?? '');
$ingredients = trim($_POST['ingredients'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$calories    = intval($_POST['calories'] ?? 0);

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
    INSERT INTO recipes
        (user_id, recipe_name, ingredients, instructions, calories)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssi",
    $userId,
    $recipeName,
    $ingredients,
    $instructions,
    $calories
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

$recipeId = $stmt->insert_id;

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Recipe added successfully',
    'recipe' => [
        'id' => $recipeId,
        'recipe_name' => $recipeName,
        'ingredients' => $ingredients,
        'instructions' => $instructions,
        'calories' => $calories
    ]
]);
