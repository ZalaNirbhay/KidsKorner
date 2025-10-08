<?php
include_once('database/db_connection.php');

// SQL commands to update the database schema
$sql_commands = [
    "ALTER TABLE `registration` ADD COLUMN `is_verified` ENUM('active', 'inactive') DEFAULT 'inactive'",
    "ALTER TABLE `registration` ADD COLUMN `verification_token` VARCHAR(255) NULL",
    "ALTER TABLE `registration` ADD COLUMN `verification_expires` DATETIME NULL",
    "ALTER TABLE `registration` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "ALTER TABLE `registration` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    "CREATE INDEX idx_verification_token ON `registration` (`verification_token`)",
    "CREATE INDEX idx_email_verification ON `registration` (`email`, `is_verified`)"
];

echo "<h2>Setting up Email Verification System</h2>";

foreach ($sql_commands as $sql) {
    if (mysqli_query($con, $sql)) {
        echo "<p style='color: green;'>✓ " . $sql . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Error: " . mysqli_error($con) . "</p>";
    }
}

echo "<h3>Database setup completed!</h3>";
echo "<p><a href='register.php'>Go to Registration</a> | <a href='login.php'>Go to Login</a></p>";
?>
