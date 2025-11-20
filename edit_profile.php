<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('database/db_connection.php');
include_once('mailer.php');
include_once('helpers/otp_helper.php');

$user_id = (int) $_SESSION['user_id'];
$profile_message = '';
$profile_message_type = '';
$password_message = '';
$password_message_type = '';

function kk_get_user(mysqli $con, int $userId): ?array
{
    $result = mysqli_query($con, "SELECT * FROM registration WHERE id = {$userId} LIMIT 1");
    return $result ? mysqli_fetch_assoc($result) : null;
}

$user = kk_get_user($con, $user_id);

if (!$user) {
    echo "<script>alert('Unable to load profile details.'); window.location.href='logout.php';</script>";
    exit;
}

if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($con, trim($_POST['fullname']));
    $mobile = mysqli_real_escape_string($con, trim($_POST['mobile']));
    $gender = mysqli_real_escape_string($con, trim($_POST['gender']));
    $address = mysqli_real_escape_string($con, trim($_POST['address']));

    if (!$fullname || !$mobile || !$gender || !$address) {
        $profile_message = "All profile fields are required.";
        $profile_message_type = "error";
    } else {
        $updates = [
            "fullname = '{$fullname}'",
            "mobile = '{$mobile}'",
            "gender = '{$gender}'",
            "address = '{$address}'"
        ];

        if (!empty($_FILES['profile_photo']['name'])) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['profile_photo']['type'];
            $file_size = $_FILES['profile_photo']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $profile_message = "Please upload a valid image file (JPG, PNG, GIF).";
                $profile_message_type = "error";
            } elseif ($file_size > 5 * 1024 * 1024) {
                $profile_message = "Profile picture must be less than 5MB.";
                $profile_message_type = "error";
            } else {
                $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                $profile_photo = uniqid('profile_', true) . '.' . $file_extension;
                $upload_dir = "images/profile_pictures/";

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $profile_photo;

                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                    if (!empty($user['profile_picture']) && file_exists($upload_dir . $user['profile_picture'])) {
                        @unlink($upload_dir . $user['profile_picture']);
                    }
                    $updates[] = "profile_picture = '{$profile_photo}'";
                    $_SESSION['user_profile_picture'] = $profile_photo;
                } else {
                    $profile_message = "Error uploading profile picture. Please try again.";
                    $profile_message_type = "error";
                }
            }
        }

        if (empty($profile_message)) {
            $update_sql = "UPDATE registration SET " . implode(', ', $updates) . " WHERE id = {$user_id}";
            if (mysqli_query($con, $update_sql)) {
                $_SESSION['user_name'] = $fullname;
                $profile_message = "Profile updated successfully.";
                $profile_message_type = "success";
                $user = kk_get_user($con, $user_id);
            } else {
                $profile_message = "An error occurred while saving your profile.";
                $profile_message_type = "error";
            }
        }
    }
}

if (isset($_POST['send_password_otp'])) {
    $otpData = kk_generate_otp($con, $user_id, $user['email'], 'password_change');
    if ($otpData) {
        $subject = "Your Kids-Korner Password Change OTP";
        $body = "
            <p>Hello " . htmlspecialchars($user['fullname']) . ",</p>
            <p>Use the OTP below to change your Kids-Korner password. This code will expire in 10 minutes.</p>
            <h2 style='letter-spacing: 4px;'>{$otpData['code']}</h2>
            <p>If you did not request this, please ignore the email.</p>
        ";
        $sent = sendEmail($user['email'], $subject, $body);
        if ($sent === true) {
            $password_message = "OTP sent to your registered email address.";
            $password_message_type = "success";
        } else {
            $password_message = "Failed to send OTP. Please try again.";
            $password_message_type = "error";
        }
    } else {
        $password_message = "Could not generate OTP. Please try again.";
        $password_message_type = "error";
    }
}

if (isset($_POST['update_password'])) {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $otp_code = trim($_POST['otp_code'] ?? '');

    if (!$new_password || !$confirm_password || !$otp_code) {
        $password_message = "All password fields are required.";
        $password_message_type = "error";
    } elseif (strlen($new_password) < 6) {
        $password_message = "New password must be at least 6 characters.";
        $password_message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $password_message = "New password and confirmation do not match.";
        $password_message_type = "error";
    } else {
        $verification = kk_verify_otp($con, $user_id, $user['email'], 'password_change', $otp_code);
        if ($verification['success']) {
            $sanitized_password = mysqli_real_escape_string($con, $new_password);
            if (mysqli_query($con, "UPDATE registration SET password = '{$sanitized_password}' WHERE id = {$user_id}")) {
                $password_message = "Password updated successfully.";
                $password_message_type = "success";
            } else {
                $password_message = "Unable to update password. Please try again.";
                $password_message_type = "error";
            }
        } else {
            $password_message = $verification['message'];
            $password_message_type = "error";
        }
    }
}

ob_start();
?>

<style>
    .profile-page {
        background: #f9fafb;
        padding: 4rem 0;
    }

    .profile-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .profile-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .profile-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .profile-card p.subtitle {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 600;
        color: #374151;
    }

    .form-control,
    .form-select,
    textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #b8735c;
        outline: none;
    }

    textarea {
        min-height: 120px;
        resize: vertical;
    }

    .btn-primary {
        background: #b8735c;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 0.9rem 1.5rem;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: #9a5b45;
    }

    .btn-ghost {
        background: #111827;
        margin-top: 0.75rem;
    }

    .profile-photo-preview {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f3f4f6;
        margin-bottom: 0.5rem;
    }

    .alert {
        padding: 0.85rem 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 992px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="profile-page">
    <div class="profile-container">
        <div class="profile-card">
            <h2>Profile Details</h2>
            <p class="subtitle">Update your personal info and profile photo.</p>

            <?php if ($profile_message): ?>
                <div class="alert alert-<?php echo $profile_message_type; ?>">
                    <?php echo htmlspecialchars($profile_message); ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select class="form-select" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Profile Photo</label>
                    <div>
                        <?php
                        $profile_pic_path = !empty($user['profile_picture'])
                            ? "images/profile_pictures/" . $user['profile_picture']
                            : 'default_avatar.php';
                        ?>
                        <img src="<?php echo $profile_pic_path; ?>" alt="Profile" class="profile-photo-preview">
                    </div>
                    <input type="file" class="form-control" name="profile_photo" accept="image/*">
                </div>
                <button type="submit" class="btn-primary">Save Changes</button>
            </form>
        </div>

        <div class="profile-card" id="security">
            <h2>Security</h2>
            <p class="subtitle">Change your password using email OTP verification.</p>

            <?php if ($password_message): ?>
                <div class="alert alert-<?php echo $password_message_type; ?>">
                    <?php echo htmlspecialchars($password_message); ?>
                </div>
            <?php endif; ?>

            <form method="post" style="margin-bottom: 1.5rem;">
                <input type="hidden" name="send_password_otp" value="1">
                <button type="submit" class="btn-primary">Send OTP to Email</button>
            </form>

            <form method="post">
                <input type="hidden" name="update_password" value="1">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label>OTP Code</label>
                    <input type="text" class="form-control" name="otp_code" placeholder="Enter 6-digit code" required>
                </div>
                <button type="submit" class="btn-primary btn-ghost">Update Password</button>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include_once('layout.php');
?>

