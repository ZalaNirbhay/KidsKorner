<?php
session_start();
include_once('database/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $order_id = (int)$_POST['order_id'];
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($con, $_POST['comment']);

    // Basic Validation
    if ($rating < 1 || $rating > 5) {
        $_SESSION['message'] = "Invalid rating.";
        $_SESSION['message_type'] = "error";
        header("Location: order_history.php");
        exit;
    }

    // Check if already reviewed
    $check_sql = "SELECT id FROM reviews WHERE user_id = $user_id AND product_id = $product_id AND order_id = $order_id";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = "You have already reviewed this product for this order.";
        $_SESSION['message_type'] = "warning";
    } else {
        $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment) VALUES ($user_id, $product_id, $order_id, $rating, '$comment')";
        
        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "Review submitted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error submitting review: " . mysqli_error($con);
            $_SESSION['message_type'] = "error";
        }
    }
    
    header("Location: order_history.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
