<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fish_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Create database connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

// Test database connection
function testConnection() {
    $pdo = getDBConnection();
    if ($pdo) {
        echo "Database connection successful!";
        return true;
    } else {
        echo "Database connection failed!";
        return false;
    }
}

// Close database connection
function closeConnection($pdo) {
    $pdo = null;
}
?>

