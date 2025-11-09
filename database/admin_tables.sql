-- Admin Dashboard Database Tables
-- Run this script in phpMyAdmin to create necessary tables

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `category_id` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert some default categories
INSERT INTO `categories` (`name`, `description`, `icon`, `status`) VALUES
('Newborns', 'Products for newborns (0-3 months)', 'ri-gift-line', 'active'),
('Toddlers', 'Products for toddlers (1-3 years)', 'ri-stack-line', 'active'),
('Gear & Nursery', 'Nursery essentials and gear', 'ri-map-pin-line', 'active');

-- Sample products (optional - you can delete these)
INSERT INTO `products` (`name`, `description`, `price`, `category_id`, `stock`, `status`) VALUES
('Besttallars Cclains Cotton', 'Premium cotton clothing for babies', 78.00, 1, 50, 'active'),
('Organic Cotton Fiiets', 'Organic cotton baby essentials', 79.00, 1, 30, 'active'),
('Hrusles Plicks', 'Comfortable baby products', 79.00, 2, 25, 'active'),
('Daster Cenn Pliets', 'Eco-friendly baby products', 74.90, 3, 40, 'active');

