<?php
session_start();
include_once "../database/koneksi.php";

// Pastikan hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("error: Metode tidak diizinkan");
}

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("error: Silakan login terlebih dahulu");
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Token CSRF tidak valid");
}

// Ambil dan bersihkan input
$id = isset($_POST['id']) ? (int) trim($_POST['id']) : 0;

// Cek apakah karyawan ada
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    exit("error: Karyawan tidak ditemukan");
}
mysqli_stmt_close($stmt);

// Hapus data
$stmt = mysqli_prepare($koneksi, "DELETE FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
