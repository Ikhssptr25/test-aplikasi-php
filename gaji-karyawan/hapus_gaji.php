<?php
session_start();
include_once "../database/koneksi.php";

// ============================
// PASTIKAN METHOD POST & CSRF
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
// AMBIL & VALIDASI INPUT
// ============================
$id_gaji = isset($_POST['id_gaji']) ? (int)$_POST['id_gaji'] : 0;
if ($id_gaji <= 0) {
    exit("error: ID gaji tidak valid");
}

// ============================
// DELETE DATA DENGAN PREPARED STATEMENT
// ============================
$stmt = mysqli_prepare($koneksi, "DELETE FROM gaji_karyawan WHERE id_gaji=?");
mysqli_stmt_bind_param($stmt, "i", $id_gaji);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
