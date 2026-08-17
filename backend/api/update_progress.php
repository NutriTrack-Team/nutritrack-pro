<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$logDate = trim($_POST['date'] ?? date('Y-m-d'));

$caloriesConsumed = intval($_POST['calories_consumed'] ?? 0);
$caloriesTarget   = intval($_POST['calories_target'] ?? 0);
$proteinConsumed  = floatval($_POST['protein_consumed'] ?? 0);
$carbsConsumed    = floatval($_POST['carbs_consumed'] ?? 0);
$fatsConsumed     = floatval($_POST['fats_consumed'] ?? 0);
$waterGlasses     = intval($_POST['water_glasses'] ?? 0);

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

if ($caloriesConsumed < 0 || $caloriesTarget < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Calories cannot be negative'
    ]);
    exit;
}

if ($proteinConsumed < 0 || $carbsConsumed < 0 || $fatsConsumed < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Nutrients cannot be negative'
    ]);
    exit;
}

if ($waterGlasses < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Water glasses cannot be negative'
    ]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO progress_tracker
        (
            user_id,
            date,
            calories_consumed,
            calories_target,
            protein_consumed,
            carbs_consumed,
            fats_consumed,
            water_glasses
        )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        calories_consumed = VALUES(calories_consumed),
        calories_target = VALUES(calories_target),
        protein_consumed = VALUES(protein_consumed),
        carbs_consumed = VALUES(carbs_consumed),
        fats_consumed = VALUES(fats_consumed),
        water_glasses = VALUES(water_glasses)
");

$stmt->bind_param(
    "isiiddii",
    $userId,
    $logDate,
    $caloriesConsumed,
    $caloriesTarget,
    $proteinConsumed,
    $carbsConsumed,
    $fatsConsumed,
    $waterGlasses
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

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Progress updated successfully',
    'progress' => [
        'date' => $logDate,
        'calories_consumed' => $caloriesConsumed,
        'calories_target' => $caloriesTarget,
        'protein_consumed' => $proteinConsumed,
        'carbs_consumed' => $carbsConsumed,
        'fats_consumed' => $fatsConsumed,
        'water_glasses' => $waterGlasses
    ]
]);
