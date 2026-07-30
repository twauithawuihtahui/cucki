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

$sql = "SELECT * FROM combo_banh WHERE ma_combo = $id";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    die("Không tìm thấy combo");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combo Details - Sweet Crumbs</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>

        <div class="logo" onclick="window.location.href='index.php'" style="cursor:pointer;">
            <img src="./img/logo.png" width="180" height="180">
        </div>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search cookies...">
            <button class="search-btn">
                <img src="./img/pngtree-vector-search-icon-png-image_320926.jpg">
            </button>
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

                    <div id="cartItems">
                        Cart empty
                    </div>

                    <div class="cart-total">
                        Total: <span id="cartTotal">0 VND</span>
                    </div>

                    <button class="checkout-btn" onclick="goCheckout()">
                        Order
                    </button>

                </div>

            </div>

            <div id="loginPrompt" class="cart-menu login-prompt">

                <h2>Please Login</h2>

                <p>To view your cart, please login first.</p>

                <button onclick="openLoginModal()">
                    Login now
                </button>

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

                    <button onclick="toggleRegister()" class="grey-btn">
                        Register
                    </button>

                </div>

                <div id="registerBoard" class="auth-board" style="display:none;">

                    <h2>Register</h2>

                    <input type="text" id="regUsername" placeholder="Username">

                    <input type="email" id="regEmail" placeholder="Email">

                    <input type="tel" id="regPhone" placeholder="Phone Number">

                    <input type="password" id="regPassword" placeholder="Password">

                    <button onclick="submitRegister()">
                        Create Account
                    </button>

                    <button onclick="toggleRegister()" class="back-to-login">
                        Back to Login
                    </button>

                </div>

            </div>

        </div>

    </header>

    <section class="product-detail">

        <a class="back-btn" href="combobanh.php">
            ← Back to Combos
        </a>

        <div class="detail-container">

            <div class="detail-image">

                <img src="img/<?php echo $row['hinh_anh']; ?>">

            </div>

            <div class="detail-info">

                <h1>

                    <?php echo $row['ten_combo']; ?>

                </h1>

                <div class="detail-price">

                    <?= formatVnd($row['gia_combo']) ?>

                </div>

                <p class="stock-text">Số lượng còn lại: <?php echo (int)$row['soluong']; ?></p>

                <h3>Description</h3>

                <div class="description-box">

                    <?php
                        $formattedDescription = str_replace('Gồm:', 'Bao gồm:', $row['mo_ta']);
                        $formattedDescription = preg_replace('/\.\s+/', ".\n", $formattedDescription);
                        $formattedDescription = preg_replace('/\.\n?(\s*Combo đã bao gồm hộp\.)/', ".\n$1", $formattedDescription);
                        echo nl2br(htmlspecialchars($formattedDescription));
                    ?>

                </div>

                <div class="quantity-selector">

                    <label>Quantity :</label>

                    <div class="qty-controls">

                        <button class="qty-decrease" id="qtyDecrease">−</button>

                        <input
                            type="number"
                            id="quantityInput"
                            value="1"
                            min="1"
                            readonly>

                        <button class="qty-increase" id="qtyIncrease">+</button>

                    </div>

                </div>

                <button class="add-cart-btn" id="addToCartBtn">

                    Add To Cart

                </button>

            </div>

        </div>

    </section>

    <script>
        const isLoggedIn = <?= isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const quantityInput = document.getElementById("quantityInput");

            document.getElementById("qtyDecrease").onclick = function() {

                let qty = parseInt(quantityInput.value);

                if (qty > 1) {

                    quantityInput.value = qty - 1;

                }

            };

            document.getElementById("qtyIncrease").onclick = function() {

                let qty = parseInt(quantityInput.value);

                quantityInput.value = qty + 1;

            };

            document.getElementById("addToCartBtn").onclick = function() {

                let qty = parseInt(quantityInput.value);
                if (qty < 1) {
                    return;
                }

                addCart('combo', <?php echo $row['ma_combo']; ?>, qty);

                alert(qty + " combo(s) added to cart!");

                quantityInput.value = 1;

            };

        });
    </script>

</body>

</html>