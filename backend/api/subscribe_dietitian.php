<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn = getDBConnection();
$userId = getCurrentUserId();

/* -------- READ JSON BODY -------- */
$data = json_decode(file_get_contents("php://input"), true);

$dietitianId   = (int)($data['dietitian_id'] ?? 0);
$planMonths    = (int)($data['plan_months'] ?? 0);
$totalAmount   = (float)($data['total_amount'] ?? 0);
$paymentMethod = $data['payment_method'] ?? '';

if ($dietitianId <= 0 || $planMonths <= 0 || $totalAmount <= 0 || !$paymentMethod) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

/* -------- BLOCK MULTIPLE ACTIVE SUBS -------- */
$check = $conn->prepare("
    SELECT 1 FROM subscriptions
    WHERE user_id = ? AND dietitian_id = ? AND status = 'active'
");
$check->bind_param("ii", $userId, $dietitianId);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already have an active subscription']);
    exit;
}

/* -------- DATE CALCULATION -------- */
$startDate  = date('Y-m-d');
$expiryDate = date('Y-m-d', strtotime("+$planMonths months"));

$conn->begin_transaction();

try {

    /* -------- INSERT SUBSCRIPTION -------- */
    $stmt = $conn->prepare("
        INSERT INTO subscriptions 
        (user_id, dietitian_id, payment_method, amount, subscription_date, expiry_date, status)
        VALUES (?, ?, ?, ?, ?, ?, 'active')
    ");
    $stmt->bind_param(
        "iisdss",
        $userId,
        $dietitianId,
        $paymentMethod,
        $totalAmount,
        $startDate,
        $expiryDate
    );
    $stmt->execute();

    /* -------- LINK CLIENT -------- */
    $stmt2 = $conn->prepare("
        INSERT INTO dietitian_clients (dietitian_id, client_id, assigned_date, status)
        VALUES (?, ?, ?, 'active')
    ");
    $stmt2->bind_param("iis", $dietitianId, $userId, $startDate);
    $stmt2->execute();

    /* -------- UPDATE USER -------- */
    $stmt3 = $conn->prepare("UPDATE users SET dietitian_id = ? WHERE id = ?");
    $stmt3->bind_param("ii", $dietitianId, $userId);
    $stmt3->execute();

    /* -------- UPDATE DIETITIAN COUNT -------- */
    $stmt4 = $conn->prepare("UPDATE dietitians SET patient_count = patient_count + 1 WHERE id = ?");
    $stmt4->bind_param("i", $dietitianId);
    $stmt4->execute();

    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Subscription failed']);
}

closeDBConnection($conn);
