<?php
include_once "../database/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Ambil dan bersihkan input
    $id      = (int) trim($_POST['id']);
    $nama    = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $alamat  = trim($_POST['alamat']);
    $no_telp = trim($_POST['no_telp']);

    // ✅ Validasi nama hanya huruf + spasi (tanpa angka & tanpa simbol)
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        echo "error: Nama hanya boleh huruf dan spasi (tidak boleh angka atau simbol)";
        exit;
    }

    // ✅ Validasi nomor telepon
    if (!preg_match('/^628\d{7,10}$/', $no_telp)) {
        echo "error: Nomor telepon harus diawali dengan 628 dan diikuti 6-13 digit angka";
        exit;
    }

    // ✅ Cek apakah karyawan ada
    $stmt = mysqli_prepare($koneksi, "SELECT 1 FROM data_karyawan WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 0) {
        echo "error: Karyawan tidak ditemukan";
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);

    // ✅ Cek unik nama & no_telp (kecuali dirinya sendiri)
    $stmt = mysqli_prepare($koneksi, 
        "SELECT 1 FROM data_karyawan 
         WHERE (nama=? OR no_telp=?) AND id<>?"
    );
    mysqli_stmt_bind_param($stmt, "ssi", $nama, $no_telp, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "error: Nama atau nomor telepon sudah terdaftar";
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);

    // ✅ Update data
    $stmt = mysqli_prepare($koneksi,
        "UPDATE data_karyawan 
         SET nama=?, jabatan=?, alamat=?, no_telp=? 
         WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssssi", $nama, $jabatan, $alamat, $no_telp, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>
