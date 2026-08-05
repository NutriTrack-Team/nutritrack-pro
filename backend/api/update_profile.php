<?php
require_once '../config/session.php';
require_once '../config/database.php';

// ✅ Role Check: Only Dietitian
requireRole('dietitian');

header('Content-Type: application/json');

$dietitianId = getCurrentUserId();
$conn = getDBConnection();

// ✅ Collect POST data (Mapped to new fields)
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$degrees = trim($_POST['degrees'] ?? '');
$about = trim($_POST['about'] ?? '');

// Validate name
if ($full_name === '' || strlen($full_name) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Full Name must be at least 3 characters'
    ]);
    exit;
}

// ✅ Prepare update query for 'dietitians' table
$stmt = $conn->prepare("
    UPDATE dietitians 
    SET 
        full_name = ?,
        phone = ?,
        degrees = ?,
        about = ?
    WHERE id = ?
");

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $conn->error
    ]);
    exit;
}

// ✅ Bind parameters: ssssi (String, String, String, String, Integer)
$stmt->bind_param(
    "ssssi",
    $full_name,
    $phone,
    $degrees,
    $about,
    $dietitianId
);

$success = $stmt->execute();

if (!$success) {
    // Optional: Log error for debugging
    error_log("Profile Update Error: " . $stmt->error);
}

closeDBConnection($conn);

echo json_encode([
    'success' => $success
]);
?>