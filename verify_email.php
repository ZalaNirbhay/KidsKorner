<?php
session_start();
include_once('database/db_connection.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and is not expired
    $query = "SELECT * FROM registration WHERE verification_token = '$token' AND verification_expires > NOW()";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Update user status to active
        $update_query = "UPDATE registration SET is_verified = 'active', status = 'Active', verification_token = NULL, verification_expires = NULL WHERE verification_token = '$token'";
        
        if (mysqli_query($con, $update_query)) {
            // Set session variables for automatic login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_profile_picture'] = $user['profile_picture'];
            $_SESSION['user_verified'] = true;
            
            $success_message = "Email verified successfully! Welcome to Kids-Korner!";
        } else {
            $error_message = "Error updating account status. Please try again.";
        }
    } else {
        $error_message = "Invalid or expired verification link. Please request a new verification email.";
    }
} else {
    $error_message = "No verification token provided.";
}
ob_start();
?>

<style>
    body {
        background: #7da6a1;
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .verification-card {
        background: #fffdf9;
        border-radius: 14px;
        padding: 40px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-align: center;
        max-width: 500px;
        width: 100%;
    }

    .verification-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .success-icon {
        color: #28a745;
    }

    .error-icon {
        color: #dc3545;
    }

    .verification-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #222;
    }

    .verification-message {
        font-size: 1rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .btn-kidskorner {
        background: #b8735c;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s ease;
    }

    .btn-kidskorner:hover {
        background: #9a5b45;
        color: #fff;
        text-decoration: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s ease;
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: #fff;
        text-decoration: none;
    }
</style>

<div class="verification-card">
    <?php if (isset($success_message)): ?>
        <div class="verification-icon success-icon">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <h2 class="verification-title">Email Verified!</h2>
        <p class="verification-message"><?php echo $success_message; ?></p>
        <a href="user_dashbord.php" class="btn-kidskorner">Go to Dashboard</a>
    <?php elseif (isset($error_message)): ?>
        <div class="verification-icon error-icon">
            <i class="fa-solid fa-exclamation-triangle"></i>
        </div>
        <h2 class="verification-title">Verification Failed</h2>
        <p class="verification-message"><?php echo $error_message; ?></p>
        <a href="login.php" class="btn-kidskorner">Go to Login</a>
        <a href="resend_verification.php" class="btn-secondary">Resend Verification</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
