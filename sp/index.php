<?php
include 'functions.php';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = getFilteredProducts($search);
$itemCount = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart System - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Shopping Cart System </h1>
            </div>
            <form action="index.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
            <nav>
                <a href="index.php">Home</a>
                <a href="cart.php">Cart (<?php echo $itemCount; ?>)</a>
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
        <h2><?php echo $search ? "Search Results for '$search'" : 'Featured Products'; ?></h2>
        <?php if (empty($products)): ?>
            <p>No products found matching your search.</p>
        <?php else: ?>
            <div class="products">
                <?php foreach ($products as $id => $product): ?>
                    <div class="product">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p class="price">₹<?php echo $product['price']; ?></p>
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="10">
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Made in India. All rights reserved.</p>
    </footer>
</body>
</html>
