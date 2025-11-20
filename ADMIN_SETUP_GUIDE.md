# Admin Dashboard Setup Guide

## 📋 Overview
This guide will help you set up the complete admin dashboard system for Kids-Korner.

## 🗄️ Database Setup

### Step 1: Run the Database Script
1. Open phpMyAdmin
2. Select your database (`kids_korner`)
3. Go to the SQL tab
4. Copy and paste the contents of `database/admin_tables.sql`
5. Click "Go" to execute

This will create:
- `categories` table - for managing product categories
- `products` table - for managing products

### Step 2: Create an Admin User
To create an admin user, run this SQL query in phpMyAdmin:

```sql
UPDATE registration 
SET role = 'admin', status = 'Active', is_verified = 'active' 
WHERE email = 'your-admin-email@example.com';
```

Or insert a new admin user:

```sql
INSERT INTO registration (fullname, email, password, role, status, is_verified, token) 
VALUES ('Admin User', 'admin@kidskorner.com', 'your-password', 'admin', 'Active', 'active', 'admin-token-123');
```

**Important:** Replace `'your-password'` with your desired admin password.

## 📁 File Structure

### Admin Pages Created:
- `admin/login.php` - Admin login page
- `admin/dashboard.php` - Main admin dashboard
- `admin/categories.php` - Category management (add, edit, delete)
- `admin/products.php` - Product management (add, edit, delete)
- `admin/users.php` - User management (view all users)
- `admin/orders.php` - Full order management panel

### Updated Pages:
- `index.php` - Now fetches categories and products from database
- `products.php` - New page to display all products
- `layout.php` - Added admin login link in navigation
- `logout.php` - Handles both user and admin logout

## 🚀 How to Use

### 1. Access Admin Login
- Navigate to: `http://your-domain/admin/login.php`
- Or click "Admin" link in the navigation bar

### 2. Login as Admin
- Use the email and password of a user with `role = 'admin'`
- The system checks if the user has admin role before allowing access

### 3. Admin Dashboard Features

#### Categories Management:
- **Add Category**: Click "Add New Category" button
  - Enter category name
  - Add icon class (RemixIcon format: `ri-gift-line`)
  - Upload category image (optional)
  - Set status (active/inactive)
  
- **Edit Category**: Click "Edit" button on any category
- **Delete Category**: Click "Delete" button (with confirmation)

#### Products Management:
- **Add Product**: Click "Add New Product" button
  - Enter product name and description
  - Select category
  - Set price and stock quantity
  - Upload product image (optional)
  - Set status (active/inactive)
  
- **Edit Product**: Click "Edit" button on any product
- **Delete Product**: Click "Delete" button (with confirmation)

#### Users Management:
- View all registered users
- See user status and verification status
- View user profile pictures

## 🎨 Features

### Admin Dashboard:
- ✅ Modern gradient design
- ✅ Statistics cards (Total Categories, Products, Users, Active Products)
- ✅ Quick action buttons
- ✅ Sidebar navigation
- ✅ Responsive design

### Category Management:
- ✅ Add/Edit/Delete categories
- ✅ Image upload support
- ✅ Icon selection (RemixIcon)
- ✅ Status management (active/inactive)
- ✅ Real-time updates on home page

### Product Management:
- ✅ Add/Edit/Delete products
- ✅ Category assignment
- ✅ Price and stock management
- ✅ Image upload support
- ✅ Status management

### Home Page Integration:
- ✅ Categories automatically fetched from database
- ✅ Products automatically fetched from database
- ✅ Changes reflect immediately on home page

## 📝 Important Notes

1. **Image Directories**: The system will automatically create these folders:
   - `images/categories/` - for category images
   - `images/products/` - for product images
   - `images/profile_pictures/` - for user profile pictures (already exists)

2. **Icon Classes**: Use RemixIcon classes for category icons:
   - Format: `ri-icon-name-line`
   - Examples: `ri-gift-line`, `ri-stack-line`, `ri-map-pin-line`
   - Browse icons: https://remixicon.com/

3. **Admin Access**: Only users with `role = 'admin'` can access admin pages

4. **Security**: Make sure to:
   - Use strong passwords for admin accounts
   - Keep admin credentials secure
   - Regularly backup your database

## 🔧 Troubleshooting

### Issue: Can't login as admin
- **Solution**: Make sure the user's `role` field is set to `'admin'` in the database
- Check that `status = 'Active'` and `is_verified = 'active'`

### Issue: Categories/Products not showing on home page
- **Solution**: Make sure categories/products have `status = 'active'` in the database

### Issue: Images not uploading
- **Solution**: Check folder permissions (should be 755 or 777)
- Make sure `images/categories/` and `images/products/` folders exist

## 📞 Support

If you encounter any issues, check:
1. Database connection in `database/db_connection.php`
2. File permissions for image uploads
3. PHP error logs
4. Database table structure matches the SQL script

---

**Happy Admin Managing! 🎉**

