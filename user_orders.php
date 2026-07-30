<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (empty($_SESSION['ma_khach_hang'])) {
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit();
}

$userId = (int) $_SESSION['ma_khach_hang'];
$res = $db->query("SELECT ma_don_hang, ngay_dat, tong_tien, trangthai FROM don_hang WHERE ma_khach_hang='$userId' ORDER BY ngay_dat DESC LIMIT 50");
$orders = [];
$labels = [1=>'accepting order',2=>'on delivery',3=>'successfully delivery'];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['status_label'] = $labels[(int)$row['trangthai']] ?? 'unknown';
        $orders[] = $row;
    }
}

echo json_encode(['success'=>true,'orders'=>$orders]);
?>