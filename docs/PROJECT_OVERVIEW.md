# Kids-Korner Platform Documentation

_Last updated: 2025-11-20_

> This document is the single source of truth for how every feature on the site works.  
> Whenever functionality is added or changed, update this file in the same pull request.

---

## 1. Platform Overview

- **Purpose:** Full-stack kids apparel storefront with customer shopping flows and a secure admin back office.
- **Stack:** PHP 8+, MySQL, Vanilla JS, Bootstrap 5, Remix Icons.
- **Key Concepts:** Session-based auth, email verification, OTP-protected security changes, persistent carts/wishlists, order management lifecycles.

---

## 2. Directory Structure

```
Kids-Korner/
├── admin/                     # Complete admin console (see §6)
│   ├── dashboard.php
│   ├── login.php
│   ├── categories.php
│   ├── products.php
│   ├── users.php
│   └── orders.php
├── auth/                      # (reserved for future auth-specific controllers)
├── database/                  # DB helpers + schema scripts
├── docs/                      # Living documentation (this file, setup guides, etc.)
├── helpers/                   # Shared PHP helpers (e.g., OTP utilities)
├── images/, js/, assets/...   # Static assets
├── layout.php                 # Global layout + header/footer/nav logic
├── index.php                  # Home / featured collections
├── category.php               # Dynamic category catalogue
├── products.php, sale.php     # Additional listing pages
├── cart.php, add_to_cart.php  # Cart UI + AJAX endpoints
├── wishlist.php, add/remove   # Wishlist flows
├── login.php, register.php    # Customer authentication
├── user_dashbord.php          # Customer dashboard overview
├── order_history.php          # Customer-facing order tracking
├── edit_profile.php           # Profile + secure password change
├── forgot_password.php        # OTP-based password reset
├── logout.php                 # Unified logout handler
└── ...                        # Remaining feature-specific pages
```

---

## 3. Shared Infrastructure

### 3.1 Database Connection
- `database/db_connection.php` centralizes the MySQL connection (localhost/root/root → `Kids_Korner` DB).
- Every PHP surface includes this file exactly once with `include_once`.

### 3.2 Layout + Header (`layout.php`)
- Provides the site-wide HTML skeleton, meta tags, CSS utility imports.
- Renders:
  - Search bar (static placeholder today).
  - Icon tray (Cart, Wishlist, Account, Admin). Admin icon auto-routes to:
    - `admin/dashboard.php` when an admin session exists.
    - `admin/login.php` for anonymous access.
  - Dynamic “Shop by Category” nav built from `categories` table.
  - Footer with helpful links and newsletter form.
- Injects page-specific content via `$content = ob_get_clean(); include 'layout.php';`.

### 3.3 Session & Auth
- Customer sessions use `$_SESSION['user_id|email|name|profile_picture']`.
- Admin sessions use `$_SESSION['admin_id|admin_name|admin_role']`.
- `logout.php` destroys the session and:
  - Sends admins to `admin/login.php`.
  - Sends customers to `login.php`.

### 3.4 Email + OTP
- `mailer.php` wraps PHPMailer with Gmail SMTP (app-password protected).
- `helpers/otp_helper.php` encapsulates all OTP logic:
  - Creates `password_reset_codes` table on demand.
  - Stores hashed 6-digit codes with expiry + purpose.
  - Prevents reuse by flagging records as used.
  - Powers both **profile password change** and **forgot password** flows.

---

## 4. Customer-Facing Functionality

### 4.1 Home + Catalogue (`index.php`, `category.php`, `products.php`, `sale.php`)
| Area | How it works | Key Files |
|------|--------------|-----------|
| Hero Banner | Static messaging rendered from `index.php`; CTA can deep-link to curated list. | `index.php` |
| Category Grid | Queries all `categories` with `status='active'`. Each tile links to `category.php?id=...`, falls back to default imagery if no upload. | `index.php`, `category.php` |
| Featured Collections | Fetches three newest `products` with `status='active'`. Each card embeds wishlist/cart actions, price comparisons, discount badge, stock indicator, and login-to-shop state. | `index.php`, `add_to_cart.php`, `add_to_wishlist.php` |
| Category Detail | Validates `id` param, loads description + header banner, then all products in that category. Provides login prompt when anonymous, includes add-to-cart buttons + stock callouts + rich description snippet. | `category.php` |
| Global Listings | `products.php` (all products) and `sale.php` (promotional filter) reuse the same card components and share JS hooks for cart/wishlist. | `products.php`, `sale.php` |

