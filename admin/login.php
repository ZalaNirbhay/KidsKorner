<?php
session_start();
include_once('../database/db_connection.php');

$error = "";

// Admin login logic (update query as needed)
if (isset($_POST['email'], $_POST['password'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM registration WHERE email='$email' AND password='$password' AND role='admin'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        if ($admin['is_verified'] === 'active' && $admin['status'] === 'Active') {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['fullname'];
            $_SESSION['admin_role'] = 'admin';

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Your admin account is not verified.";
        }
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - KidsKorner</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<style>
html, body {
    height: 100vh;
    width: 100vw;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

:root {
    --primary-color: #6a11cb;
    --secondary-color: #2575fc;
    --bg-light: #f4f7f9;
    --text-dark: #333;
    --text-muted: #666;
    --input-border: #ddd;
    --box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}

/* background like quiz game */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f0f4f8;
    background-image: radial-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px),
                      radial-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
    background-position: 0 0, 10px 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* card */
.quiz-card {
    background-color: #fff;
    border-radius: 20px;
    box-shadow: var(--box-shadow);
    text-align: center;
    position: relative;
    width: 90%;
    max-width: 450px;
    padding: 30px;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.quiz-card::before {
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    width: 150px;
    height: 150px;
    background: rgba(106, 17, 203, 0.1);
    border-radius: 30% 70% 70% 30%;
    animation: blob1 15s infinite linear;
}

.quiz-card::after {
    content: '';
    position: absolute;
    bottom: -30px;
    right: -30px;
    width: 200px;
    height: 200px;
    background: rgba(37, 117, 252, 0.1);
    border-radius: 70% 30% 30% 70%;
    animation: blob2 12s infinite linear;
}

@keyframes blob1 {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes blob2 {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(-360deg); }
}

.quiz-card > * {
    position: relative;
    z-index: 2;
}

/* title */
.quiz-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 10px;
}
.quiz-title span { color: var(--secondary-color); }

/* label */
.form-label {
    text-align: left;
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 6px;
}

/* inputs */
.form-control {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #ccc;
    background: #f7faff;
    font-size: 1rem;
    outline: none;
    transition: 0.3s;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.25);
    background: #fff;
}

/* button */
.btn-quiz {
    width: 100%;
    padding: 14px;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
    transition: 0.3s;
}

.btn-quiz:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(75, 0, 200, 0.3);
}

/* error box */
.alert {
    background: #fde8e8;
    border-left: 5px solid #ff4d4d;
    padding: 12px;
    font-size: 0.9rem;
    color: #c0392b;
    margin-bottom: 15px;
    border-radius: 8px;
}

/* animations */
.anim-group { opacity: 0; transform: translateY(20px); }

</style>
</head>

<body>

<div class="quiz-card">

    <h2 class="quiz-title">Admin <span>Login</span></h2>

    <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group anim-group">
            <label class="form-label">Admin Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="form-group anim-group">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="anim-group">
            <button class="btn-quiz" type="submit">Login</button>
        </div>
        <div class="anim-group">
            <button class="btn-quiz" type="button" onclick="window.location.href='../index.php'">Back Home</button>
        </div>
    </form>
</div>

<script>
anime({
    targets: '.anim-group',
    opacity: [0,1],
    translateY: [20, 0],
    delay: anime.stagger(120, { start: 300 }),
    duration: 850,
    easing: 'easeOutQuad'
});

anime({
    targets: '.quiz-card',
    scale: [0.95, 1],
    opacity: [0, 1],
    duration: 1000,
    easing: 'spring(1, 80, 10, 0)'
});
</script>

</body>
</html>
