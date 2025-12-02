<?php
include 'functions.php';

if (!isAdminLoggedIn()) {
    header('Location: admin.php');
    exit();
}

$message = '';
$products = getProducts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $product = [
                'name' => trim($_POST['name']),
                'price' => (float)$_POST['price'],
                'description' => trim($_POST['description']),
                'image' => trim($_POST['image']),
                'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0)
            ];

            $result = saveProduct($id, $product);
            if ($result) {
                $message = 'Product saved successfully!';
                $products = getProducts(); // Refresh products
            } else {
                $message = 'Error saving product!';
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $result = deleteProduct((int)$_POST['id']);
            if ($result) {
                $message = 'Product deleted successfully!';
                $products = getProducts(); // Refresh products
            } else {
                $message = 'Error deleting product!';
            }
        }
    }
}

$editProduct = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editProduct = $products[$_GET['id']] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Manage Products</h1>
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
        <div class="admin-content">
            <div class="product-form-section">
                <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
                <?php if (!empty($message)): ?>
                    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form action="admin_products.php" method="post" class="product-form">
                    <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (₹):</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($editProduct['price'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Image URL:</label>
                        <input type="url" id="image" name="image" value="<?php echo htmlspecialchars($editProduct['image'] ?? ''); ?>" required>
                    </div>

                    <button type="submit" class="save-btn"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                    <?php if ($editProduct): ?>
                        <a href="admin_products.php" class="cancel-btn">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="products-list-section">
                <h2>Existing Products</h2>
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $id => $product): ?>
                            <div class="product-admin">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                <h3><?php echo $product['name']; ?></h3>
                                <p>₹<?php echo $product['price']; ?></p>
                                <div class="product-actions">
                                    <a href="admin_products.php?action=edit&id=<?php echo $id; ?>" class="edit-btn">Edit</a>
                                    <form action="admin_products.php" method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Shopping Cart System. Admin Panel.</p>
    </footer>
</body>
</html>
