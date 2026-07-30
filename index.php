<?php
session_start();
include 'config.php';
$user = null;

if(isset($_SESSION['ma_khach_hang'])){

    $id = $_SESSION['ma_khach_hang'];

    $sqlUser = "SELECT * FROM nguoi_dung
                WHERE ma_khach_hang='$id'";

    $rs = $db->query($sqlUser);

    if($rs && $rs->num_rows > 0){

        $user = $rs->fetch_assoc();

    }

}

// Cookie
$sql = "SELECT * FROM cookie";
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

           <div class="welcome-text">

<?php

if($user){

    echo "Hi, ".$user['ho_ten'];

}

?>

</div>

        <?php if($user){ ?>

                <button id="myOrdersBtn" class="orders-btn" title="My Orders">📦</button>

<a href="logout.php" class="login-btn">

🚪

</a>

<?php }else{ ?>

<a href="login.php" class="login-btn">

👤

</a>

<?php } ?>
            <div class="cart-container">


                <button id="cartBtn">

                    🛒

                    <span id="cartCount">
                        0
                    </span>

                </button>



                <div id="cartMenu" class="cart-menu">


                    <h2>Giỏ hàng</h2>


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

                <h2>Hãy đăng nhập</h2>
                <p>Để xem giỏ hàng, vui lòng đăng nhập.</p>

             <button onclick="window.location='login.php'">
Login now
</button>

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

            <h1>Our Cookies</h1>

            <a href="topping.php">Our Toppings</a>
            <a href="combobanh.php">Our Combos</a>
            <a href="box.php">Our Box</a>
            <a href="about.php">About Us</a>


        </div>
        <div class="cookie-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <div class="cookie-card">

                    <img src="img/<?php echo $row['hinh_anh']; ?>">

                    <h2>
                        <?php
                        echo str_replace(" (", "<br><span class='en-name'>(", $row['ten_sp']);
                        ?>
                        </span>
                    </h2>

                    <b>
                        <?= formatVnd($row['gia']) ?>
                    </b>

                    <p class="stock-text">
                        Số lượng còn lại: <?php echo (int)$row['so_luong']; ?>
                    </p>

                    <div class="button-group">

                        <button onclick="addCart('cookie', <?= $row['ma_cookie'] ?>)">
                            Thêm vào giỏ hàng
                        </button>

                        <a class="see-details-btn"
                            href="product-details.php?id=<?php echo $row['ma_cookie']; ?>">
                            Xem chi tiết
                        </a>

                    </div>

                </div>

            <?php } ?>

        </div>
    </section>


<script>
const isLoggedIn =
<?= isset($_SESSION['ma_khach_hang']) ? 'true' : 'false'; ?>;
</script>
    <script src="script.js"></script>

<!-- Orders modal and script -->
<div id="ordersModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:9999">
    <div style="background:#fff;max-width:900px;width:95%;margin:auto;border-radius:12px;padding:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <h3>Your Orders</h3>
            <button id="closeOrders" style="padding:6px 10px;border-radius:8px;border:none;cursor:pointer">Close</button>
        </div>
        <div id="ordersList">Loading...</div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){
    if (e.target && e.target.id === 'myOrdersBtn'){
        var modal = document.getElementById('ordersModal');
        modal.style.display = 'flex';
        var list = document.getElementById('ordersList');
        list.innerHTML = 'Loading...';
        fetch('user_orders.php')
            .then(r=>r.json())
            .then(data=>{
                if (!data.success) { list.innerHTML = '<p>Please login to see orders.</p>'; return; }
                if (!data.orders || data.orders.length===0) { list.innerHTML = '<p>No orders found.</p>'; return; }
                var html = '<ul style="list-style:none;padding:0;margin:0;display:grid;grid-template-columns:1fr;gap:10px">';
                data.orders.forEach(function(o){
                    html += '<li style="padding:10px;border-radius:8px;border:1px solid #f0e7de;display:flex;justify-content:space-between;align-items:center">'
                         + '<div><strong>#'+o.ma_don_hang+'</strong><div class="small">'+o.ngay_dat+' — '+(o.tong_tien||0)+' VND</div></div>'
                         + '<div style="display:flex;gap:8px;align-items:center">'
                         + '<span class="small">'+o.status_label+'</span>'
                         + '<button class="view-progress-btn" data-id="'+o.ma_don_hang+'" style="padding:6px 8px;background:#6b4226;color:#fff;border-radius:8px;border:none;cursor:pointer">View progress</button>'
                         + '</div></li>';
                });
                html += '</ul>';
                list.innerHTML = html;
            }).catch(()=>{ list.innerHTML = '<p>Error loading orders.</p>'; });
    }
    if (e.target && e.target.id === 'closeOrders'){
        document.getElementById('ordersModal').style.display='none';
    }
    // open order progress inside modal
    if (e.target && e.target.matches('.view-progress-btn')){
        var id = e.target.getAttribute('data-id');
        // navigate in-place to the order progress page (same tab / same site)
        window.location.href = 'order-success.php?order_id=' + encodeURIComponent(id);
    }
    if (e.target && e.target.id === 'backToOrders'){
        // re-trigger the orders button to reload list
        document.getElementById('myOrdersBtn').click();
    }
    if (e.target && e.target.id === 'closeFrame'){
        document.getElementById('ordersModal').style.display='none';
    }
});
</script>


</body>

</html>