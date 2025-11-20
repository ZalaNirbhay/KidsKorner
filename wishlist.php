<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('database/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle remove from wishlist
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    
    if (mysqli_query($con, $delete_query)) {
        $message = "Product removed from wishlist!";
        $message_type = "success";
    } else {
        $message = "Error removing product from wishlist";
        $message_type = "error";
    }
}

// Get wishlist items with product details
$wishlist_query = "SELECT w.*, p.name, p.description, p.price, p.current_price, p.original_price, p.discount_percentage, p.image, p.stock, p.id as product_id
                   FROM wishlist w 
                   JOIN products p ON w.product_id = p.id 
                   WHERE w.user_id = $user_id AND p.status = 'active'
                   ORDER BY w.created_at DESC";
$wishlist_result = mysqli_query($con, $wishlist_query);

ob_start();
?>

<style>
    .wishlist-page {
        padding: 4rem 0;
        background: #f9fafb;
        min-height: 80vh;
    }

    .wishlist-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .wishlist-header {
        margin-bottom: 2rem;
    }

    .wishlist-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .wishlist-subtitle {
        color: #6b7280;
        font-size: 1rem;
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

    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }

    .wishlist-card {
        background: #ffffff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }

    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .wishlist-image-wrapper {
        position: relative;
        padding: 2rem;
        background: #f9fafb;
        text-align: center;
    }

    .wishlist-image {
        width: 100%;
        height: 250px;
        object-fit: contain;
    }

    .wishlist-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    .wishlist-action-btn {
        width: 36px;
        height: 36px;
        background: #ffffff;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: background 0.3s;
        color: #dc2626;
        text-decoration: none;
    }

    .wishlist-action-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .wishlist-info {
        padding: 1.5rem;
    }

    .wishlist-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .wishlist-description {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .wishlist-price-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .wishlist-current-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .wishlist-original-price {
        font-size: 1rem;
        color: #9ca3af;
        text-decoration: line-through;
    }

    .wishlist-discount-badge {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .wishlist-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-add-cart {
        flex: 1;
        background: #b8735c;
        color: #ffffff;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: background 0.3s;
    }

    .btn-add-cart:hover {
        background: #9a5b45;
        color: #ffffff;
    }

    .btn-remove {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.3s;
    }

    .btn-remove:hover {
        background: #fecaca;
    }

    .empty-wishlist {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .empty-wishlist-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-wishlist h3 {
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .empty-wishlist p {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    .btn-shop {
        background: #b8735c;
        color: #ffffff;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s;
    }

    .btn-shop:hover {
        background: #9a5b45;
        color: #ffffff;
    }
</style>

<section class="wishlist-page">
    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1 class="wishlist-title">My Wishlist</h1>
            <p class="wishlist-subtitle">Products you've saved for later</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($wishlist_result) > 0): ?>
            <div class="wishlist-grid">
                <?php while ($item = mysqli_fetch_assoc($wishlist_result)): ?>
                    <?php
                    $current_price = $item['current_price'] ?? $item['price'];
                    $original_price = $item['original_price'] ?? $item['price'];
                    $discount_percentage = $item['discount_percentage'] ?? 0;
                    $has_discount = $discount_percentage > 0 && $current_price < $original_price;
                    ?>
                    <div class="wishlist-card">
                        <div class="wishlist-image-wrapper">
                            <?php if ($item['image']): ?>
                                <img src="images/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="wishlist-image">
                            <?php else: ?>
                                <img src="asetes/images/sitting-baby.png" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="wishlist-image">
                            <?php endif; ?>
                            <div class="wishlist-actions">
                                <a href="?remove=<?php echo $item['product_id']; ?>" 
                                   class="wishlist-action-btn" 
                                   title="Remove from Wishlist"
                                   onclick="return confirm('Remove this product from wishlist?');">
                                    <i class="ri-heart-fill"></i>
                                </a>
                            </div>
                        </div>
                        <div class="wishlist-info">
                            <div class="wishlist-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <?php if ($item['description']): ?>
                                <div class="wishlist-description">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="wishlist-price-container">
                                <span class="wishlist-current-price">$<?php echo number_format($current_price, 2); ?></span>
                                <?php if ($has_discount): ?>
                                    <span class="wishlist-original-price">$<?php echo number_format($original_price, 2); ?></span>
                                    <span class="wishlist-discount-badge">-<?php echo number_format($discount_percentage, 0); ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="wishlist-buttons">
                                <a href="add_to_cart.php" 
                                   class="btn-add-cart add-to-cart-from-wishlist" 
                                   data-product-id="<?php echo $item['product_id']; ?>">
                                    <i class="ri-shopping-cart-line"></i> Add to Cart
                                </a>
                                <a href="?remove=<?php echo $item['product_id']; ?>" 
                                   class="btn-remove"
                                   onclick="return confirm('Remove from wishlist?');">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-wishlist">
                <div class="empty-wishlist-icon">
                    <i class="ri-heart-line"></i>
                </div>
                <h3>Your Wishlist is Empty</h3>
                <p>Start adding products you love to your wishlist!</p>
                <a href="products.php" class="btn-shop">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-from-wishlist');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                } else {
                    alert(data.message || 'Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product to cart');
            });
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include_once("layout.php");
?>

