<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    updateCartQuantity($productId, $quantity);

    // Redirect back to cart
    header('Location: cart.php');
    exit();
}
?>
