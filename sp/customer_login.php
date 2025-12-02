<?php
include 'functions.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } else {
        $result = loginCustomer($username, $password);
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            // Redirect to home page after successful login
            header('Location: index.php');
            exit();
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart System - Customer Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Shopping Cart System</h1>
            </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="cart.php">Cart (<?php echo getCartItemCount(); ?>)</a>
                <?php if (isCustomerLoggedIn()): ?>
                    <a href="customer_orders.php">My Orders</a>
                    <a href="customer_logout.php">Logout</a>
                <?php else: ?>
                    <a href="customer_login.php">Login</a>
                    <a href="customer_register.php">Register</a>
                <?php endif; ?>
                <a href="admin.php">Admin</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h2>Customer Login</h2>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="customer_login.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">Username or Email:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="auth-btn">Login</button>
            </form>

            <p class="auth-links">
                Don't have an account? <a href="customer_register.php">Register here</a>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System   Made in India.</p>
    </footer>
</body>
</html>
