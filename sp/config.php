<?php
// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'shopping_cart');
if (!defined('DB_USER')) define('DB_USER', 'root'); // Change this to your database username
if (!defined('DB_PASS')) define('DB_PASS', ''); // Change this to your database password
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Razorpay Configuration
// Get your API keys from https://dashboard.razorpay.com/app/keys

if (!defined('RAZORPAY_KEY_ID')) define('RAZORPAY_KEY_ID', 'YOUR_RAZORPAY_KEY_ID'); // Replace with your Razorpay Key ID
if (!defined('RAZORPAY_KEY_SECRET')) define('RAZORPAY_KEY_SECRET', 'YOUR_RAZORPAY_KEY_SECRET'); // Replace with your Razorpay Key Secret

// Currency
if (!defined('CURRENCY')) define('CURRENCY', 'INR');

// Company details
if (!defined('COMPANY_NAME')) define('COMPANY_NAME', 'Indian Shopping Cart');
if (!defined('COMPANY_EMAIL')) define('COMPANY_EMAIL', 'support@indianshoppingcart.com');

// Database connection function
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        static $pdo = null;

        if ($pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return $pdo;
    }
}
?>
