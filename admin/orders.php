<?php
session_start();
include_once('../database/db_connection.php');

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = '';
$status_options = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
$payment_options = ['pending', 'paid', 'failed', 'refunded'];

if (isset($_POST['update_order'])) {
    $order_id = (int) $_POST['order_id'];
    $status = in_array($_POST['status'], $status_options) ? $_POST['status'] : 'pending';
    $payment_status = in_array($_POST['payment_status'], $payment_options) ? $_POST['payment_status'] : 'pending';

    $update_query = "UPDATE orders SET status = '$status', payment_status = '$payment_status' WHERE id = $order_id";
    if (mysqli_query($con, $update_query)) {
        $message = "Order updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update order. Please try again.";
        $message_type = "error";
    }
}

$orders = [];
$stats = [];
$orders_table_exists = mysqli_query($con, "SHOW TABLES LIKE 'orders'");

if ($orders_table_exists && mysqli_num_rows($orders_table_exists) > 0) {
    $stats_query = mysqli_query($con, "
        SELECT status, COUNT(*) as total
        FROM orders
        GROUP BY status
    ");
    if ($stats_query) {
        while ($row = mysqli_fetch_assoc($stats_query)) {
            $stats[$row['status']] = $row['total'];
        }
    }

    $orders_query = mysqli_query($con, "SELECT * FROM orders ORDER BY created_at DESC");
    if ($orders_query) {
        while ($order = mysqli_fetch_assoc($orders_query)) {
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

ob_start();
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f3f4f6;
    }

    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    }

    .admin-header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .admin-header-actions a {
        color: #ffffff;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: background 0.3s;
        font-size: 0.9rem;
    }

    .admin-header-actions a:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .admin-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
    }

    .admin-sidebar {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .admin-sidebar h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .admin-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .admin-menu li {
        margin-bottom: 0.5rem;
    }

    .admin-menu a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .admin-menu a:hover,
    .admin-menu a.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
    }

    .admin-main {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    .order-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .order-info h4 {
        margin: 0;
        font-size: 1.1rem;
        color: #111827;
    }

    .order-info span {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .badge {
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-processing { background: #e0f2fe; color: #075985; }
    .badge-shipped { background: #ede9fe; color: #5b21b6; }
    .badge-delivered { background: #dcfce7; color: #166534; }
    .badge-cancelled { background: #fee2e2; color: #b91c1c; }

    .order-body {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.25rem;
    }

    .items-list {
        border-right: 1px dashed #e5e7eb;
        padding-right: 1rem;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }

    .item-row strong {
        color: #111827;
    }

    .order-details ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-details li {
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        color: #374151;
    }

    .order-details span {
        display: block;
        color: #6b7280;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .order-footer {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px dashed #e5e7eb;
    }

    .order-footer form {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .order-footer select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.45rem 0.75rem;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: #b8735c;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.25rem;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: #9a5b45;
    }

    @media (max-width: 992px) {
        .admin-container {
            grid-template-columns: 1fr;
        }

        .items-list {
            border-right: none;
            border-bottom: 1px dashed #e5e7eb;
            padding-right: 0;
            padding-bottom: 1rem;
        }

        .order-body {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <h1><i class="ri-file-list-line"></i> Orders</h1>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="../index.php" target="_blank"><i class="ri-external-link-line"></i> View Website</a>
            <a href="../logout.php"><i class="ri-logout-box-line"></i> Logout</a>
        </div>
    </div>
</div>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Navigation</h3>
        <ul class="admin-menu">
            <li><a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
            <li><a href="categories.php"><i class="ri-folder-line"></i> Categories</a></li>
            <li><a href="products.php"><i class="ri-shopping-bag-line"></i> Products</a></li>
            <li><a href="users.php"><i class="ri-user-line"></i> Users</a></li>
            <li><a href="orders.php" class="active"><i class="ri-file-list-line"></i> Orders</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <h2 class="page-title">Order Management</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="alert alert-error">
                No orders found yet. Orders will appear here once customers place them.
            </div>
        <?php else: ?>
            <div class="stats-grid">
                <?php foreach ($status_options as $status): ?>
                    <div class="stat-card">
                        <div class="stat-label"><?php echo ucfirst($status); ?></div>
                        <div class="stat-value"><?php echo $stats[$status] ?? 0; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <div class="order-info">
                            <h4>Order #<?php echo htmlspecialchars($order['order_number']); ?></h4>
                            <span>Placed on <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="badges">
                            <span class="badge badge-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span>
                            <span class="badge" style="background:#e0e7ff;color:#3730a3;">Payment: <?php echo ucfirst($order['payment_status']); ?></span>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="items-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="item-row">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        <div style="color:#6b7280;font-size:0.85rem;">Qty: <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                            <div style="margin-top:1rem;font-weight:700;">Total: $<?php echo number_format($order['total_amount'], 2); ?></div>
                        </div>

                        <div class="order-details">
                            <ul>
                                <li>
                                    <span>Customer</span>
                                    <?php echo htmlspecialchars($order['full_name']); ?><br>
                                    <?php echo htmlspecialchars($order['email']); ?><br>
                                    <?php echo htmlspecialchars($order['phone']); ?>
                                </li>
                                <li>
                                    <span>Shipping</span>
                                    <?php echo htmlspecialchars($order['address_line1']); ?>
                                    <?php if (!empty($order['address_line2'])): ?>
                                        , <?php echo htmlspecialchars($order['address_line2']); ?>
                                    <?php endif; ?>
                                    <br>
                                    <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['postal_code']); ?>
                                </li>
                                <li>
                                    <span>Payment Method</span>
                                    <?php echo strtoupper($order['payment_method']); ?>
                                    <?php if ($order['payment_method'] === 'upi' && !empty($order['upi_reference'])): ?>
                                        <br><small>UPI Ref: <?php echo htmlspecialchars($order['upi_reference']); ?></small>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="order-footer">
                        <form method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" required>
                                <?php foreach ($status_options as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="payment_status" required>
                                <?php foreach ($payment_options as $payment): ?>
                                    <option value="<?php echo $payment; ?>" <?php echo $order['payment_status'] === $payment ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($payment); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_order" class="btn-primary">
                                <i class="ri-save-3-line"></i> Update
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>
