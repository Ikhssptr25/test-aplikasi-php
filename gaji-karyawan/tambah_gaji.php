<?php
include_once "../database/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") { echo "error: Metode tidak valid"; exit; }

// Ambil & sanitize
$id_karyawan = isset($_POST['id_karyawan']) ? (int)$_POST['id_karyawan'] : 0;
$bulan = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
$gaji_pokok = isset($_POST['gaji_pokok']) ? (int)$_POST['gaji_pokok'] : 0;
$tunjangan = isset($_POST['tunjangan']) ? (int)$_POST['tunjangan'] : 0;
$potongan = isset($_POST['potongan']) ? (int)$_POST['potongan'] : 0;

// validasi dasar
if ($id_karyawan <= 0) { echo "error: Pilih karyawan"; exit; }
if ($bulan === '') { echo "error: Isi bulan"; exit; }
if ($gaji_pokok < 0 || $tunjangan < 0 || $potongan < 0) { echo "error: Nilai tidak boleh negatif"; exit; }

// pastikan karyawan ada
$stmt = mysqli_prepare($koneksi, "SELECT id FROM data_karyawan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) === 0) { echo "error: Karyawan tidak ditemukan"; exit; }
mysqli_stmt_close($stmt);

// hitung total (tidak negatif)
$total_gaji = max(0, $gaji_pokok + $tunjangan - $potongan);

// insert dengan prepared statement
$stmt = mysqli_prepare($koneksi, "INSERT INTO gaji_karyawan (id_karyawan, bulan, gaji_pokok, tunjangan, potongan, total_gaji) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isiiii", $id_karyawan, $bulan, $gaji_pokok, $tunjangan, $potongan, $total_gaji);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}
mysqli_stmt_close($stmt);

