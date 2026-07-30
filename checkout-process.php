<?php
session_start();
include 'config.php';

if (!isset($_SESSION['ma_khach_hang'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$ma_khach_hang = (int) $_SESSION['ma_khach_hang'];
$customer_name = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'cod');

if ($customer_name === '' || $email === '' || $phone === '' || $address === '') {
    header('Location: checkout.php?error=1');
    exit();
}

$customer_name_safe = $db->real_escape_string($customer_name);
$email_safe = $db->real_escape_string($email);
$phone_safe = $db->real_escape_string($phone);
$address_safe = $db->real_escape_string($address);
$payment_text = $payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($payment_method);
$payment_text_safe = $db->real_escape_string($payment_text);

// Load cart items
$items = [];
$sql = "
SELECT
    COALESCE(ct.ma_cookie, 0) AS ma_cookie,
    COALESCE(ct.ma_combo, 0) AS ma_combo,
    COALESCE(ct.ma_hop, 0) AS ma_hop,
    COALESCE(ct.ma_topping, 0) AS ma_topping,
    c.ten_sp AS name,
    c.gia AS don_gia,
    ct.so_luong AS quantity,
    c.gia * ct.so_luong AS thanh_tien
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN cookie c ON ct.ma_cookie = c.ma_cookie
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    0 AS ma_cookie,
    COALESCE(ct.ma_combo, 0) AS ma_combo,
    COALESCE(ct.ma_hop, 0) AS ma_hop,
    COALESCE(ct.ma_topping, 0) AS ma_topping,
    cb.ten_combo AS name,
    cb.gia_combo AS don_gia,
    ct.so_luong AS quantity,
    cb.gia_combo * ct.so_luong AS thanh_tien
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN combo_banh cb ON ct.ma_combo = cb.ma_combo
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    0 AS ma_cookie,
    0 AS ma_combo,
    COALESCE(ct.ma_hop, 0) AS ma_hop,
    COALESCE(ct.ma_topping, 0) AS ma_topping,
    tt.ten_topping AS name,
    tt.gia_them AS don_gia,
    ct.so_luong AS quantity,
    tt.gia_them * ct.so_luong AS thanh_tien
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN topping_banh_them tt ON ct.ma_topping = tt.ma_topping
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    0 AS ma_cookie,
    0 AS ma_combo,
    ct.ma_hop AS ma_hop,
    0 AS ma_topping,
    CONCAT('Box: ', hb.mau_hop) AS name,
    0 AS don_gia,
    ct.so_luong AS quantity,
    0 AS thanh_tien
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN hop_banh hb ON ct.ma_hop = hb.ma_hop
WHERE gh.ma_khach_hang='$ma_khach_hang'
";
$rs = $db->query($sql);
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $items[] = $row;
    }
}

if (empty($items)) {
    header('Location: checkout.php?error=1');
    exit();
}

$total = 0.0;
foreach ($items as $item) {
    $total += (float) $item['thanh_tien'];
}

$db->query("INSERT INTO don_hang (ma_khach_hang, ngay_dat, trangthai, tong_tien) VALUES ('$ma_khach_hang', NOW(), 1, '$total')");
$ma_don_hang = $db->insert_id;
if (!$ma_don_hang) {
    header('Location: checkout.php?error=1');
    exit();
}

$hasToppingColumn = false;
$colResult = $db->query("SHOW COLUMNS FROM chi_tiet_don_hang LIKE 'ma_topping'");
if ($colResult && $colResult->num_rows > 0) {
    $hasToppingColumn = true;
}

foreach ($items as $item) {
    $ma_cookie = (int) $item['ma_cookie'];
    $ma_combo = (int) $item['ma_combo'];
    $ma_hop = (int) $item['ma_hop'];
    $ma_topping = (int) $item['ma_topping'];
    $quantity = (int) $item['quantity'];
    $don_gia = number_format((float) $item['don_gia'], 2, '.', '');
    $thanh_tien = number_format((float) $item['thanh_tien'], 2, '.', '');

    $fields = ['ma_cookie', 'ma_combo', 'ma_hop', 'ma_don_hang', 'so_luong', 'don_gia', 'thanh_tien'];
    $values = [
        $ma_cookie > 0 ? "'$ma_cookie'" : '0',
        $ma_combo > 0 ? "'$ma_combo'" : 'NULL',
        "'$ma_hop'",
        "'$ma_don_hang'",
        "'$quantity'",
        "'$don_gia'",
        "'$thanh_tien'",
    ];

    if ($hasToppingColumn) {
        array_unshift($fields, 'ma_topping');
        array_unshift($values, $ma_topping > 0 ? "'$ma_topping'" : 'NULL');
    }

    $fieldSql = implode(', ', $fields);
    $valueSql = implode(', ', $values);
    $db->query("INSERT INTO chi_tiet_don_hang ($fieldSql) VALUES ($valueSql)");

    if ($ma_cookie > 0) {
        $db->query("UPDATE cookie SET so_luong = GREATEST(0, so_luong - '$quantity') WHERE ma_cookie = '$ma_cookie'");
    }
    if ($ma_combo > 0) {
        $db->query("UPDATE combo_banh SET soluong = GREATEST(0, soluong - '$quantity') WHERE ma_combo = '$ma_combo'");
    }
    if ($ma_hop > 0) {
        $db->query("UPDATE hop_banh SET soluong = GREATEST(0, soluong - '$quantity') WHERE ma_hop = '$ma_hop'");
    }
    if ($ma_topping > 0) {
        $db->query("UPDATE topping_banh_them SET soluong = GREATEST(0, soluong - '$quantity') WHERE ma_topping = '$ma_topping'");
    }
}

$cartResult = $db->query("SELECT ma_gio_hang FROM gio_hang WHERE ma_khach_hang='$ma_khach_hang'");
if ($cartResult && $cartResult->num_rows > 0) {
    $cart = $cartResult->fetch_assoc();
    $ma_gio_hang = (int) $cart['ma_gio_hang'];
    $db->query("DELETE FROM chi_tiet_gio_hang WHERE ma_gio_hang='$ma_gio_hang'");
    $db->query("DELETE FROM gio_hang WHERE ma_gio_hang='$ma_gio_hang'");
}

$_SESSION['last_order_id'] = $ma_don_hang;
$_SESSION['last_order_customer'] = [
    'customer_name' => $customer_name_safe,
    'email' => $email_safe,
    'phone' => $phone_safe,
    'address' => $address_safe,
    'payment_method' => $payment_text_safe,
    'placed_at' => date('Y-m-d H:i:s'),
];

header('Location: order-success.php?order_id=' . $ma_don_hang);
exit();
