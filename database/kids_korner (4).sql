-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 01, 2025 at 04:40 PM
-- Server version: 8.0.43
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kids_korner`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Toys', 'Toys For Your Childrens', '692dbba35563f_1764604835_car2.png', 'ri-gift-line', 'active', '2025-12-01 15:30:56', '2025-12-01 16:00:35'),
(6, 'Baby Care', 'Baby Care Productes For Your New Born Baby', '692dbb82d357a_1764604802_categories-img.jpeg', 'ri-gift-line', 'active', '2025-12-01 16:00:02', '2025-12-01 16:00:02'),
(7, 'Clothes', 'Clothes For Your New Born Babys', '692dbc6840413_1764605032_IMG_0197.jpg', 'ri-gift-line', 'active', '2025-12-01 16:03:24', '2025-12-01 16:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `user_id` int NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(120) NOT NULL,
  `state` varchar(120) NOT NULL,
  `postal_code` varchar(30) NOT NULL,
  `payment_method` enum('cod','upi','cashfree') NOT NULL,
  `upi_reference` varchar(120) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'processing',
  `payment_status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `full_name`, `email`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `payment_method`, `upi_reference`, `subtotal`, `shipping_amount`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES
(1, 'KK20251120095822675', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'KhandheriKhandheri', 'asd', 'abc', 'abc', '212121', 'cod', '', 5000.00, 10.00, 5010.00, 'delivered', 'paid', '2025-11-20 09:58:22'),
(2, 'KK20251121065454900', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'KhandheriKhandheri', 'Khandheri', 'Khandheri', 'Gujarat', '362150', 'cod', '', 2500.00, 10.00, 2510.00, 'pending', 'pending', '2025-11-21 06:54:54'),
(3, 'KK20251128125952811', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'KhandheriKhandheri', 'Khandheri', 'Khandheri', 'Gujarat', '362150', 'cashfree', '', 454.00, 10.00, 464.00, 'processing', 'paid', '2025-11-28 12:59:52'),
(4, 'KK20251201162910699', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'Rajkot', 'samras boys hostel rajkot', 'rajkot', 'Gujarat', '360005', 'cashfree', '', 549.00, 10.00, 559.00, 'pending', 'pending', '2025-12-01 16:29:10'),
(5, 'KK20251201163100317', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'Rajkot', 'samras boys hostel rajkot', 'rajkot', 'Gujarat', '360005', 'cashfree', '', 549.00, 10.00, 559.00, 'pending', 'pending', '2025-12-01 16:31:00'),
(6, 'KK20251201163850621', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'Rajkot', 'samras boys hostel rajkot', 'rajkot', 'Gujarat', '360005', 'cashfree', '', 549.00, 10.00, 559.00, 'pending', 'pending', '2025-12-01 16:38:50'),
(7, 'KK20251201163859431', 5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '1234567890', 'Rajkot', 'samras boys hostel rajkot', 'rajkot', 'Gujarat', '360005', 'cashfree', '', 549.00, 10.00, 559.00, 'processing', 'paid', '2025-12-01 16:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `created_at`) VALUES
(4, 4, 10, 'Jungle animal 12 piss mix cractor set multicolour toys Rattle', 1, 549.00, '2025-12-01 16:29:10'),
(5, 5, 10, 'Jungle animal 12 piss mix cractor set multicolour toys Rattle', 1, 549.00, '2025-12-01 16:31:00'),
(6, 6, 10, 'Jungle animal 12 piss mix cractor set multicolour toys Rattle', 1, 549.00, '2025-12-01 16:38:50'),
(7, 7, 10, 'Jungle animal 12 piss mix cractor set multicolour toys Rattle', 1, 549.00, '2025-12-01 16:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_codes`
--

CREATE TABLE `password_reset_codes` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `purpose` enum('password_change','forgot_password') NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_reset_codes`
--

INSERT INTO `password_reset_codes` (`id`, `user_id`, `email`, `otp_hash`, `purpose`, `expires_at`, `is_used`, `used_at`, `created_at`) VALUES
(1, 5, 'zalanirbhay21@gmail.com', '$2y$10$On7R7Qa3/P3BeAfaAZ5SoukMqYPnyLCKm6DNiiU2i3geP1j41ydeS', 'password_change', '2025-11-20 10:06:56', 1, '2025-11-20 15:27:34', '2025-11-20 09:56:56'),
(3, 5, 'zalanirbhay21@gmail.com', '$2y$10$xoZSzWiGqe1GC/ariKsd4.ByimKqAl8mLvWfr0OZunbYCO.J8N8ci', 'forgot_password', '2025-11-28 13:42:11', 0, NULL, '2025-11-28 13:32:11'),
(5, 9, 'bca2022nirbhay1746@tnraocollege.org', '$2y$10$X7kI3z3Gczf3N4alHE4mbeOefqvygKSFv/568pIB36xo7jdjWEtFW', 'password_change', '2025-11-28 13:54:37', 1, '2025-11-28 19:15:05', '2025-11-28 13:44:37');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `current_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT '0.00',
  `category_id` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `original_price`, `current_price`, `discount_percentage`, `category_id`, `image`, `stock`, `status`, `created_at`, `updated_at`) VALUES
(8, 'AEXONIZ TOYS 85+ Piece ', 'oy Building Block is perfect for developing fine motor skills, hand- eye coordination and social skills.Toy building block is made of environmentally friendly abs plastic with smooth edge, harmless to your kids', 499.00, 899.00, 349.00, 0.00, 5, '692db67de5198_1764603517_toy3.png', 15, 'active', '2025-12-01 15:37:40', '2025-12-01 15:53:15'),
(9, 'Pipe Puzzle Shape Building Block Game for Kids  (Multicolor)', 'Raptor Ride on the jeep, with a remote is a safe, easy-to-operate, ride-on toy that can be used on any hard surface and lets your kid build a happy memory. This car is made from the most durable material allowing for an always smooth and enjoyable ride. With life-like features, ease of use, and durable body construction, they provide miles of enjoyment for children', 9999.00, 12999.00, 9999.00, 0.00, 5, '692db7f91f7a7_1764603897_car2.png', 28, 'active', '2025-12-01 15:44:57', '2025-12-01 15:44:57'),
(10, 'Jungle animal 12 piss mix cractor set multicolour toys Rattle', 'Back Features & details [ SET OF 12 ANIMALS ] : With Saleon wildlife animal toy set, your child will have a diverse collection of 12 animals, including lion, tiger, elephant, hippopotamus, rhinosorous, deer, panda and more', 549.00, 649.00, 549.00, 0.00, 5, '692db94b18276_1764604235_image.png', 14, 'active', '2025-12-01 15:50:35', '2025-12-01 15:50:35'),
(11, 'BRANDONN Fleece Baby Bed Sized Bedding Set ', 'Baby Bedding For New Borns', 249.00, 399.00, 249.00, 0.00, 6, '692dbc1566b78_1764604949_image.png', 5, 'active', '2025-12-01 16:02:29', '2025-12-01 16:02:29'),
(12, 'Dinosaur Casual Shirt Pyjama', 'Baby Boys & Baby Girls Dinosaur Casual Shirt Pyjama  (Gold)', 219.00, 299.00, 219.00, 0.00, 7, '692dbd87ece3a_1764605319_image.png', 43, 'active', '2025-12-01 16:08:39', '2025-12-01 16:08:39'),
(13, 'Casual Dress Pyjama, Bootie, Bib, Cap', 'Baby Boys & Baby Girls Casual Dress Pyjama, Bootie, Bib, Cap  (Multicolor)\r\n', 429.00, 539.00, 429.00, 0.00, 7, '692dbe77897a9_1764605559_image copy.png', 29, 'active', '2025-12-01 16:12:39', '2025-12-01 16:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int NOT NULL,
  `fullname` char(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` char(6) DEFAULT NULL,
  `mobile` bigint DEFAULT NULL,
  `profile_picture` varchar(100) DEFAULT NULL,
  `address` text,
  `status` char(8) DEFAULT 'Inactive',
  `role` char(10) DEFAULT 'User',
  `token` varchar(200) NOT NULL,
  `is_verified` enum('active','inactive') DEFAULT 'inactive',
  `verification_token` varchar(255) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `fullname`, `email`, `password`, `gender`, `mobile`, `profile_picture`, `address`, `status`, `role`, `token`, `is_verified`, `verification_token`, `verification_expires`, `created_at`, `updated_at`) VALUES
(5, 'Zala Nirbhay', 'zalanirbhay21@gmail.com', '@NNn12345', 'Male', 1234567890, '68e6a4c1df7b7profile1.jpg', 'Khandheri\r\nKhandheri', 'Active', 'User', 'c4d698a71cbde51cc24e978e3bb94d69', 'active', NULL, NULL, '2025-10-08 17:52:01', '2025-11-20 09:57:34'),
(7, 'Admin User', 'admin@kidskorner.com', '@Nn12345', NULL, NULL, NULL, NULL, 'Active', 'admin', 'admin-token-123', 'active', NULL, NULL, '2025-11-09 16:00:46', '2025-11-09 16:01:32'),
(9, 'rajesh', 'bca2022nirbhay1746@tnraocollege.org', '@NNn12345', 'Male', 1234567890, '6929a4e2dd3e4_1764336866.jpg', 'Khandheri\r\nKhandheri', 'Active', 'User', 'a31805066a86bfa101da03535a7cf8e1', 'active', NULL, NULL, '2025-11-28 13:34:26', '2025-11-28 13:45:05');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `order_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_purpose` (`user_id`,`purpose`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_verification_token` (`verification_token`),
  ADD KEY `idx_email_verification` (`email`,`is_verified`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  ADD CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
