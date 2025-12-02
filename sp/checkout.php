<?php
include 'functions.php';
include 'config.php';

$cart = $_SESSION['cart'];
$total = getCartTotal();
$discount = 0;
$discount_code = '';
$discount_message = '';

if (empty($cart)) {
    header('Location: index.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate shipping information
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    $discount_code = trim($_POST['discount_code'] ?? '');
    $priority = trim($_POST['priority'] ?? '');

    // Apply discount if code provided
    if (!empty($discount_code)) {
        $discount_result = applyDiscount($total, $discount_code);
        if ($discount_result['success']) {
            $discount = $discount_result['discount'];
            $total -= $discount;
            $discount_message = $discount_result['message'];
        } else {
            $errors[] = $discount_result['message'];
        }
    }

    // Basic validation
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($pincode)) $errors[] = 'Pincode is required';
    if (empty($payment_method)) $errors[] = 'Payment method is required';

    // Validate payment fields
    if (in_array($payment_method, ['credit_card', 'debit_card'])) {
        $card_number = trim($_POST['card_number'] ?? '');
        $card_expiry = trim($_POST['card_expiry'] ?? '');
        $card_cvv = trim($_POST['card_cvv'] ?? '');
        $card_name = trim($_POST['card_name'] ?? '');
        if (empty($card_number)) $errors[] = 'Card number is required';
        if (empty($card_expiry)) $errors[] = 'Card expiry is required';
        if (empty($card_cvv)) $errors[] = 'Card CVV is required';
        if (empty($card_name)) $errors[] = 'Name on card is required';
    }
    if ($payment_method === 'upi') {
        $upi_id = trim($_POST['upi_id'] ?? '');
        if (empty($upi_id)) $errors[] = 'UPI ID is required';
    }

    if (empty($errors)) {
        if ($payment_method === 'cod') {
            // Store order for COD
            $order = [
                'receipt' => 'rcpt_' . time(),
                'amount' => $total,
                'currency' => 'INR',
                'customer' => [
                    'name' => $name,
                    'email' => $email,
                    'address' => $address,
                    'city' => $city,
                    'pincode' => $pincode
                ],
                'items' => $cart,
                'total' => $total,
                'discount' => $discount,
                'discount_code' => $discount_code,
                'payment_method' => 'cod',
                'priority' => $priority,
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s')
            ];
            $_SESSION['order'] = $order;
            saveOrder($order); // Save to JSON
            $_SESSION['cart'] = []; // Clear cart
            header('Location: payment_success.php');
            exit();
        } else {
            // Create Custom order
            $receipt = 'rcpt_' . time();
            $orderResult = createCustomOrder($total, $receipt);

            if (!$orderResult['success']) {
                $errors[] = 'Failed to create payment order: ' . $orderResult['error'];
            } else {
                // Store order details in session
                $_SESSION['custom_order'] = [
                    'order_id' => $orderResult['order_id'],
                    'receipt' => $receipt,
                    'amount' => $orderResult['amount'],
                    'currency' => $orderResult['currency'],
                    'customer' => [
                        'name' => $name,
                        'email' => $email,
                        'address' => $address,
                        'city' => $city,
                        'pincode' => $pincode
                    ],
                    'items' => $cart,
                    'total' => $total,
                    'discount' => $discount,
                    'discount_code' => $discount_code,
                    'payment_method' => $payment_method,
                    'priority' => $priority
                ];
                $_SESSION['cart'] = []; // Clear cart
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart System - Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1> Shopping Cart System</h1>
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
        <h2>Checkout</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="checkout-summary">
            <h3>Order Summary</h3>
            <table class="order-summary-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot id="order-tfoot">
                    <tr id="subtotal-row">
                        <td colspan="3" class="text-right">Subtotal:</td>
                        <td id="subtotal-amount">₹<?php echo number_format(getCartTotal(), 2); ?></td>
                    </tr>
                    <?php if ($discount > 0): ?>
                        <tr id="discount-row">
                            <td colspan="3" class="text-right">Discount (<?php echo htmlspecialchars($discount_code); ?>):</td>
                            <td id="discount-amount">-₹<?php echo number_format($discount, 2); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td id="total-amount"><strong>₹<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <?php if (!empty($discount_message)): ?>
                <p class="discount-message"><?php echo htmlspecialchars($discount_message); ?></p>
            <?php endif; ?>
        </div>

        <form action="checkout.php" method="post" class="checkout-form">
            <h3>Shipping Information</h3>
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pincode">Pincode:</label>
                <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="priority">Order Priority:</label>
                <select id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <h3>Discount Code</h3>
            <div class="form-group">
                <label for="discount_code">Discount Code (optional):</label>
                <input type="text" id="discount_code" name="discount_code" value="<?php echo htmlspecialchars($_POST['discount_code'] ?? ''); ?>" placeholder="Enter discount code">
                <button type="button" id="apply_discount" class="btn">Apply Discount</button>
            </div>
            <div id="discount_message" class="discount-message" style="display: none;"></div>

            <h3>Payment Method</h3>
            <div class="form-group">
                <label><input type="radio" name="payment_method" value="credit_card" required> Credit Card</label><br>
                <label><input type="radio" name="payment_method" value="debit_card"> Debit Card</label><br>
                <label><input type="radio" name="payment_method" value="upi"> UPI</label><br>
                <label><input type="radio" name="payment_method" value="cod"> Cash on Delivery</label>
            </div>

            <div id="card-fields" style="display:none;">
                <h4>Card Details</h4>
                <div class="form-group">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number">
                </div>
                <div class="form-group">
                    <label for="card_expiry">Expiry (MM/YY):</label>
                    <input type="text" id="card_expiry" name="card_expiry">
                </div>
                <div class="form-group">
                    <label for="card_cvv">CVV:</label>
                    <input type="text" id="card_cvv" name="card_cvv">
                </div>
                <div class="form-group">
                    <label for="card_name">Name on Card:</label>
                    <input type="text" id="card_name" name="card_name">
                </div>
            </div>

            <div id="upi-fields" style="display:none;">
                <h4>UPI Details</h4>
                <div class="form-group">
                    <label for="upi_id">UPI ID:</label>
                    <input type="text" id="upi_id" name="upi_id">
                </div>
            </div>

            <button type="submit" class="checkout-btn">Complete Payment</button>
        </form>



        <?php if (isset($_SESSION['custom_order'])): ?>
            <div id="custom-payment" style="margin-top: 20px; padding: 20px; border: 1px solid #ccc; background: #f9f9f9;">
                <h3>Custom Payment Gateway</h3>
                <p>Payment Method: <?php echo htmlspecialchars($_SESSION['custom_order']['payment_method']); ?></p>
                <p>Amount: ₹<?php echo number_format($_SESSION['custom_order']['total'], 2); ?></p>
                <button id="pay-now-btn" class="checkout-btn">Pay Now</button>
            </div>
            <script>
                document.getElementById('pay-now-btn').addEventListener('click', function() {
                    alert('Payment processed successfully!');
                    window.location.href = 'payment_success.php';
                });
            </script>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025  Shopping Cart System. Made in India.</p>
    </footer>

    <script>
        document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('card-fields').style.display = 'none';
                document.getElementById('upi-fields').style.display = 'none';
                if (this.value === 'credit_card' || this.value === 'debit_card') {
                    document.getElementById('card-fields').style.display = 'block';
                } else if (this.value === 'upi') {
                    document.getElementById('upi-fields').style.display = 'block';
                }
            });
        });

        document.getElementById('apply_discount').addEventListener('click', function() {
            const code = document.getElementById('discount_code').value.trim();
            const subtotalText = document.getElementById('subtotal-amount').textContent.replace('₹', '').replace(',', '');
            const subtotal = parseFloat(subtotalText);

            if (!code) {
                showDiscountMessage('Please enter a discount code.', false);
                return;
            }

            fetch('discount_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'total=' + subtotal + '&code=' + encodeURIComponent(code)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateOrderSummary(data, code);
                    showDiscountMessage(data.message, true);
                } else {
                    showDiscountMessage(data.message, false);
                }
            })
            .catch(error => {
                showDiscountMessage('Error applying discount.', false);
            });
        });

        function updateOrderSummary(data, code) {
            const tfoot = document.getElementById('order-tfoot');
            let discountRow = document.getElementById('discount-row');

            if (data.discount > 0) {
                if (!discountRow) {
                    discountRow = document.createElement('tr');
                    discountRow.id = 'discount-row';
                    tfoot.insertBefore(discountRow, document.querySelector('.total-row'));
                }
                discountRow.innerHTML = `
                    <td colspan="3" class="text-right">Discount (${code}):</td>
                    <td id="discount-amount">-₹${data.discount.toFixed(2)}</td>
                `;
            } else {
                if (discountRow) {
                    discountRow.remove();
                }
            }

            document.getElementById('total-amount').innerHTML = `<strong>₹${data.total.toFixed(2)}</strong>`;
        }

        function showDiscountMessage(message, success) {
            const messageDiv = document.getElementById('discount_message');
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            messageDiv.style.color = success ? '#155724' : '#721c24';
        }
    </script>
</body>
</html>
