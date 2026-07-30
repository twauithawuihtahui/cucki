<?php
include 'config.php';
$res = $db->query('SHOW CREATE TABLE cookie');
if ($res) {
    $row = $res->fetch_assoc();
    echo "CREATE TABLE cookie:\n";
    echo $row['Create Table'] . "\n\n";
}
$rows = $db->query('SELECT ma_cookie, ten_sp, gia, so_luong, hinh_anh FROM cookie ORDER BY ma_cookie DESC LIMIT 20');
if ($rows) {
    echo "Last 20 rows:\n";
    while ($r = $rows->fetch_assoc()) {
        echo implode(' | ', [$r['ma_cookie'], $r['ten_sp'], $r['gia'], $r['so_luong'], $r['hinh_anh']]) . "\n";
    }
}
