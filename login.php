<?php
// login.php
ob_start();
?>

<style>
    body {
        
        font-family: 'Poppins', sans-serif;
    }

    .login-wrapper {
        max-width: 1100px;
        margin: auto;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        display: flex;
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        background: #fffdf9;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    /* Left Side - Banner with background image */
    .login-left {
        flex: 1;
        background: url("designing_pages_images/baby-image.png") center/cover no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 30px;
    }

    .login-left .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.35); /* dark overlay */
    }

    .login-left h1 {
        position: relative;
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 10px;
        z-index: 1;
    }

    /* Right Side - Form */
    .login-right {
        flex: 1.3;
        padding: 40px;
    }

    .login-right h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-align: center;
        color: #222;
    }

    .login-right p {
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

    .login-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 0.9rem;
        color: #555;
    }

    .login-footer a {
        color: #b8735c;
        font-weight: 500;
        text-decoration: none;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    @media(max-width: 768px) {
        .login-card {
            flex-direction: column;
        }
        .login-left {
            height: 200px;
        }
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        
        <!-- Left Panel with background image -->
        <div class="login-left">
            <div class="overlay"></div>
            <h1>Welcome Back!</h1>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <h2>Login to KidsKorner</h2>
            <p>Welcome back! Please enter your details to continue.</p>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #fecaca;">
                    <?php 
                    echo htmlspecialchars($_SESSION['error_message']); 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="check_login.php">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-kidskorner">Log in</button>
            </form>

            <div class="login-footer">
                <p>Don’t have an account? <a href="register.php">Register Now</a></p>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
