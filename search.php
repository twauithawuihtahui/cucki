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

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($q !== '') {
    $escaped = $db->real_escape_string($q);
    $escapedLike = "%$escaped%";

    $sql = "
        SELECT ma_cookie AS id, ten_sp AS ten_sp, gia AS gia, hinh_anh AS hinh_anh, so_luong AS stock, 'cookie' AS type
        FROM cookie
        WHERE LOWER(ten_sp) LIKE LOWER('{$escapedLike}')
        UNION ALL
        SELECT ma_combo AS id, ten_combo AS ten_sp, gia_combo AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'combo' AS type
        FROM combo_banh
        WHERE LOWER(ten_combo) LIKE LOWER('{$escapedLike}')
        UNION ALL
        SELECT ma_topping AS id, ten_topping AS ten_sp, gia_them AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'topping' AS type
        FROM topping_banh_them
        WHERE LOWER(ten_topping) LIKE LOWER('{$escapedLike}')
        UNION ALL
        SELECT ma_hop AS id, mau_hop AS ten_sp, 0 AS gia, hinh_anh AS hinh_anh, soluong AS stock, 'hop' AS type
        FROM hop_banh
        WHERE LOWER(mau_hop) LIKE LOWER('{$escapedLike}')
    ";

    $res = $db->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $results[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
            <img src="./img/logo.png" width="180" height="180">
        </div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($q); ?>">
            <button class="search-btn"><img src="./img/pngtree-vector-search-icon-png-image_320926.jpg" alt="Search"></button>
        </div>
        <div class="header-actions">
            <div class="welcome-text">
                <?php if ($user) { echo 'Hi, ' . htmlspecialchars($user['ho_ten']); } ?>
            </div>
            <?php if ($user) { ?>
                <a href="logout.php" class="login-btn">🚪</a>
            <?php } else { ?>
                <a href="login.php" class="login-btn">👤</a>
            <?php } ?>
            <div class="cart-container">
                <button id="cartBtn">🛒<span id="cartCount">0</span></button>
                <div id="cartMenu" class="cart-menu">
                    <h2>Your Cart</h2>
                    <div id="cartItems">Cart empty</div>
                    <div class="cart-total">Total: <span id="cartTotal">0đ</span></div>
                    <button class="checkout-btn" onclick="goCheckout()">Order</button>
                </div>
            </div>
            <div id="loginPrompt" class="cart-menu login-prompt">
                <h2>Please Login</h2>
                <p>To view your cart, please login first.</p>
                <button onclick="window.location='login.php'">Login now</button>
            </div>
        </div>
    </header>
    <section class="hero">
        <h1>Search Results</h1>
        <p><?php echo $q === '' ? 'Type a product name and press search.' : 'Showing results for "' . htmlspecialchars($q) . '"'; ?></p>
    </section>
    <section class="cookies">
        <div class="product-title">
            <a href="index.php">Our Cookies</a>
            <a href="topping.php">Our Toppings</a>
            <a href="combobanh.php">Our Combos</a>
            <a href="box.php">Our Box</a>
            <a href="about.php">About Us</a>
        </div>
        <div class="cookie-grid">
            <?php if ($q === ''): ?>
                <div class="search-message">Enter a search term to find products.</div>
            <?php elseif (empty($results)): ?>
                <div class="search-message">No products found for "<?php echo htmlspecialchars($q); ?>".</div>
            <?php else: ?>
                <?php foreach ($results as $row): ?>
                    <div class="cookie-card">
                        <img src="img/<?php echo htmlspecialchars($row['hinh_anh']); ?>">
                        <h2><?php echo htmlspecialchars($row['ten_sp']); ?></h2>
                        <b><?= formatVnd($row['gia']) ?></b>
                        <p class="stock-text">Tồn kho: <?php echo (int)$row['stock']; ?></p>
                        <div class="button-group">
                            <button onclick="addCart('<?php echo htmlspecialchars($row['type']); ?>', <?php echo (int)$row['id']; ?>)">Add To Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
