<?php
include "../database/koneksi.php";
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_karyawan = $_POST['nama_karyawan'];
    $bulan = $_POST['bulan'];
    $gaji_pokok = $_POST['gaji_pokok'];
    $tunjangan = $_POST['tunjangan'];
    $potongan = $_POST['potongan'];

    // Cari ID karyawan berdasarkan nama
    $result = mysqli_query($koneksi, "SELECT id FROM data_karyawan WHERE nama='$nama_karyawan'");
    $data = mysqli_fetch_assoc($result);
    $id_karyawan = $data['id'];

    if (!$id_karyawan) {
        die("Error: Karyawan tidak ditemukan!");
    }

    $total_gaji = $gaji_pokok + $tunjangan - $potongan;

    $query = "INSERT INTO gaji_karyawan (id_karyawan, bulan, gaji_pokok, tunjangan, potongan, total_gaji)
              VALUES ('$id_karyawan', '$bulan', '$gaji_pokok', '$tunjangan', '$potongan', '$total_gaji')";
    
    if (mysqli_query($koneksi, $query)) {
         echo "success";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
