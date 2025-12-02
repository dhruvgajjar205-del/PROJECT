<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $request[1] ?? '';

switch ($method) {
    case 'GET':
        if ($resource === 'products') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $products = getProducts();
                $product = $products[$id] ?? null;
                if ($product) {
                    echo json_encode(['success' => true, 'data' => $product]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product not found']);
                }
            } else {
                $products = getProducts();
                echo json_encode(['success' => true, 'data' => array_values($products)]);
            }
        }
        break;

    case 'POST':
        if ($resource === 'products') {
            if (!isAdminLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $id = getNextProductId();
            $product = [
                'name' => $data['name'] ?? '',
                'price' => (float)($data['price'] ?? 0),
                'description' => $data['description'] ?? '',
                'image' => $data['image'] ?? ''
            ];

            if (empty($product['name']) || $product['price'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product data']);
                exit;
            }

            saveProduct($id, $product);
            echo json_encode(['success' => true, 'data' => ['id' => $id] + $product]);
        }
        break;

    case 'PUT':
        if ($resource === 'products') {
            if (!isAdminLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $product = [
                'name' => $data['name'] ?? '',
                'price' => (float)($data['price'] ?? 0),
                'description' => $data['description'] ?? '',
                'image' => $data['image'] ?? ''
            ];

            if (empty($product['name']) || $product['price'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product data']);
                exit;
            }

            saveProduct($id, $product);
            echo json_encode(['success' => true, 'data' => ['id' => $id] + $product]);
        }
        break;

    case 'DELETE':
        if ($resource === 'products') {
            if (!isAdminLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }

            deleteProduct($id);
            echo json_encode(['success' => true, 'message' => 'Product deleted']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
