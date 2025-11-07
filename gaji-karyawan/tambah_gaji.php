<?php
session_start();
include_once "../database/koneksi.php";

// Pastikan request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Error: Invalid CSRF token');
}

// Ambil input
$id_karyawan = isset($_POST['id_karyawan']) ? (int)$_POST['id_karyawan'] : 0;
$bulan       = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
$tahun       = isset($_POST['tahun']) ? (int)$_POST['tahun'] : 0;
$gaji_pokok  = isset($_POST['gaji_pokok']) ? trim($_POST['gaji_pokok']) : '0';
$tunjangan   = isset($_POST['tunjangan']) ? trim($_POST['tunjangan']) : '0';
$potongan    = isset($_POST['potongan']) ? trim($_POST['potongan']) : '0';

// Ganti koma dengan titik untuk desimal
$gaji_pokok = str_replace(',', '.', $gaji_pokok);
$tunjangan  = str_replace(',', '.', $tunjangan);
$potongan   = str_replace(',', '.', $potongan);

// Validasi input
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

if ($id_karyawan <= 0) { echo "error: Pilih karyawan"; exit; }
if (!in_array($bulan, $bulan_list)) { echo "error: Bulan tidak valid"; exit; }
if ($tahun < 2000 || $tahun > 2100) { echo "error: Tahun tidak valid"; exit; }

// Fungsi validasi decimal (maks 12 digit, 2 desimal)
function validasi_decimal($nilai, $field) {
    if (!is_numeric($nilai)) {
        exit("error: $field harus berupa angka");
    }

    $nilai = round((float)$nilai, 2);

    if ($nilai < 0) {
        exit("error: $field tidak boleh negatif");
    }

    if ($nilai > 9999999999.99) {
        exit("error: $field terlalu besar");
    }

    return $nilai;
}

// Validasi gaji, tunjangan, potongan
$gaji_pokok = validasi_decimal($gaji_pokok, "Gaji Pokok");
$tunjangan  = validasi_decimal($tunjangan, "Tunjangan");
$potongan   = validasi_decimal($potongan, "Potongan");

// Hitung total gaji
$total_gaji = $gaji_pokok + $tunjangan - $potongan;
if ($total_gaji < 0) $total_gaji = 0; // total gaji minimal 0
$total_gaji = round($total_gaji, 2);

// Cek karyawan
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

// Cek apakah periode gaji sudah ada
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM gaji_karyawan WHERE id_karyawan=? AND bulan=? AND tahun=?");
mysqli_stmt_bind_param($stmt, "isi", $id_karyawan, $bulan, $tahun);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "error: Gaji untuk periode ini sudah ada";
    mysqli_stmt_close($stmt);
    exit;
}
mysqli_stmt_close($stmt);

// Insert data gaji
$stmt = mysqli_prepare($koneksi, "INSERT INTO gaji_karyawan (id_karyawan, bulan, tahun, gaji_pokok, tunjangan, potongan, total_gaji) VALUES (?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($stmt, "issdddd", $id_karyawan, $bulan, $tahun, $gaji_pokok, $tunjangan, $potongan, $total_gaji);

if(mysqli_stmt_execute($stmt)){ 
    echo "success"; 
} else { 
    echo "error: " . mysqli_error($koneksi); 
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
