<?php
session_start();
include_once('../database/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get statistics
$total_categories = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM categories"))['count'];
$total_products = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM products"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM registration WHERE role = 'User'"))['count'];
$active_products = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM products WHERE status = 'active'"))['count'];

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-title {
        color: #6b7280;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
    .stat-icon.green { background: #d1fae5; color: #10b981; }
    .stat-icon.purple { background: #ede9fe; color: #8b5cf6; }
    .stat-icon.orange { background: #fed7aa; color: #f97316; }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
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

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .action-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        transition: transform 0.2s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .action-card:hover {
        transform: translateY(-4px);
    }

    .action-card i {
        font-size: 2rem;
    }

    .action-card-content h3 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .action-card-content p {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .admin-content {
            grid-template-columns: 1fr;
        }

        .admin-sidebar {
            position: relative;
            top: 0;
        }
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <h1><i class="ri-dashboard-line"></i> Admin Dashboard</h1>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="../index.php" target="_blank"><i class="ri-external-link-line"></i> View Website</a>
            <a href="../logout.php"><i class="ri-logout-box-line"></i> Logout</a>
        </div>
    </div>
</div>

<div class="admin-container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Categories</span>
                <div class="stat-icon blue">
                    <i class="ri-folder-line"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $total_categories; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Products</span>
                <div class="stat-icon green">
                    <i class="ri-shopping-bag-line"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $total_products; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Active Products</span>
                <div class="stat-icon purple">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $active_products; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Users</span>
                <div class="stat-icon orange">
                    <i class="ri-user-line"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $total_users; ?></div>
        </div>
    </div>

    <div class="admin-content">
        <div class="admin-sidebar">
            <h3>Navigation</h3>
            <ul class="admin-menu">
                <li><a href="dashboard.php" class="active"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="categories.php"><i class="ri-folder-line"></i> Categories</a></li>
                <li><a href="products.php"><i class="ri-shopping-bag-line"></i> Products</a></li>
                <li><a href="users.php"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="orders.php"><i class="ri-file-list-line"></i> Orders</a></li>
                <li><a href="reviews.php"><i class="ri-star-line"></i> Reviews</a></li>
            </ul>
        </div>

        <div class="admin-main">
            <h2 class="page-title">Welcome to Admin Panel</h2>
            
            <div class="quick-actions">
                <a href="categories.php?action=add" class="action-card">
                    <i class="ri-add-circle-line"></i>
                    <div class="action-card-content">
                        <h3>Add Category</h3>
                        <p>Create new category</p>
                    </div>
                </a>

                <a href="products.php?action=add" class="action-card">
                    <i class="ri-add-circle-line"></i>
                    <div class="action-card-content">
                        <h3>Add Product</h3>
                        <p>Add new product</p>
                    </div>
                </a>

                <a href="categories.php" class="action-card">
                    <i class="ri-edit-box-line"></i>
                    <div class="action-card-content">
                        <h3>Manage Categories</h3>
                        <p>Edit or delete</p>
                    </div>
                </a>

                <a href="products.php" class="action-card">
                    <i class="ri-edit-box-line"></i>
                    <div class="action-card-content">
                        <h3>Manage Products</h3>
                        <p>Edit or delete</p>
                    </div>
                </a>
                  <a href="orders.php" class="action-card">
                    <i class="ri-add-circle-line"></i>
                    <div class="action-card-content">
                        <h3>Orders</h3>
                        <p>Manage orders</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
// Don't include layout for admin pages
echo $content;
?>

