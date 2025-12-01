<?php
// user_dashboard.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'database/db_connection.php';

// Security check: Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    $_SESSION['error_message'] = "Please log in to access your dashboard.";
    header("Location: login.php");
    exit;
}
ob_start();
?>

<!-- Custom CSS for a clean, professional look -->
<style>
    /* Color Palette: Modern Minimalist (Accent: Muted Gold/Bronze #A37B30) */
    .dashboard-bg {
        background-color: #F8F9FA; /* Very light gray background */
        min-height: 100vh;
        padding-top: 3rem; /* More padding */
        padding-bottom: 3rem;
    }

    .card {
        border: 1px solid #E5E7EB; /* Subtle light border */
        border-radius: 0.75rem; /* Slightly less rounded */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); /* Lighter, broader shadow */
        background-color: #FFFFFF; /* Pure white card background */
    }

    /* Sidebar Profile Header Styling */
    .profile-header {
        background: #A37B30; /* Solid Gold accent color */
        color: white;
        /* Removed border-bottom accent for cleaner look */
    }

    .profile-img {
        border: 4px solid #F8F9FA; /* Border matches dashboard background */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        object-fit: cover; /* Ensure image covers the entire area */
        object-position: center; /* Center the image */
    }

    /* Sidebar Menu Styling - Pill-shaped active state */
    .list-group-item-action {
        color: #343A40;
        transition: background-color 0.2s, color 0.2s, border-right 0.2s;
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem; /* Slightly smaller, cleaner font */
    }
    
    .list-group-item-action i {
        width: 1.25rem; /* Ensure consistent icon alignment */
    }

    .list-group-item-action:hover {
        background-color: #FEF3C7; /* Very light gold hover */
        color: #A37B30; /* Accent color on hover */
    }
    
    .list-group-item-action.active {
        background-color: #FEF3C7; 
        color: #A37B30; 
        font-weight: 600;
        border-right: 4px solid #A37B30; /* Accent border on active */
    }
    
    .list-group-flush .list-group-item:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }


    /* Overview Card Styling - Minimalist and engaging */
    .overview-card {
        transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s ease;
        overflow: hidden;
        border: 1px solid #E5E7EB;
        position: relative;
    }

    .overview-card:hover {
        transform: scale(1.02); /* Slight scale for modern pop */
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .overview-icon-wrapper {
        color: #A37B30; /* Gold Icon */
        background-color: #FFFBEB; /* Lightest gold background */
        border-radius: 0.5rem;
        padding: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .card-stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: #343A40; /* Dark number */
    }

    .card-title-main {
        color: #1E293B; /* Darker heading text */
    }
    
    /* Ensure the entire card is clickable */
    .stretched-link:after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        pointer-events: auto;
    }
</style>

