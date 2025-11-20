<?php
session_start();
include_once('../database/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get all users
$users = mysqli_query($con, "SELECT * FROM registration WHERE role = 'User' ORDER BY created_at DESC");

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

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2rem;
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
    }

    th {
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
    }

    .user-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-verified {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-unverified {
        background: #fef3c7;
        color: #92400e;
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <h1><i class="ri-user-line"></i> Manage Users</h1>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
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
                <li><a href="users.php" class="active"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="orders.php"><i class="ri-file-list-line"></i> Orders</a></li>
            </ul>
        </div>

        <div class="admin-main">
            <h2 class="page-title">All Users</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($users) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td>
                                        <?php if ($user['profile_picture']): ?>
                                            <img src="images/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($user['fullname']); ?>" 
                                                 class="user-image">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="ri-user-line" style="color: #9ca3af;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo $user['mobile'] ? htmlspecialchars($user['mobile']) : 'N/A'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($user['status']); ?>">
                                            <?php echo htmlspecialchars($user['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['is_verified'] == 'active' ? 'verified' : 'unverified'; ?>">
                                            <?php echo ucfirst($user['is_verified']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No users found.
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

