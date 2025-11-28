<?php
session_start();
include_once('database/db_connection.php');
include_once('helpers/CashfreeHelper.php');

if (!isset($_GET['order_id'])) {
    die("Invalid request");
}

$order_id = $_GET['order_id'];
$helper = new CashfreeHelper();

try {
    $paymentData = $helper->verifyPayment($order_id);
    
    // Check if payment is successful
    // Cashfree status can be 'PAID', 'ACTIVE', 'EXPIRED', etc.
    // We are looking for 'PAID' in the order status or payment status
    
    $orderStatus = $paymentData['order_status'];
    
    if ($orderStatus === 'PAID') {
        // Update order status in database
        $update_sql = "UPDATE orders SET status = 'processing', payment_status = 'paid' WHERE order_number = '$order_id'";
        if (mysqli_query($con, $update_sql)) {
            // Clear cart for the user associated with this order
            // We need to get the user_id from the order
            $user_query = mysqli_query($con, "SELECT user_id FROM orders WHERE order_number = '$order_id'");
            if ($user_row = mysqli_fetch_assoc($user_query)) {
                $u_id = $user_row['user_id'];
                mysqli_query($con, "DELETE FROM cart WHERE user_id = $u_id");
            }

            // Redirect to success page
            $_SESSION['message'] = "Payment successful! Your order has been confirmed.";
            $_SESSION['message_type'] = "success";
            header("Location: order_history.php");
            exit;
        } else {
            throw new Exception("Database update failed");
        }
    } else {
        // Payment failed or pending
        $update_sql = "UPDATE orders SET payment_status = 'failed' WHERE order_number = '$order_id'";
        mysqli_query($con, $update_sql);
        
        $_SESSION['message'] = "Payment failed or incomplete. Please try again.";
        $_SESSION['message_type'] = "error";
        header("Location: cart.php"); // Or order history if we want them to retry from there
        exit;
    }

} catch (Exception $e) {
    $_SESSION['message'] = "Payment verification failed: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header("Location: cart.php");
    exit;
}
