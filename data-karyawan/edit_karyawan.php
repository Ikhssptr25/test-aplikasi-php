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
// CEK SESSION (harus login admin)
// ============================
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("error: Silakan login sebagai admin");
}

// ============================
// VALIDASI CSRF
// ============================
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Token CSRF tidak valid");
}

// ============================
// AMBIL & BERSIHKAN INPUT
// ============================
$id      = isset($_POST['id']) ? (int) trim($_POST['id']) : 0;
$nama    = trim($_POST['nama'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');

// ============================
// VALIDASI INPUT
// ============================
if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) exit("error: Nama hanya boleh huruf dan spasi");
if (!preg_match('/^[a-zA-Z\s]+$/', $jabatan)) exit("error: Jabatan hanya boleh huruf dan spasi");
if (!preg_match('/^[a-zA-Z0-9\s\.,\-\/#]{3,}$/', $alamat)) exit("error: Alamat tidak valid");
if (!preg_match('/^628\d{7,10}$/', $no_telp)) exit("error: Nomor telepon harus diawali 628");

// ============================
// CEK KARYAWAN ADA
// ============================
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    exit("error: Karyawan tidak ditemukan");
}
mysqli_stmt_close($stmt);

// ============================
// CEK DUPLIKAT NOMOR TELEPON
// ============================
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE no_telp=? AND id<>?");
mysqli_stmt_bind_param($stmt, "si", $no_telp, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    exit("error: Nomor telepon sudah terdaftar");
}
mysqli_stmt_close($stmt);

// ============================
// UPDATE DATA KARYAWAN
// ============================
$stmt = mysqli_prepare(
    $koneksi,
    "UPDATE data_karyawan 
     SET nama=?, jabatan=?, alamat=?, no_telp=? 
     WHERE id=?"
);
mysqli_stmt_bind_param($stmt, "ssssi", $nama, $jabatan, $alamat, $no_telp, $id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
