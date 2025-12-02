<?php
include 'functions.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match';
        $messageType = 'error';
    } else {
        $result = registerCustomer($username, $email, $password);
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            // Auto-login after successful registration
            $loginResult = loginCustomer($username, $password);
            if ($loginResult['success']) {
                header('Location: index.php');
                exit();
            }
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
    <title>Shopping Cart System - Customer Registration</title>
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
            <h2>Customer Registration</h2>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="customer_register.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="auth-btn">Register</button>
            </form>

            <p class="auth-links">
                Already have an account? <a href="customer_login.php">Login here</a>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Made In India.</p>
    </footer>
</body>
</html>
