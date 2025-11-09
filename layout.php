<?php
session_start();
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
      gap: 1.5rem;
    }

    .header-icon {
      color: #374151;
      font-size: 1.3rem;
      text-decoration: none;
      position: relative;
      transition: color 0.3s;
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
      justify-content: center;
      gap: 2rem;
      list-style: none;
    }

    .nav-links a {
      color: #374151;
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      padding: 0.5rem 0;
      transition: color 0.3s;
      text-transform: capitalize;
    }

    .nav-links a:hover,
    .nav-links a.active {
      color: #b8735c;
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
        <li><a href="index.php">Newborns</a></li>
        <li><a href="#">Infants</a></li>
        <li><a href="#" class="active">Toddlers</a></li>
        <li><a href="#">Preschool</a></li>
        <li><a href="#">Sale</a></li>
        <?php if (!isset($_SESSION['user_email'])): ?>
        <li><a href="login.php" style="color: #b8735c; font-weight: 600;">Login</a></li>
        <li><a href="register.php" style="color: #b8735c; font-weight: 600;">Register</a></li>
        <?php else: ?>
        <li><a href="user_dashbord.php" style="color: #b8735c; font-weight: 600;">Dashboard</a></li>
        <li><a href="logout.php" style="color: #dc2626; font-weight: 600;">Logout</a></li>
        <?php endif; ?>
        <?php if (!isset($_SESSION['admin_id'])): ?>
        <li><a href="admin_login.php" style="color: #667eea; font-weight: 600;"><i class="ri-shield-user-line"></i> Admin</a></li>
        <?php endif; ?>
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
