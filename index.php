<?php
ob_start();
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <style type="text/tailwindcss">
    body {
      background-color: #4a6a6e; /* teal background */
      font-family: 'Segoe UI', sans-serif;
    }

    /* Hero Section */
    .hero {
      background: url('asetes/images/Gemini_Generated_Image_tbxk92tbxk92tbxk.png') no-repeat center center/cover;
      width: 80%;
      margin: 2rem auto;
      min-height: 400px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 4px;
      position: relative;
      text-align: center;
      color: white;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    }

    .hero h1 {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }

    .btn-theme {
      background: #d76d6d;
      color: #fff;
      border-radius: 1rem;
      padding: 0.6rem 1.5rem;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .btn-theme:hover {
      background: #c55454;
    }
  </style>
</head>

<!-- Hero Section -->
<div class="hero">
  <div>
    <h1>Discover the Joy of Parenting</h1>
    <button class="btn-theme">Shop Now</button>
  </div>
</div>

<div class="main-categories w-[80%] mx-auto my-10 text-white">
  <h1>Categories Spotlight</h1>
</div>

<div class="categories w-[80%] mx-auto my-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 cursor-pointer">
  <!-- Card 1 -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
    <img src="asetes/images/sitting-baby.png" alt="Newborns" class="w-full h-48 object-cover">
    <div class="p-4 text-center">
      <h3 class="text-lg font-medium text-gray-800">Newborns</h3>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
    <img src="asetes/images/baby-2.png" alt="Infants" class="w-full h-48 object-cover">
    <div class="p-4 text-center">
      <h3 class="text-lg font-medium text-gray-800">Infants</h3>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
    <img src="asetes/images/baby-3.png" alt="Toddlers" class="w-full h-48 object-cover">
    <div class="p-4 text-center">
      <h3 class="text-lg font-medium text-gray-800">Toddlers</h3>
    </div>
  </div>
</div>

<div class="collection-main border-2px border-gray-300 rounded-sm p-4">
  <div class="main-categories w-[80%] mx-auto my-10 text-white">
    <h1>Collections</h1>
  </div>

  <div class="categories w-[80%] mx-auto my-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 cursor-pointer">
    <!-- Card 1 -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
      <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <a href="#">
          <img class="p-8 rounded-t-lg" src="asetes/images/shop-wheel.png" alt="product image" />
        </a>
        <div class="px-5 pb-5">
          <a class="text-decoration-none" href="#">
            <h5 class="text-xl font-semibold tracking-tight text-black">jula for new born baby</h5>
          </a>
          <div class="flex items-center mt-2.5 mb-5">
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-sm dark:bg-blue-200 dark:text-blue-800 ms-3">5.0</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-3xl font-bold text-black">$599</span>
            <a href="#" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add to cart</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</html>

<?php
$content = ob_get_clean();
include_once("layout.php");
?>
