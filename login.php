<?php
session_start();
include "config.php";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Admin shortcut: allow admin/admin to login to admin dashboard
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['is_admin'] = true;
        header('Location: admin/dashboard_admin.php');
        exit();
    }

    $sql = "SELECT * FROM nguoi_dung
            WHERE ten_dang_nhap='$username'
            OR email='$username'";

    $result = $db->query($sql);

    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        if(password_verify($password,$user['mat_khau'])){

            $_SESSION['ma_khach_hang']=$user['ma_khach_hang'];
            $_SESSION['ho_ten']=$user['ho_ten'];
            $_SESSION['ten_dang_nhap']=$user['ten_dang_nhap'];

            header("Location:index.php");
            exit();

        }else{

            echo "<script>alert('Sai mật khẩu!');</script>";

        }

    }else{

        echo "<script>alert('Tài khoản không tồn tại!');</script>";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <div class="logo"
        onclick="window.location.href='index.php'"
        style="cursor:pointer;">

        <img src="./img/logo.png" width="180" height="180">

    </div>

</header>

<div class="login-container">

<div class="login-box">

<h1>Đăng Nhập 🍪</h1>

<p>Hãy nhập thông tin của bạn</p>

<form method="POST">

<input
type="text"
name="username"
placeholder="Tên tài khoản Hoặc Email"
required>

<input
type="password"
name="password"
placeholder="Mật khẩu"
required>

<button
type="submit"
name="login">

Xác Nhận

</button>

</form>

<p class="register-link">

Bạn chưa có tài khoản? <a href="register.php">Đăng ký</a>

</p>

</div>

</div>

</body>

</html>