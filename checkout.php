<?php
session_start();
include 'config.php';

if (!isset($_SESSION['ma_khach_hang'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

$ma_khach_hang = (int) $_SESSION['ma_khach_hang'];

$user = null;
$res = $db->query("SELECT * FROM nguoi_dung WHERE ma_khach_hang='$ma_khach_hang'");
if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
}

// Load cart items for the user (map DB columns to expected keys)
$items = [];
$sql = "
SELECT
    c.ma_cookie AS id,
    c.ten_sp AS name,
    c.gia AS price,
    c.hinh_anh AS image,
    ct.so_luong AS quantity
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN cookie c ON ct.ma_cookie = c.ma_cookie
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    cb.ma_combo AS id,
    cb.ten_combo AS name,
    cb.gia_combo AS price,
    cb.hinh_anh AS image,
    ct.so_luong AS quantity
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN combo_banh cb ON ct.ma_combo = cb.ma_combo
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    tt.ma_topping AS id,
    tt.ten_topping AS name,
    tt.gia_them AS price,
    tt.hinh_anh AS image,
    ct.so_luong AS quantity
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN topping_banh_them tt ON ct.ma_topping = tt.ma_topping
WHERE gh.ma_khach_hang='$ma_khach_hang'

UNION ALL

SELECT
    hb.ma_hop AS id,
    CONCAT('Box: ', hb.mau_hop) AS name,
    0 AS price,
    hb.hinh_anh AS image,
    ct.so_luong AS quantity
FROM gio_hang gh
JOIN chi_tiet_gio_hang ct ON gh.ma_gio_hang = ct.ma_gio_hang
JOIN hop_banh hb ON ct.ma_hop = hb.ma_hop
WHERE gh.ma_khach_hang='$ma_khach_hang'
";
$rs = $db->query($sql);
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $items[] = $row;
    }
}
$total = 0.0;
foreach ($items as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}

function resolve_image($path) {
    // Default image
    $default = 'img/logo.png';
    if (empty($path)) return $default;
    $path = trim($path);
    // If it's a full URL, return as-is
    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
    // If file exists relative to this script, return it
    $candidate = __DIR__ . '/' . $path;
    if (is_file($candidate)) return $path;
    // Try inside the img/ folder
    $candidate2 = __DIR__ . '/img/' . $path;
    if (is_file($candidate2)) return 'img/' . $path;
    // Fallback
    return $default;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
 <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
            <img src="./img/logo.png" width="180" height="180">

        </div>
        <a class="back-btn" href="index.php">← Back to Main</a>
    </header>
    <section class="checkout">
        <div class="checkout-header">
            <div class="checkout-logo">🍪</div>
            <h1>Checkout</h1>
            <p>Review your order and enter your delivery details to place it now.</p>
        </div>
        <div id="orderList">
            <?php if (empty($items)): ?>
                <p class="empty-order">Your cart is empty.</p>
            <?php else: foreach ($items as $item): ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars(resolve_image($item['image'])) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="order-details">
                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                        <span>Qty: <?= (int) $item['quantity'] ?></span>
                        <span><?= formatVnd((float) $item['price'] * (int) $item['quantity']) ?></span>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="checkout-total">
            <h2>Total: <span id="orderTotal"><?= formatVnd($total) ?></span></h2>
        </div>
        <form id="customerForm" method="POST" action="checkout-process.php">
            <input name="customer_name" placeholder="Full Name" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>
            <input name="email" type="email" placeholder="Email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            <input name="phone" placeholder="Phone Number" required>
            <textarea name="address" placeholder="Address" required></textarea>

            <div class="payment-methods">
                <h3>Payment method</h3>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>Cash on Delivery</span>
                </label>
            </div>

            <button type="submit">Place Order</button>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var paymentOptions = document.querySelectorAll('.payment-option');
                function updateSelected() {
                    paymentOptions.forEach(function(option) {
                        var radio = option.querySelector('input[type="radio"]');
                        if (radio && radio.checked) {
                            option.classList.add('selected');
                        } else {
                            option.classList.remove('selected');
                        }
                    });
                }
                paymentOptions.forEach(function(option) {
                    var radio = option.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.addEventListener('change', updateSelected);
                    }
                });
                updateSelected();
            });
        </script>
        <div id="done">
            <?php if (isset($_GET['success'])): ?>
                ✅ Order placed successfully!
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                ⚠️ There was a problem placing your order.
            <?php endif; ?>
        </div>
    </section>
    <script src="script.js"></script>
</body>
</html>