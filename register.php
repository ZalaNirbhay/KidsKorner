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
    // $token = bin2hex(random_bytes(50));

    $q = "INSERT INTO `registration`(`fullname`, `email`, `password`, `mobile_number`, `gender`, `profile_picture`, `address`) VALUES ('$fullname','$email','$password',$mobile,'$gender','$profile_photo','$address')";

    if (mysqli_query($con, $q)) {
        if (!is_dir("images/profile_pictures")) {
            mkdir("images/profile_pictures");
            move_uploaded_file($profile_photo_tmp, "d:\laragon\www\projectes\Kids-Korner\images\profile_pictures" . $profile_photo);
        }
    } else {
        move_uploaded_file($profile_photo_tmp, "images/profile_pictures/" . $profile_photo);
    }

?>
    <script>
        window.location.href = "register.php";
    </script>
<?php
}
ob_start();
?>

<style>
    body {
        background: #f3f4f6;
        font-family: 'Poppins', sans-serif;
    }

    .register-wrapper {
        max-width: 1100px;
        margin: auto;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-card {
        display: flex;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .register-left {
        flex: 1;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        padding: 30px;
        text-align: center;
    }

    .register-left h1 {
        font-size: 2rem;
        font-weight: 700;
    }

    .register-right {
        flex: 2;
        padding: 40px;
    }

    .register-right h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #111827;
    }

    .form-control,
    .form-select,
    textarea {
        border: none;
        border-bottom: 2px solid #e5e7eb;
        border-radius: 0;
        box-shadow: none;
        padding: 10px 0;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: none;
    }

    .btn-kapadabazar {
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: #fff;
        border: none;
        padding: 12px;
        font-weight: 600;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .btn-kapadabazar:hover {
        background: linear-gradient(135deg, #6366f1, #4338ca);
    }

    .register-footer {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #6b7280;
        text-align: center;
    }

    .register-footer a {
        color: #6366f1;
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
            padding: 20px;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <!-- Left Panel -->
        <div class="register-left">
            <h1><i class="fa-solid fa-user-plus me-2"></i> Welcome To Kids Korner</h1>
        </div>

        <!-- Right Panel -->
        <div class="register-right">
            <h2>Create your account</h2>
            <p class="text-muted">Fill in the details to start shopping with us.</p>

            <form method="post" action="register.php" enctype="multipart/form-data" id="registerForm">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="fullname" placeholder="Full Name"
                                data-validation="required alpha">
                            <span class="error text-danger" id="fullnameError"></span>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email Address"
                                data-validation="required email">
                            <span class="error text-danger" id="emailError"></span>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" id="password"
                                placeholder="Password" data-validation="required strongPassword min max"
                                data-min="8" data-max="25">
                            <span class="error text-danger" id="passwordError"></span>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Confirm Password" data-validation="required confirmPassword"
                                data-password-id="password">
                            <span class="error text-danger" id="confirm_passwordError"></span>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <select class="form-select" name="gender" data-validation="required">
                                <option value="" selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <span class="error text-danger" id="genderError"></span>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="mobile" placeholder="Mobile Number"
                                data-validation="required numeric min max" data-min="10" data-max="10">
                            <span class="error text-danger" id="mobileError"></span>
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control" name="profile_photo"
                                data-validation="required file filesize" data-filesize="200">
                            <span class="error text-danger" id="profile_photoError"></span>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="address" rows="3" placeholder="Address"
                                data-validation="required"></textarea>
                            <span class="error text-danger" id="addressError"></span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12">
                        <button type="submit" class="btn w-100 btn-kapadabazar" name="regbtn">Register</button>
                        <div class="register-footer">
                            <p class="mb-0 mt-3">Already have an account ? <a href="login.php">Login Now</a></p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>