<?php
session_start();
include_once "../database/koneksi.php";

// ============================
// CEK SESSION
// ============================
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("error: Silakan login terlebih dahulu");
}

// ============================
// PASTIKAN METHOD POST DAN CSRF
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("error: Method not allowed");
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Invalid CSRF token");
}

// ============================
// AMBIL & BERSIHKAN INPUT
// ============================
$id_gaji     = isset($_POST['id_gaji']) ? (int)$_POST['id_gaji'] : 0;
$id_karyawan = isset($_POST['id_karyawan']) ? (int)$_POST['id_karyawan'] : 0;
$bulan       = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
$tahun       = isset($_POST['tahun']) ? (int)$_POST['tahun'] : 0;
$gaji_pokok  = str_replace(',', '.', trim($_POST['gaji_pokok'] ?? '0'));
$tunjangan   = str_replace(',', '.', trim($_POST['tunjangan'] ?? '0'));
$potongan    = str_replace(',', '.', trim($_POST['potongan'] ?? '0'));

// ============================
// VALIDASI INPUT
// ============================
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

if ($id_gaji <= 0 || $id_karyawan <= 0) exit("error: Data tidak valid");
if (!in_array($bulan, $bulan_list)) exit("error: Bulan tidak valid");
if ($tahun < 2000 || $tahun > 2100) exit("error: Tahun tidak valid");

function validasi_decimal($nilai, $field) {
    if (!is_numeric($nilai)) exit("error: $field harus berupa angka");
    $nilai = round((float)$nilai, 2);
    if ($nilai < 0) exit("error: $field tidak boleh negatif");
    if ($nilai > 9999999999.99) exit("error: $field terlalu besar");
    return $nilai;
}

$gaji_pokok = validasi_decimal($gaji_pokok, "Gaji Pokok");
$tunjangan  = validasi_decimal($tunjangan, "Tunjangan");
$potongan   = validasi_decimal($potongan, "Potongan");

$total_gaji = max(0, $gaji_pokok + $tunjangan - $potongan);
$total_gaji = round($total_gaji, 2);

// ============================
// CEK KARYAWAN ADA
// ============================
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if(mysqli_stmt_num_rows($stmt) === 0) { 
    echo "error: Karyawan tidak ditemukan"; 
    mysqli_stmt_close($stmt);
    exit; 
}
mysqli_stmt_close($stmt);

// ============================
// CEK DUPLIKAT PERIODE (kecuali record ini)
// ============================
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

// ============================
// UPDATE DATA GAJI
// ============================
$stmt = mysqli_prepare($koneksi, "UPDATE gaji_karyawan 
                                  SET bulan=?, tahun=?, gaji_pokok=?, tunjangan=?, potongan=?, total_gaji=? 
                                  WHERE id_gaji=?");
mysqli_stmt_bind_param($stmt, "sdddddi", $bulan, $tahun, $gaji_pokok, $tunjangan, $potongan, $total_gaji, $id_gaji);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
