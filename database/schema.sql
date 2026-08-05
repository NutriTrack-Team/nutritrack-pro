-- NutriTrack Pro Database Schema
-- MySQL Database

CREATE DATABASE IF NOT EXISTS nutritrack_pro;
USE nutritrack_pro;

-- Dietitians Table
CREATE TABLE dietitians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    passing_institute VARCHAR(200) NOT NULL,
    degrees TEXT NOT NULL,
    experience INT NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default-dietitian.jpg',
    about TEXT,
    rating DECIMAL(3,2) DEFAULT 4.50,
    patient_count INT DEFAULT 0,
    monthly_price DECIMAL(10,2) DEFAULT 2500.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    height DECIMAL(5,2) NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    target_weight DECIMAL(5,2) NOT NULL,
    activity_level ENUM('Sedentary', 'Lightly Active', 'Moderately Active', 'Very Active', 'Extra Active') NOT NULL,
    medical_conditions TEXT,
    dietary_preference VARCHAR(100),
    allergies TEXT,
    bmi DECIMAL(5,2),
    daily_calories INT,
    daily_protein INT,
    daily_carbs INT,
    daily_fats INT,
    profile_picture VARCHAR(255) DEFAULT 'default.jpg',
    about TEXT,
    dietitian_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dietitian_id) REFERENCES dietitians(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Pending Dietitian Registrations
CREATE TABLE pending_dietitians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    passing_institute VARCHAR(200) NOT NULL,
    degrees TEXT NOT NULL,
    experience INT NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default-dietitian.jpg',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Food Items Table
CREATE TABLE food_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    calories INT NOT NULL,
    protein DECIMAL(5,2) DEFAULT 0,
    carbs DECIMAL(5,2) DEFAULT 0,
    fats DECIMAL(5,2) DEFAULT 0,
    serving_size VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Food Logs Table
CREATE TABLE food_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    food_id INT NOT NULL,
    meal_type ENUM('Breakfast', 'Lunch', 'Dinner', 'Snacks') NOT NULL,
    servings DECIMAL(5,2) DEFAULT 1.0,
    calories INT NOT NULL,
    protein DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fats DECIMAL(5,2),
    log_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Water Logs Table
CREATE TABLE water_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    glasses INT DEFAULT 0,
    log_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, log_date)
) ENGINE=InnoDB;

-- Pantry Items Table
CREATE TABLE pantry_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Recipes Table
CREATE TABLE recipes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    recipe_name VARCHAR(200) NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT,
    calories INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Subscriptions Table
CREATE TABLE subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    dietitian_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    subscription_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dietitian_id) REFERENCES dietitians(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Diet Plans Table
CREATE TABLE diet_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    dietitian_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    overview TEXT,
    duration_weeks INT NOT NULL,
    daily_calories INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dietitian_id) REFERENCES dietitians(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Diet Plan Days Table
CREATE TABLE diet_plan_days (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_id INT NOT NULL,
    day_name ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    breakfast TEXT,
    breakfast_completed TINYINT(1) DEFAULT 0,
    lunch TEXT,
    lunch_completed TINYINT(1) DEFAULT 0,
    dinner TEXT,
    dinner_completed TINYINT(1) DEFAULT 0,
    snacks TEXT,
    snacks_completed TINYINT(1) DEFAULT 0,
    FOREIGN KEY (plan_id) REFERENCES diet_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Messages Table
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    sender_type ENUM('user', 'dietitian') NOT NULL,
    receiver_id INT NOT NULL,
    receiver_type ENUM('user', 'dietitian') NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Alerts Table
CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unseen', 'active', 'dismissed') DEFAULT 'unseen',
    source ENUM('system', 'admin', 'dietitian') NOT NULL,
    meta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Progress Tracker Table
CREATE TABLE progress_tracker (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    calories_consumed INT DEFAULT 0,
    calories_target INT NOT NULL,
    protein_consumed DECIMAL(5,2) DEFAULT 0,
    carbs_consumed DECIMAL(5,2) DEFAULT 0,
    fats_consumed DECIMAL(5,2) DEFAULT 0,
    water_glasses INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date)
) ENGINE=InnoDB;

-- Weight Logs Table
CREATE TABLE weight_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    log_date DATE NOT NULL,
    bmi DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE dietitian_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dietitian_id INT NOT NULL,
    client_id INT NOT NULL,
    assigned_date DATE DEFAULT CURRENT_DATE,
    status ENUM('active','inactive') DEFAULT 'active',

    FOREIGN KEY (dietitian_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

