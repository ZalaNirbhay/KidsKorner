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

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $price = $_POST['price'];
    $original_price = isset($_POST['original_price']) && $_POST['original_price'] ? $_POST['original_price'] : $price;
    $current_price = isset($_POST['current_price']) && $_POST['current_price'] ? $_POST['current_price'] : $price;
    $discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : 0;
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = uniqid() . '_' . time() . '_' . $_FILES['image']['name'];
        $upload_path = "images/products/" . $image_name;
        
        if (!is_dir("images/products")) {
            mkdir("images/products", 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image = $image_name;
        }
    }
    
    $query = "INSERT INTO products (name, description, price, original_price, current_price, discount_percentage, category_id, image, stock, status) 
              VALUES ('$name', '$description', $price, $original_price, $current_price, $discount_percentage, $category_id, '$image', $stock, '$status')";
    
    if (mysqli_query($con, $query)) {
        $message = "Product added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding product: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Handle Update Product
if (isset($_POST['update_product'])) {
    $id = $_POST['product_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $price = $_POST['price'];
    $original_price = isset($_POST['original_price']) && $_POST['original_price'] ? $_POST['original_price'] : $price;
    $current_price = isset($_POST['current_price']) && $_POST['current_price'] ? $_POST['current_price'] : $price;
    $discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : 0;
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image_query = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = uniqid() . '_' . time() . '_' . $_FILES['image']['name'];
        $upload_path = "images/products/" . $image_name;
        
        if (!is_dir("images/products")) {
            mkdir("images/products", 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_query = ", image = '$image_name'";
        }
    }
    
    $query = "UPDATE products SET name = '$name', description = '$description', 
              price = $price, original_price = $original_price, current_price = $current_price, 
              discount_percentage = $discount_percentage, category_id = $category_id, stock = $stock, status = '$status' 
              $image_query WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        $message = "Product updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating product: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM products WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        $message = "Product deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting product: " . mysqli_error($con);
        $message_type = "error";
    }
}

// Get products with category names
$products = mysqli_query($con, "SELECT p.*, c.name as category_name FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.created_at DESC");

// Get categories for dropdown
$categories = mysqli_query($con, "SELECT * FROM categories WHERE status = 'active' ORDER BY name");

// Get product for edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = $edit_id");
    $edit_product = mysqli_fetch_assoc($result);
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

    .product-image {
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
        <h1><i class="ri-shopping-bag-line"></i> Manage Products</h1>
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
                <li><a href="admin_categories.php"><i class="ri-folder-line"></i> Categories</a></li>
                <li><a href="admin_products.php" class="active"><i class="ri-shopping-bag-line"></i> Products</a></li>
                <li><a href="admin_users.php"><i class="ri-user-line"></i> Users</a></li>
                <li><a href="admin_orders.php"><i class="ri-file-list-line"></i> Orders</a></li>
            </ul>
        </div>

        <div class="admin-main">
            <div class="page-header">
                <h2 class="page-title"><?php echo $edit_product ? 'Edit Product' : 'Products'; ?></h2>
                <?php if (!$edit_product): ?>
                    <a href="?action=add" class="btn-primary">+ Add New Product</a>
                <?php else: ?>
                    <a href="admin_products.php" class="btn-primary">← Back to List</a>
                <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['action']) && $_GET['action'] == 'add' || $edit_product): ?>
                <!-- Add/Edit Form -->
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select class="form-control" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php 
                                mysqli_data_seek($categories, 0);
                                while ($cat = mysqli_fetch_assoc($categories)): 
                                ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Base Price ($) *</label>
                            <input type="number" step="0.01" class="form-control" name="price" 
                                   value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Original Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="original_price" 
                                   value="<?php echo $edit_product ? ($edit_product['original_price'] ?? $edit_product['price']) : ''; ?>" 
                                   placeholder="Leave empty to use base price">
                            <small style="color: #6b7280;">The original price before discount (for display purposes)</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current/Sale Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="current_price" 
                                   value="<?php echo $edit_product ? ($edit_product['current_price'] ?? $edit_product['price']) : ''; ?>" 
                                   placeholder="Leave empty to use base price">
                            <small style="color: #6b7280;">The price customers will pay (if different from base price)</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Discount Percentage (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control" name="discount_percentage" 
                                   value="<?php echo $edit_product ? ($edit_product['discount_percentage'] ?? 0) : 0; ?>">
                            <small style="color: #6b7280;">Enter discount percentage (e.g., 20 for 20% off)</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock" 
                                   value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <?php if ($edit_product && $edit_product['image']): ?>
                                <small>Current: <?php echo htmlspecialchars($edit_product['image']); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select class="form-control" name="status" required>
                                <option value="active" <?php echo ($edit_product && $edit_product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_product && $edit_product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="<?php echo $edit_product ? 'update_product' : 'add_product'; ?>" class="btn-primary">
                        <?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
                    </button>
                </form>
            <?php else: ?>
                <!-- Products List -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($products) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($products)): ?>
                                    <tr>
                                        <td>
                                            <?php if ($product['image']): ?>
                                                <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="product-image">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px; background: #f3f4f6; border-radius: 8px;"></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $product['status']; ?>">
                                                <?php echo ucfirst($product['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?edit=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                                <a href="?delete=<?php echo $product['id']; ?>" 
                                                   class="btn-delete" 
                                                   onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                                        No products found. <a href="?action=add">Add your first product</a>
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

