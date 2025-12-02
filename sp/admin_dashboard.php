<?php
include 'functions.php';

if (!isAdminLoggedIn()) {
    header('Location: admin.php');
    exit();
}

$products = getProducts();
$orders = getOrders();
$totalProducts = count($products);
$totalOrders = count($orders);
$totalRevenue = 0;
foreach ($orders as $order) {
    $totalRevenue += $order['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shopping Cart System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Admin Dashboard</h1>
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
        <h2>Dashboard Overview</h2>
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?php echo $totalOrders; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>₹<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>

        <div class="dashboard-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="admin_products.php?action=add" class="action-btn">Add New Product</a>
                <a href="admin_orders.php" class="action-btn">View All Orders</a>
                <a href="admin_products.php" class="action-btn">Manage Products</a>
            </div>
        </div>

        <div class="recent-orders">
            <h3>Recent Orders</h3>
            <?php if (empty($orders)): ?>
                <p>No orders yet.</p>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentOrders = array_slice(array_reverse($orders), 0, 5);
                        foreach ($recentOrders as $index => $order):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['receipt'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['customer']['name'] ?? 'N/A'); ?></td>
                                <td>₹<?php echo number_format($order['total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['status'] ?? 'pending'); ?></td>
                                <td><?php echo htmlspecialchars($order['date'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Admin Panel.</p>
    </footer>
</body>
</html>
