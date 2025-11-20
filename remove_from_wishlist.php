<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('database/db_connection.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    
    $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    
    if (mysqli_query($con, $delete_query)) {
        $wishlist_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id"))['total'];
        echo json_encode(['success' => true, 'message' => 'Product removed from wishlist', 'wishlist_count' => $wishlist_count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing from wishlist']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
}
?>

