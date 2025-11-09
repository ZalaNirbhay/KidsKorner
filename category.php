<?php
session_start();
include_once('database/db_connection.php');

// Get category ID
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id == 0) {
    header("Location: index.php");
    exit;
}

// Get category details
$category_query = "SELECT * FROM categories WHERE id = $category_id AND status = 'active'";
$category_result = mysqli_query($con, $category_query);

if (mysqli_num_rows($category_result) == 0) {
    header("Location: index.php");
    exit;
}

$category = mysqli_fetch_assoc($category_result);

// Get products for this category
$products_query = "SELECT p.*, c.name as category_name FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.category_id = $category_id AND p.status = 'active' 
                   ORDER BY p.created_at DESC";
$products_result = mysqli_query($con, $products_query);

ob_start();
?>

<style>
    .category-page {
        padding: 4rem 0;
        background: #f9fafb;
    }

    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 3rem 0;
        margin-bottom: 3rem;
    }

    .category-header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        text-align: center;
    }

    .category-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .category-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .category-container {
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

    .product-info {
        padding: 1.5rem;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .login-prompt {
        background: #fef3c7;
        border: 2px solid #fbbf24;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-prompt h3 {
        color: #92400e;
        margin-bottom: 1rem;
    }

    .login-prompt p {
        color: #78350f;
        margin-bottom: 1rem;
    }

    .btn-login {
        background: #b8735c;
        color: #ffffff;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        transition: background 0.3s;
    }

    .btn-login:hover {
        background: #9a5b45;
        color: #ffffff;
    }

    .no-products {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }
</style>

<div class="category-header">
    <div class="category-header-content">
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        <?php if ($category['description']): ?>
            <p><?php echo htmlspecialchars($category['description']); ?></p>
        <?php endif; ?>
    </div>
</div>

<section class="category-page">
    <div class="category-container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="login-prompt">
                <h3><i class="ri-information-line"></i> Login Required</h3>
                <p>Please login to add products to cart and make purchases.</p>
                <a href="login.php" class="btn-login">Login Now</a>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
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
                                    <a href="#" class="product-action-btn add-to-cart" 
                                       data-product-id="<?php echo $product['id']; ?>"
                                       title="Add to Cart">
                                        <i class="ri-shopping-cart-line"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <?php if ($product['description']): ?>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 60)) . '...'; ?>
                                </div>
                            <?php endif; ?>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
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
                <div class="no-products">
                    <h3>No products found in this category</h3>
                    <p>Check back later for new products!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                    // Update cart count if you have a cart counter
                    if (data.cart_count !== undefined) {
                        // Update cart count in header if exists
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
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

