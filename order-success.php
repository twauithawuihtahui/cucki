<?php
session_start();
include 'config.php';

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : ($_SESSION['last_order_id'] ?? 0);
if ($order_id <= 0) {
    header('Location: checkout.php');
    exit();
}

$orderResult = $db->query("SELECT * FROM don_hang WHERE ma_don_hang='$order_id'");
if (!$orderResult || $orderResult->num_rows === 0) {
    header('Location: checkout.php');
    exit();
}
$orderRow = $orderResult->fetch_assoc();
$statusMap = [1 => 'accepting order', 2 => 'on delivery', 3 => 'successfully delivery'];
$order_status = $statusMap[(int) $orderRow['trangthai']] ?? 'accepting order';

// Build customer info: prefer session (fresh checkout), otherwise fetch from order's user or use defaults
$customer = ['customer_name' => 'Customer', 'email' => '', 'phone' => '', 'address' => '', 'payment_method' => 'Cash on Delivery', 'placed_at' => $orderRow['ngay_dat']];
if (!empty($_SESSION['last_order_customer'])) {
    $sess = $_SESSION['last_order_customer'];
    $customer['customer_name'] = $sess['customer_name'] ?? $customer['customer_name'];
    $customer['email'] = $sess['email'] ?? $customer['email'];
    $customer['phone'] = $sess['phone'] ?? $customer['phone'];
    $customer['address'] = $sess['address'] ?? $customer['address'];
    $customer['payment_method'] = $sess['payment_method'] ?? $customer['payment_method'];
}
else {
    // try to read user info from DB if this order is attached to a registered user
    $uid = isset($orderRow['ma_khach_hang']) ? (int)$orderRow['ma_khach_hang'] : 0;
    if ($uid > 0) {
        $uQ = $db->query("SELECT ho_ten, email, so_dien_thoai, dia_chi FROM nguoi_dung WHERE ma_khach_hang='".$uid."'");
        if ($uQ && $uQ->num_rows>0) {
            $u = $uQ->fetch_assoc();
            $customer['customer_name'] = $u['ho_ten'] ?: $customer['customer_name'];
            $customer['email'] = $u['email'] ?: $customer['email'];
            $customer['phone'] = $u['so_dien_thoai'] ?: $customer['phone'];
            $customer['address'] = $u['dia_chi'] ?: $customer['address'];
        }
    }
    // use any payment method stored on the order row if present
    if (!empty($orderRow['phuong_thuc'])) {
        $customer['payment_method'] = $orderRow['phuong_thuc'];
    } elseif (!empty($orderRow['payment_method'])) {
        $customer['payment_method'] = $orderRow['payment_method'];
    }
}

$hasToppingColumn = false;
$colResult = $db->query("SHOW COLUMNS FROM chi_tiet_don_hang LIKE 'ma_topping'");
if ($colResult && $colResult->num_rows > 0) {
    $hasToppingColumn = true;
}

$itemQuery = "
SELECT
    CASE
        WHEN ct.ma_cookie > 0 THEN c.ten_sp
        WHEN ct.ma_combo > 0 THEN cb.ten_combo
        WHEN ct.ma_hop > 0 THEN CONCAT('Box: ', hb.mau_hop)
        WHEN ct.ma_topping > 0 THEN tt.ten_topping
        ELSE 'Order item'
    END AS name,
    CASE
        WHEN ct.ma_cookie > 0 THEN 'Cookie'
        WHEN ct.ma_combo > 0 THEN 'Combo'
        WHEN ct.ma_hop > 0 THEN 'Box'
        WHEN ct.ma_topping > 0 THEN 'Topping'
        ELSE 'Item'
    END AS type,
    ct.so_luong AS quantity,
    ct.don_gia AS don_gia,
    ct.thanh_tien AS thanh_tien
FROM chi_tiet_don_hang ct
LEFT JOIN cookie c ON ct.ma_cookie = c.ma_cookie
LEFT JOIN combo_banh cb ON ct.ma_combo = cb.ma_combo
LEFT JOIN hop_banh hb ON ct.ma_hop = hb.ma_hop
";

if ($hasToppingColumn) {
    $itemQuery .= "LEFT JOIN topping_banh_them tt ON ct.ma_topping = tt.ma_topping\n";
} else {
    $itemQuery .= "LEFT JOIN topping_banh_them tt ON 1=0\n";
}

$itemQuery .= "WHERE ct.ma_don_hang = '$order_id'\n";

