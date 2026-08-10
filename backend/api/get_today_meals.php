<?php
require_once '../config/database.php';
session_start();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$conn = getDBConnection();

$sql = "
SELECT 
    meal_type,
    SUM(calories * servings) AS total_calories
FROM food_logs
WHERE user_id = ?
AND log_date = ?
GROUP BY meal_type
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();

$meals = [
    'Breakfast' => 0,
    'Lunch' => 0,
    'Dinner' => 0,
    'Snack' => 0
];

while ($row = $result->fetch_assoc()) {
    $meals[$row['meal_type']] = (int)$row['total_calories'];
}

echo json_encode([
    'success' => true,
    'meals' => $meals
]);
