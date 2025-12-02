<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $cart = $_SESSION['cart'] ?? [];
        $total = getCartTotal();
        $itemCount = getCartItemCount();
        echo json_encode([
            'success' => true,
            'data' => [
                'items' => $cart,
                'total' => $total,
                'itemCount' => $itemCount
            ]
        ]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = $data['product_id'] ?? null;
        $quantity = (int)($data['quantity'] ?? 1);

        if ($productId && $quantity > 0) {
            addToCart($productId, $quantity);
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = $data['product_id'] ?? null;
        $quantity = (int)($data['quantity'] ?? 0);

        if ($productId) {
            updateCartQuantity($productId, $quantity);
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
        }
        break;

    case 'DELETE':
        $productId = $_GET['product_id'] ?? null;
        if ($productId) {
            removeFromCart($productId);
            echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
