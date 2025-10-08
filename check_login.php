<?php
session_start();
include_once('database/db_connection.php');
include_once('mailer.php');

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if user exists
    $query = "SELECT * FROM registration WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if user is verified
        if ($user['is_verified'] == 'active' && $user['status'] == 'Active') {
            // User is verified, allow login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_profile_picture'] = $user['profile_picture'];
            $_SESSION['user_verified'] = true;
            
            echo "<script>alert('Login successful!'); window.location.href='user_dashbord.php';</script>";
        } else {
            // User is not verified, send verification email
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
                        <h2 style='color: #b8735c;'>Welcome to Kids-Korner!</h2>
                        <p>Hello " . htmlspecialchars($user['fullname']) . ",</p>
                        <p>Thank you for registering with Kids-Korner. To complete your registration and access your account, please verify your email address by clicking the button below:</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$verification_link' style='background-color: #b8735c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a>
                        </div>
                        
                        <p>Or copy and paste this link into your browser:</p>
                        <p style='word-break: break-all; color: #666;'>$verification_link</p>
                        
                        <p><strong>Note:</strong> This verification link will expire in 24 hours.</p>
                        
                        <p>If you didn't create an account with Kids-Korner, please ignore this email.</p>
                        
                        <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                        <p style='color: #666; font-size: 12px;'>This email was sent from Kids-Korner. Please do not reply to this email.</p>
                    </div>
                </body>
                </html>
            ";
            
            $email_sent = sendEmail($email, $email_subject, $email_body);
            
            if ($email_sent === true) {
                echo "<script>alert('Your account is not verified. A verification email has been sent to your email address. Please check your inbox and click the verification link.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Error sending verification email. Please try again later.'); window.location.href='login.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('Please fill in all fields!'); window.location.href='login.php';</script>";
}
?>
