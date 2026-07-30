<?php 
$host = "localhost";
$user = "root";
$pass = "";
$database = "csdl";
//gobal $db;
$db = @new mysqli($host,$user,$pass,$database);
if ($db->connect_errno){
    die("Không thể kết nối CSDL <br> ".$db->connect_error);
}   else{
  // echo "<h1>Kết nối thành công</h1>";
}
$db->query("SET NAMES utf8");

function formatVnd($amount) {
    if (is_string($amount)) {
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);
    }
    return number_format((float)$amount, 0, ',', '.') . 'đ';
}
?> 