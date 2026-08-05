<?php
require_once '../config/session.php';
require_once '../config/database.php';
requireRole('admin');

// ✅ FIXED: Validation keys updated to match JS and DB
$required = ['id', 'name', 'calories', 'protein', 'carbs', 'fats', 'serving_size'];

foreach ($required as $f) {
    if (!isset($_POST[$f]) || $_POST[$f] === '') {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
}

$conn = getDBConnection();

// ✅ FIXED: SQL Column names (fats, serving_size)
$stmt = $conn->prepare("
    UPDATE food_items
    SET name=?, calories=?, protein=?, carbs=?, fats=?, serving_size=?
    WHERE id=?
");

// ✅ FIXED: Bind params using correct POST keys
$stmt->bind_param(
    "sdddddi",
    $_POST['name'],
    $_POST['calories'],
    $_POST['protein'],
    $_POST['carbs'],
    $_POST['fats'],          // 🔥 Updated
    $_POST['serving_size'],  // 🔥 Updated
    $_POST['id']
);

$ok = $stmt->execute();
closeDBConnection($conn);

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>