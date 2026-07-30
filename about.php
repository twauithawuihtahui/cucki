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

// no database queries needed for About page
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Cúc Ki</title>
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
                <button onclick="openLoginModal()">Login now</button>
            </div>
        </div>
    </header>

    <section class="hero about-hero">
        <div class="about-hero-badge">About Cúc Ki</div>
        <h1>Được chăm chút tỉ mỉ, dành riêng cho gu thưởng thức của bạn</h1>
        <p>Chúng tôi tin rằng mỗi chiếc bánh quy nên cảm thấy cá nhân, ấm áp và đáng nhớ — từ miếng đầu tiên đến miếng cuối cùng.</p>
    </section>

    <section id="products" class="cookies">
        <div class="product-title">
            <a href="index.php">Our Cookies</a>
            <a href="topping.php">Our Toppings</a>
            <a href="combobanh.php">Our Combos</a>
            <a href="box.php">Our Box</a>
            <a href="about.php">About Us</a>
        </div>

        <div class="about-content">
            <div class="about-grid">
                <div class="about-card">
                    <span class="eyebrow">ABOUT US</span>
                    <h2>Cúc Ki brings cookies made just the way you like them.</h2>
                    <p class="lead">Đã bao lâu rồi bạn chưa được ăn những chiếc bánh quy “đúng gu” của bạn?</p>
                    <p>Nhận thấy thị trường hiện nay chủ yếu cung cấp các loại bánh quy với công thức và topping cố định, không thể điều chỉnh topping bánh theo ý muốn của khách hàng. Vì vậy chúng mình mong muốn là một sự lựa chọn khác biệt, không muốn chạy theo số đông, Cúc Ki quyết định chiếm trọn trái tim người dùng với đa dạng loại bánh quy, từ dòng bánh truyền thống đến dòng bánh dành cho người ăn kiêng. Đặc biệt, mỗi chiếc bánh đều được tùy chỉnh theo sở thích của bạn từ vị bánh đến topping yêu thích, tạo nên những chiếc bánh quy “đúng gu” của bạn.</p>
                    <p>Được thành lập bởi 7 sinh viên trẻ tuổi chó (Ki) với niềm đam mê làm bánh (Cúc) và tinh thần luôn đặt khách hàng làm trung tâm, chúng mình tin rằng mỗi chiếc bánh không chỉ là một món ăn mà còn là sự kết nối giữa người làm bánh và người thưởng thức. Vì thế, Cúc Ki luôn tỉ mỉ trong từng công đoạn, lựa chọn nguyên liệu chất lượng, lắng nghe mọi nhu cầu của khách hàng và không ngừng cải thiện dịch vụ. Mục tiêu của chúng mình là mang đến những chiếc bánh thơm ngon, phù hợp với từng khẩu vị và để mỗi khách hàng đều có được trải nghiệm trọn vẹn khi lựa chọn Cúc Ki.</p>
                </div>

                <!-- brown panel immediately below the white card -->
                <div class="contact-panel">
                    <div class="contact-inner">
                        <h3>Why Cúc Ki?</h3>
                        <p>How long has it been since you last enjoyed a cookie made just the way you like it? We noticed that most cookies on the market today come with fixed recipes and pre-selected toppings, leaving little room for customers to personalize their treats. That's why we wanted to offer something different. Instead of following the crowd, Cúc Ki is dedicated to winning hearts with a wide variety of cookies—from classic favorites to healthier options for those with dietary preferences.</p>
                        <p>Best of all, every cookie can be customized to your taste, from the flavor to your favorite toppings, so you can enjoy a cookie that's truly made just for you.</p>
                        <p>Founded by seven young students—with "Cúc" representing our passion for baking and "Ki" symbolizing the Year of the Dog—we believe that every cookie is more than just a sweet treat; it's a connection between the baker and the person enjoying it.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-card">
                <div class="footer-contact">
                    <p><strong>Địa chỉ / address:</strong> Đường 3/2, Phường Ninh Kiều, Thành phố Cần Thơ</p>
                    <p><strong>Thời gian làm việc / service hours:</strong> 9:00 am - 21:00 pm</p>
                    <p><strong>Liên hệ / contact:</strong> coro9112006@gmail.com</p>
                    <p><strong>Hotline:</strong> 0123456789</p>
                </div>
                <div class="footer-cta">
                    <img src="./img/logo.png" alt="Cúc Ki" style="height:56px; border-radius:8px;">
                </div>
            </div>
            <div class="footer-base">&copy; <?php echo date('Y'); ?> Cúc Ki · All rights reserved</div>
        </div>
    </footer>

    <script>
        const isLoggedIn = <?= isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
