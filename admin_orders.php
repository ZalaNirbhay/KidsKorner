<?php
session_start();
include_once('database/db_connection.php');

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$message_type = '';

$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$valid_payment_statuses = ['pending', 'paid', 'refunded'];

if (isset($_POST['update_order_status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    if (in_array($status, $valid_statuses) && in_array($payment_status, $valid_payment_statuses)) {
        $status_safe = mysqli_real_escape_string($con, $status);
        $payment_safe = mysqli_real_escape_string($con, $payment_status);
        if (mysqli_query($con, "UPDATE orders SET status = '{$status_safe}', payment_status = '{$payment_safe}' WHERE id = {$order_id}")) {
            $message = "Order #{$order_id} updated successfully.";
            $message_type = "success";
        } else {
            $message = "Unable to update order. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "Invalid status supplied.";
        $message_type = "error";
    }
}

$status_counts = [
    'pending' => 0,
    'processing' => 0,
    'shipped' => 0,
    'delivered' => 0,
    'cancelled' => 0
];
$totals_query = mysqli_query($con, "SELECT status, COUNT(*) as total FROM orders GROUP BY status");
if ($totals_query) {
    while ($row = mysqli_fetch_assoc($totals_query)) {
        $status_counts[$row['status']] = $row['total'];
    }
}

$orders_query = "
    SELECT o.*, r.fullname AS customer_name, r.email AS customer_email
    FROM orders o
    LEFT JOIN registration r ON o.user_id = r.id
    ORDER BY o.created_at DESC
";
$orders_result = mysqli_query($con, $orders_query);
$orders = [];
$order_ids = [];
if ($orders_result) {
    while ($order = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $order;
        $order_ids[] = $order['id'];
    }
}

$order_items = [];
if (!empty($order_ids)) {
    $ids = implode(',', array_map('intval', $order_ids));
    $items_query = mysqli_query($con, "SELECT * FROM order_items WHERE order_id IN ({$ids})");
    if ($items_query) {
        while ($item = mysqli_fetch_assoc($items_query)) {
            $order_items[$item['order_id']][] = $item;
        }
    }
}

ob_start();
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f3f4f6;
    }

    .admin-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
        color: #ffffff;
        padding: 1.5rem 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .admin-header-content {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .admin-header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .admin-header-actions a {
        color: #ffffff;
        text-decoration: none;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.4);
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .admin-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1);
    }

    .stat-card span {
        display: block;
        color: #6b7280;
        font-size: 0.85rem;
    }

    .stat-card strong {
        font-size: 1.75rem;
        color: #111827;
    }

    .orders-wrapper {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.5rem;
    }

    .admin-sidebar {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        height: fit-content;
        position: sticky;
        top: 1.5rem;
    }

    .admin-sidebar h3 {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #111827;
    }

    .admin-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .admin-menu a {
        text-decoration: none;
        color: #4b5563;
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .admin-menu a.active,
    .admin-menu a:hover {
        background: #eef2ff;
        color: #3730a3;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .order-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .order-card header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .order-card h4 {
        margin: 0;
        font-size: 1.2rem;
        color: #111827;
    }

    .order-meta {
        color: #6b7280;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge.success { background: #dcfce7; color: #15803d; }
    .badge.info { background: #dbeafe; color: #1d4ed8; }
    .badge.warning { background: #fef3c7; color: #92400e; }
    .badge.danger { background: #fee2e2; color: #b91c1c; }
    .badge.neutral { background: #e5e7eb; color: #374151; }

    .order-items {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .order-items ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .order-items li {
        display: flex;
        justify-content: space-between;
        color: #374151;
        font-size: 0.95rem;
    }

    .order-actions {
        margin-top: 1.25rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .order-actions form {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    select {
        padding: 0.5rem 0.75rem;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: #4f46e5;
        color: #ffffff;
        border: none;
        border-radius: 999px;
        padding: 0.55rem 1.25rem;
        font-weight: 600;
        cursor: pointer;
    }

    .alert {
        padding: 0.9rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert.success { background: #dcfce7; color: #065f46; }
    .alert.error { background: #fee2e2; color: #b91c1c; }

    @media (max-width: 960px) {
        .orders-wrapper {
            grid-template-columns: 1fr;
        }

        .admin-sidebar {
            position: relative;
            top: 0;
        }
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <div>
            <h1><i class="ri-file-list-line"></i> Manage Orders</h1>
            <p style="opacity:0.85;">Monitor customer purchases and keep fulfillment status up to date.</p>
        </div>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="index.php" target="_blank"><i class="ri-external-link-line"></i> View Website</a>
            <a href="logout.php"><i class="ri-logout-circle-r-line"></i> Logout</a>
        </div>
    </div>
</div>

<div class="admin-container">
    <?php if ($message): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <?php foreach ($status_counts as $status => $count): ?>
            <div class="stat-card">
                <span><?php echo ucfirst($status); ?></span>
                <strong><?php echo $count; ?></strong>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="orders-wrapper">
        <aside class="admin-sidebar">
            <h3>Navigation</h3>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="admin_categories.php"><i class="ri-folder-line"></i> Categories</a></li>
                <li><a href="admin_products.php"><i class="ri-shopping-bag-line"></i> Products</a></li>
                <li><a href="admin_users.php"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="admin_orders.php" class="active"><i class="ri-file-list-line"></i> Orders</a></li>
            </ul>
        </aside>

        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <div class="order-card" style="text-align:center;">
                    <h4>No orders yet</h4>
                    <p style="color:#6b7280;">New purchases will appear here as soon as customers place them.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status_badge = 'neutral';
                    if ($order['status'] === 'delivered') $status_badge = 'success';
                    elseif ($order['status'] === 'shipped') $status_badge = 'info';
                    elseif ($order['status'] === 'processing') $status_badge = 'warning';
                    elseif ($order['status'] === 'cancelled') $status_badge = 'danger';
                    ?>
                    <div class="order-card">
                        <header>
                            <div>
                                <h4>#<?php echo htmlspecialchars($order['order_number']); ?></h4>
                                <span class="order-meta">
                                    Placed on <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?> ·
                                    <?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?> ·
                                    <?php echo htmlspecialchars($order['customer_email'] ?? ''); ?>
                                </span>
                            </div>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <span class="badge <?php echo $status_badge; ?>">
                                    <i class="ri-flag-line"></i> <?php echo ucfirst($order['status']); ?>
                                </span>
                                <span class="badge <?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    <i class="ri-bank-card-line"></i> <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                                <span class="badge neutral">
                                    <i class="ri-wallet-3-line"></i> <?php echo strtoupper($order['payment_method']); ?>
                                </span>
                            </div>
                        </header>

                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">
                            <div>
                                <strong>Shipping</strong>
                                <p style="color:#4b5563; margin:0.4rem 0;">
                                    <?php echo htmlspecialchars($order['full_name']); ?><br>
                                    <?php echo htmlspecialchars($order['address_line1']); ?><br>
                                    <?php if (!empty($order['address_line2'])): ?>
                                        <?php echo htmlspecialchars($order['address_line2']); ?><br>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' ' . $order['postal_code']); ?><br>
                                    Phone: <?php echo htmlspecialchars($order['phone']); ?>
                                </p>
                            </div>
                            <div>
                                <strong>Summary</strong>
                                <div class="order-meta">
                                    Subtotal: $<?php echo number_format($order['subtotal'], 2); ?><br>
                                    Shipping: $<?php echo number_format($order['shipping_amount'], 2); ?><br>
                                    <strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                            </div>
                            <?php if ($order['payment_method'] === 'upi' && !empty($order['upi_reference'])): ?>
                                <div>
                                    <strong>UPI Reference</strong>
                                    <p style="color:#4b5563;"><?php echo htmlspecialchars($order['upi_reference']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="order-items">
                            <strong>Items</strong>
                            <ul>
                                <?php foreach ($order_items[$order['id']] ?? [] as $item): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                                        <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="order-actions">
                            <form method="post">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" required>
                                    <?php foreach ($valid_statuses as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="payment_status" required>
                                    <?php foreach ($valid_payment_statuses as $p_status): ?>
                                        <option value="<?php echo $p_status; ?>" <?php echo $order['payment_status'] === $p_status ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($p_status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_order_status" class="btn-primary">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>