$itemResult = $db->query($itemQuery);
$orderItems = [];
if ($itemResult) {
    while ($row = $itemResult->fetch_assoc()) {
        $orderItems[] = $row;
    }
}

function html_escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">🍪 Sweet Crumbs</div>
        <a class="back-btn" href="index.php">← Continue Shopping</a>
    </header>
    <section class="order-success-page">
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you, <?= html_escape($customer['customer_name']) ?>. Your order is now confirmed and is on its way.</p>
            <div class="order-status-bar">
                <?php
                $steps = [
                    'accepting order' => 'Accepting order',
                    'on delivery' => 'On delivery',
                    'successfully delivery' => 'Successfully delivered',
                ];
                foreach ($steps as $key => $label):
                    $completed = array_search($key, array_keys($steps)) < array_search($order_status, array_keys($steps));
                    $stepClass = $order_status === $key ? 'current' : ($completed ? 'completed' : 'upcoming');
                ?>
                    <div class="status-step <?= $stepClass ?>" data-step="<?= html_escape($key) ?>">
                        <div class="status-dot"></div>
                        <div class="status-label"><?= $label ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="status-description" class="status-description"></div>
            <div class="order-meta">
                <div>
                    <strong>Order number</strong>
                    <p><?= html_escape($orderRow['ma_don_hang']) ?></p>
                </div>
                <div>
                    <strong>Placed at</strong>
                    <p><?= html_escape($customer['placed_at'] ?? $orderRow['ngay_dat']) ?></p>
                </div>
                <div>
                    <strong>Payment</strong>
                    <p><?= html_escape($customer['payment_method']) ?></p>
                </div>
            </div>
            <div class="details-grid">
                <div class="customer-details">
                    <h2>Customer Details</h2>
                    <p><strong>Name:</strong> <?= html_escape($customer['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= html_escape($customer['email']) ?></p>
                    <p><strong>Phone:</strong> <?= html_escape($customer['phone']) ?></p>
                    <p><strong>Address:</strong> <?= nl2br(html_escape($customer['address'])) ?></p>
                </div>
                <div class="order-details-box">
                    <h2>Order Summary</h2>
                    <div class="order-items">
                        <?php foreach ($orderItems as $item): ?>
                            <div class="order-item-row">
                                <div class="order-item-name"><?= html_escape($item['name']) ?></div>
                                <div class="order-item-meta">
                                    <span>Qty: <?= (int) $item['quantity'] ?></span>
                                    <span>$<?= number_format((float) $item['thanh_tien'], 2) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-total-row">
                        <strong>Total paid</strong>
                        <strong>$<?= number_format((float) $orderRow['tong_tien'], 2) ?></strong>
                    </div>
                </div>
            </div>
            <div class="status-note">
                Your order is being prepared and will soon be on its way. You can return to the shop at any time to place another order.
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var steps = Array.from(document.querySelectorAll('.status-step'));
            var description = document.getElementById('status-description');
            var statusOrder = ['accepting order', 'on delivery', 'successfully delivery'];
            var current = '<?= addslashes($order_status) ?>';
            var currentIndex = statusOrder.indexOf(current);
            var orderId = <?= (int) $order_id ?>;

            function updateDescription(status) {
                var messages = {
                    'accepting order': 'We are accepting your order and confirming the payment information.',
                    'on delivery': 'Your order is on delivery and heading to your address.',
                    'successfully delivery': 'Your order has been successfully delivered. Enjoy your treats!'
                };
                description.textContent = messages[status] || '';
            }

            function activateStep(index) {
                steps.forEach(function(step, stepIndex) {
                    step.classList.remove('completed', 'current', 'upcoming');
                    if (stepIndex < index) {
                        step.classList.add('completed');
                    } else if (stepIndex === index) {
                        step.classList.add('current');
                    } else {
                        step.classList.add('upcoming');
                    }
                });
                updateDescription(statusOrder[index]);
            }

            function pollStatus() {
                fetch('order-status.php?order_id=' + orderId)
                    .then(function(response){ return response.json(); })
                    .then(function(data){
                        if (!data.success) return;
                        var idx = statusOrder.indexOf(data.status_label);
                        if (idx >= 0) activateStep(idx);
                        if (idx < statusOrder.length - 1) {
                            // poll again after 5s
                            setTimeout(pollStatus, 5000);
                        }
                    })
                    .catch(function(){ /* ignore */ });
            }

            if (currentIndex === -1) currentIndex = 0;
            activateStep(currentIndex);
            if (currentIndex < statusOrder.length - 1) pollStatus();
        });
    </script>
</body>
</html>
