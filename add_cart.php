<?php
session_start();
include "config.php";

//========================
// Kiểm tra đăng nhập
//========================
if(!isset($_SESSION['ma_khach_hang'])){
    echo "login";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$ma_cookie = isset($_POST['ma_cookie']) ? intval($_POST['ma_cookie']) : null;
$ma_combo = isset($_POST['ma_combo']) ? intval($_POST['ma_combo']) : null;
$ma_hop = isset($_POST['ma_hop']) ? intval($_POST['ma_hop']) : null;
$ma_topping = isset($_POST['ma_topping']) ? intval($_POST['ma_topping']) : null;
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

if (!$ma_cookie && !$ma_combo && !$ma_hop && !$ma_topping) {
    echo "Không nhận được sản phẩm để thêm vào giỏ hàng!";
    exit();
}

$itemType = null;
if ($ma_cookie) {
    $itemType = 'cookie';
} elseif ($ma_combo) {
    $itemType = 'combo';
} elseif ($ma_hop) {
    $itemType = 'hop';
} else {
    $itemType = 'topping';
}

$stock = 0;
if ($itemType === 'cookie') {
    $result = $db->query("SELECT so_luong FROM cookie WHERE ma_cookie='$ma_cookie'");
    if ($result && $result->num_rows > 0) {
        $stock = (int)$result->fetch_assoc()['so_luong'];
    }
} elseif ($itemType === 'combo') {
    $result = $db->query("SELECT soluong FROM combo_banh WHERE ma_combo='$ma_combo'");
    if ($result && $result->num_rows > 0) {
        $stock = (int)$result->fetch_assoc()['soluong'];
    }
} elseif ($itemType === 'hop') {
    $result = $db->query("SELECT soluong FROM hop_banh WHERE ma_hop='$ma_hop'");
    if ($result && $result->num_rows > 0) {
        $stock = (int)$result->fetch_assoc()['soluong'];
    }
} elseif ($itemType === 'topping') {
    $result = $db->query("SELECT soluong FROM topping_banh_them WHERE ma_topping='$ma_topping'");
    if ($result && $result->num_rows > 0) {
        $stock = (int)$result->fetch_assoc()['soluong'];
    }
}

if ($stock <= 0) {
    echo "Sản phẩm hết hàng";
    exit();
}

//========================
// Tìm giỏ hàng
//========================
$sql = "SELECT * FROM gio_hang
        WHERE ma_khach_hang='$ma_khach_hang'";

$rs = $db->query($sql);

if($rs && $rs->num_rows > 0){

    $gio = $rs->fetch_assoc();
    $ma_gio_hang = $gio['ma_gio_hang'];

}else{

    $sql = "INSERT INTO gio_hang(ma_khach_hang)
            VALUES('$ma_khach_hang')";

    if(!$db->query($sql)){
        die($db->error);
    }

    $ma_gio_hang = $db->insert_id;
}

//========================
// Kiểm tra mục đã có trong giỏ hàng
//========================
$cookieClause = $ma_cookie ? "ma_cookie='$ma_cookie'" : "ma_cookie IS NULL";
$comboClause = $ma_combo ? "ma_combo='$ma_combo'" : "ma_combo IS NULL";
$hopClause = $ma_hop ? "ma_hop='$ma_hop'" : "ma_hop IS NULL";
$toppingClause = $ma_topping ? "ma_topping='$ma_topping'" : "ma_topping IS NULL";

$sql = "
SELECT *
FROM chi_tiet_gio_hang
WHERE ma_gio_hang='$ma_gio_hang'
AND $cookieClause
AND $comboClause
AND $hopClause
AND $toppingClause
";

$rs = $db->query($sql);

if(!$rs){
    die($db->error);
}

//========================
// Nếu có rồi thì tăng số lượng
//========================
if($rs->num_rows > 0){
    $current = $rs->fetch_assoc();
    if ((int)$current['so_luong'] + $quantity > $stock) {
        echo "Số lượng trong giỏ hàng không thể vượt quá tồn kho";
        exit();
    }

    $sql = "
    UPDATE chi_tiet_gio_hang
    SET so_luong = so_luong + $quantity
    WHERE ma_gio_hang='$ma_gio_hang'
    AND $cookieClause
    AND $comboClause
    AND $hopClause
    AND $toppingClause
    ";

    if(!$db->query($sql)){
        die($db->error);
    }

}else{

    if ($quantity > $stock) {
        echo "Số lượng trong giỏ hàng không thể vượt quá tồn kho";
        exit();
    }

    //========================
    // Thêm mục mới vào giỏ hàng
    //========================
    $sql = "
    INSERT INTO chi_tiet_gio_hang
    (
        ma_gio_hang,
        ma_cookie,
        ma_combo,
        ma_hop,
        ma_topping,
        so_luong
    )

    VALUES
    (
        '$ma_gio_hang',
        ".($ma_cookie ? "'$ma_cookie'" : "NULL").",
        ".($ma_combo ? "'$ma_combo'" : "NULL").",
        ".($ma_hop ? "'$ma_hop'" : "NULL").",
        ".($ma_topping ? "'$ma_topping'" : "NULL").",
        $quantity
    )
    ";

    if(!$db->query($sql)){
        die($db->error);
    }

}

echo "success"; exit;
?>