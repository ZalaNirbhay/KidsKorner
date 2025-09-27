<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery-3.7.1.min.js"></script>
  <script src="js/jquery.validate.js"></script>
  <script src="js/additional-methods.js"> </script>
  <script src="js/validate.js"> </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Hello, world!</title>
</head>

<body>

  <div class="navbar p-4 border-b">
    <nav class="flex justify-between items-center w-full">
      <!-- Left side -->
      <div class="flex items-center gap-10">
        <h4 class="text-lg font-bold">Kids-Korner</h4>
        <ul class="flex gap-5">
          <li><a class="hover:text-gray-600" href="index.php">Home</a></li>
          <li><a class="hover:text-gray-600" href="">Men</a></li>
          <li><a class="hover:text-gray-600" href="">Women</a></li>
          <li><a class="hover:text-gray-600" href="">Sale</a></li>
          <li><a class="hover:text-gray-600" href="register.php">Register</a></li>
        </ul>
      </div>

      <!-- Right side -->
      <div class="flex items-center gap-4">
        <i class="ri-search-line cursor-pointer text-lg"></i>
        <i class="ri-shopping-cart-2-fill cursor-pointer text-lg"></i>
      </div>
    </nav>
  </div>

  <?php
  if (isset($content)) {
    echo $content;
  }

  ?>

  <footer class="bg-gray-100 text-gray-700 mt-10 bottom-0">
    <div class="max-w-7xl  mx-auto px-6  grid grid-cols-1 md:grid-cols-4 gap-8">

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


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>