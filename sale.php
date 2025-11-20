<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('database/db_connection.php');

// Get products with discount
$products_query = "SELECT p.*, c.name as category_name FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.status = 'active' 
                   AND (p.discount_percentage > 0 OR (p.current_price IS NOT NULL AND p.original_price IS NOT NULL AND p.current_price < p.original_price))
                   ORDER BY p.discount_percentage DESC, p.created_at DESC";
$products_result = mysqli_query($con, $products_query);

ob_start();
?>

<style>
    .sale-page {
        padding: 4rem 0;
        background: #f9fafb;
    }

    .sale-header {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: #ffffff;
        padding: 3rem 0;
        margin-bottom: 3rem;
        text-align: center;
    }

    .sale-header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .sale-header h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .sale-header p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .sale-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: #ffffff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .product-image-wrapper {
        position: relative;
        padding: 2rem;
        background: #f9fafb;
        text-align: center;
    }

    .product-image {
        width: 100%;
        height: 250px;
        object-fit: contain;
    }

    .discount-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: #dc2626;
        color: #ffffff;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        z-index: 10;
    }

    .product-actions {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    .product-action-btn {
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
        color: #374151;
        text-decoration: none;
    }

    .product-action-btn:hover {
        background: #b8735c;
        color: #ffffff;
    }

    .product-action-btn.wishlisted {
        background: #fee2e2;
        color: #dc2626;
    }

    .product-action-btn.wishlisted:hover {
        background: #fecaca;
        color: #dc2626;
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .product-price-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .product-current-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc2626;
    }

    .product-original-price {
        font-size: 1.1rem;
        color: #9ca3af;
        text-decoration: line-through;
    }

    .product-discount {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .no-products {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .no-products-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
</style>

<div class="sale-header">
    <div class="sale-header-content">
        <h1>🔥 Sale Items</h1>
        <p>Special discounts on selected products - Limited time offers!</p>
    </div>
</div>

<section class="sale-page">
    <div class="sale-container">
        <div class="products-grid">
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <?php
                    $current_price = $product['current_price'] ?? $product['price'];
                    $original_price = $product['original_price'] ?? $product['price'];
                    $discount_percentage = $product['discount_percentage'] ?? 0;
                    
                    // Calculate discount if not set
                    if ($discount_percentage == 0 && $original_price > $current_price) {
                        $discount_percentage = round((($original_price - $current_price) / $original_price) * 100, 2);
                    }
                    
                    $has_discount = $discount_percentage > 0 && $current_price < $original_price;
                    
                    // Check if product is in wishlist
                    $is_wishlisted = false;
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $wishlist_check = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = " . $product['id']);
                        $is_wishlisted = mysqli_num_rows($wishlist_check) > 0;
                    }
                    ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <?php if ($has_discount): ?>
                                <div class="discount-badge">-<?php echo number_format($discount_percentage, 0); ?>% OFF</div>
                            <?php endif; ?>
                            <?php if ($product['image']): ?>
                                <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <img src="asetes/images/sitting-baby.png" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image">
                            <?php endif; ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="product-actions">
                                    <a href="#" class="product-action-btn add-to-wishlist <?php echo $is_wishlisted ? 'wishlisted' : ''; ?>" 
                                       data-product-id="<?php echo $product['id']; ?>"
                                       title="<?php echo $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                        <i class="ri-heart-<?php echo $is_wishlisted ? 'fill' : 'line'; ?>"></i>
                                    </a>
                                    <a href="#" class="product-action-btn add-to-cart" 
                                       data-product-id="<?php echo $product['id']; ?>"
                                       title="Add to Cart">
                                        <i class="ri-shopping-cart-line"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="product-actions">
                                    <a href="login.php" class="product-action-btn" title="Login to Add">
                                        <i class="ri-login-box-line"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                            </div>
                            <div class="product-price-container">
                                <span class="product-current-price">$<?php echo number_format($current_price, 2); ?></span>
                                <?php if ($has_discount): ?>
                                    <span class="product-original-price">$<?php echo number_format($original_price, 2); ?></span>
                                    <span class="product-discount">Save $<?php echo number_format($original_price - $current_price, 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($product['stock'] > 0): ?>
                                <div style="font-size: 0.85rem; color: #10b981; margin-top: 0.5rem;">
                                    <i class="ri-checkbox-circle-line"></i> In Stock (<?php echo $product['stock']; ?>)
                                </div>
                            <?php else: ?>
                                <div style="font-size: 0.85rem; color: #dc2626; margin-top: 0.5rem;">
                                    <i class="ri-close-circle-line"></i> Out of Stock
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products" style="grid-column: 1 / -1;">
                    <div class="no-products-icon">
                        <i class="ri-fire-line"></i>
                    </div>
                    <h3>No Sale Items Available</h3>
                    <p>Check back later for special discounts!</p>
                    <a href="products.php" style="display: inline-block; margin-top: 1rem; color: #b8735c; text-decoration: none; font-weight: 600;">
                        Browse All Products →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
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

    // Add to wishlist functionality
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const icon = this.querySelector('i');
            
            fetch('add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_wishlisted) {
                        icon.className = 'ri-heart-fill';
                        this.classList.add('wishlisted');
                        this.setAttribute('title', 'Remove from Wishlist');
                    } else {
                        icon.className = 'ri-heart-line';
                        this.classList.remove('wishlisted');
                        this.setAttribute('title', 'Add to Wishlist');
                    }
                } else {
                    alert(data.message || 'Error updating wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating wishlist');
            });
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include_once("layout.php");
?>

