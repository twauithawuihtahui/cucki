<?php
session_start();
$_SESSION['ma_khach_hang'] = 2;
$_POST['type'] = 'cookie';
$_POST['id'] = 205;
$_POST['quantity'] = 7;
include 'update_cart.php';
