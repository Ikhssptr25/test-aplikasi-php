<?php
session_start(); // Pastikan session aktif

// ============================
// HAPUS SEMUA SESSION
// ============================
$_SESSION = [];

// ============================
// HAPUS COOKIE SESSION
// ============================
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ============================
// HAPUS COOKIE "REMEMBER ME"
// ============================
if (isset($_COOKIE['remember_email'])) {
    setcookie("remember_email", "", time() - 3600, "/");
}

// ============================
// HANCURKAN SESSION DI SERVER
// ============================
session_destroy();

// ============================
// REDIRECT KE LOGIN
// ============================
header("Location: login.php");
exit;
?>
