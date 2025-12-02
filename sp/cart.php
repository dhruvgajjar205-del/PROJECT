<?php
include 'functions.php';
$cart = $_SESSION['cart'];
$total = getCartTotal();
$itemCount = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart System - Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Shopping Cart System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart (<?php echo $itemCount; ?>)</a>
        </nav>
    </header>

    <main>
        <h2>Your Shopping Cart</h2>
        <?php if (empty($cart)): ?>
            <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                        <tr>
                            <td><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-image"></td>
                            <td><?php echo $item['name']; ?></td>
                            <td>₹<?php echo $item['price']; ?></td>
                            <td>
                                <form action="update_cart.php" method="post" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                    <button type="submit" class="update-btn">Update</button>
                                </form>
                            </td>
                            <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                            <td>
                                <form action="remove_from_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-total">
                <h3>Total: ₹<?php echo $total; ?></h3>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System . Made in India.</p>
    </footer>
</body>
</html>
