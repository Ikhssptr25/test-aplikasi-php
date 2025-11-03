<?php
include "../database/koneksi.php";
?>

<?php
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    $query = mysqli_query($koneksi, "UPDATE data_karyawan SET nama='$nama', jabatan='$jabatan', alamat='$alamat', no_telp='$no_telp' WHERE id='$id'");
    echo $query ? "success" : "error: " . mysqli_error($koneksi);
}
?>
