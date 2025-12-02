<?php
session_start();
require_once 'functions.php';

$order = null;
if (isset($_SESSION['order'])) {
    $order = $_SESSION['order'];
    unset($_SESSION['order']);
} elseif (isset($_SESSION['razorpay_order'])) {
    $order = $_SESSION['razorpay_order'];
    $order['status'] = 'paid'; // Mark as paid for Razorpay orders
    $order['date'] = date('Y-m-d H:i:s');
    saveOrder($order); // Save to JSON
    unset($_SESSION['razorpay_order']);
} elseif (isset($_SESSION['custom_order'])) {
    $order = $_SESSION['custom_order'];
    $order['status'] = 'paid'; // Mark as paid for Custom orders
    $order['date'] = date('Y-m-d H:i:s');
    saveOrder($order); // Save to database
    unset($_SESSION['custom_order']);
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Shopping Cart System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart (<?php echo getCartItemCount(); ?>)</a>
        </nav>
    </header>

    <main>
        <div class="success-message">
            <h2>Thank You for Your Order!</h2>
            <p>Your payment has been processed successfully.</p>
        </div>

        <div class="checkout-form">
            <h3>Order Details</h3>
            <div class="form-group">
                <label>Order ID:</label>
                <p><?php echo htmlspecialchars($order['receipt']); ?></p>
            </div>
            <div class="form-group">
                <label>Customer Name:</label>
                <p><?php echo htmlspecialchars($order['customer']['name']); ?></p>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <p><?php echo htmlspecialchars($order['customer']['email']); ?></p>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <p><?php echo htmlspecialchars($order['customer']['address']); ?></p>
            </div>
            <div class="form-group">
                <label>Payment Method:</label>
                <p><?php echo htmlspecialchars($order['payment_method']); ?></p>
            </div>
            <div class="form-group">
                <label>Priority:</label>
                <p><?php echo htmlspecialchars($order['priority']); ?></p>
            </div>

            <h4>Items Ordered:</h4>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
                        <td>₹<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                <h3>Total Amount: ₹<?php echo htmlspecialchars($order['total']); ?></h3>
            </div>

            <p><a href="index.php" class="checkout-btn">Continue Shopping</a></p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. All rights reserved.</p>
    </footer>
</body>
</html>
