<?php
session_start();
include 'config.php';

$user = null;
if (isset($_SESSION['ma_khach_hang'])) {
    $id = $_SESSION['ma_khach_hang'];
    $sqlUser = "SELECT * FROM nguoi_dung WHERE ma_khach_hang='$id'";
    $rs = $db->query($sqlUser);
    if ($rs && $rs->num_rows > 0) {
        $user = $rs->fetch_assoc();
    }
}

$sql = "SELECT * FROM combo_banh";
$result = $db->query($sql);
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sweet Crumbs</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


     <header>

        <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
            <img src="./img/logo.png" width="180" height="180">

        </div>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search cookies...">
            <button class="search-btn"><img src="./img/pngtree-vector-search-icon-png-image_320926.jpg" alt="Search"></button>
        </div>

        <div class="header-actions">

            <div id="welcomeText" class="welcome-text">
                <?php if ($user) { echo 'Hi, ' . htmlspecialchars($user['ho_ten']); } ?>
            </div>

            <?php if ($user) { ?>
                <a href="logout.php" class="login-btn">🚪</a>
            <?php } else { ?>
                <a href="login.php" class="login-btn">👤</a>
            <?php } ?>

            <div class="cart-container">


                <button id="cartBtn">

                    🛒

                    <span id="cartCount">
                        0
                    </span>

                </button>



                <div id="cartMenu" class="cart-menu">


                    <h2>Your Cart</h2>


                    <div id="cartItems">
                        Cart empty
                    </div>



                    <div class="cart-total">

                        Total: <span id="cartTotal">0 VND</span>

                    </div>



                    <button class="checkout-btn"
                        onclick="goCheckout()">

                        Order

                    </button>


                </div>


            </div>


            <div id="loginPrompt" class="cart-menu login-prompt">

                <h2>Please Login</h2>
                <p>To view your cart, please login first.</p>

                <button onclick="openLoginModal()">Login now</button>

            </div>


          

    </header>





    <section class="hero">


        <h1>
            Fresh Cookies 🍪
        </h1>


        <p>
            Cookies tươi mới cho mọi nhà.
        </p>

        <button class="scroll-btn" onclick="scrollToProducts()">
            Xem sản phẩm
        </button>

    </section>

    <section id="products" class="cookies">

        <div class="product-title">

            <a href="index.php">Our Cookies</a>
            <a href="topping.php">Our Toppings</a>
            <a href="combobanh.php">Our Combos</a>
            <a href="box.php">Our Box</a>
            <a href="about.php">About Us</a>


        </div>


        <div class="cookie-grid">

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <div class="cookie-card">

                    <img class="combo-img" src="img/<?php echo $row['hinh_anh']; ?>">

                    <h2>
                        <?php echo $row['ten_combo']; ?>
                    </h2>

<?php
                            $description = $row['mo_ta'];
                            $footerText = '';
                            if (strpos($description, 'Combo đã bao gồm hộp.') !== false) {
                                $description = str_replace('Combo đã bao gồm hộp.', '', $description);
                                $footerText = 'Combo đã bao gồm hộp.';
                            }
                        ?>
                    <p class="description-box">
                        <?php echo nl2br(htmlspecialchars(trim($description))); ?>
                    </p>
                    <?php if ($footerText): ?>
                        <p class="combo-footer"><?php echo htmlspecialchars($footerText); ?></p>
                    <?php endif; ?>

                    <b>
                        <?= formatVnd($row['gia_combo']) ?>
                    </b>

                    <p class="stock-text">
                        Số lượng còn lại: <?php echo (int)$row['soluong']; ?>
                    </p>

                    <div class="button-group">

                        <button class="add-cart-btn" onclick="addCart('combo', <?= $row['ma_combo'] ?>)">
                            Thêm vào giỏ hàng
                        </button>
                    </div>

                </div>

            <?php } ?>

        </div>

    </section>




    <script>
        const isLoggedIn = <?= isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>


</body>

</html>