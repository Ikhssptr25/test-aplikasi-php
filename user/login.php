<?php
session_start();
include_once "../database/koneksi.php";

// ============================
// Konfigurasi brute-force
// ============================
$max_attempts = 5; // maksimal percobaan login
$lock_time    = 120; // lock 5 menit (300 detik)
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}

// cek lock
if ($_SESSION['login_attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lock_time) {
    $error = "Terlalu banyak percobaan. Coba lagi setelah beberapa menit.";
}

// ============================
// Proses login
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Prepared statement
    $stmt = mysqli_prepare($koneksi, "SELECT id_user, email, password FROM user WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            // Reset login attempts
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;

            // Simpan session
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['email']   = $row['email'];

            // Remember me dengan SameSite & HttpOnly
            if ($remember) {
                setcookie("remember_email", $email, [
                    'expires' => time() + (86400 * 7),
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }

            header("Location: ../index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }

    mysqli_stmt_close($stmt);

    // Update login attempts
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen flex flex-col justify-center items-center bg-gradient-to-b from-green-400 to-green-100 font-poppins">

    <div class="flex flex-col items-center mb-4">
        <img src="../assets/logo.png" alt="Logo" class="w-20 mb-3">
        <h1 class="text-3xl font-bold text-green-800">Welcome</h1>
    </div>

    <form method="POST" class="w-80 bg-transparent">

        <?php if (!empty($error)): ?>
            <p class="text-red-600 text-center text-sm mb-3"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($_COOKIE['remember_email'] ?? '') ?>"
               class="w-full p-2 rounded-full border border-green-300 mb-4 focus:ring focus:ring-green-200"
               placeholder="Masukkan email anda" required>

        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <input type="password" name="password"
               class="w-full p-2 rounded-full border border-green-300 mb-2 focus:ring focus:ring-green-200"
               placeholder="********" required>

        <div class="flex items-center gap-2 mb-4">
            <input type="checkbox" name="remember" class="w-4 h-4"
                   <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
            <label class="text-sm text-gray-700">Ingat saya</label>
        </div>

        <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-full hover:bg-green-700 transition">
            Masuk
        </button>
    </form>

    <footer class="absolute bottom-5 text-sm text-gray-700">
        © 2025 Intern. All rights reserved.
    </footer>

</body>
</html>
