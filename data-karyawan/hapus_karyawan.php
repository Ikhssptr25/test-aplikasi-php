<?php
session_start();
include_once "../database/koneksi.php";

// PASTIKAN METHOD POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("error: Metode tidak diizinkan");
}

// CEK SESSION (harus login admin)
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("error: Silakan login terlebih dahulu");
}

// VALIDASI CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Token CSRF tidak valid");
}

// AMBIL & VALIDASI ID
$id = isset($_POST['id']) ? (int) trim($_POST['id']) : 0;
if ($id <= 0) {
    exit("error: ID karyawan tidak valid");
}

// CEK KARYAWAN ADA
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    exit("error: Karyawan tidak ditemukan");
}
mysqli_stmt_close($stmt);

// HAPUS DATA KARYAWAN
$stmt = mysqli_prepare($koneksi, "DELETE FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
