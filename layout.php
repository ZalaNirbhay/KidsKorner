<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($con)) {
    include_once('database/db_connection.php');
}

$header_categories = [];
if (isset($con) && $con instanceof mysqli) {
    $category_sql = "SELECT id, name FROM categories WHERE status = 'active' ORDER BY name";
    $category_result = mysqli_query($con, $category_sql);
    if ($category_result) {
        while ($category = mysqli_fetch_assoc($category_result)) {
            $header_categories[] = $category;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KidsKorner</title>

  <!-- Bootstrap + Tailwind + Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #ffffff;
      color: #333;
    }

    /* Header Styles */
    .main-header {
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem 0;
    }

    .header-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      text-decoration: none;
    }

    .logo-star {
      color: #dc2626;
      font-size: 1.2rem;
    }

    .search-section {
      flex: 1;
      max-width: 500px;
      position: relative;
    }

    .search-input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 3rem;
      border: 1px solid #d1d5db;
      border-radius: 0.5rem;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.3s;
    }

    .search-input:focus {
      border-color: #b8735c;
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
    }

    .header-icons {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .header-icon {
      color: #374151;
      font-size: 1.3rem;
      text-decoration: none;
      position: relative;
      transition: color 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .header-icon span {
      font-size: 0.85rem;
      font-weight: 600;
    }

    .header-icon:hover {
      color: #b8735c;
    }

    .account-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #374151;
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.3s;
    }

    .account-link:hover {
      color: #b8735c;
    }

    /* Navigation Bar */
    .nav-bar {
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      padding: 0.75rem 0;
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    .nav-links {
      display: flex;
      justify-content: flex-start;
      gap: 1.5rem;
      list-style: none;
      overflow-x: auto;
      padding-bottom: 0.25rem;
      scrollbar-width: none;
    }

    .nav-links::-webkit-scrollbar {
      display: none;
    }

    .nav-links a {
      color: #374151;
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      padding: 0.5rem 0;
      transition: color 0.3s;
      text-transform: capitalize;
      white-space: nowrap;
    }

    .nav-links a:hover,
    .nav-links a.active {
      color: #b8735c;
    }

    .nav-login-link a {
      color: #b8735c;
      font-weight: 600;
    }

    .nav-admin-link a {
      color: #667eea;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }

    .nav-admin-link i {
      font-size: 1rem;
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header class="main-header">
    <div class="header-container">
      <a href="index.php" class="logo-section">
        <span>KidsKorner</span>
        <span class="logo-star">*</span>
      </a>

      <div class="search-section">
        <i class="ri-search-line search-icon"></i>
        <input type="text" class="search-input" placeholder="Search for onesies...">
      </div>

      <div class="header-icons">
        <a href="<?php echo isset($_SESSION['admin_id']) ? 'admin/dashboard.php' : 'admin/login.php'; ?>" class="header-icon" title="<?php echo isset($_SESSION['admin_id']) ? 'Admin Dashboard' : 'Admin Login'; ?>">
          <i class="ri-shield-user-line"></i>
          <span style="font-size: 0.85rem;"><?php echo isset($_SESSION['admin_id']) ? 'Admin' : 'Admin Login'; ?></span>
        </a>
        <a href="cart.php" class="header-icon" title="Shopping Cart">
          <i class="ri-shopping-cart-line"></i>
        </a>
        <a href="wishlist.php" class="header-icon" title="Wishlist">
          <i class="ri-heart-line"></i>
        </a>
        <a href="<?php echo isset($_SESSION['user_email']) ? 'user_dashbord.php' : 'login.php'; ?>" class="account-link" title="<?php echo isset($_SESSION['user_email']) ? 'My Account' : 'Login'; ?>">
          <i class="ri-user-line"></i>
          <span><?php echo isset($_SESSION['user_email']) ? 'My Account' : 'Login'; ?></span>
        </a>
      </div>
    </div>
  </header>

  <!-- Navigation Bar -->
  <nav class="nav-bar">
    <div class="nav-container">
      <ul class="nav-links">
        <?php if (!empty($header_categories)): ?>
          <?php foreach ($header_categories as $category): ?>
            <li>
              <a href="category.php?id=<?php echo $category['id']; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li style="color: #9ca3af;">No categories available</li>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_email'])): ?>
          <li class="nav-login-link">
            <a href="login.php">Login</a>
          </li>
        <?php endif; ?>
        <li class="nav-admin-link">
          <a href="<?php echo isset($_SESSION['admin_id']) ? 'admin/dashboard.php' : 'admin/login.php'; ?>">
            <i class="ri-shield-user-line"></i>
            <?php echo isset($_SESSION['admin_id']) ? 'Admin Dashboard' : 'Admin Login'; ?>
          </a>
        </li>
      </ul>
    </div>
  </nav>


  <?php
  if (isset($content)) {
    echo $content;
  }
  ?>

  <!-- FOOTER -->
  <footer class="footer" style="background: #f9fafb; padding: 3rem 0 1.5rem; border-top: 1px solid #e5e7eb;">
    <div class="footer-container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 3rem;">
      <div class="footer-column">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">About Us</h3>
        <ul style="list-style: none;">
          <li><a href="#" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">Shipping & Returns</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Customer Service</h3>
        <ul style="list-style: none;">
          <li><a href="#" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">Shipping & Returns</a></li>
          <li><a href="#" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">Size Guide</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">FAQ</h3>
        <ul style="list-style: none;">
          <li><a href="#" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">Careers</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Stay</h3>
        <input type="email" class="newsletter-input" placeholder="Enter your email" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.9rem; margin-bottom: 1rem;">
        <div class="payment-icons" style="display: flex; gap: 1rem; margin-top: 1rem;">
          <div class="payment-icon" style="width: 40px; height: 25px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #6b7280;">Visa</div>
          <div class="payment-icon" style="width: 40px; height: 25px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #6b7280;">MC</div>
          <div class="payment-icon" style="width: 40px; height: 25px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #6b7280;">PayPal</div>
        </div>
      </div>
    </div>
    <div class="footer-bottom" style="max-width: 1200px; margin: 2rem auto 0; padding: 1.5rem 2rem; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 0.875rem;">
      © 2025 KidsKorner. All Rights Reserved.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>
