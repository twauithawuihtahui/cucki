<?php
session_start();
include "config.php";

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "login";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];
$type = isset($_POST['type']) ? $_POST['type'] : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$type || $id <= 0) {
    echo "Invalid request";
    exit();
}

$columns = [
    'cookie' => 'ma_cookie',
    'combo' => 'ma_combo',
    'hop' => 'ma_hop',
    'topping' => 'ma_topping'
];

$stockSources = [
    'cookie' => ['table' => 'cookie', 'key' => 'ma_cookie', 'stock' => 'so_luong'],
    'combo' => ['table' => 'combo_banh', 'key' => 'ma_combo', 'stock' => 'soluong'],
    'hop' => ['table' => 'hop_banh', 'key' => 'ma_hop', 'stock' => 'soluong'],
    'topping' => ['table' => 'topping_banh_them', 'key' => 'ma_topping', 'stock' => 'soluong']
];

if (!isset($columns[$type]) || !isset($stockSources[$type])) {
    echo "Invalid item type";
    exit();
}

$field = $columns[$type];
$stockInfo = $stockSources[$type];

// Find the user's cart
$sql = "SELECT * FROM gio_hang WHERE ma_khach_hang='$ma_khach_hang'";
$rs = $db->query($sql);
if (!$rs || $rs->num_rows === 0) {
    echo "Cart not found";
    exit();
}
$cart = $rs->fetch_assoc();
$ma_gio_hang = $cart['ma_gio_hang'];

// Load stock quantity from product table
$sql = "SELECT {$stockInfo['stock']} AS stock FROM {$stockInfo['table']} WHERE {$stockInfo['key']}='$id'";
$rs = $db->query($sql);
if (!$rs || $rs->num_rows === 0) {
    echo "Item not found";
    exit();
}
$row = $rs->fetch_assoc();
$stock = (int)$row['stock'];

if ($quantity !== null) {
    if ($quantity < 0) {
        $quantity = 0;
    }
    if ($quantity > $stock) {
        echo "Quantity exceeds stock";
        exit();
    }
}

$otherColumns = [
    'ma_cookie',
    'ma_combo',
    'ma_hop',
    'ma_topping'
];
$whereParts = [];
foreach ($otherColumns as $col) {
    if ($col === $field) {
        $whereParts[] = "$col='$id'";
    } else {
        $whereParts[] = "$col IS NULL";
    }
}
$whereClause = implode(' AND ', $whereParts);

$sql = "SELECT * FROM chi_tiet_gio_hang WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
$rs = $db->query($sql);
if (!$rs) {
    echo $db->error;
    exit();
}

$exists = $rs->num_rows > 0;
$cartItem = $exists ? $rs->fetch_assoc() : null;
$currentQty = $exists ? (int)$cartItem['so_luong'] : 0;

if ($quantity !== null) {
    if ($quantity === 0) {
        $sql = "DELETE FROM chi_tiet_gio_hang WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
        if (!$db->query($sql)) {
            echo $db->error;
            exit();
        }
        echo "removed";
        exit();
    }
    if ($exists) {
        if ($quantity === $currentQty) {
            echo "success";
            exit();
        }
        $sql = "UPDATE chi_tiet_gio_hang SET so_luong='$quantity' WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
        if (!$db->query($sql)) {
            echo $db->error;
            exit();
        }
        echo "success";
        exit();
    }
    // Create new row if not present and quantity is valid
    $sql = "INSERT INTO chi_tiet_gio_hang (ma_gio_hang, ma_cookie, ma_combo, ma_hop, ma_topping, so_luong) VALUES ('{$ma_gio_hang}', ";
    $sql .= $field === 'ma_cookie' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_combo' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_hop' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_topping' ? "'{$id}'" : "NULL";
    $sql .= ", {$quantity})";
    if (!$db->query($sql)) {
        echo $db->error;
        exit();
    }
    echo "success";
    exit();
}

if (!$exists && $action === 'inc') {
    if ($stock < 1) {
        echo "Out of stock";
        exit();
    }
    $sql = "INSERT INTO chi_tiet_gio_hang (ma_gio_hang, ma_cookie, ma_combo, ma_hop, ma_topping, so_luong) VALUES ('{$ma_gio_hang}', ";
    $sql .= $field === 'ma_cookie' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_combo' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_hop' ? "'{$id}'" : "NULL";
    $sql .= ", ";
    $sql .= $field === 'ma_topping' ? "'{$id}'" : "NULL";
    $sql .= ", 1)";
    if (!$db->query($sql)) {
        echo $db->error;
        exit();
    }
    echo "success";
    exit();
}

if ($action === 'inc') {
    $newQty = $currentQty + 1;
    if ($newQty > $stock) {
        echo "Quantity exceeds stock";
        exit();
    }
    $sql = "UPDATE chi_tiet_gio_hang SET so_luong='$newQty' WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
    if (!$db->query($sql)) {
        echo $db->error;
        exit();
    }
    echo "success";
    exit();
}

if ($action === 'dec') {
    if ($currentQty <= 1) {
        $sql = "DELETE FROM chi_tiet_gio_hang WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
        if (!$db->query($sql)) {
            echo $db->error;
            exit();
        }
        echo "removed";
        exit();
    }
    $newQty = $currentQty - 1;
    $sql = "UPDATE chi_tiet_gio_hang SET so_luong='$newQty' WHERE ma_gio_hang='$ma_gio_hang' AND $whereClause";
    if (!$db->query($sql)) {
        echo $db->error;
        exit();
    }
    echo "success";
    exit();
}

echo "Unknown action";
exit();
