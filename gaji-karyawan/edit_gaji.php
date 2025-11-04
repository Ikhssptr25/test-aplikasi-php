<?php
include_once "../database/koneksi.php";

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