### 4.2 Authentication & Profiles
#### Registration Flow (`register.php`)
1. Validates all required fields (fullname, email, password/confirm, gender, mobile, address).
2. Ensures email uniqueness via `registration` lookup.
3. Forces profile photo upload with type/size validation, stores in `images/profile_pictures/`.
4. Generates verification + user tokens, inserts inactive user record.
5. Moves uploaded file (creating folder if needed) and sends email with 24h verification link.

#### Login Flow (`login.php` + `check_login.php`)
1. Form posts to `check_login.php`.
2. Script checks email/password pair and ensures `role='User'`, `status='Active'`, `is_verified='active'`.
3. If user is inactive, refreshes verification token and re-sends email.
4. On success, session variables `user_id`, `user_name`, `user_email`, `user_profile_picture` are set and user redirects to `user_dashbord.php`.

#### Profile Dashboard (`user_dashbord.php`)
- Gated by session guard; otherwise redirect to login.
- Computes cart count, wishlist count, and orders count (if `orders` table exists).
- Hero cards link to cart/wishlist/order history.
- Recent orders panel shows placeholder until real data exists.
- Sidebar links unify navigation to profile editing, security, cart, wishlist, history, logout.

#### Edit Profile (`edit_profile.php`)
1. Loads current profile details; handles unauthorized access by redirect.
2. On update:
   - Sanitizes inputs and rebuilds update statement.
   - Handles optional profile-photo replacement (validates, moves file, purges previous image).
   - Updates `registration` and session variables.
3. Security section:
   - “Send OTP” button triggers `kk_generate_otp` with purpose `password_change`.
   - Password form requires OTP, new password + confirm; verifies OTP via `kk_verify_otp` before updating DB.

### 4.3 Shopping Tools
#### Wishlist (`wishlist.php`, `add_to_wishlist.php`, `remove_from_wishlist.php`)
- Requires login for add/remove; anonymous users get redirect alerts.
- AJAX endpoint toggles record in `wishlist` table and returns JSON flag so UI can update heart icon state.
- `wishlist.php` renders tabular view with remove action and quick add-to-cart.

#### Cart (`cart.php`, `add_to_cart.php`, `remove_from_cart`)
1. Add-to-cart endpoint:
   - Validates login + product existence + stock > 0.
   - Inserts or increments `cart` row per user/product.
   - Returns JSON for on-card notifications.
2. Cart page:
   - Queries joined product info (name, description, image, price, stock).
   - Exposes quantity form; updates constrained by available stock.
   - Removal link deletes row via GET `?remove=` parameter.
   - Calculates subtotal + fixed `$10` shipping; displays summary.
3. Checkout section:
   - Prefills contact data from `registration` table.
   - Requires address lines, city, state, postal code, phone.
   - Radio selects COD vs UPI; UPI selection reveals additional input.
   - On submit, `kk_ensure_order_tables()` ensures `orders` + `order_items` exist.
   - Generates order number, inserts order + items, clears cart, surfaces confirmation message.
   - Payment status defaults to `paid` for UPI or `pending` for COD.

### 4.4 Orders & Tracking
#### Customer Order History (`order_history.php`)
- Only accessible after login; otherwise redirect to login.
- Pulls all orders sorted newest-first; each order also fetches sub-items.
- Timeline rendering:
  - Steps: Pending → Processing → Shipped → Delivered.
  - Completed vs current vs upcoming states highlight accordingly.
- Cards also show:
  - Order metadata (number, placed date, payment status).
  - Item breakdown with quantity and per-line totals.
  - Delivery block (full shipping address, phone, payment method, UPI reference if present).
- Empty state encourages shopping when no orders exist.

### 4.5 Password Recovery (`forgot_password.php`)
1. **OTP request form:**
   - Accepts registered email, looks up user, generates OTP via helper, emails code.
   - Stores `$_SESSION['password_reset_email']` for convenience.
2. **Reset form:**
   - Requires email, OTP, new password, confirm password.
   - Validates lengths/match, verifies OTP with purpose `forgot_password`.
   - Updates `registration.password` on success, clears session hint, and shows confirmation.

### 4.6 Email Verification (`verify_email.php`, `resend_verification.php`, `setup_verification.php`)
- `register.php` seeds `verification_token` and expiry.
- Verification link hits `verify_email.php?token=...`, which activates account if token is valid.
- `resend_verification.php` re-issues fresh token with new expiry and emails the link.
- `setup_verification.php` contains SQL migration helper for adding the verification-related columns/indexes.

