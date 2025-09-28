<?php
// login.php
ob_start();
?>


<style>
    body {
        background: #f3f4f6;
        font-family: 'Poppins', sans-serif;
    }

    .login-wrapper {
        max-width: 900px;
        margin: auto;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        display: flex;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        background: #fff;
    }

    /* Left Panel */
    .login-left {
        flex: 1;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        padding: 30px;
    }
    .login-left h1 {
        font-weight: 700;
        font-size: 2rem;
    }

    /* Right Panel (Form) */
    .login-right {
        flex: 1;
        padding: 50px 40px;
    }

    .login-right h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #111827;
    }
    .login-right p {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 25px;
    }

    /* Inputs - Underline Style */
    .form-control {
        border: none;
        border-bottom: 2px solid #e5e7eb;
        border-radius: 0;
        box-shadow: none;
        padding: 10px 0;
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: none;
    }

    /* Button */
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

    /* Footer Links */
    .login-footer {
        margin-top: 20px;
        font-size: 0.85rem;
        color: #6b7280;
    }
    .login-footer a {
        color: #6366f1;
        font-weight: 500;
        text-decoration: none;
    }
    .login-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        
        <!-- Left Panel -->
        <div class="login-left">
            <h1><i class="fa-solid fa-shirt me-2"></i> Kids-Korner</h1>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <h2>We are <span style="color:#8b5cf6;">Kids-Korner</span></h2>
            <p>Welcome back! Log in to your account to continue shopping.</p>

            <form id="loginForm" method="post" action="check_login.php">
                <div class="mb-4">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                </div>

                <div class="mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>

                <button type="submit" class="btn w-100 btn-kapadabazar">Log in</button>
            </form>

            <div class="login-footer text-center mt-4">
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
