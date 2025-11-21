<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('database/db_connection.php');
include_once('mailer.php');
include_once('helpers/otp_helper.php');

$request_message = '';
$request_type = '';
$reset_message = '';
$reset_type = '';

if (isset($_POST['request_reset'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));

    if (!$email) {
        $request_message = "Please enter your registered email address.";
        $request_type = "error";
    } else {
        $user_result = mysqli_query($con, "SELECT * FROM registration WHERE email = '{$email}' LIMIT 1");
        if ($user_result && mysqli_num_rows($user_result) > 0) {
            $user = mysqli_fetch_assoc($user_result);
            $otpData = kk_generate_otp($con, (int) $user['id'], $user['email'], 'forgot_password');
            if ($otpData) {
                $subject = "Kids-Korner Password Reset Code";
                $body = "
                    <p>Hello " . htmlspecialchars($user['fullname']) . ",</p>
                    <p>We received a request to reset your password. Use the OTP below within 10 minutes.</p>
                    <h2 style='letter-spacing: 4px;'>{$otpData['code']}</h2>
                    <p>If you did not request this change, please ignore this email.</p>
                ";
                $sent = sendEmail($user['email'], $subject, $body);
                if ($sent === true) {
                    $_SESSION['password_reset_email'] = $user['email'];
                    $request_message = "OTP sent. Please check your email inbox.";
                    $request_type = "success";
                } else {
                    $request_message = "Unable to send OTP email right now. Please try again.";
                    $request_type = "error";
                }
            } else {
                $request_message = "Failed to generate OTP. Please try again.";
                $request_type = "error";
            }
        } else {
            $request_message = "No account found with that email.";
            $request_type = "error";
        }
    }
}

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['reset_email']));
    $otp_code = trim($_POST['otp_code'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!$email || !$otp_code || !$new_password || !$confirm_password) {
        $reset_message = "All fields are required.";
        $reset_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $reset_message = "New password and confirmation do not match.";
        $reset_type = "error";
    } elseif (strlen($new_password) < 6) {
        $reset_message = "Password must be at least 6 characters.";
        $reset_type = "error";
    } else {
        $user_result = mysqli_query($con, "SELECT * FROM registration WHERE email = '{$email}' LIMIT 1");
        if ($user_result && mysqli_num_rows($user_result) > 0) {
            $user = mysqli_fetch_assoc($user_result);
            $verification = kk_verify_otp($con, (int) $user['id'], $user['email'], 'forgot_password', $otp_code);
            if ($verification['success']) {
                $sanitized_password = mysqli_real_escape_string($con, $new_password);
                if (mysqli_query($con, "UPDATE registration SET password = '{$sanitized_password}' WHERE id = {$user['id']}")) {
                    $reset_message = "Password updated successfully. You can now login.";
                    $reset_type = "success";
                    unset($_SESSION['password_reset_email']);
                } else {
                    $reset_message = "Failed to update password. Please try again.";
                    $reset_type = "error";
                }
            } else {
                $reset_message = $verification['message'];
                $reset_type = "error";
            }
        } else {
            $reset_message = "No account found with that email.";
            $reset_type = "error";
        }
    }
}

$prefilled_email = $_SESSION['password_reset_email'] ?? '';

ob_start();
?>


<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .reset-wrapper {
        max-width: 900px; /* smaller card width */
        margin: auto;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .reset-card {
        display: grid;
        grid-template-columns: 1fr 0.9fr; /* right form slightly narrower */
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        background: #fffdf9;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .reset-left {
        background: url("designing_pages_images/baby-image.png") center/cover no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 25px;
    }
    .reset-left::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.35);
    }
    .reset-left h1 {
        position: relative;
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        z-index: 1;
    }

    .reset-right {
        padding: 30px 35px; /* reduced padding */
        background: #fffdf9;
    }

    .reset-right h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 8px;
        text-align: center;
        color: #222;
    }

    .reset-right p {
        text-align: center;
        color: #666;
        margin-bottom: 18px; /* reduced */
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px 12px; /* smaller inputs */
        font-size: 0.9rem;
        margin-bottom: 14px; /* reduced space */
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: #b8735c;
        outline: none;
    }

    .btn-kidskorner {
        background: #b8735c;
        color: #fff;
        border: none;
        padding: 11px;
        width: 100%;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: background 0.3s ease;
        margin-top: 5px;
    }

    .btn-kidskorner:hover {
        background: #9a5b45;
    }

    .alert {
        padding: 0.65rem 1rem;
        border-radius: 10px;
        margin-bottom: 0.9rem;
        font-size: 0.85rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
    }
    .alert-error {
       background: #fee2e2;
        color: #b91c1c;
    }

    /* Laptop screens (1000px–1300px) */
    @media (max-width: 1150px) {
        .reset-card {
            grid-template-columns: 1fr 1fr;
            max-width: 820px;
        }
    }

    @media (max-width: 900px) {
        .reset-card {
            grid-template-columns: 1fr;
        }
        .reset-left {
            min-height: 200px;
        }
    }
</style>


<div class="reset-wrapper">
    <div class="reset-card">
        <div class="reset-left">
            <h1>Reset your password securely</h1>
        </div>
        <div class="reset-right">
            <h2>Forgot Password</h2>
            <p>Request an OTP and reset your password in just two steps.</p>

            <?php if ($request_message): ?>
                <div class="alert alert-<?php echo $request_type; ?>">
                    <?php echo htmlspecialchars($request_message); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="request_reset" value="1">
                <input type="email" class="form-control" name="email" placeholder="Registered Email" required>
                <button type="submit" class="btn-kidskorner">Send OTP</button>
            </form>

            <hr style="margin: 2rem 0;">

            <?php if ($reset_message): ?>
                <div class="alert alert-<?php echo $reset_type; ?>">
                    <?php echo htmlspecialchars($reset_message); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="reset_password" value="1">
                <input type="email" class="form-control" name="reset_email" placeholder="Registered Email" value="<?php echo htmlspecialchars($prefilled_email); ?>" required>
                <input type="text" class="form-control" name="otp_code" placeholder="Enter OTP" required>
                <input type="password" class="form-control" name="new_password" placeholder="New Password" minlength="6" required>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" minlength="6" required>
                <button type="submit" class="btn-kidskorner">Reset Password</button>
            </form>

            <div style="margin-top: 1rem; text-align: center;">
                <a href="login.php" style="color: #b8735c; text-decoration: none; font-weight: 600;">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once('layout.php');
?>

