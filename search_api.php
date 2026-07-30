<?php
session_start();
include 'config.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode([]);
    exit();
}

$escaped = $db->real_escape_string($q);
$escapedLike = "%$escaped%";

$sql = "
    SELECT ma_cookie AS id, ten_sp AS ten_sp, gia AS gia, hinh_anh AS hinh_anh, so_luong AS stock, 'cookie' AS type
    FROM cookie
    WHERE LOWER(ten_sp) LIKE LOWER('{$escapedLike}')
    UNION ALL
    SELECT ma_combo AS id, ten_combo AS ten_sp, gia_combo AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'combo' AS type
    FROM combo_banh
    WHERE LOWER(ten_combo) LIKE LOWER('{$escapedLike}')
    UNION ALL
    SELECT ma_topping AS id, ten_topping AS ten_sp, gia_them AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'topping' AS type
    FROM topping_banh_them
    WHERE LOWER(ten_topping) LIKE LOWER('{$escapedLike}')
    UNION ALL
    SELECT ma_hop AS id, mau_hop AS ten_sp, 0 AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'hop' AS type
    FROM hop_banh
    WHERE LOWER(mau_hop) LIKE LOWER('{$escapedLike}')
";

$result = $db->query($sql);
if (!$result) {
    echo json_encode(['error' => $db->error]);
    exit();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