<div class="dashboard-bg">
    <div class="container py-5">
        <!-- Main Dashboard Layout -->
        <div class="row g-4">

            <!-- Main Dashboard Content (col-lg-9, now on the left) -->
            <div class="col-lg-9">
                <!-- Welcome Header -->
                <h2 class="card-title-main fw-bold mb-5">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h2>

                <!-- Overview Cards -->
                <div class="row g-4 mb-5">
                    <?php
                    $user_id = $_SESSION['user_id'];
                    
                    // Get cart count
                    $cart_count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
                    $cart_count_result = mysqli_query($con, $cart_count_query);
                    $cart_count = mysqli_fetch_assoc($cart_count_result)['total'] ?? 0;
                    
                    // Get wishlist count
                    $wishlist_count_query = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id";
                    $wishlist_count_result = mysqli_query($con, $wishlist_count_query);
                    $wishlist_count = mysqli_fetch_assoc($wishlist_count_result)['total'] ?? 0;
                    
                    // Get orders count (check if orders table exists first)
                    $orders_count = 0;
                    $orders_table_exists = mysqli_query($con, "SHOW TABLES LIKE 'orders'");
                    if ($orders_table_exists && mysqli_num_rows($orders_table_exists) > 0) {
                        $orders_count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
                        $orders_count_result = mysqli_query($con, $orders_count_query);
                        if ($orders_count_result) {
                            $orders_count = mysqli_fetch_assoc($orders_count_result)['total'] ?? 0;
                        }
                    }
                    ?>
                    <!-- Cart Overview -->
                    <div class="col-md-4">
                        <div class="card text-center overview-card p-4">
                            <div class="overview-icon-wrapper mx-auto">
                                <i class="fa-solid fa-cart-shopping fa-xl"></i>
                            </div>
                            <p class="mb-1 card-stat-number"><?php echo $cart_count; ?></p>
                            <h6 class="fw-bold mb-0 text-muted">Items in Cart</h6>
                            <a href="cart.php" class="stretched-link"></a>
                        </div>
                    </div>
                    <!-- Wishlist Overview -->
                    <div class="col-md-4">
                        <div class="card text-center overview-card p-4">
                            <div class="overview-icon-wrapper mx-auto">
                                <i class="fa-solid fa-heart fa-xl"></i>
                            </div>
                            <p class="mb-1 card-stat-number"><?php echo $wishlist_count; ?></p>
                            <h6 class="fw-bold mb-0 text-muted">Items in Wishlist</h6>
                            <a href="wishlist.php" class="stretched-link"></a>
                        </div>
                    </div>
                    <!-- Orders Overview -->
                    <div class="col-md-4">
                        <div class="card text-center overview-card p-4">
                            <div class="overview-icon-wrapper mx-auto">
                                <i class="fa-solid fa-box fa-xl"></i>
                            </div>
                            <p class="mb-1 card-stat-number"><?php echo $orders_count; ?></p>
                            <h6 class="fw-bold mb-0 text-muted">Total Orders</h6>
                            <a href="order_history.php" class="stretched-link"></a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity / Recent Orders -->
                <div class="card shadow-lg rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 card-title-main"><i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Order Activity</h5>
                        <a href="order_history.php" class="btn btn-sm fw-semibold" style="color:#A37B30 !important;">View All Orders <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
                    </div>

                    <!-- Placeholder for Recent Orders Table/List -->
                    <div class="alert border-0 rounded-3 text-center py-4" style="background-color: #FEF3C7; color: #A37B30;" role="alert">
                        <i class="fa-solid fa-receipt fa-2x mb-2"></i>
                        <h6 class="fw-bold mb-1">No Recent Orders Found</h6>
                        <p class="mb-0">You haven't placed an order in a while. Browse our collections today!</p>
                    </div>

                    <!-- Example of a single recent order entry (if data existed)
                    <div class="list-group">
                        <a href="order_details.php?id=123" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Order #ODR-12345</h6>
                                <small class="text-muted">Placed on 2025-05-20</small>
                            </div>
                            <span class="badge bg-success rounded-pill">Shipped</span>
                        </a>
                    </div>
                    -->
                </div>
            </div>
            
            <!-- Sidebar (Profile & Navigation) (col-lg-3, now on the right) -->
            <div class="col-lg-3">
                <div class="card shadow-lg rounded-4 overflow-hidden sticky-top" style="top: 20px;">
                    <!-- Profile Card Header -->
                    <div class="profile-header text-center p-4">
                        <?php 
                        $profile_pic = $_SESSION['user_profile_picture'] ?? 'default-avatar.png';
                        $profile_pic_path = "images/profile_pictures/" . $profile_pic;
                        ?>
                        <img src="<?php echo file_exists($profile_pic_path) ? $profile_pic_path : 'default_avatar.php'; ?>" alt="Profile" class="profile-img rounded-circle mb-3" width="100" height="100">
                        <h4 class="fw-bolder mb-0 text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest'); ?></h4>
                        <p class="text-white-50 mb-0"><small><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'guest@example.com'); ?></small></p>
                    </div>
                    <!-- Navigation Menu -->
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active"><i class="fa-solid fa-gauge me-2"></i>Dashboard Overview</a>
                        <a href="edit_profile.php" class="list-group-item list-group-item-action"><i class="fa-solid fa-user-pen me-2"></i>Profile &amp; Security</a>
                        <a href="cart.php" class="list-group-item list-group-item-action"><i class="fa-solid fa-cart-shopping me-2"></i>Shopping Cart</a>
                        <a href="wishlist.php" class="list-group-item list-group-item-action"><i class="fa-solid fa-heart me-2"></i>Wishlist</a>
                        <a href="order_history.php" class="list-group-item list-group-item-action"><i class="fa-solid fa-box-open me-2"></i>Order History</a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
