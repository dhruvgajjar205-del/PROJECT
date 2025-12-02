<?php
include 'functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (loginAdmin($username, $password)) {
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $message = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shopping Cart System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Shopping Cart System - Admin</h1>
            </div>
            <nav>
                <a href="index.php">Back to Store</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-login">
            <h2>Admin Login</h2>
            <?php if (!empty($message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form action="admin.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p class="login-note">Default credentials: admin / admin123</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Admin Panel.</p>
    </footer>
</body>
</html>
