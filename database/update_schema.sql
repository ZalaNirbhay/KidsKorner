-- Update registration table to include email verification fields
ALTER TABLE `registration` 
ADD COLUMN `is_verified` ENUM('active', 'inactive') DEFAULT 'inactive',
ADD COLUMN `verification_token` VARCHAR(255) NULL,
ADD COLUMN `verification_expires` DATETIME NULL,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create index for faster verification token lookups
CREATE INDEX idx_verification_token ON `registration` (`verification_token`);
CREATE INDEX idx_email_verification ON `registration` (`email`, `is_verified`);
