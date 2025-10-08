<?php
include_once('database/db_connection.php');
include_once('mailer.php');

if (isset($_POST['regbtn'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    // Handle profile picture upload
    $profile_photo = '';
    $profile_photo_tmp = '';
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_photo']['type'];
        $file_size = $_FILES['profile_photo']['size'];
        
        // Validate file type
        if (in_array($file_type, $allowed_types)) {
            // Validate file size (max 5MB)
            if ($file_size <= 5 * 1024 * 1024) {
                $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                $profile_photo = uniqid() . '_' . time() . '.' . $file_extension;
                $profile_photo_tmp = $_FILES['profile_photo']['tmp_name'];
            } else {
                echo "<script>alert('Profile picture size should be less than 5MB!'); window.location.href='register.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Please upload a valid image file (JPG, PNG, GIF)!'); window.location.href='register.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please select a profile picture!'); window.location.href='register.php';</script>";
        exit;
    }

    // Validate password confirmation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='register.php';</script>";
        exit;
    }

    // Check if email already exists
    $check_email = "SELECT * FROM registration WHERE email = '$email'";
    $email_result = mysqli_query($con, $check_email);
    
    if (mysqli_num_rows($email_result) > 0) {
        echo "<script>alert('Email already exists! Please use a different email.'); window.location.href='register.php';</script>";
        exit;
    }

    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Generate a separate token for the token field (can be used for other purposes)
    $user_token = bin2hex(random_bytes(16));
    
    $q = "INSERT INTO `registration`(`fullname`, `email`, `password`, `mobile`, `gender`, `profile_picture`, `address`, `status`, `role`, `token`, `is_verified`, `verification_token`, `verification_expires`) 
          VALUES ('$fullname','$email','$password',$mobile,'$gender','$profile_photo','$address','Inactive','User','$user_token','inactive','$verification_token','$expires')";

    if (mysqli_query($con, $q)) {
        // Create directory if it doesn't exist
        if (!is_dir("images/profile_pictures")) {
            mkdir("images/profile_pictures", 0777, true);
        }
        
        // Move uploaded file to the directory
        $upload_path = "images/profile_pictures/" . $profile_photo;
        if (move_uploaded_file($profile_photo_tmp, $upload_path)) {
            // File uploaded successfully
            // Set proper permissions
            chmod($upload_path, 0644);
        } else {
            echo "<script>alert('Error uploading profile picture! Please try again.'); window.location.href='register.php';</script>";
            exit;
        }
        
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
                    <p>Hello " . htmlspecialchars($fullname) . ",</p>
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
            echo "<script>alert('Registration successful! Please check your email and click the verification link to activate your account.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Registration successful, but there was an error sending the verification email. Please contact support.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Registration failed! Please try again.'); window.location.href='register.php';</script>";
    }
}
ob_start();
?>

<style>
    body {
        background: #7da6a1; /* soft muted teal */
        font-family: 'Poppins', sans-serif;
    }

    .register-wrapper {
        max-width: 1100px;
        margin: auto;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-card {
        display: flex;
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        background: #fffdf9;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    /* Left Side - Banner with background image */
    .register-left {
        flex: 1;
        background: url("designing_pages_images/baby-image.png") center/cover no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 30px;
    }

    .register-left .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.35); /* dark overlay for readability */
    }

    .register-left h1 {
        position: relative;
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 10px;
        z-index: 1;
    }

    /* Right Side - Form */
    .register-right {
        flex: 1.3;
        padding: 40px;
    }

    .register-right h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-align: center;
        color: #222;
    }

    .register-right p {
        text-align: center;
        color: #666;
        margin-bottom: 25px;
    }

    .form-control,
    .form-select,
    textarea {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.95rem;
        margin-bottom: 18px;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #b8735c; /* KidsKorner theme */
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

    .register-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 0.9rem;
        color: #555;
    }

    .register-footer a {
        color: #b8735c;
        font-weight: 500;
        text-decoration: none;
    }

    .register-footer a:hover {
        text-decoration: underline;
    }

    @media(max-width: 768px) {
        .register-card {
            flex-direction: column;
        }
        .register-left {
            height: 200px;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <!-- Left Panel with background image -->
        <div class="register-left">
            <div class="overlay"></div>
            <h1>Welcome to KidsKorner</h1>
        </div>

        <!-- Right Panel with form -->
        <div class="register-right">
            <h2>Join the KidsKorner Family!</h2>
            <p>Create your account and start shopping with us</p>

            <form method="post" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <div class="col-lg-6">
                        <select class="form-select" name="gender" required>
                            <option value="" selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="mobile" placeholder="Mobile Number" required>
                        <input type="file" class="form-control" name="profile_photo" required>
                        <textarea class="form-control" name="address" rows="3" placeholder="Address" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-kidskorner" name="regbtn">Create Account</button>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
