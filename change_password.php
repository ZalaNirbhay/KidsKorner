<?php
session_start();
include_once('database/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required!";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $message_type = "error";
    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters long!";
        $message_type = "error";
    } else {
        // Check old password
        $user_query = "SELECT * FROM registration WHERE id = $user_id AND password = '$old_password'";
        $user_result = mysqli_query($con, $user_query);
        
        if (mysqli_num_rows($user_result) > 0) {
            // Update password
            $update_query = "UPDATE registration SET password = '$new_password' WHERE id = $user_id";
            
            if (mysqli_query($con, $update_query)) {
                $message = "Password changed successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating password. Please try again.";
                $message_type = "error";
            }
        } else {
            $message = "Old password is incorrect!";
            $message_type = "error";
        }
    }
}

ob_start();
?>

<style>
    .change-password-page {
        padding: 4rem 0;
        background: #f9fafb;
        min-height: 70vh;
    }

    .password-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .password-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: #6b7280;
        margin-bottom: 2rem;
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

    .form-group {
        margin-bottom: 1.5rem;
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
        border-color: #b8735c;
    }

    .btn-primary {
        background: #b8735c;
        color: #ffffff;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }

    .btn-primary:hover {
        background: #9a5b45;
    }

    .back-link {
        text-align: center;
        margin-top: 1.5rem;
    }

    .back-link a {
        color: #b8735c;
        text-decoration: none;
        font-weight: 500;
    }

    .back-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="change-password-page">
    <div class="password-container">
        <div class="password-card">
            <h1 class="page-title">Change Password</h1>
            <p class="page-subtitle">Update your account password</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label class="form-label" for="old_password">Current Password *</label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password *</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                    <small style="color: #6b7280;">Password must be at least 6 characters long</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password *</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                <button type="submit" name="change_password" class="btn-primary">Change Password</button>
            </form>

            <div class="back-link">
                <a href="user_dashbord.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once("layout.php");
?>

