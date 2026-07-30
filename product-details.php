<?php
session_start();
include 'config.php';

$user = null;
if (isset($_SESSION['ma_khach_hang'])) {
    $idUser = $_SESSION['ma_khach_hang'];
    $sqlUser = "SELECT * FROM nguoi_dung WHERE ma_khach_hang='$idUser'";
    $rs = $db->query($sqlUser);
    if ($rs && $rs->num_rows > 0) {
        $user = $rs->fetch_assoc();
    }
}

$id = $_GET['id'];

$sql = "SELECT * FROM cookie WHERE ma_cookie = $id";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    die("Không tìm thấy sản phẩm");
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Sweet Crumbs</title>
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
                    <span id="cartCount">0</span>
                </button>

                <div id="cartMenu" class="cart-menu">
                    <h2>Your Cart</h2>
                    <div id="cartItems">Cart empty</div>
                    <div class="cart-total">
                        Total: <span id="cartTotal">0đ</span>
                    </div>
                    <button class="checkout-btn" onclick="goCheckout()">Order</button>
                </div>
            </div>

            <div id="loginPrompt" class="cart-menu login-prompt">
                <h2>Please Login</h2>
                <p>To view your cart, please login first.</p>
                <button onclick="openLoginModal()">Login now</button>
            </div>

            <div id="authModal" class="auth-modal">
                <div id="loginBoard" class="auth-board">
                    <h2>Login</h2>
                    <input type="text" id="loginUsername" placeholder="Username or Email">
                    <input type="password" id="loginPassword" placeholder="Password">
                    <label class="remember-me">
                        <input type="checkbox"> Remember me
                    </label>
                    <button onclick="submitLogin()">Login</button>
                    <button onclick="toggleRegister()" class="grey-btn">Register</button>
                </div>

                <div id="registerBoard" class="auth-board" style="display:none;">
                    <h2>Register</h2>
                    <input type="text" id="regUsername" placeholder="Username">
                    <input type="email" id="regEmail" placeholder="Email">
                    <input type="tel" id="regPhone" placeholder="Phone Number">
                    <input type="password" id="regPassword" placeholder="Password">
                    <button onclick="submitRegister()">Create Account</button>
                    <button onclick="toggleRegister()" class="back-to-login">Back to Login</button>
                </div>
            </div>
        </div>
    </header>

    <section class="product-detail">
        <a class="back-btn" href="index.php">← Back to Products</a>

        <div class="detail-container">
            <div class="detail-image">
                <img src="img/<?php echo $row['hinh_anh']; ?>" alt="">
            </div>

            <div class="detail-info">
                <h1><?php echo $row['ten_sp']; ?></h1>
                <div class="detail-price">
                    <span><?= formatVnd($row['gia']) ?></span>
                </div>

                <p class="stock-text">Tồn kho: <?php echo (int)$row['so_luong']; ?></p>

                <h3>Mô tả</h3>

                <div class="description-box">
                    <?php echo nl2br($row['mo_ta']); ?>
                </div>

                <div class="quantity-selector">
                    <label>Số lượng:</label>
                    <div class="qty-controls">
                        <button class="qty-decrease" id="qtyDecrease">−</button>
                        <input type="number" id="quantityInput" value="1" min="1" readonly>
                        <button class="qty-increase" id="qtyIncrease">+</button>
                    </div>
                </div>

                <button class="add-cart-btn" id="addToCartBtn">Thêm vào giỏ hàng</button>
            </div>
        </div>
    </section>

    <script>
        const isLoggedIn = <?= isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                window.location.href = 'index.php';
                return;
            }



            // Quantity controls
            const quantityInput = document.getElementById('quantityInput');
            const qtyDecrease = document.getElementById('qtyDecrease');
            const qtyIncrease = document.getElementById('qtyIncrease');

            qtyDecrease.onclick = function() {
                let currentQty = parseInt(quantityInput.value);
                if (currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                }
            };

            qtyIncrease.onclick = function() {
                let currentQty = parseInt(quantityInput.value);
                quantityInput.value = currentQty + 1;
            };

            // Add to cart button
            const addBtn = document.getElementById('addToCartBtn');
            addBtn.onclick = function() {
                const quantity = parseInt(quantityInput.value);
                if (quantity < 1) {
                    return;
                }
                addCart('cookie', <?= $row['ma_cookie'] ?>, quantity);
                alert(quantity + ' cookie(s) added to cart!');
                quantityInput.value = 1;
            };
        });
    </script>
</body>

</html>