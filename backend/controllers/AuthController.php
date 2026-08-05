<?php 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

class AuthController {
    
    // Admin credentials (hardcoded)
    private static $ADMIN_CREDENTIALS = [
        [
            'email' => 'adminalvi@nutritrack.com',
            'password' => 'Admin@123'
        ],
        [
            'email' => 'adminfaiza@nutritrack.com',
            'password' => 'Admin@123'
        ]
    ];
    
    // Login handler
    public static function login($email, $password, $role) {
        $conn = getDBConnection();
        
        // Admin login (hardcoded check)
        if ($role === 'admin') {
            foreach (self::$ADMIN_CREDENTIALS as $admin) {
                if ($email === $admin['email'] && $password === $admin['password']) {
                    $_SESSION['user_id'] = 0; // Admin has ID 0
                    $_SESSION['role'] = 'admin';
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = 'Admin';
                    closeDBConnection($conn);
                    return ['success' => true, 'redirect' => '/nutritrack-pro-fixed/pages/admin/dashboard.php'];
                }
            }
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Invalid admin credentials'];
        }
        
        // User login
        if ($role === 'user') {
            $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = 'user';
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $stmt->close();
                    closeDBConnection($conn);
                    return ['success' => true, 'redirect' => '/nutritrack-pro-fixed/pages/user/dashboard.php'];

                }
            }
            $stmt->close();
        }
        
        // Dietitian login
        if ($role === 'dietitian') {
            $stmt = $conn->prepare("SELECT id, full_name, password, is_active FROM dietitians WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $dietitian = $result->fetch_assoc();
                if (!$dietitian['is_active']) {
                    $stmt->close();
                    closeDBConnection($conn);
                    return ['success' => false, 'message' => 'Your account is not activated yet. Please wait for admin approval.'];
                }
                if (password_verify($password, $dietitian['password'])) {
                    $_SESSION['user_id'] = $dietitian['id'];
                    $_SESSION['role'] = 'dietitian';
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $dietitian['full_name'];
                    $stmt->close();
                    closeDBConnection($conn);
                    return ['success' => true, 'redirect' => '/nutritrack-pro-fixed/pages/dietitian/dashboard.php'];
                } // <-- Fixed closing brace
                $stmt->close();
            }
            
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
    }
    
    // User signup
    public static function signupUser($data) {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Email already exists'];
        }
        $stmt->close();
        
        // ========= TARGET WEIGHT VALIDATION =========

// Convert height from cm to meter
$height_m = $data['height'] / 100;

// Minimum healthy BMI
$min_bmi = 18.5;

// Minimum healthy weight for this height
$min_weight = $min_bmi * ($height_m * $height_m);

// Stop signup if target weight is unhealthy
if ($data['target_weight'] < $min_weight) {
    closeDBConnection($conn);
    return [
        'success' => false,
        'message' => 'Target weight is too low for your height. Please choose a healthy target weight.'
    ];
}

// ==========================================

        // Calculate BMI
        $height_m = $data['height'] / 100;
        $bmi = round($data['weight'] / ($height_m * $height_m), 2);
        
        // Calculate daily calories using Mifflin-St Jeor equation
        $bmr = 0;
        if ($data['gender'] === 'Male') {
            $bmr = 10 * $data['weight'] + 6.25 * $data['height'] - 5 * $data['age'] + 5;
        } else {
            $bmr = 10 * $data['weight'] + 6.25 * $data['height'] - 5 * $data['age'] - 161;
        }
        
        // Activity level multipliers
        $activity_multipliers = [
            'Sedentary' => 1.2,
            'Lightly Active' => 1.375,
            'Moderately Active' => 1.55,
            'Very Active' => 1.725,
            'Extra Active' => 1.9
        ];
        
        $daily_calories = round($bmr * $activity_multipliers[$data['activity_level']]);
        
        // Calculate macros (40% carbs, 30% protein, 30% fats)
        $daily_protein = round(($daily_calories * 0.30) / 4);
        $daily_carbs = round(($daily_calories * 0.40) / 4);
        $daily_fats = round(($daily_calories * 0.30) / 9);
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Auto assign dietitian if requested
        $dietitian_id = null;
        if (isset($data['auto_assign_dietitian']) && $data['auto_assign_dietitian']) {
            $dietitian_result = $conn->query("SELECT id FROM dietitians WHERE is_active = 1 ORDER BY patient_count ASC LIMIT 1");
            if ($dietitian_result->num_rows > 0) {
                $dietitian = $dietitian_result->fetch_assoc();
                $dietitian_id = $dietitian['id'];
                
                // Update patient count
                $conn->query("UPDATE dietitians SET patient_count = patient_count + 1 WHERE id = $dietitian_id");
            }
        }
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, age, gender, height, weight, target_weight, activity_level, medical_conditions, dietary_preference, allergies, bmi, daily_calories, daily_protein, daily_carbs, daily_fats, dietitian_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssisdddssssdiiiii", 
            $data['first_name'], 
            $data['last_name'], 
            $data['email'], 
            $hashed_password, 
            $data['age'], 
            $data['gender'], 
            $data['height'], 
            $data['weight'], 
            $data['target_weight'], 
            $data['activity_level'], 
            $data['medical_conditions'], 
            $data['dietary_preference'], 
            $data['allergies'], 
            $bmi, 
            $daily_calories, 
            $daily_protein, 
            $daily_carbs, 
            $daily_fats,
            $dietitian_id
        );
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Initialize weight log
            $today = date('Y-m-d');
            $conn->query("INSERT INTO weight_logs (user_id, weight, log_date, bmi) VALUES ($user_id, {$data['weight']}, '$today', $bmi)");
            
            // Initialize water log
            $conn->query("INSERT INTO water_logs (user_id, glasses, log_date) VALUES ($user_id, 0, '$today')");
            
            // Initialize progress tracker
            $conn->query("INSERT INTO progress_tracker (user_id, date, calories_target) VALUES ($user_id, '$today', $daily_calories)");
            
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => true, 'message' => 'User registered successfully'];
        } else {
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    // Dietitian signup (goes to pending)
    public static function signupDietitian($data) {
        $conn = getDBConnection();
        
        // Check if email already exists in dietitians or pending
        $stmt = $conn->prepare("SELECT id FROM dietitians WHERE email = ? UNION SELECT id FROM pending_dietitians WHERE email = ?");
        $stmt->bind_param("ss", $data['email'], $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Email already exists'];
        }
        $stmt->close();
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Handle profile picture upload
        $profile_picture = 'default-dietitian.jpg';
        if (isset($data['profile_picture']) && !empty($data['profile_picture'])) {
            $profile_picture = $data['profile_picture'];
        }
        
        // Insert into pending dietitians
        $stmt = $conn->prepare("INSERT INTO pending_dietitians (full_name, email, phone, password, passing_institute, degrees, experience, specialization, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssssss", 
            $data['full_name'], 
            $data['email'], 
            $data['phone'], 
            $hashed_password, 
            $data['passing_institute'], 
            $data['degrees'], 
            $data['experience'], 
            $data['specialization'], 
            $profile_picture
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => true, 'message' => 'Registration submitted for admin approval'];
        } else {
            $stmt->close();
            closeDBConnection($conn);
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
}
?>
