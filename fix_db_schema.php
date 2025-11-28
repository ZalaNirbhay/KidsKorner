<?php
include_once('database/db_connection.php');

$sql = "ALTER TABLE `orders` MODIFY COLUMN `payment_method` ENUM('cod','upi','cashfree') NOT NULL";

if (mysqli_query($con, $sql)) {
    echo "Successfully updated orders table schema.";
} else {
    echo "Error updating schema: " . mysqli_error($con);
}
?>
