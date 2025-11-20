<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('database/db_connection.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add products to cart']);
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
    
    $product = mysqli_fetch_assoc($product_check);
    
    // Check stock
    if ($product['stock'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Product is out of stock']);
        exit;
    }
    
    // Check if product already in cart
    $cart_check = mysqli_query($con, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
    
    if (mysqli_num_rows($cart_check) > 0) {
        // Update quantity
        $cart_item = mysqli_fetch_assoc($cart_check);
        $new_quantity = $cart_item['quantity'] + 1;
        
        // Check if new quantity exceeds stock
        if ($new_quantity > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more. Only ' . $product['stock'] . ' items available']);
            exit;
        }
        
        $update_query = "UPDATE cart SET quantity = $new_quantity WHERE user_id = $user_id AND product_id = $product_id";
        
        if (mysqli_query($con, $update_query)) {
            // Get cart count
            $cart_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id"))['total'];
            echo json_encode(['success' => true, 'message' => 'Cart updated', 'cart_count' => $cart_count ? $cart_count : 0]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating cart']);
        }
    } else {
        // Add new item to cart
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)";
        
        if (mysqli_query($con, $insert_query)) {
            // Get cart count
            $cart_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id"))['total'];
            echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count ? $cart_count : 0]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
}
?>

