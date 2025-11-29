<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('database/db_connection.php');

echo "Connected to database: " . mysqli_fetch_row(mysqli_query($con, "SELECT DATABASE()"))[0] . "\n";

// Try creating table again explicitly here to see errors
$sql = "CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `registration`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($con, $sql)) {
    echo "Table 'reviews' created/exists.\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}

$result = mysqli_query($con, "SHOW TABLES");
if ($result) {
    echo "Tables in database:\n";
    while ($row = mysqli_fetch_row($result)) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Error showing tables: " . mysqli_error($con);
}
?>
