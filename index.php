<?php
session_start();
include_once('database/db_connection.php');

// Fetch active categories from database
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY name";
$categories_result = mysqli_query($con, $categories_query);

// Fetch featured products from database
$products_query = "SELECT p.*, c.name as category_name FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.status = 'active' 
                   ORDER BY p.created_at DESC LIMIT 3";
$products_result = mysqli_query($con, $products_query);

ob_start();
?>

<style>
  /* Hero Section */
  .hero-section {
    background: #f9fafb;
    padding: 4rem 0;
  }

  .hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
  }

  .hero-content h1 {
    font-size: 3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.2;
  }

  .hero-content p {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 2rem;
    line-height: 1.6;
  }

  .btn-primary {
    background: #b8735c;
    color: #ffffff;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
    display: inline-block;
  }

  .btn-primary:hover {
    background: #9a5b45;
    color: #ffffff;
  }

  .hero-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 1rem;
  }

  /* Section Styles */
  .section {
    padding: 4rem 0;
  }

  .section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
  }

  .section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 2rem;
  }

  /* Category Cards */
  .category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }

  .category-card {
    position: relative;
    background: #ffffff;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
  }

  .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }

  .category-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
  }

  .category-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #b8735c;
  }

  .category-name {
    padding: 1.5rem;
    text-align: center;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
  }

  /* Product Cards */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

  /* Eco-Friendly Section */
  .eco-section {
    background: #f9fafb;
    padding: 4rem 0;
  }

  .eco-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
      align-items: center;
  }

  .eco-product-card {
    background: #ffffff;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .eco-product-image-wrapper {
    padding: 2rem;
    background: #f9fafb;
      text-align: center;
  }

  .eco-product-image {
    width: 100%;
    height: 300px;
    object-fit: contain;
  }

  .eco-product-info {
    padding: 2rem;
  }

  .eco-product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
      margin-bottom: 1rem;
  }

  .eco-product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1.5rem;
  }

  .eco-link {
    color: #b8735c;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    margin-top: 1rem;
    display: inline-block;
  }

  .eco-link:hover {
    text-decoration: underline;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .hero-container,
    .eco-container {
      grid-template-columns: 1fr;
    }

    .category-grid,
    .product-grid {
      grid-template-columns: 1fr;
    }

    .hero-content h1 {
      font-size: 2rem;
    }
    }
  </style>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-container">
    <div class="hero-content">
      <h1>Discover Joyful Essentials</h1>
      <p>Quality essentials for your little ones</p>
      <a href="#" class="btn-primary">Shop New Arrivals</a>
  </div>
    <div class="hero-image-wrapper">
      <img src="asetes/images/sitting-baby.png" alt="Happy Baby" class="hero-image">
    </div>
  </div>
</section>

<!-- Shop By Category Section -->
<section class="section">
  <div class="section-container">
    <h2 class="section-title">Shop By Category</h2>
    <div class="category-grid">
      <?php if (mysqli_num_rows($categories_result) > 0): ?>
        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
          <a href="category.php?id=<?php echo $category['id']; ?>" style="text-decoration: none; color: inherit;">
            <div class="category-card">
              <?php if ($category['image']): ?>
                <img src="images/categories/<?php echo htmlspecialchars($category['image']); ?>" 
                     alt="<?php echo htmlspecialchars($category['name']); ?>" 
                     class="category-image">
              <?php else: ?>
                <img src="asetes/images/baby-2.png" alt="<?php echo htmlspecialchars($category['name']); ?>" 
                     class="category-image">
              <?php endif; ?>
              <div class="category-icon">
                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
              </div>
              <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #6b7280;">
          No categories available at the moment.
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Featured Collections Section -->
<section class="section">
  <div class="section-container">
    <h2 class="section-title">Featured Collections</h2>
    <div class="product-grid">
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
                     title="Add to Wishlist">
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
              <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #6b7280;">
          No products available at the moment.
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Eco-Friendly Picks Section -->
<section class="eco-section">
  <div class="eco-container">
    <div>
      <h2 class="section-title">Eco-Friendly Picks</h2>
      <div class="eco-product-card">
        <div class="eco-product-image-wrapper">
          <img src="asetes/images/shop-wheel.png" alt="Eco Product" class="eco-product-image">
  </div>
        <div class="eco-product-info">
          <div class="eco-product-name">Daster Cenn Pliets</div>
          <div class="eco-product-price">$74.90</div>
          <a href="#" class="btn-primary">Add to Cart</a>
          <br>
          <a href="#" class="eco-link">Learn More About Our Commitment</a>
        </div>
      </div>
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
