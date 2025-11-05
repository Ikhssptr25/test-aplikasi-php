<?php
session_start();
include_once "../database/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit('Method not allowed');
}

// validasi CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Error: Invalid CSRF token');
}
?>

<?php
$id_gaji = $_POST['id_gaji'];
$bulan = $_POST['bulan'];
$gaji_pokok = $_POST['gaji_pokok'];
$tunjangan = $_POST['tunjangan'];
$potongan = $_POST['potongan'];

$total_gaji = $gaji_pokok + $tunjangan - $potongan;

$query = "UPDATE gaji_karyawan
          SET bulan='$bulan', gaji_pokok='$gaji_pokok', tunjangan='$tunjangan', potongan='$potongan', total_gaji='$total_gaji'
          WHERE id_gaji='$id_gaji'";

if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "Error: " . mysqli_error($koneksi);
}

