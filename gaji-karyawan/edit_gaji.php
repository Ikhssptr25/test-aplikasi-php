<?php
session_start();
include_once "../database/koneksi.php";

// Pastikan request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Error: Invalid CSRF token');
}

// Ambil dan bersihkan input
$id_gaji     = isset($_POST['id_gaji']) ? (int)$_POST['id_gaji'] : 0;
$id_karyawan = isset($_POST['id_karyawan']) ? (int)$_POST['id_karyawan'] : 0;
$bulan       = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
$tahun       = isset($_POST['tahun']) ? (int)$_POST['tahun'] : 0;
$gaji_pokok  = isset($_POST['gaji_pokok']) ? (int)$_POST['gaji_pokok'] : 0;
$tunjangan   = isset($_POST['tunjangan']) ? (int)$_POST['tunjangan'] : 0;
$potongan    = isset($_POST['potongan']) ? (int)$_POST['potongan'] : 0;

// Validasi input
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
if ($id_gaji <= 0 || $id_karyawan <= 0) { echo "error: Data tidak valid"; exit; }
if (!in_array($bulan, $bulan_list)) { echo "error: Bulan tidak valid"; exit; }
if ($tahun < 2000 || $tahun > 2100) { echo "error: Tahun tidak valid"; exit; }
if ($gaji_pokok < 0 || $tunjangan < 0 || $potongan < 0) { echo "error: Nilai tidak boleh negatif"; exit; }

// Cek karyawan ada
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) === 0) {
    echo "error: Karyawan tidak ditemukan";
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Cek apakah periode gaji sudah ada untuk karyawan yang sama (kecuali record saat ini)
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM gaji_karyawan WHERE id_karyawan=? AND bulan=? AND tahun=? AND id_gaji<>?");
mysqli_stmt_bind_param($stmt, "isii", $id_karyawan, $bulan, $tahun, $id_gaji);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "error: Gaji untuk periode ini sudah ada";
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Hitung total gaji
$total_gaji = max(0, $gaji_pokok + $tunjangan - $potongan);

// Update gaji menggunakan prepared statement
$stmt = mysqli_prepare($koneksi, "UPDATE gaji_karyawan 
                                  SET bulan=?, tahun=?, gaji_pokok=?, tunjangan=?, potongan=?, total_gaji=? 
                                  WHERE id_gaji=?");
mysqli_stmt_bind_param($stmt, "siiiiii", $bulan, $tahun, $gaji_pokok, $tunjangan, $potongan, $total_gaji, $id_gaji);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
