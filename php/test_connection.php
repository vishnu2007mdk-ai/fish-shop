<?php
// Test database connection
require_once 'config/database.php';

echo "<h1>Fish Shop Database Connection Test</h1>";

// Test connection
if (testConnection()) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test basic query
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $result = $stmt->fetch();
        echo "<p>📊 Total products in database: " . $result['total'] . "</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $result = $stmt->fetch();
        echo "<p>🏷️ Total categories in database: " . $result['total'] . "</p>";
        
        closeConnection($pdo);
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Query test failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL service is running</li>";
    echo "<li>Database 'fish_shop' exists</li>";
    echo "<li>Database credentials in config/database.php</li>";
    echo "<li>PHP PDO extension is enabled</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Import the database schema: <code>php/database/schema.sql</code></li>";
echo "<li>Test the API endpoints</li>";
echo "<li>Open <code>index.html</code> in your browser</li>";
echo "</ol>";
?>

