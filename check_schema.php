<?php
include_once('database/db_connection.php');

$result = mysqli_query($con, "SHOW COLUMNS FROM `orders` LIKE 'payment_method'");
$row = mysqli_fetch_assoc($result);
echo "Type: " . $row['Type'];
?>
