<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include_once('database/db_connection.php');

$user_id = (int) $_SESSION['user_id'];
$orders = [];
$orders_table_exists = mysqli_query($con, "SHOW TABLES LIKE 'orders'");

if ($orders_table_exists && mysqli_num_rows($orders_table_exists) > 0) {
    $orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
    $orders_result = mysqli_query($con, $orders_query);
    if ($orders_result) {
        while ($order = mysqli_fetch_assoc($orders_result)) {
            $items_query = mysqli_query($con, "SELECT * FROM order_items WHERE order_id = {$order['id']}");
            $items = [];
            if ($items_query) {
                while ($item = mysqli_fetch_assoc($items_query)) {
                    $items[] = $item;
                }
            }
            $order['items'] = $items;
            $orders[] = $order;
        }
    }
}

$status_steps = ['pending', 'processing', 'shipped', 'delivered'];

function kk_status_badge($status)
{
    $status = strtolower($status);
    $map = [
        'pending' => ['Waiting', '#fbbf24', '#92400e'],
        'processing' => ['Processing', '#bfdbfe', '#1d4ed8'],
        'shipped' => ['Shipped', '#c7d2fe', '#4338ca'],
        'delivered' => ['Delivered', '#bbf7d0', '#15803d'],
        'cancelled' => ['Cancelled', '#fecaca', '#b91c1c'],
    ];

    if (!isset($map[$status])) {
        return ['label' => ucfirst($status), 'bg' => '#e5e7eb', 'color' => '#374151'];
    }

    return ['label' => $map[$status][0], 'bg' => $map[$status][1], 'color' => $map[$status][2]];
}

ob_start();
?>

