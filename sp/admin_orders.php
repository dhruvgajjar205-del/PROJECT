<?php
include 'functions.php';

if (!isAdminLoggedIn()) {
    header('Location: admin.php');
    exit();
}

$message = '';
$orders = getOrders();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id']) && isset($_POST['status'])) {
        $result = updateOrderStatus((int)$_POST['order_id'], $_POST['status']);
        if ($result) {
            $message = 'Order status updated to ' . htmlspecialchars($_POST['status']) . ' successfully!';
            $orders = getOrders(); // Refresh orders
        } else {
            $message = 'Error updating order status!';
        }
    } elseif (isset($_POST['delete_order_id'])) {
        $result = deleteOrder((int)$_POST['delete_order_id']);
        if ($result) {
            $message = 'Order deleted successfully!';
            $orders = getOrders(); // Refresh orders
        } else {
            $message = 'Error deleting order!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Manage Orders</h1>
            </div>
            <nav>
                <a href="index.php">Store</a>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_products.php">Products</a>
                <a href="admin_orders.php">Orders</a>
                <a href="admin_logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <h2>Order Management</h2>
        <?php if (!empty($message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <div class="orders-container">
                <?php foreach ($orders as $index => $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['receipt'] ?? 'N/A'); ?></h3>
                            <span class="order-status status-<?php echo htmlspecialchars($order['order_status'] ?? 'pending'); ?>">
                                <?php echo htmlspecialchars($order['order_status'] ?? 'pending'); ?>
                            </span>
                        </div>

                        <div class="order-details">
                            <div class="customer-info">
                                <h4>Customer Information</h4>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer']['name'] ?? 'N/A'); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer']['email'] ?? 'N/A'); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['customer']['address'] ?? 'N/A'); ?>, <?php echo htmlspecialchars($order['customer']['city'] ?? 'N/A'); ?>, <?php echo htmlspecialchars($order['customer']['pincode'] ?? 'N/A'); ?></p>
                            </div>

                            <div class="order-items">
                                <h4>Order Items</h4>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>) - ₹<?php echo $item['price'] * $item['quantity']; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="order-summary">
                                <h4>Order Summary</h4>
                                <p><strong>Subtotal:</strong> ₹<?php echo number_format($order['total'] + ($order['discount'] ?? 0), 2); ?></p>
                                <?php if (isset($order['discount']) && $order['discount'] > 0): ?>
                                    <p><strong>Discount:</strong> -₹<?php echo number_format($order['discount'], 2); ?></p>
                                <?php endif; ?>
                                <p><strong>Total:</strong> ₹<?php echo number_format($order['total'], 2); ?></p>
                                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></p>
                                <p><strong>Priority:</strong> <?php echo htmlspecialchars($order['priority'] ?? 'N/A'); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($order['date'] ?? 'N/A'); ?></p>
                            </div>
                        </div>

                        <div class="order-actions">
                            <form action="admin_orders.php" method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php echo ($order['order_status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo ($order['order_status'] ?? 'pending') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo ($order['order_status'] ?? 'pending') === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order['order_status'] ?? 'pending') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($order['order_status'] ?? 'pending') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                            <form action="admin_orders.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                <input type="hidden" name="delete_order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Admin Panel.</p>
    </footer>
</body>
</html>
