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

    // ✅ Validasi nama hanya boleh huruf + spasi (tanpa angka & simbol)
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        echo "error: Nama hanya boleh mengandung huruf dan spasi (tidak boleh angka atau simbol)";
        exit;
    }

    // ✅ Validasi nomor telepon
    if (!preg_match('/^628\d{6,12}$/', $no_telp)) {
        echo "error: Nomor telepon harus diawali dengan 628 dan diikuti 6-12 digit angka";
        exit;
    }

    // ✅ Cek nama dan no_telp unik
    $stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE nama=? OR no_telp=?");
    mysqli_stmt_bind_param($stmt, "ss", $nama, $no_telp);
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
