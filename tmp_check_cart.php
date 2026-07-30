<?php
include 'config.php';
session_start();
$id = 2;
$results = [];
$gio = $db->query("SELECT * FROM gio_hang WHERE ma_khach_hang='$id'");
$results['gio_hang_rows'] = $gio ? $gio->num_rows : 0;
if ($gio && $gio->num_rows) {
    $gh = $gio->fetch_assoc();
    $ct = $db->query("SELECT * FROM chi_tiet_gio_hang WHERE ma_gio_hang='" . $gh['ma_gio_hang'] . "'");
    $items = [];
    if ($ct) {
        while ($r = $ct->fetch_assoc()) {
            $items[] = $r;
        }
    }
    $results['items'] = $items;
}
var_export($results);
