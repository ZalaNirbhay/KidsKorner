<?php
session_start();
include_once('../database/db_connection.php');

// Check if already logged in as admin
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'admin') {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if user exists and is admin
    $query = "SELECT * FROM registration WHERE email = '$email' AND password = '$password' AND role = 'admin'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Check if admin is verified
        if ($admin['is_verified'] == 'active' && $admin['status'] == 'Active') {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['fullname'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Your account is not verified. Please verify your email first.";
        }
    } else {
        $error = "Invalid credentials or you don't have admin access!";
    }
}
ob_start();
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .admin-login-wrapper {
        max-width: 450px;
        width: 100%;
    }

    .admin-login-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .admin-login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .admin-login-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }

    .admin-login-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .admin-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
        color: #ffffff;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        outline: none;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-admin {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-admin:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .alert-error {
        background: #fee2e2;
        color: #dc2626;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        border-left: 4px solid #dc2626;
    }

    .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .back-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <div class="admin-login-header">
            <div class="admin-icon">
                <i class="ri-shield-user-line"></i>
            </div>
            <h1>Admin Login</h1>
            <p>Access the admin dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="admin@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="admin_login" class="btn-admin">Login to Dashboard</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Back to Home</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
// Don't include layout for admin login
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - KidsKorner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>
    <?php echo $content; ?>
</body>
</html>

