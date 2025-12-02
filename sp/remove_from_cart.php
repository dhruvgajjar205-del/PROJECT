<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? 0;

    if ($productId > 0) {
        removeFromCart($productId);
    }
}

// Redirect back to cart
header('Location: cart.php');
exit();
?>
