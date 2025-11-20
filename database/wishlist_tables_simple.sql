-- ============================================
-- WISHLIST TABLES SETUP - SIMPLE VERSION
-- ============================================
-- Run each section separately in phpMyAdmin
-- If you get an error saying a column already exists, that's fine - just skip that section
-- ============================================

-- STEP 1: Create Wishlist Table
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================
-- STEP 2: Add original_price column to products table
-- Run this first. If column exists, you'll get an error - just continue to next step.
-- ============================================
ALTER TABLE `products` 
ADD COLUMN `original_price` decimal(10,2) NULL AFTER `price`;

-- ============================================
-- STEP 3: Add current_price column to products table
-- Run this second. If column exists, you'll get an error - just continue to next step.
-- ============================================
ALTER TABLE `products` 
ADD COLUMN `current_price` decimal(10,2) NULL AFTER `original_price`;

-- ============================================
-- STEP 4: Add discount_percentage column to products table
-- Run this third. If column exists, you'll get an error - that's fine.
-- ============================================
ALTER TABLE `products` 
ADD COLUMN `discount_percentage` decimal(5,2) DEFAULT 0.00 AFTER `current_price`;

-- ============================================
-- STEP 5: Update existing products with default values
-- Run this last to set default values for existing products
-- ============================================
UPDATE `products` SET `current_price` = `price` WHERE `current_price` IS NULL;
UPDATE `products` SET `original_price` = `price` WHERE `original_price` IS NULL;

-- ============================================
-- DONE! Your database is now set up for wishlist and discount features.
-- ============================================

