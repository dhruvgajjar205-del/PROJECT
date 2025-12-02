<?php
include 'functions.php';

// Check if customer is logged in
if (!isCustomerLoggedIn()) {
    header('Location: customer_login.php');
    exit();
}

$customerId = $_SESSION['customer_id'];
$customerInfo = getCustomerInfo($customerId);
$orders = getCustomerOrders($customerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart System - My Orders</title>
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
        <h2>My Orders</h2>
        <p>Welcome back, <?php echo htmlspecialchars($customerInfo['username']); ?>!</p>

        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="index.php" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['receipt']); ?></h3>
                            <span class="order-date"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></span>
                        </div>

                        <div class="order-details">
                            <div class="order-info">
                                <p><strong>Total:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                                <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?> (<?php echo htmlspecialchars($order['payment_status']); ?>)</p>
                                <p><strong>Status:</strong>
                                    <span class="status status-<?php echo strtolower($order['order_status']); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </p>
                            </div>

                            <div class="order-items">
                                <h4>Items:</h4>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li>
                                            <?php echo htmlspecialchars($item['product_name']); ?> -
                                            <?php echo $item['quantity']; ?> x ₹<?php echo number_format($item['product_price'], 2); ?> =
                                            ₹<?php echo number_format($item['total_price'], 2); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Made in India.</p>
    </footer>
</body>
</html>
