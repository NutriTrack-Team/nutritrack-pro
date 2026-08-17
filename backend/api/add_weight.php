<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$weight  = floatval($_POST['weight'] ?? 0);
$logDate = trim($_POST['log_date'] ?? date('Y-m-d'));
$bmi     = ($_POST['bmi'] ?? '') !== '' ? floatval($_POST['bmi']) : null;

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
    INSERT INTO weight_logs
        (user_id, weight, log_date, bmi)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "idsd",
    $userId,
    $weight,
    $logDate,
    $bmi
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

$weightId = $stmt->insert_id;

$stmt->close();
closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Weight logged successfully',
    'weight' => [
        'id' => $weightId,
        'weight' => $weight,
        'log_date' => $logDate,
        'bmi' => $bmi
    ]
]);
