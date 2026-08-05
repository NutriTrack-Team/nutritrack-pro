<?php
// ================================
// Database Configuration
// ================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nutritrack_pro');
define('DB_PORT', 3307); // <-- IMPORTANT (XAMPP MySQL Port)

// ================================
// Create database connection
// ================================
function getDBConnection() {
    $conn = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        DB_PORT
    );

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// ================================
// Close database connection
// ================================
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
