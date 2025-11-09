<?php
session_start();
include_once('database/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle update quantity
if (isset($_POST['update_cart'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        // Check product stock
        $cart_item = mysqli_fetch_assoc(mysqli_query($con, "SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = $cart_id AND c.user_id = $user_id"));
        
        if ($cart_item && $quantity <= $cart_item['stock']) {
            mysqli_query($con, "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id");
            $message = "Cart updated successfully!";
            $message_type = "success";
        } else {
            $message = "Quantity exceeds available stock!";
            $message_type = "error";
        }
    }
}

// Handle remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($con, "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
    $message = "Item removed from cart!";
    $message_type = "success";
}

// Get cart items with product details
$cart_query = "SELECT c.*, p.name, p.price, p.image, p.stock, p.description 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id 
               ORDER BY c.created_at DESC";
$cart_result = mysqli_query($con, $cart_query);

// Calculate totals
$subtotal = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $item_total = $item['price'] * $item['quantity'];
    $subtotal += $item_total;
    $cart_items[] = $item;
}

$shipping = 10.00; // Fixed shipping cost
$total = $subtotal + $shipping;

ob_start();
?>

<style>
    .cart-page {
        padding: 4rem 0;
        background: #f9fafb;
        min-height: 70vh;
    }

    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .cart-header {
        margin-bottom: 2rem;
    }

    .cart-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .cart-breadcrumb {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .cart-breadcrumb a {
        color: #b8735c;
        text-decoration: none;
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

    .cart-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .cart-items {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .cart-item {
        display: grid;
        grid-template-columns: 120px 1fr auto;
        gap: 1.5rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        background: #f9fafb;
    }

    .cart-item-details {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cart-item-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .cart-item-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #b8735c;
        margin-bottom: 1rem;
    }

    .cart-item-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-input {
        width: 60px;
        padding: 0.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        text-align: center;
    }

    .btn-update {
        background: #3b82f6;
        color: #ffffff;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-remove {
        background: #dc2626;
        color: #ffffff;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        display: inline-block;
    }

    .btn-remove:hover,
    .btn-update:hover {
        opacity: 0.9;
    }

    .cart-item-total {
        text-align: right;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .item-total-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .cart-summary {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .summary-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        color: #374151;
    }

    .summary-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        padding-top: 1rem;
        border-top: 2px solid #e5e7eb;
        margin-top: 1rem;
    }

    .btn-checkout {
        width: 100%;
        background: #b8735c;
        color: #ffffff;
        padding: 1rem;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1.5rem;
        transition: background 0.3s;
    }

    .btn-checkout:hover {
        background: #9a5b45;
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .empty-cart-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-cart h2 {
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .empty-cart p {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    .btn-continue {
        background: #b8735c;
        color: #ffffff;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .cart-content {
            grid-template-columns: 1fr;
        }

        .cart-item {
            grid-template-columns: 100px 1fr;
        }

        .cart-item-total {
            grid-column: 1 / -1;
            text-align: left;
            margin-top: 1rem;
        }
    }
</style>

<div class="cart-page">
    <div class="cart-container">
        <div class="cart-header">
            <h1 class="cart-title">Shopping Cart</h1>
            <div class="cart-breadcrumb">
                <a href="index.php">Home</a> / Shopping Cart
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (count($cart_items) > 0): ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div>
                                <?php if ($item['image']): ?>
                                    <img src="images/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="cart-item-image">
                                <?php else: ?>
                                    <img src="asetes/images/sitting-baby.png" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="cart-item-image">
                                <?php endif; ?>
                            </div>
                            
                            <div class="cart-item-details">
                                <div>
                                    <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                
                                <div class="cart-item-actions">
                                    <form method="post" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <label>Qty:</label>
                                        <input type="number" name="quantity" class="quantity-input" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock']; ?>" required>
                                        <button type="submit" name="update_cart" class="btn-update">Update</button>
                                    </form>
                                    <a href="?remove=<?php echo $item['id']; ?>" 
                                       class="btn-remove"
                                       onclick="return confirm('Remove this item from cart?');">Remove</a>
                                </div>
                            </div>
                            
                            <div class="cart-item-total">
                                <div class="item-total-price">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h2 class="summary-title">Order Summary</h2>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>$<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <button class="btn-checkout" onclick="alert('Checkout functionality will be implemented soon!');">
                        Proceed to Checkout
                    </button>
                    
                    <a href="index.php" class="btn-continue" style="display: block; text-align: center; margin-top: 1rem;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="ri-shopping-cart-line"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="index.php" class="btn-continue">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once("layout.php");
?>

