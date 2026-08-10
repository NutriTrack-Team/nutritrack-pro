<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();

$foodId   = intval($_POST['food_id'] ?? 0);
$mealType = $_POST['meal_type'] ?? '';
$servings = floatval($_POST['servings'] ?? 1);
$today    = date('Y-m-d');

/*
|--------------------------------------------------------------------------
| Basic validation
|--------------------------------------------------------------------------
*/
$validMeals = ['Breakfast','Lunch','Dinner','Snacks'];

if ($foodId <= 0 || !in_array($mealType, $validMeals)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Get food calories (SOURCE OF TRUTH)
|--------------------------------------------------------------------------
*/
$foodStmt = $conn->prepare("
    SELECT calories 
    FROM food_items 
    WHERE id = ?
");
$foodStmt->bind_param("i", $foodId);
$foodStmt->execute();
$foodResult = $foodStmt->get_result();

if ($foodResult->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Food not found']);
    exit;
}

$food = $foodResult->fetch_assoc();
$foodCalories = floatval($food['calories']);   // calories per serving

$foodStmt->close();

/*
|--------------------------------------------------------------------------
| Calculate total calories
|--------------------------------------------------------------------------
*/
$totalCalories = $foodCalories * $servings;

/*
|--------------------------------------------------------------------------
| Insert food log
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO food_logs 
    (user_id, food_id, meal_type, servings, calories, log_date) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iisdds",
    $userId,
    $foodId,
    $mealType,
    $servings,
    $totalCalories,
    $today
);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
    exit;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Daily calorie limit alert (safe)
|--------------------------------------------------------------------------
*/
$userRes = $conn->query("
    SELECT daily_calories 
    FROM users 
    WHERE id = $userId
");

if ($userRes && $userRes->num_rows) {
    $dailyLimit = (int)$userRes->fetch_assoc()['daily_calories'];

    if ($dailyLimit > 0) {
        $sumRes = $conn->query("
            SELECT SUM(calories) AS total 
            FROM food_logs 
            WHERE user_id = $userId 
              AND log_date = '$today'
        ");
        $consumed = (float)($sumRes->fetch_assoc()['total'] ?? 0);

        if ($consumed > $dailyLimit) {
            $conn->query("
                INSERT INTO alerts 
                (user_id, title, type, message, status, source) 
                VALUES (
                    $userId,
                    'Calorie Limit Exceeded',
                    'warning',
                    'You have exceeded your daily calorie limit of $dailyLimit calories.',
                    'unseen',
                    'system'
                )
            ");
        }
    }
}

closeDBConnection($conn);

echo json_encode(['success' => true]);