### 4.7 Security Enhancements
- `helpers/otp_helper.php` is the only place generating/verifying OTPs, preventing duplicated logic.
- Passwords currently stored in plain text (legacy) — flagged for future hashing (see TODO).
- Session guards exist on every sensitive page; unauthorized users are always redirected to login.
- All file uploads enforce MIME/size checks and write into dedicated directories.

---

## 5. Data & Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `registration` | Stores all users (customers + admins) | `role`, `status`, `is_verified`, `profile_picture` |
| `categories` | Product taxonomy | `name`, `description`, `image`, `icon`, `status` |
| `products` | Catalogue items | `category_id`, `price`, `stock`, `status`, `image` |
| `cart` | User cart entries | `user_id`, `product_id`, `quantity`, timestamps |
| `wishlist` | User wishlist entries | `user_id`, `product_id` |
| `orders` | Orders placed from checkout | `order_number`, `user_id`, `payment_method`, `status`, address fields, totals |
| `order_items` | Line items for each order | `order_id`, `product_id`, `name`, `price`, `quantity` |
| `password_reset_codes` | OTP storage | `user_id`, `email`, `otp_hash`, `purpose`, `expires_at`, `is_used` |

> See `database/*.sql` for schema bootstrap scripts.

---

## 6. Admin Console (`/admin`)

| File | Description | Key Features |
|------|-------------|--------------|
| `login.php` | Dedicated admin login | Verifies role `admin`, ensures verified + active account, redirects to dashboard |
| `dashboard.php` | High-level KPIs | Category/product/user stats, quick actions, sticky sidebar navigation |
| `categories.php` | CRUD for categories | Add/edit/delete with icon + image upload, status toggles |
| `products.php` | CRUD for products | Category assignment, pricing, stock, images, bulk list view |
| `users.php` | User directory | Lists all customers, statuses, verification flags, avatars |
| `orders.php` | Full order management | Status + payment updates, customer + shipping info, item detail per order |

**Persistent Header Actions**
- Present on every admin page: welcome label, Dashboard shortcut, “View Website” (opens `/index.php` in new tab), and logout (calls shared `../logout.php`).

**Access Control**
- Every admin page starts with the same guard:
  ```php
  if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
      header("Location: login.php");
      exit;
  }
  ```

**Orders Workflow**
- Admin selects a status (Pending, Processing, Shipped, Delivered, Cancelled) and payment state (Pending, Paid, Failed, Refunded).
- Submitting the inline form updates the master `orders` row.
- Customer-facing history reflects field changes immediately (shared DB source).

---

## 7. Email & Notifications

| Trigger | Sender | Template Highlights |
|---------|--------|---------------------|
| Registration | `mailer.php` via Gmail SMTP | 24h verification link |
| Profile password change | `edit_profile.php` + OTP helper | 6-digit OTP, 10-minute validity |
| Forgot password | `forgot_password.php` + OTP helper | Recovery OTP, instructions |

> SMTP credentials live inside `mailer.php`. For production, move to environment variables or secrets management.

---

## 8. Deployment & Maintenance Notes

1. **PHP Sessions:** Ensure `session.save_path` is writable; every page referencing `$_SESSION` calls `session_start()` guard.
2. **File Permissions:** Upload directories (`images/products`, `images/categories`, `images/profile_pictures`) must be writable by PHP user.
3. **Cron/Cleanup (optional):** `password_reset_codes` stores historical entries—periodically purge expired/used rows if desired.
4. **Backups:** Version the `database/*.sql` files whenever table structures change (e.g., OTP table, orders table).
5. **Future Enhancements:** Suggested placeholders for metrics dashboards, email notifications on status changes, search integration, etc.

---

## 9. Change Log Template

When you ship a change, document it here:

```
### [YYYY-MM-DD] Short Title
- What changed?
- Which files are involved?
- How do users/admins experience it?
- Were any schema updates required?
```

Add the newest entry at the top of this section for quick reference.

---

## 10. Support & Contacts

- **Primary Maintainer:** _Fill in name/email here_
- **Escalation:** _Add secondary contacts_
- **Issue Tracking:** _Link to ticketing system / Git repo issues_

---

_Keep this document accurate. If a feature exists in code, its behavior must be captured here._