<style>
    .orders-page {
        padding: 4rem 0;
        background: #f9fafb;
    }

    .orders-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .orders-header {
        margin-bottom: 2rem;
    }

    .orders-title {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .orders-subtitle {
        color: #6b7280;
    }

    .order-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.75rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e5e7eb;
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .order-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    .order-meta {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .status-badges {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .status-pill {
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .order-timeline {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .timeline-step {
        text-align: center;
        position: relative;
    }

    .timeline-step::before {
        content: "";
        position: absolute;
        top: 16px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }

    .timeline-step:last-child::before {
        display: none;
    }

    .timeline-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin: 0 auto 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        position: relative;
    }

    .timeline-icon.complete {
        background: #bbf7d0;
        color: #15803d;
    }

    .timeline-icon.active {
        background: #bfdbfe;
        color: #1d4ed8;
    }

    .timeline-icon.pending {
        background: #e5e7eb;
        color: #6b7280;
    }

    .timeline-label {
        font-size: 0.85rem;
        color: #374151;
        font-weight: 600;
    }

    .order-body {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .items-card,
    .details-card {
        background: #f9fafb;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
    }

    .items-card h4,
    .details-card h4 {
        margin-bottom: 1rem;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px dashed #e5e7eb;
    }

    .item-name {
        font-weight: 600;
        color: #1f2937;
    }

    .item-meta {
        color: #6b7280;
        font-size: 0.85rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        font-size: 1.1rem;
        margin-top: 1rem;
    }

    .details-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details-list li {
        margin-bottom: 0.85rem;
        color: #374151;
        font-size: 0.95rem;
    }

    .details-list span {
        display: block;
        color: #6b7280;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.15rem;
    }

    .empty-state {
        background: #ffffff;
        border-radius: 18px;
        padding: 3rem 2rem;
        text-align: center;
        border: 1px dashed #d1d5db;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        margin-bottom: 0.5rem;
        color: #111827;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background: #b8735c;
        color: #ffffff;
        padding: 0.85rem 1.5rem;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        background: #9a5b45;
    }

    @media (max-width: 768px) {
        .order-body {
            grid-template-columns: 1fr;
        }

        .order-timeline {
            grid-template-columns: repeat(2, 1fr);
            row-gap: 1.25rem;
        }
    }
</style>

<section class="orders-page">
    <div class="orders-container">
        <div class="orders-header">
            <h1 class="orders-title">My Orders</h1>
            <p class="orders-subtitle">Track every purchase and delivery update in one place.</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="ri-shopping-bag-3-line"></i>
                <h3>No orders yet</h3>
                <p>Your future purchases will appear here. Start exploring our collections!</p>
                <a href="index.php" class="btn-primary"><i class="ri-store-2-line"></i> Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php $badge = kk_status_badge($order['status']); ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <div>
                            <div class="order-number">Order #<?php echo htmlspecialchars($order['order_number']); ?></div>
                            <div class="order-meta">Placed on <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></div>
                        </div>
                        <div class="status-badges">
                            <span class="status-pill" style="background: <?php echo $badge['bg']; ?>; color: <?php echo $badge['color']; ?>;">
                                <i class="ri-checkbox-circle-line"></i> <?php echo $badge['label']; ?>
                            </span>
                            <span class="status-pill" style="background: #e0e7ff; color: #4338ca;">
                                Payment: <?php echo ucfirst($order['payment_status'] ?: 'pending'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-timeline">
                        <?php foreach ($status_steps as $index => $step): ?>
                            <?php
                                $stepState = 'pending';
                                $current_index = array_search(strtolower($order['status']), $status_steps);
                                if ($current_index === false) {
                                    $current_index = 0;
                                }
                                if ($index < $current_index) {
                                    $stepState = 'complete';
                                } elseif ($index == $current_index) {
                                    $stepState = 'active';
                                }
                            ?>
                            <div class="timeline-step">
                                <div class="timeline-icon <?php echo $stepState; ?>">
                                    <i class="ri-check-line"></i>
                                </div>
                                <div class="timeline-label"><?php echo ucfirst($step); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-body">
                        <div class="items-card">
                            <h4>Items</h4>
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="item-row">
                                    <div>
                                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div class="item-meta">Qty: <?php echo $item['quantity']; ?> • $<?php echo number_format($item['price'], 2); ?></div>
                                    </div>
                                    <div class="item-price">
                                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="total-row">
                                <span>Total</span>
                                <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>

                        <div class="details-card">
                            <h4>Delivery & Payment</h4>
                            <ul class="details-list">
                                <li>
                                    <span>Payment Method</span>
                                    <?php echo strtoupper($order['payment_method']); ?>
                                    <?php if ($order['payment_method'] === 'upi' && !empty($order['upi_reference'])): ?>
                                        <br><small style="color:#6b7280;">UPI Ref: <?php echo htmlspecialchars($order['upi_reference']); ?></small>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <span>Ship To</span>
                                    <?php echo htmlspecialchars($order['full_name']); ?><br>
                                    <?php echo htmlspecialchars($order['address_line1']); ?>
                                    <?php if (!empty($order['address_line2'])): ?>
                                        , <?php echo htmlspecialchars($order['address_line2']); ?>
                                    <?php endif; ?>
                                    <br>
                                    <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['postal_code']); ?>
                                    <br>
                                    Phone: <?php echo htmlspecialchars($order['phone']); ?>
                                </li>
                                <li>
                                    <span>Subtotal</span>
                                    $<?php echo number_format($order['subtotal'], 2); ?>
                                </li>
                                <li>
                                    <span>Shipping</span>
                                    $<?php echo number_format($order['shipping_amount'], 2); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
include_once('layout.php');
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include_once('database/db_connection.php');

$user_id = (int) $_SESSION['user_id'];

$orders_query = mysqli_query($con, "SELECT * FROM orders WHERE user_id = {$user_id} ORDER BY created_at DESC");
$orders = [];
$order_ids = [];

if ($orders_query) {
    while ($order = mysqli_fetch_assoc($orders_query)) {
        $orders[] = $order;
        $order_ids[] = $order['id'];
    }
}

$items_by_order = [];
if (!empty($order_ids)) {
    $ids = implode(',', array_map('intval', $order_ids));
    $items_result = mysqli_query($con, "SELECT * FROM order_items WHERE order_id IN ({$ids})");
    if ($items_result) {
        while ($item = mysqli_fetch_assoc($items_result)) {
            $items_by_order[$item['order_id']][] = $item;
        }
    }
}

$status_steps = [
    'pending' => 'Order Placed',
    'processing' => 'Processing',
    'shipped' => 'Shipped',
    'delivered' => 'Delivered'
];

function kk_status_badge_class($status)
{
    switch ($status) {
        case 'delivered':
            return 'success';
        case 'shipped':
            return 'info';
        case 'processing':
            return 'warning';
        case 'cancelled':
            return 'danger';
        default:
            return 'muted';
    }
}

ob_start();
?>

<style>
    .orders-page {
        background: #f3f4f6;
        padding: 4rem 0;
    }

    .orders-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .page-headline {
        margin-bottom: 2rem;
    }

    .page-headline h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .page-headline p {
        color: #6b7280;
    }

    .order-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
    }

    .order-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .order-meta h3 {
        font-size: 1.25rem;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .order-meta span {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.4rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge.success { background: #dcfce7; color: #15803d; }
    .badge.info { background: #dbeafe; color: #1d4ed8; }
    .badge.warning { background: #fef3c7; color: #92400e; }
    .badge.danger { background: #fee2e2; color: #b91c1c; }
    .badge.muted { background: #e5e7eb; color: #374151; }

    .status-tracker {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .status-step {
        background: #f9fafb;
        padding: 0.75rem;
        border-radius: 12px;
        text-align: center;
        font-size: 0.85rem;
        color: #6b7280;
        border: 1px dashed #e5e7eb;
    }

    .status-step.active {
        background: #ecfccb;
        color: #4d7c0f;
        border-color: #84cc16;
        font-weight: 600;
    }

    .order-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .order-section h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.75rem;
    }

    .order-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.95rem;
        color: #374151;
    }

    .order-summary-row {
        display: flex;
        justify-content: space-between;
        color: #4b5563;
        margin-bottom: 0.35rem;
    }

    .order-summary-row.total {
        font-weight: 700;
        color: #111827;
        margin-top: 0.35rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(107, 114, 128, 0.15);
    }

    .empty-state i {
        font-size: 3rem;
        color: #c7d2fe;
        display: block;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .btn-return {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #b8735c;
        color: #ffffff;
        border: none;
        border-radius: 999px;
        padding: 0.85rem 1.5rem;
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 640px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .status-tracker {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<section class="orders-page">
    <div class="orders-container">
        <div class="page-headline">
            <h1>Order History</h1>
            <p>Track the progress of your recent purchases.</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="ri-archive-line"></i>
                <h3>No orders yet</h3>
                <p>When you place an order, you’ll see it listed here with live status updates.</p>
                <a href="index.php" class="btn-return"><i class="ri-store-line"></i> Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php
                $status = strtolower($order['status'] ?? 'pending');
                $created_at = date('M d, Y g:i A', strtotime($order['created_at']));
                $items = $items_by_order[$order['id']] ?? [];
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-meta">
                            <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <span>Placed on <?php echo $created_at; ?></span>
                        </div>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <span class="badge <?php echo kk_status_badge_class($status); ?>">
                                <i class="ri-flag-line"></i> <?php echo ucfirst($status); ?>
                            </span>
                            <span class="badge muted">
                                <i class="ri-wallet-3-line"></i> <?php echo htmlspecialchars(strtoupper($order['payment_method'])); ?>
                            </span>
                            <span class="badge <?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <i class="ri-bank-card-line"></i> <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="status-tracker">
                        <?php
                        $step_reached = false;
                        foreach ($status_steps as $step_key => $step_label):
                            if ($status === $step_key) {
                                $step_reached = true;
                            }
                            $active = ($status === $step_key) || !$step_reached;
                        ?>
                            <div class="status-step <?php echo $active ? 'active' : ''; ?>">
                                <?php echo $step_label; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-body">
                        <div class="order-section">
                            <h4>Items</h4>
                            <ul>
                                <?php foreach ($items as $item): ?>
                                    <li class="order-item">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                                        <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="order-section">
                            <h4>Delivery</h4>
                            <p style="color:#4b5563; margin-bottom:0.25rem;"><?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p style="color:#6b7280; margin-bottom:0.25rem;">
                                <?php echo nl2br(htmlspecialchars($order['address_line1'] .
                                    ($order['address_line2'] ? "\n" . $order['address_line2'] : '') .
                                    "\n" . $order['city'] . ', ' . $order['state'] . ' ' . $order['postal_code'])); ?>
                            </p>
                            <p style="color:#6b7280;">Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                        </div>

                        <div class="order-section">
                            <h4>Summary</h4>
                            <div class="order-summary-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                            </div>
                            <div class="order-summary-row">
                                <span>Shipping</span>
                                <span>$<?php echo number_format($order['shipping_amount'], 2); ?></span>
                            </div>
                            <div class="order-summary-row total">
                                <span>Total</span>
                                <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($order['payment_method'] === 'upi' && !empty($order['upi_reference'])): ?>
                        <div style="margin-top:1rem; color:#6b7280; font-size:0.9rem;">
                            <strong>UPI Reference:</strong> <?php echo htmlspecialchars($order['upi_reference']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
include_once('layout.php');
?>

