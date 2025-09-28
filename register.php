<?php
include_once('database/db_connection.php');
if (isset($_POST['regbtn'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $profile_photo = uniqid() . $_FILES['profile_photo']['name'];
    $profile_photo_tmp = $_FILES['profile_photo']['tmp_name'];

    $q = "INSERT INTO `registration`(`fullname`, `email`, `password`, `mobile`, `gender`, `profile_picture`, `address`) 
          VALUES ('$fullname','$email','$password',$mobile,'$gender','$profile_photo','$address')";

    if (mysqli_query($con, $q)) {
        if (!is_dir("images/profile_pictures")) {
            mkdir("images/profile_pictures");
        }
        move_uploaded_file($profile_photo_tmp, "images/profile_pictures/" . $profile_photo);
    }

    echo "<script>window.location.href='register.php';</script>";
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
