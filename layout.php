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
    body{
            background-color: #4a6a6e; /* teal background */

    }
    .header-top {
      background: #b84d52; /* Top bar background */
      color: #fff;
      padding: 6px 20px;
      font-size: 14px;
    }

    .search-bar input {
      border: 1px solid #ccc;
    }

    .nav-links a {
      padding: 8px 14px;
      font-size: 15px;
      font-weight: 500;
      color: #333;
      text-decoration: none;
    }

    .nav-links a:hover {
      color: #b84d52;
    }
  </style>
</head>

<body style="background-color: #4a6a6e;">

  <!-- HEADER -->
  <header>
    <!-- Top Bar -->
    <div class="header-top flex justify-between items-center">
      <div class="font-bold">KidsKorner</div>
      <div><a href="login.php" class="hover:underline">My Account / Login</a></div>
    </div>

    <!-- Main Header -->
    <div class="flex justify-between items-center py-4 px-6 bg-white border-b">
      <!-- Logo -->
      <div class="text-xl font-bold text-red-600">KidsKorner</div>

      <!-- Search Bar -->
      <div class="flex items-center w-1/2 search-bar">
        <input type="text" placeholder="Search for onesies, toys, strollers..."
          class="rounded-l-md px-3 py-2 w-full focus:outline-none text-sm">
        <button class="bg-red-600 text-white px-4 rounded-r-md"><i class="ri-search-line"></i></button>
      </div>

      <!-- Icons -->
      <div class="flex items-center gap-5 text-xl">
        <a href="cart.php" class="hover:text-red-600"><i class="ri-shopping-cart-line"></i></a>
        <a href="login.php" class="hover:text-red-600"><i class="ri-user-line"></i></a>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <ul class="flex justify-center nav-links py-2">
        <li><a href="#">Newborns</a></li>
        <li><a href="#">Infants</a></li>
        <li><a href="#">Toddlers</a></li>
        <li><a href="#">Preschool</a></li>
        <li><a href="#">Sale</a></li>
      </ul>
    </nav>
  </header>


  <?php
  if (isset($content)) {
    echo $content;
  }
  ?>

  <!-- FOOTER stays same -->
  <footer class="bg-gray-100 text-gray-700 mt-10 bottom-0">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8">

      <!-- Logo & About -->
      <div>
        <h2 class="text-xl font-bold mb-3">KidsKorner</h2>
        <p class="text-sm leading-6">
          Your one-stop shop for trendy kids' fashion, toys, and accessories.
          Bringing joy and comfort to every child.
        </p>
      </div>

      <!-- Shop Links -->
      <div>
        <h3 class="text-lg font-semibold mb-3">Shop</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-black">Boys</a></li>
          <li><a href="#" class="hover:text-black">Girls</a></li>
          <li><a href="#" class="hover:text-black">New Arrivals</a></li>
          <li><a href="#" class="hover:text-black">Sale</a></li>
        </ul>
      </div>

      <!-- Customer Service -->
      <div>
        <h3 class="text-lg font-semibold mb-3">Customer Service</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-black">Contact Us</a></li>
          <li><a href="#" class="hover:text-black">FAQs</a></li>
          <li><a href="#" class="hover:text-black">Shipping & Returns</a></li>
          <li><a href="#" class="hover:text-black">Privacy Policy</a></li>
        </ul>
      </div>

      <!-- Newsletter & Social -->
      <div>
        <h3 class="text-lg font-semibold mb-3">Stay Connected</h3>
        <p class="text-sm mb-3">Subscribe for updates & offers</p>
        <form class="flex">
          <input type="email" placeholder="Enter your email"
            class="border border-gray-300 rounded-l-md p-2 w-full text-sm focus:outline-none">
          <button type="submit" class="bg-black text-white px-4 rounded-r-md text-sm">Subscribe</button>
        </form>
        <div class="flex gap-4 mt-4 text-xl">
          <a href="#"><i class="ri-facebook-fill hover:text-black"></i></a>
          <a href="#"><i class="ri-instagram-fill hover:text-black"></i></a>
          <a href="#"><i class="ri-twitter-fill hover:text-black"></i></a>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="border-t border-gray-300 mt-6 py-4 text-center text-sm text-gray-500">
      © 2025 KidsKorner. All Rights Reserved.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>
