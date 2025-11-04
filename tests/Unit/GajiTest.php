<?php

beforeEach(function () {
    require __DIR__ . '/../../database/koneksi.php';
    $this->koneksi = $koneksi;

    // Pastikan ada minimal 1 karyawan untuk referensi
    $check = mysqli_query($this->koneksi, "SELECT id, nama FROM data_karyawan LIMIT 1");
    if (mysqli_num_rows($check) === 0) {
        mysqli_query($this->koneksi, "INSERT INTO data_karyawan (nama, jabatan, alamat, no_telp)
            VALUES ('Dummy Karyawan', 'Tester', 'Jl. Dummy', '08111111111')");
    }
});

test('can insert gaji karyawan', function () {
    $result = mysqli_query($this->koneksi, "SELECT id FROM data_karyawan LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    $id_karyawan = $row['id'];

    $bulan = "November 2025";
    $gaji_pokok = 3000000;
    $tunjangan = 500000;
    $potongan = 200000;
    $total_gaji = $gaji_pokok + $tunjangan - $potongan;

    $query = "INSERT INTO gaji_karyawan (id_karyawan, bulan, gaji_pokok, tunjangan, potongan, total_gaji)
              VALUES ('$id_karyawan', '$bulan', '$gaji_pokok', '$tunjangan', '$potongan', '$total_gaji')";
    $insert = mysqli_query($this->koneksi, $query);

    expect($insert)->toBeTrue();

    $this->id_gaji = mysqli_insert_id($this->koneksi);
    $check = mysqli_query($this->koneksi, "SELECT * FROM gaji_karyawan WHERE id_gaji = '$this->id_gaji'");
    expect(mysqli_num_rows($check))->toBe(1);
});

test('can update gaji karyawan', function () {
    $find = mysqli_query($this->koneksi, "SELECT id_gaji FROM gaji_karyawan ORDER BY id_gaji DESC LIMIT 1");
    $row = mysqli_fetch_assoc($find);
    $id_gaji = $row['id_gaji'] ?? null;

    expect($id_gaji)->not->toBeNull();

    $bulan_baru = "Desember 2025";
    $query = "UPDATE gaji_karyawan SET bulan='$bulan_baru' WHERE id_gaji='$id_gaji'";
    $update = mysqli_query($this->koneksi, $query);

    expect($update)->toBeTrue();

    $check = mysqli_query($this->koneksi, "SELECT bulan FROM gaji_karyawan WHERE id_gaji='$id_gaji'");
    $data = mysqli_fetch_assoc($check);
    expect($data['bulan'])->toBe($bulan_baru);
});

test('can delete gaji karyawan', function () {
    $find = mysqli_query($this->koneksi, "SELECT id_gaji FROM gaji_karyawan ORDER BY id_gaji DESC LIMIT 1");
    $row = mysqli_fetch_assoc($find);
    $id_gaji = $row['id_gaji'] ?? null;

    expect($id_gaji)->not->toBeNull();

    $delete = mysqli_query($this->koneksi, "DELETE FROM gaji_karyawan WHERE id_gaji='$id_gaji'");
    expect($delete)->toBeTrue();

    $check = mysqli_query($this->koneksi, "SELECT * FROM gaji_karyawan WHERE id_gaji='$id_gaji'");
    expect(mysqli_num_rows($check))->toBe(0);
});
