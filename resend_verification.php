<?php
include_once('database/db_connection.php');
include_once('mailer.php');

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Check if user exists
    $query = "SELECT * FROM registration WHERE email = '$email'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if user is already verified
        if ($user['is_verified'] == 'active') {
            echo "<script>alert('Your account is already verified!'); window.location.href='login.php';</script>";
            exit;
        }
        
        // Generate new verification token
        $verification_token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Update verification token
        $update_query = "UPDATE registration SET verification_token = '$verification_token', verification_expires = '$expires' WHERE email = '$email'";
        mysqli_query($con, $update_query);
        
        // Send verification email
        $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify_email.php?token=" . $verification_token;
        
        $email_subject = "Verify Your Kids-Korner Account";
        $email_body = "
            <html>
            <head>
                <title>Email Verification</title>
            </head>
            <body>
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #b8735c;'>Verify Your Kids-Korner Account</h2>
                    <p>Hello " . htmlspecialchars($user['fullname']) . ",</p>
                    <p>You requested a new verification email. To complete your registration and access your account, please verify your email address by clicking the button below:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$verification_link' style='background-color: #b8735c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a>
                    </div>
                    
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #666;'>$verification_link</p>
                    
                    <p><strong>Note:</strong> This verification link will expire in 24 hours.</p>
                    
                    <p>If you didn't request this verification email, please ignore this message.</p>
                    
                    <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                    <p style='color: #666; font-size: 12px;'>This email was sent from Kids-Korner. Please do not reply to this email.</p>
                </div>
            </body>
            </html>
        ";
        
        $email_sent = sendEmail($email, $email_subject, $email_body);
        
        if ($email_sent === true) {
            echo "<script>alert('Verification email sent successfully! Please check your inbox and click the verification link.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error sending verification email. Please try again later.'); window.location.href='resend_verification.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found in our records!'); window.location.href='resend_verification.php';</script>";
    }
}
ob_start();
?>

<style>
    body {
        background: #7da6a1;
        font-family: 'Poppins', sans-serif;
    }

    .resend-wrapper {
        max-width: 500px;
        margin: auto;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .resend-card {
        background: #fffdf9;
        border-radius: 14px;
        padding: 40px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        width: 100%;
    }

    .resend-card h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-align: center;
        color: #222;
    }

    .resend-card p {
        text-align: center;
        color: #666;
        margin-bottom: 25px;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.95rem;
        margin-bottom: 18px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #b8735c;
        outline: none;
    }

    .btn-kidskorner {
        background: #b8735c;
        color: #fff;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.3s ease;
    }

    .btn-kidskorner:hover {
        background: #9a5b45;
    }

    .resend-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 0.9rem;
        color: #555;
    }

    .resend-footer a {
        color: #b8735c;
        font-weight: 500;
        text-decoration: none;
    }

    .resend-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="resend-wrapper">
    <div class="resend-card">
        <h2>Resend Verification Email</h2>
        <p>Enter your email address to receive a new verification link</p>

        <form method="post">
            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
            <button type="submit" class="btn-kidskorner">Send Verification Email</button>
        </form>

        <div class="resend-footer">
            <p>Remember your password? <a href="login.php">Log In</a></p>
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
