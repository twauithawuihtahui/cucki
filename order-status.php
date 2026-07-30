<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order id']);
    exit();
}

$result = $db->query("SELECT trangthai FROM don_hang WHERE ma_don_hang='".$order_id."'");
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$row = $result->fetch_assoc();
$status = (int) $row['trangthai'];
$labels = [1 => 'accepting order', 2 => 'on delivery', 3 => 'successfully delivery'];
$status_label = $labels[$status] ?? 'accepting order';

echo json_encode(['success' => true, 'status' => $status, 'status_label' => $status_label]);
?>