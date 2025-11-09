<?php
session_start();

// Check if admin or user
$is_admin = isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'admin';

// Destroy all session data
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect based on user type
if ($is_admin) {
    header("Location: admin_login.php");
} else {
    header("Location: login.php");
}
exit();
?>
