<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (!isAdminLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $orders = getOrders();
        echo json_encode(['success' => true, 'data' => array_values($orders)]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $order = [
            'receipt' => 'rcpt_' . time(),
            'customer' => $data['customer'] ?? [],
            'items' => $data['items'] ?? [],
            'total' => (float)($data['total'] ?? 0),
            'status' => $data['status'] ?? 'pending',
            'date' => date('Y-m-d H:i:s')
        ];

        saveOrder($order);
        echo json_encode(['success' => true, 'data' => $order]);
        break;

    case 'PUT':
        if (!isAdminLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        updateOrderStatus($id, $data['status'] ?? 'pending');
        echo json_encode(['success' => true, 'message' => 'Order updated']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
