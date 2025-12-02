<?php
require_once 'config.php';

if (!session_id()) {
    session_start();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get products from database
function getProducts() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id");
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[$row['id']] = [
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'description' => $row['description'],
                'image' => $row['image']
            ];
        }
        return $products;
    } catch (PDOException $e) {
        error_log("Database error in getProducts(): " . $e->getMessage());
        return [];
    }
}

// Get product by ID
function getProduct($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        return $product ? [
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'description' => $product['description'],
            'image' => $product['image']
        ] : null;
    } catch (PDOException $e) {
        error_log("Database error in getProduct(): " . $e->getMessage());
        return null;
    }
}

// Add or update product
function saveProduct($id, $product) {
    try {
        $pdo = getDBConnection();

        if ($id === null || !getProduct($id)) {
            // Insert new product
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image, stock_quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $product['name'],
                $product['price'],
                $product['description'] ?? '',
                $product['image'] ?? '',
                $product['stock_quantity'] ?? 0
            ]);
            return $pdo->lastInsertId();
        } else {
            // Update existing product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ?, stock_quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([
                $product['name'],
                $product['price'],
                $product['description'] ?? '',
                $product['image'] ?? '',
                $product['stock_quantity'] ?? 0,
                $id
            ]);
            return $id;
        }
    } catch (PDOException $e) {
        error_log("Database error in saveProduct(): " . $e->getMessage());
        return false;
    }
}

// Delete product (soft delete)
function deleteProduct($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE products SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error in deleteProduct(): " . $e->getMessage());
        return false;
    }
}

// Get next product ID (not needed with auto-increment, but keeping for compatibility)
function getNextProductId() {
    return null; // Database handles auto-increment
}

// Discount codes
$discountCodes = [
    'SAVE10' => ['type' => 'percentage', 'value' => 10],
    'FLAT50' => ['type' => 'fixed', 'value' => 50],
    'WELCOME20' => ['type' => 'percentage', 'value' => 20],
    'FLAT100' => ['type' => 'fixed', 'value' => 100],
    'SPECIAL15' => ['type' => 'percentage', 'value' => 15]
];

// Apply discount
function applyDiscount($total, $code) {
    global $discountCodes;
    if (isset($discountCodes[$code])) {
        $disc = $discountCodes[$code];
        if ($disc['type'] == 'percentage') {
            $discountAmount = $total * ($disc['value'] / 100);
        } else {
            $discountAmount = $disc['value'];
        }
        $newTotal = $total - $discountAmount;
        return [
            'success' => true,
            'total' => max(0, $newTotal),
            'discount' => $discountAmount,
            'message' => 'Discount applied successfully'
        ];
    }
    return [
        'success' => false,
        'total' => $total,
        'discount' => 0,
        'message' => 'Invalid discount code'
    ];
}

// Get filtered products based on search term
function getFilteredProducts($search = '') {
    $products = getProducts();
    if (empty($search)) {
        return $products;
    }
    $filtered = [];
    foreach ($products as $id => $product) {
        if (stripos($product['name'], $search) !== false || stripos($product['description'], $search) !== false) {
            $filtered[$id] = $product;
        }
    }
    return $filtered;
}

// Add product to cart
function addToCart($productId, $quantity = 1) {
    $products = getProducts();
    if (isset($products[$productId])) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'name' => $products[$productId]['name'],
                'price' => $products[$productId]['price'],
                'quantity' => $quantity,
                'image' => $products[$productId]['image']
            ];
        }
    }
}

// Remove product from cart
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

// Update product quantity in cart
function updateCartQuantity($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId]) && $quantity > 0) {
        $_SESSION['cart'][$productId]['quantity'] = $quantity;
    } elseif ($quantity == 0) {
        removeFromCart($productId);
    }
}

// Get cart total
function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Get cart item count
function getCartItemCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Create Custom Payment Order
function createCustomOrder($amount, $receipt) {
    // Custom gateway: always create a successful order
    return [
        'success' => true,
        'order_id' => 'custom_' . $receipt,
        'amount' => $amount * 100, // Amount in paisa for consistency
        'currency' => CURRENCY
    ];
}

// Verify Custom Payment
function verifyCustomPayment($paymentId, $orderId) {
    // Custom gateway: always verify as successful
    return ['success' => true];
}

// Customer Authentication Functions

// Register a new customer
function registerCustomer($username, $email, $password) {
    try {
        $pdo = getDBConnection();

        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new customer
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmt->execute([$username, $email, $hashedPassword]);

        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

// Customer login
function loginCustomer($username, $password) {
    try {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE (username = ? OR email = ?) AND role = 'customer'");
        $stmt->execute([$username, $username]);

        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_username'] = $user['username'];
            $_SESSION['customer_email'] = $user['email'];
            return ['success' => true, 'message' => 'Login successful'];
        }

        return ['success' => false, 'message' => 'Invalid username/email or password'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
    }
}

// Check if customer is logged in
function isCustomerLoggedIn() {
    return isset($_SESSION['customer_id']);
}

// Logout customer
function logoutCustomer() {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_username']);
    unset($_SESSION['customer_email']);
}

// Get customer orders
function getCustomerOrders($customerId) {
    try {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare("
            SELECT o.*, oi.product_name, oi.quantity, oi.product_price, oi.total_price
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.customer_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$customerId]);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $orderId = $row['id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $row['id'],
                    'receipt' => $row['receipt'],
                    'total_amount' => $row['total_amount'],
                    'payment_method' => $row['payment_method'],
                    'payment_status' => $row['payment_status'],
                    'order_status' => $row['order_status'],
                    'created_at' => $row['created_at'],
                    'items' => []
                ];
            }

            if ($row['product_name']) {
                $orders[$orderId]['items'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'product_price' => $row['product_price'],
                    'total_price' => $row['total_price']
                ];
            }
        }

        return array_values($orders);
    } catch (PDOException $e) {
        return [];
    }
}

