<?php
include_once "../database/koneksi.php";
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_gaji = $_POST['id_gaji'];
    $query = "DELETE FROM gaji_karyawan WHERE id_gaji='$id_gaji'";

    if (mysqli_query($koneksi, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }
}
?>
