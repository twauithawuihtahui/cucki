<?php
$db = new mysqli('localhost', 'root', '', 'csdl');
if ($db->connect_errno) { echo 'DBERR: ' . $db->connect_error; exit; }
$res = $db->query('SELECT ma_khach_hang, ho_ten FROM nguoi_dung LIMIT 3');
while ($row = $res->fetch_assoc()) {
    echo $row['ma_khach_hang'] . ' ' . $row['ho_ten'] . "\n";
}