// Get customer info
function getCustomerInfo($customerId) {
    try {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ? AND role = 'customer'");
        $stmt->execute([$customerId]);

        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Admin authentication
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function loginAdmin($username, $password) {
    // Simple hardcoded credentials (in production, use proper authentication)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logoutAdmin() {
    unset($_SESSION['admin_logged_in']);
}

// Get orders from database
function getOrders() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT o.*, GROUP_CONCAT(
                CONCAT(oi.product_name, ':', oi.quantity, ':', oi.product_price)
                SEPARATOR '|'
            ) as items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");

        $orders = [];
        while ($row = $stmt->fetch()) {
            $order = [
                'id' => $row['id'],
                'receipt' => $row['receipt'],
                'customer' => [
                    'name' => $row['customer_name'],
                    'email' => $row['customer_email'],
                    'address' => $row['customer_address'],
                    'city' => $row['customer_city'],
                    'pincode' => $row['customer_pincode']
                ],
                'total' => (float)$row['total_amount'],
                'discount' => (float)$row['discount_amount'],
                'discount_code' => $row['discount_code'],
                'payment_method' => $row['payment_method'],
                'payment_status' => $row['payment_status'],
                'order_status' => $row['order_status'],
                'priority' => $row['priority'],
                'date' => $row['created_at'],
                'items' => []
            ];

            // Parse items from GROUP_CONCAT
            if ($row['items']) {
                $items = explode('|', $row['items']);
                foreach ($items as $item) {
                    list($name, $quantity, $price) = explode(':', $item);
                    $order['items'][] = [
                        'name' => $name,
                        'quantity' => (int)$quantity,
                        'price' => (float)$price
                    ];
                }
            }

            $orders[] = $order;
        }

        return $orders;
    } catch (PDOException $e) {
        error_log("Database error in getOrders(): " . $e->getMessage());
        return [];
    }
}

// Save order to database
function saveOrder($order) {
    try {
        $pdo = getDBConnection();

        // Get customer ID if customer is logged in
        $customerId = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                customer_id, receipt, customer_name, customer_email, customer_address,
                customer_city, customer_pincode, total_amount, payment_method,
                payment_status, order_status, priority, discount_code, discount_amount,
                razorpay_order_id, razorpay_payment_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $customerId,
            $order['receipt'],
            $order['customer']['name'],
            $order['customer']['email'],
            $order['customer']['address'],
            $order['customer']['city'],
            $order['customer']['pincode'],
            $order['total'],
            $order['payment_method'],
            $order['payment_status'] ?? 'pending',
            $order['status'] ?? 'pending',
            $order['priority'],
            $order['discount_code'] ?? null,
            $order['discount'] ?? 0,
            $order['razorpay_order_id'] ?? null,
            $order['razorpay_payment_id'] ?? null
        ]);

        $orderId = $pdo->lastInsertId();

        // Insert order items
        if (isset($order['items']) && is_array($order['items'])) {
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total_price)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($order['items'] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['id'] ?? null,
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['price'] * $item['quantity']
                ]);
            }
        }

        return $orderId;
    } catch (PDOException $e) {
        error_log("Database error in saveOrder(): " . $e->getMessage());
        return false;
    }
}

// Update order status
function updateOrderStatus($orderId, $status) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error in updateOrderStatus(): " . $e->getMessage());
        return false;
    }
}

// Delete order
function deleteOrder($orderId) {
    try {
        $pdo = getDBConnection();
        // Delete order items first
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        // Delete order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error in deleteOrder(): " . $e->getMessage());
        return false;
    }
}

// Get order by ID
function getOrder($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT o.*, GROUP_CONCAT(
                CONCAT(oi.product_name, ':', oi.quantity, ':', oi.product_price)
                SEPARATOR '|'
            ) as items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ?
            GROUP BY o.id
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $order = [
            'id' => $row['id'],
            'receipt' => $row['receipt'],
            'customer' => [
                'name' => $row['customer_name'],
                'email' => $row['customer_email'],
                'address' => $row['customer_address'],
                'city' => $row['customer_city'],
                'pincode' => $row['customer_pincode']
            ],
            'total' => (float)$row['total_amount'],
            'discount' => (float)$row['discount_amount'],
            'discount_code' => $row['discount_code'],
            'payment_method' => $row['payment_method'],
            'payment_status' => $row['payment_status'],
            'order_status' => $row['order_status'],
            'priority' => $row['priority'],
            'date' => $row['created_at'],
            'items' => []
        ];

        // Parse items from GROUP_CONCAT
        if ($row['items']) {
            $items = explode('|', $row['items']);
            foreach ($items as $item) {
                list($name, $quantity, $price) = explode(':', $item);
                $order['items'][] = [
                    'name' => $name,
                    'quantity' => (int)$quantity,
                    'price' => (float)$price
                ];
            }
        }

        return $order;
    } catch (PDOException $e) {
        error_log("Database error in getOrder(): " . $e->getMessage());
        return null;
    }
}
?>
