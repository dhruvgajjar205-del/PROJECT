<?php
// Database Connection Test Script
// Run this to verify your database setup

require_once 'config.php';

echo "<h1>Database Connection Test</h1>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";

    // Test if tables exist
    $tables = ['users', 'products', 'orders', 'order_items', 'cart_sessions'];
    echo "<h2>Checking Tables:</h2>";

    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
            echo "<p style='color: green;'>✅ Table `$table` exists</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Table `$table` missing: " . $e->getMessage() . "</p>";
        }
    }

    // Test admin user
    echo "<h2>Checking Admin User:</h2>";
    $stmt = $pdo->query("SELECT username, email, role FROM users WHERE role = 'admin'");
    $admin = $stmt->fetch();
    if ($admin) {
        echo "<p style='color: green;'>✅ Admin user found: {$admin['username']} ({$admin['email']})</p>";
    } else {
        echo "<p style='color: red;'>❌ No admin user found</p>";
    }

    // Test products
    echo "<h2>Checking Products:</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✅ Active products: {$result['count']}</p>";

    // Test sample product
    $stmt = $pdo->prepare("SELECT name, price FROM products WHERE is_active = 1 LIMIT 1");
    $stmt->execute();
    $product = $stmt->fetch();
    if ($product) {
        echo "<p style='color: green;'>✅ Sample product: {$product['name']} - ₹{$product['price']}</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<h2>Troubleshooting:</h2>";
    echo "<ul>";
    echo "<li>Make sure MySQL server is running</li>";
    echo "<li>Check database credentials in config.php</li>";
    echo "<li>Run setup_database.sql first, then database.sql</li>";
    echo "<li>Verify database 'shopping_cart' exists</li>";
    echo "</ul>";
}
?>
