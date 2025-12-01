<?php
session_start();
include_once('../database/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle Delete Review
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = "DELETE FROM reviews WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        $message = "Review deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting review: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Get reviews with details
$query = "SELECT r.*, u.fullname as user_name, p.name as product_name, p.image as product_image 
          FROM reviews r 
          JOIN registration u ON r.user_id = u.id 
          JOIN products p ON r.product_id = p.id 
          ORDER BY r.created_at DESC";
$reviews = mysqli_query($con, $query);

ob_start();
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f3f4f6;
    }

    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 1.5rem 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .admin-header-content {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .admin-header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .admin-header-actions a {
        color: #ffffff;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: background 0.3s;
        font-size: 0.9rem;
    }

    .admin-header-actions a:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .admin-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .admin-content {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
    }

    .admin-sidebar {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .admin-sidebar h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .admin-menu {
        list-style: none;
    }

    .admin-menu li {
        margin-bottom: 0.5rem;
    }

    .admin-menu a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .admin-menu a:hover,
    .admin-menu a.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
    }

    .admin-menu a i {
        font-size: 1.2rem;
    }

    .admin-main {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }

    th {
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .product-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    .rating-stars {
        color: #fbbf24;
        font-size: 1rem;
    }

    .review-comment {
        color: #4b5563;
        font-size: 0.9rem;
        margin-top: 0.25rem;
        max-width: 400px;
    }

    .btn-delete {
        background: #dc2626;
        color: #ffffff;
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .btn-delete:hover {
        opacity: 0.9;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <h1><i class="ri-star-line"></i> Manage Reviews</h1>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="../index.php" target="_blank"><i class="ri-external-link-line"></i> View Website</a>
            <a href="../logout.php"><i class="ri-logout-box-line"></i> Logout</a>
        </div>
    </div>
</div>

<div class="admin-container">
    <div class="admin-content">
        <div class="admin-sidebar">
            <h3>Navigation</h3>
            <ul class="admin-menu">
                <li><a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="categories.php"><i class="ri-folder-line"></i> Categories</a></li>
                <li><a href="products.php"><i class="ri-shopping-bag-line"></i> Products</a></li>
                <li><a href="users.php"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="orders.php"><i class="ri-file-list-line"></i> Orders</a></li>
                <li><a href="reviews.php" class="active"><i class="ri-star-line"></i> Reviews</a></li>
            </ul>
        </div>

        <div class="admin-main">
            <div class="page-header">
                <h2 class="page-title">Product Reviews</h2>
            </div>

            <?php if (isset($message) && $message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($reviews) > 0): ?>
                            <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <?php if ($review['product_image']): ?>
                                                <img src="../images/products/<?php echo htmlspecialchars($review['product_image']); ?>" class="product-image">
                                            <?php else: ?>
                                                <div style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 6px;"></div>
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($review['product_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                    <td>
                                        <div class="rating-stars">
                                            <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-comment">
                                            <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $review['id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No reviews found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>
