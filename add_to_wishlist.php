<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('database/db_connection.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add products to wishlist']);
    exit;
}

if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    
    // Check if product exists and is active
    $product_check = mysqli_query($con, "SELECT * FROM products WHERE id = $product_id AND status = 'active'");
    
    if (mysqli_num_rows($product_check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found or unavailable']);
        exit;
    }
    
    // Check if product already in wishlist
    $wishlist_check = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    
    if (mysqli_num_rows($wishlist_check) > 0) {
        // Remove from wishlist
        $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        
        if (mysqli_query($con, $delete_query)) {
            $wishlist_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id"))['total'];
            echo json_encode(['success' => true, 'message' => 'Product removed from wishlist', 'wishlist_count' => $wishlist_count, 'is_wishlisted' => false]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error removing from wishlist']);
        }
    } else {
        // Add to wishlist
        $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
        
        if (mysqli_query($con, $insert_query)) {
            $wishlist_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id"))['total'];
            echo json_encode(['success' => true, 'message' => 'Product added to wishlist', 'wishlist_count' => $wishlist_count, 'is_wishlisted' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding to wishlist']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
}
?>

