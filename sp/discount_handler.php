<?php
include 'functions.php';
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = (float)($_POST['total'] ?? 0);
    $code = trim($_POST['code'] ?? '');

    $result = applyDiscount($total, $code);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}
?>
