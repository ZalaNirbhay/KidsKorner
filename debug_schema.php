<?php
include_once('database/db_connection.php');

function get_table_schema($con, $table) {
    $result = mysqli_query($con, "SHOW CREATE TABLE `$table`");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['Create Table'];
    } else {
        return "Error getting schema for $table: " . mysqli_error($con);
    }
}

echo "<pre>";
echo "<h3>Database: " . mysqli_fetch_row(mysqli_query($con, "SELECT DATABASE()"))[0] . "</h3>";

$tables = ['registration', 'products', 'orders', 'reviews'];
foreach ($tables as $table) {
    echo "<h4>$table</h4>";
    echo htmlspecialchars(get_table_schema($con, $table));
    echo "\n\n";
}

// Try to create reviews table and show error if any
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
    echo "<h3>Attempt to create 'reviews' table: Success</h3>";
} else {
    echo "<h3>Attempt to create 'reviews' table: Failed</h3>";
    echo "Error: " . mysqli_error($con);
}

echo "</pre>";
?>
