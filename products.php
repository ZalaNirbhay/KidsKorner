<?php
session_start();
include_once('database/db_connection.php');

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT p.*, c.name as category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'active'";

if ($category_filter) {
    $query .= " AND p.category_id = $category_filter";
}

if ($search) {
    $search_escaped = mysqli_real_escape_string($con, $search);
    $query .= " AND (p.name LIKE '%$search_escaped%' OR p.description LIKE '%$search_escaped%')";
}

$query .= " ORDER BY p.created_at DESC";
$products_result = mysqli_query($con, $query);

// Get categories for filter
$categories = mysqli_query($con, "SELECT * FROM categories WHERE status = 'active' ORDER BY name");

ob_start();
?>

<style>
    .products-page {
        padding: 4rem 0;
        background: #f9fafb;
    }

    .products-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .products-header {
        margin-bottom: 2rem;
    }

    .products-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .products-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
    }

    .filter-input:focus {
        outline: none;
        border-color: #b8735c;
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

    .no-products {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }
</style>

<section class="products-page">
    <div class="products-container">
        <div class="products-header">
            <h1 class="products-title">All Products</h1>
            
            <div class="products-filters">
                <div class="filter-group">
                    <form method="get" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" class="filter-input" 
                               placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" style="padding: 0.75rem 1.5rem; background: #b8735c; color: white; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="ri-search-line"></i>
                        </button>
                    </form>
                </div>
                <div class="filter-group">
                    <form method="get" style="display: flex; gap: 0.5rem;">
                        <select name="category" class="filter-input" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php 
                            mysqli_data_seek($categories, 0);
                            while ($cat = mysqli_fetch_assoc($categories)): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

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
                                    <a href="#" class="product-action-btn" title="Add to Wishlist">
                                        <i class="ri-heart-line"></i>
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
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filter criteria.</p>
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

