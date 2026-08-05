<?php
require_once '../config/session.php';
require_once '../config/database.php';
requireRole('admin');

// ✅ FIXED: Updated keys to match DB columns & JS payload
$required = ['name', 'calories', 'protein', 'carbs', 'fats', 'serving_size'];

foreach ($required as $f) {
    if (!isset($_POST[$f]) || $_POST[$f] === '') {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
}

$conn = getDBConnection();

// ✅ FIXED: Insert into correct columns (fats, serving_size)
$stmt = $conn->prepare("
    INSERT INTO food_items (name, calories, protein, carbs, fats, serving_size)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sddddd",
    $_POST['name'],
    $_POST['calories'],
    $_POST['protein'],
    $_POST['carbs'],
    $_POST['fats'],          // Updated from 'fat'
    $_POST['serving_size']   // Updated from 'portion'
);

$ok = $stmt->execute();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
} else {
    echo json_encode(['success' => true]);
}

closeDBConnection($conn);
?>