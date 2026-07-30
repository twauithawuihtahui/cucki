<?php
session_start();
include "config.php";

if(!isset($_SESSION['ma_khach_hang'])){
    echo json_encode([]);
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

$sql = "
SELECT
    c.ma_cookie AS id,
    c.ten_sp AS ten_sp,
    c.gia AS gia,
    c.hinh_anh AS hinh_anh,
    ct.so_luong,
    c.so_luong AS stock,
    'cookie' AS type
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN cookie c ON ct.ma_cookie = c.ma_cookie
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    cb.ma_combo AS id,
    cb.ten_combo AS ten_sp,
    cb.gia_combo AS gia,
    cb.hinh_anh AS hinh_anh,
    ct.so_luong,
    cb.soluong AS stock,
    'combo' AS type
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN combo_banh cb ON ct.ma_combo = cb.ma_combo
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    tt.ma_topping AS id,
    tt.ten_topping AS ten_sp,
    tt.gia_them AS gia,
    tt.hinh_anh AS hinh_anh,
    ct.so_luong,
    tt.soluong AS stock,
    'topping' AS type
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN topping_banh_them tt ON ct.ma_topping = tt.ma_topping
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    hb.ma_hop AS id,
    CONCAT('Box: ', hb.mau_hop) AS ten_sp,
    0 AS gia,
    hb.hinh_anh AS hinh_anh,
    ct.so_luong,
    hb.soluong AS stock,
    'hop' AS type
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN hop_banh hb ON ct.ma_hop = hb.ma_hop
WHERE gh.ma_khach_hang='$ma_khach_hang'
";

$result = $db->query($sql);

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;

}

echo json_encode($data);