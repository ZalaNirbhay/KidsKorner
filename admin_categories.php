<?php
session_start();
include_once('database/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$message_type = '';

// Handle Add Category
if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $icon = mysqli_real_escape_string($con, $_POST['icon']);
    $status = $_POST['status'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = uniqid() . '_' . time() . '_' . $_FILES['image']['name'];
        $upload_path = "images/categories/" . $image_name;
        
        if (!is_dir("images/categories")) {
            mkdir("images/categories", 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image = $image_name;
        }
    }
    
    $query = "INSERT INTO categories (name, description, image, icon, status) 
              VALUES ('$name', '$description', '$image', '$icon', '$status')";
    
    if (mysqli_query($con, $query)) {
        $message = "Category added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding category: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Handle Update Category
if (isset($_POST['update_category'])) {
    $id = $_POST['category_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $icon = mysqli_real_escape_string($con, $_POST['icon']);
    $status = $_POST['status'];
    
    // Handle image upload
    $image_query = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = uniqid() . '_' . time() . '_' . $_FILES['image']['name'];
        $upload_path = "images/categories/" . $image_name;
        
        if (!is_dir("images/categories")) {
            mkdir("images/categories", 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_query = ", image = '$image_name'";
        }
    }
    
    $query = "UPDATE categories SET name = '$name', description = '$description', 
              icon = '$icon', status = '$status' $image_query WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        $message = "Category updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating category: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM categories WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        $message = "Category deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting category: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Get categories
$categories = mysqli_query($con, "SELECT * FROM categories ORDER BY created_at DESC");

// Get category for edit
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($con, "SELECT * FROM categories WHERE id = $edit_id");
    $edit_category = mysqli_fetch_assoc($result);
}

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

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: transform 0.2s;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
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

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #374151;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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

    .category-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit {
        background: #3b82f6;
        color: #ffffff;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .btn-delete {
        background: #dc2626;
        color: #ffffff;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .btn-edit:hover,
    .btn-delete:hover {
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .admin-content {
            grid-template-columns: 1fr;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-header">
    <div class="admin-header-content">
        <h1><i class="ri-folder-line"></i> Manage Categories</h1>
        <div class="admin-header-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="index.php" target="_blank"><i class="ri-external-link-line"></i> View Website</a>
            <a href="logout.php"><i class="ri-logout-box-line"></i> Logout</a>
        </div>
    </div>
</div>

<div class="admin-container">
    <div class="admin-content">
        <div class="admin-sidebar">
            <h3>Navigation</h3>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="admin_categories.php" class="active"><i class="ri-folder-line"></i> Categories</a></li>
                <li><a href="admin_products.php"><i class="ri-shopping-bag-line"></i> Products</a></li>
                <li><a href="admin_users.php"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="admin_orders.php"><i class="ri-file-list-line"></i> Orders</a></li>
            </ul>
        </div>

        <div class="admin-main">
            <div class="page-header">
                <h2 class="page-title"><?php echo $edit_category ? 'Edit Category' : 'Categories'; ?></h2>
                <?php if (!$edit_category): ?>
                    <a href="?action=add" class="btn-primary">+ Add New Category</a>
                <?php else: ?>
                    <a href="admin_categories.php" class="btn-primary">← Back to List</a>
                <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['action']) && $_GET['action'] == 'add' || $edit_category): ?>
                <!-- Add/Edit Form -->
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Icon Class (RemixIcon) *</label>
                            <input type="text" class="form-control" name="icon" 
                                   value="<?php echo $edit_category ? htmlspecialchars($edit_category['icon']) : ''; ?>" 
                                   placeholder="ri-gift-line" required>
                            <small style="color: #6b7280;">Example: ri-gift-line, ri-stack-line, ri-map-pin-line</small>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <?php if ($edit_category && $edit_category['image']): ?>
                                <small>Current: <?php echo htmlspecialchars($edit_category['image']); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select class="form-control" name="status" required>
                                <option value="active" <?php echo ($edit_category && $edit_category['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_category && $edit_category['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="<?php echo $edit_category ? 'update_category' : 'add_category'; ?>" class="btn-primary">
                        <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                    </button>
                </form>
            <?php else: ?>
                <!-- Categories List -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($categories) > 0): ?>
                                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                    <tr>
                                        <td>
                                            <?php if ($category['image']): ?>
                                                <img src="images/categories/<?php echo htmlspecialchars($category['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                                     class="category-image">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="<?php echo htmlspecialchars($category['icon']); ?>" style="font-size: 1.5rem; color: #9ca3af;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                        <td><i class="<?php echo htmlspecialchars($category['icon']); ?>"></i></td>
                                        <td>
                                            <span class="badge badge-<?php echo $category['status']; ?>">
                                                <?php echo ucfirst($category['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?edit=<?php echo $category['id']; ?>" class="btn-edit">Edit</a>
                                                <a href="?delete=<?php echo $category['id']; ?>" 
                                                   class="btn-delete" 
                                                   onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem; color: #6b7280;">
                                        No categories found. <a href="?action=add">Add your first category</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>

