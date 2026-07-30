<?php
session_start();
include "config.php";

if(isset($_POST['register'])){

    $hoten = trim($_POST['hoten']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Kiểm tra tài khoản hoặc email đã tồn tại
    $check = "SELECT * FROM nguoi_dung
              WHERE ten_dang_nhap='$username'
              OR email='$email'";

    $result = $db->query($check);

    if($result->num_rows > 0){

        echo "<script>
                alert('Tên đăng nhập hoặc Email đã tồn tại!');
              </script>";

    }else{

        $sql = "INSERT INTO nguoi_dung
        (
            ho_ten,
            ten_dang_nhap,
            mat_khau,
            gioi_tinh,
            email,
            so_dien_thoai,
            dia_chi
        )
        VALUES
        (
            '$hoten',
            '$username',
            '$password',
            '$gender',
            '$email',
            '$phone',
            '$address'
        )";

        if($db->query($sql)){

            echo "<script>
                    alert('Đăng ký thành công!');
                    window.location='login.php';
                  </script>";

        }else{

            echo "<script>
                    alert('Đăng ký thất bại!');
                  </script>";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Register</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<div class="logo"
onclick="window.location.href='index.php'"
style="cursor:pointer;">

<img src="./img/logo.png"
width="180"
height="180">

</div>

</header>

<div class="login-container">

<div class="login-box">

<h1>Tạo tài khoản 🍪</h1>

<p>Hãy nhập thông tin của bạn</p>

<form method="POST">

<input
type="text"
name="hoten"
placeholder="Họ và tên"
required>

<input
type="text"
name="username"
placeholder="Tên đăng nhập"
required>

<input
type="email"
name="email"
placeholder="Email"
required>

<input
type="text"
name="phone"
placeholder="Số điện thoại"
required>

<input
type="text"
name="address"
placeholder="Địa chỉ"
required>

<select
name="gender"
required>

<option value="">-- Giới tính --</option>

<option value="Nam">Nam</option>

<option value="Nữ">Nữ</option>

<option value="Khác">Khác</option>

</select>

<input
type="password"
name="password"
placeholder="Mật khẩu"
required>

<button
type="submit"
name="register">

Đăng ký

</button>

</form>

<p class="register-link">

Đã có tài khoản? <a href="login.php">Đăng nhập</a>

</p>

</div>

</div>

</body>

</html>