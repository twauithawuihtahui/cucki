<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order id']);
    exit();
}

$result = $db->query("SELECT trangthai FROM don_hang WHERE ma_don_hang='$order_id'");
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = $result->fetch_assoc();
$currentStatus = (int) $order['trangthai'];
if ($currentStatus >= 3) {
    echo json_encode(['success' => true, 'status' => 3, 'message' => 'Already final status']);
    exit();
}

$newStatus = $currentStatus + 1;
$db->query("UPDATE don_hang SET trangthai='$newStatus' WHERE ma_don_hang='$order_id'");
if ($db->affected_rows < 0) {
    echo json_encode(['success' => false, 'message' => 'Unable to update status']);
    exit();
}

$statusLabel = [1 => 'accepting order', 2 => 'on delivery', 3 => 'successfully delivery'][$newStatus] ?? 'unknown';

echo json_encode([
    'success' => true,
    'status' => $newStatus,
    'status_label' => $statusLabel,
]);
