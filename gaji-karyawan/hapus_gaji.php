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
