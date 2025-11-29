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

// Fetch orders
$orders_query = mysqli_query($con, "SELECT * FROM orders WHERE user_id = {$user_id} ORDER BY created_at DESC");
$orders = [];
$order_ids = [];

if ($orders_query) {
    while ($order = mysqli_fetch_assoc($orders_query)) {
        $orders[] = $order;
        $order_ids[] = $order['id'];
    }
}

// Fetch order items
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

// Fetch existing reviews by this user
$reviews_query = mysqli_query($con, "SELECT product_id, order_id FROM reviews WHERE user_id = {$user_id}");
$user_reviews = [];
if ($reviews_query) {
    while ($review = mysqli_fetch_assoc($reviews_query)) {
        $user_reviews[$review['order_id']][$review['product_id']] = true;
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
        align-items: center;
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

    .btn-review {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-review:hover {
        background: #2563eb;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        position: relative;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .close-modal {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 2rem;
        color: #d1d5db;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #fbbf24;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #374151;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-family: inherit;
    }

    .btn-submit-review {
        width: 100%;
        background: #b8735c;
        color: white;
        border: none;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
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

        <?php if (isset($_SESSION['message'])): ?>
            <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background: <?php echo $_SESSION['message_type'] == 'success' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $_SESSION['message_type'] == 'success' ? '#15803d' : '#b91c1c'; ?>;">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

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
                                        <div>
                                            <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                                            <?php 
                                            $is_delivered = ($status === 'delivered');
                                            $has_reviewed = isset($user_reviews[$order['id']][$item['product_id']]);
                                            
                                            if ($is_delivered && !$has_reviewed): 
                                            ?>
                                                <button class="btn-review" onclick="openReviewModal(<?php echo $order['id']; ?>, <?php echo $item['product_id']; ?>, '<?php echo addslashes($item['product_name']); ?>')">
                                                    Write Review
                                                </button>
                                            <?php elseif ($has_reviewed): ?>
                                                <span style="font-size: 0.8rem; color: #10b981; margin-left: 0.5rem;">
                                                    <i class="ri-check-line"></i> Reviewed
                                                </span>
                                            <?php endif; ?>
                                        </div>
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

<!-- Review Modal -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeReviewModal()">&times;</span>
        <h2 style="margin-bottom: 1rem; color: #1f2937;">Write a Review</h2>
        <p id="modalProductName" style="color: #6b7280; margin-bottom: 1.5rem;"></p>
        
        <form action="submit_review.php" method="POST">
            <input type="hidden" name="order_id" id="modalOrderId">
            <input type="hidden" name="product_id" id="modalProductId">
            
            <div class="form-group">
                <label>Rating</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 stars">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star">&#9733;</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="comment">Your Review</label>
                <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Share your experience with this product..." required></textarea>
            </div>
            
            <button type="submit" class="btn-submit-review">Submit Review</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('reviewModal');
    const modalProductName = document.getElementById('modalProductName');
    const modalOrderId = document.getElementById('modalOrderId');
    const modalProductId = document.getElementById('modalProductId');

    function openReviewModal(orderId, productId, productName) {
        modal.style.display = 'flex';
        modalProductName.textContent = productName;
        modalOrderId.value = orderId;
        modalProductId.value = productId;
    }

    function closeReviewModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeReviewModal();
        }
    }
</script>

<?php
$content = ob_get_clean();
include_once('layout.php');
?>
