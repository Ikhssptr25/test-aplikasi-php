<?php
include_once "../database/koneksi.php";
?>

<?php
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $query = mysqli_query($koneksi, "DELETE FROM data_karyawan WHERE id='$id'");
    if ($query) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }
}
?>
