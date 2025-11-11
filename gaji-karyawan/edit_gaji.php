<?php
session_start();
include_once "../database/koneksi.php";

// CEK SESSION
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("error: Silakan login terlebih dahulu");
}

// METHOD POST & CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("error: Method not allowed");
}
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit("error: Invalid CSRF token");
}

// AMBIL INPUT
$token      = $_POST['ftoken'] ?? '';
$bulan      = trim($_POST['bulan'] ?? '');
$tahun      = (int)($_POST['tahun'] ?? 0);
$gaji_pokok = str_replace(',', '.', trim($_POST['gaji_pokok'] ?? '0'));
$tunjangan  = str_replace(',', '.', trim($_POST['tunjangan'] ?? '0'));
$potongan   = str_replace(',', '.', trim($_POST['potongan'] ?? '0'));

// VALIDASI TOKEN
if (!isset($_SESSION['edit_gaji_tokens'][$token])) {
    exit("error: Token edit tidak valid");
}
$token_data = $_SESSION['edit_gaji_tokens'][$token];
if ($token_data['expires'] < time()) {
    unset($_SESSION['edit_gaji_tokens'][$token]);
    exit("error: Token edit sudah kadaluarsa");
}
$id_gaji = (int)$token_data['id_gaji'];
unset($_SESSION['edit_gaji_tokens'][$token]); // hapus token setelah dipakai

// VALIDASI INPUT
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
if (!in_array($bulan, $bulan_list)) {
    exit("error: Bulan tidak valid");
}
if ($tahun < 2000 || $tahun > 2100) {
    exit("error: Tahun tidak valid");
}

function validasiDecimal($nilai, $field) {
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

$gaji_pokok = validasiDecimal($gaji_pokok, "Gaji Pokok");
$tunjangan  = validasiDecimal($tunjangan, "Tunjangan");
$potongan   = validasiDecimal($potongan, "Potongan");
$total_gaji = max(0, $gaji_pokok + $tunjangan - $potongan);
$total_gaji = round($total_gaji, 2);

// CEK GAJI ADA & AMBIL ID KARYAWAN
$stmt = mysqli_prepare($koneksi, "SELECT id_karyawan FROM gaji_karyawan WHERE id_gaji=?");
mysqli_stmt_bind_param($stmt, "i", $id_gaji);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_karyawan);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!isset($id_karyawan)) {
    exit("error: Gaji tidak ditemukan");
}

// CEK DUPLIKAT PERIODE (kecuali record ini)
$stmt = mysqli_prepare($koneksi, "SELECT 1 FROM gaji_karyawan WHERE id_karyawan=? AND bulan=? AND tahun=? AND id_gaji<>?");
mysqli_stmt_bind_param($stmt, "isii", $id_karyawan, $bulan, $tahun, $id_gaji);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    exit("error: Gaji untuk periode ini sudah ada");
}
mysqli_stmt_close($stmt);

// UPDATE DATA
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
?>
