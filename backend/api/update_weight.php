<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$weightId = intval($_POST['id'] ?? 0);
$weight   = floatval($_POST['weight'] ?? 0);
$logDate  = trim($_POST['log_date'] ?? '');
$bmi      = ($_POST['bmi'] ?? '') !== '' ? floatval($_POST['bmi']) : null;

if ($weightId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid weight log ID'
    ]);
    exit;
}

if ($weight <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Weight must be greater than 0'
    ]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format'
    ]);
    exit;
}

if ($bmi !== null && $bmi < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'BMI cannot be negative'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE weight_logs
    SET weight = ?, log_date = ?, bmi = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "dsdii",
    $weight,
    $logDate,
    $bmi,
    $weightId,
    $userId
);

$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Weight log not found or unchanged'
    ]);

    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Weight log updated successfully',
    'weight' => [
        'id' => $weightId,
        'weight' => $weight,
        'log_date' => $logDate,
        'bmi' => $bmi
    ]
]);
