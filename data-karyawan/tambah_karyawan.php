<?php
session_start();
include_once "../database/koneksi.php";

// ============================
// PASTIKAN METHOD POST
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("error: Metode tidak diizinkan");
}

// ============================
// CEK SESSION & CSRF
// ============================
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("error: Silakan login terlebih dahulu");
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Token CSRF tidak valid");
}

// ============================
// AMBIL & BERSIHKAN INPUT
// ============================
$nama    = trim($_POST['nama'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');

// ============================
// VALIDASI INPUT
// ============================

// Nama: hanya huruf & spasi
if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
    exit("error: Nama hanya boleh huruf dan spasi");
}

// Jabatan: hanya huruf & spasi
if (!preg_match('/^[a-zA-Z\s]+$/', $jabatan)) {
    exit("error: Jabatan hanya boleh huruf dan spasi");
}

// Alamat: minimal 3 karakter, huruf, angka, spasi, titik, koma, minus, slash /, #
if (!preg_match('/^[a-zA-Z0-9\s\.,\-\/#]{3,}$/', $alamat)) {
    exit("error: Alamat tidak valid, minimal 3 karakter dan hanya boleh huruf, angka, spasi, titik, koma, minus, slash /, atau #");
}

// Nomor telepon: harus diawali 628 dan 10-13 digit
if (!preg_match('/^628\d{7,10}$/', $no_telp)) {
    exit("error: Nomor telepon harus diawali dengan 628 dan diikuti 10-13 digit angka");
}

// ============================
// CEK DUPLIKAT NOMOR TELEPON
// ============================
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE no_telp=?");
mysqli_stmt_bind_param($stmt, "s", $no_telp);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    exit("error: Nomor telepon sudah terdaftar");
}
mysqli_stmt_close($stmt);

// ============================
// INSERT DATA
// ============================
$stmt = mysqli_prepare($koneksi, "INSERT INTO data_karyawan (nama, jabatan, alamat, no_telp) VALUES (?,?,?,?)");
mysqli_stmt_bind_param($stmt, "ssss", $nama, $jabatan, $alamat, $no_telp);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
