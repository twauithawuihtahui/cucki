<?php
$db = new mysqli('localhost', 'root', '', 'csdl');
if ($db->connect_errno) {
    echo 'DBERR: ' . $db->connect_error;
    exit;
}
$res = $db->query('SELECT so_luong FROM cookie WHERE ma_cookie=205');
if ($res) {
    $row = $res->fetch_assoc();
    var_export($row);
} else {
    echo 'ERROR: ' . $db->error;
}
