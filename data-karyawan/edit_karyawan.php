<?php
include_once "../database/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Ambil dan bersihkan input
    $id      = (int) trim($_POST['id']);
    $nama    = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $alamat  = trim($_POST['alamat']);
    $no_telp = trim($_POST['no_telp']);

    // ============================
    // VALIDASI INPUT
    // ============================

    // Nama: hanya huruf & spasi
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        echo "error: Nama hanya boleh huruf dan spasi (tidak boleh angka atau simbol)";
        exit;
    }

    // Jabatan: hanya huruf & spasi
    if (!preg_match('/^[a-zA-Z\s]+$/', $jabatan)) {
        echo "error: Jabatan hanya boleh huruf dan spasi (tidak boleh angka atau simbol)";
        exit;
    }

    // Alamat: huruf, angka, spasi, titik, koma, minus, slash /, #
    if (!preg_match('/^[a-zA-Z0-9\s\.,\-\/#]{3,}$/', $alamat)) {
        echo "error: Alamat tidak valid, minimal 3 karakter dan hanya boleh huruf, angka, spasi, titik, koma, minus, slash /, atau #";
        exit;
    }

    // Nomor telepon: harus diawali 628 dan 10-13 digit
    if (!preg_match('/^628\d{7,10}$/', $no_telp)) {
        echo "error: Nomor telepon harus diawali dengan 628 dan diikuti 10-13 digit angka";
        exit;
    }

    // ============================
    // CEK KARYAWAN ADA
    // ============================

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

    // ============================
    // CEK UNIK NAMA & NO_TELP (kecuali dirinya sendiri)
    // ============================

    $stmt = mysqli_prepare($koneksi, 
        "SELECT 1 FROM data_karyawan WHERE (nama=? OR no_telp=?) AND id<>?"
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

    // ============================
    // UPDATE DATA
    // ============================

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
