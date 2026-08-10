<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

requireRole('user');

$conn   = getDBConnection();
$userId = getCurrentUserId();
$today  = date('Y-m-d');

/*
|--------------------------------------------------------------------------
| Initialize meal buckets
|--------------------------------------------------------------------------
*/
$meals = [
    'Breakfast' => 0,
    'Lunch'     => 0,
    'Dinner'    => 0,
    'Snacks'    => 0
];

$foods = [];
$totalCalories = 0;

/* ===== NEW: MACRO TOTALS ===== */
$totalProtein = 0;
$totalCarbs   = 0;
$totalFats    = 0;

/*
|--------------------------------------------------------------------------
| Fetch today's food logs WITH MACROS
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        fl.id        AS log_id,
        fi.name      AS food_name,
        fl.meal_type,
        fl.servings,
        fl.calories,
        (fi.protein * fl.servings) AS protein,
        (fi.carbs   * fl.servings) AS carbs,
        (fi.fats    * fl.servings) AS fats
    FROM food_logs fl
    JOIN food_items fi ON fl.food_id = fi.id
    WHERE fl.user_id = ? AND fl.log_date = ?
    ORDER BY fl.created_at ASC
");
$stmt->bind_param("is", $userId, $today);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $foods[] = [
        'id'        => (int)$row['log_id'],
        'name'      => $row['food_name'],
        'meal_type' => $row['meal_type'],
        'servings'  => (float)$row['servings'],
        'calories'  => (int)$row['calories'],
        'protein'   => (float)$row['protein'],
        'carbs'     => (float)$row['carbs'],
        'fats'      => (float)$row['fats']
    ];

    if (isset($meals[$row['meal_type']])) {
        $meals[$row['meal_type']] += (int)$row['calories'];
    }

    $totalCalories += (int)$row['calories'];

    /* ===== ADD MACROS ===== */
    $totalProtein += (float)$row['protein'];
    $totalCarbs   += (float)$row['carbs'];
    $totalFats    += (float)$row['fats'];
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Fetch today's water intake
|--------------------------------------------------------------------------
*/
$stmt2 = $conn->prepare("
    SELECT glasses 
    FROM water_logs 
    WHERE user_id = ? AND log_date = ?
");
$stmt2->bind_param("is", $userId, $today);
$stmt2->execute();
$result2 = $stmt2->get_result();

$water = 0;
if ($row2 = $result2->fetch_assoc()) {
    $water = (int)$row2['glasses'];
}
$stmt2->close();

/*
|--------------------------------------------------------------------------
| Fetch daily calorie goal
|--------------------------------------------------------------------------
*/
$goal = 0;
$userRes = $conn->query("
    SELECT daily_calories 
    FROM users 
    WHERE id = $userId
");
if ($userRes && $userRes->num_rows) {
    $goal = (int)$userRes->fetch_assoc()['daily_calories'];
}

closeDBConnection($conn);

/*
|--------------------------------------------------------------------------
| Final JSON response
|--------------------------------------------------------------------------
*/
echo json_encode([
    'meals'           => $meals,
    'foods'           => $foods,
    'totalCalories'   => $totalCalories,
    'dailyGoal'       => $goal,
    'remaining'       => max(0, $goal - $totalCalories),
    'water'           => $water,

    /* ===== NEW MACRO DATA ===== */
    'proteinTotal'    => round($totalProtein, 1),
    'carbsTotal'      => round($totalCarbs, 1),
    'fatTotal'        => round($totalFats, 1)
]);
