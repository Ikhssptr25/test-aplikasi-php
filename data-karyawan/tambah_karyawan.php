<?php
include_once "../database/koneksi.php";
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $alamat  = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    $sql = "INSERT INTO data_karyawan (nama, jabatan, alamat, no_telp) VALUES ('$nama','$jabatan','$alamat','$no_telp')";
    if (mysqli_query($koneksi, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
}
?>
