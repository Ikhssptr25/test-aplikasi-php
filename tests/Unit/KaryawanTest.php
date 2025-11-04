<?php

beforeEach(function () {
    require __DIR__ . '/../../database/koneksi.php';
    $this->koneksi = $koneksi;
});

test('can insert karyawan data', function () {
    $nama    = "Unit Test";
    $jabatan = "Tester";
    $alamat  = "Jl. Test";
    $no_telp = "081234567890";

    $query = "INSERT INTO data_karyawan (nama, jabatan, alamat, no_telp) VALUES ('$nama', '$jabatan', '$alamat', '$no_telp')";
    $result = mysqli_query($this->koneksi, $query);

    expect($result)->toBeTrue();

    $this->inserted_id = mysqli_insert_id($this->koneksi);
    expect($this->inserted_id)->toBeGreaterThan(0);
});

test('can update karyawan data', function () {
    $nama_baru = "Unit Test Updated";

    // Ambil ID dari test sebelumnya
    $result = mysqli_query($this->koneksi, "SELECT id FROM data_karyawan ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    $id = $row['id'] ?? null;

    expect($id)->not->toBeNull();

    $query = "UPDATE data_karyawan SET nama='$nama_baru' WHERE id='$id'";
    $update = mysqli_query($this->koneksi, $query);

    expect($update)->toBeTrue();

    $check = mysqli_query($this->koneksi, "SELECT nama FROM data_karyawan WHERE id='$id'");
    $data = mysqli_fetch_assoc($check);
    expect($data['nama'])->toBe($nama_baru);
});

test('can delete karyawan data', function () {
    $result = mysqli_query($this->koneksi, "SELECT id FROM data_karyawan ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    $id = $row['id'] ?? null;

    expect($id)->not->toBeNull();

    $delete = mysqli_query($this->koneksi, "DELETE FROM data_karyawan WHERE id='$id'");
    expect($delete)->toBeTrue();

    $check = mysqli_query($this->koneksi, "SELECT * FROM data_karyawan WHERE id='$id'");
    expect(mysqli_num_rows($check))->toBe(0);
});
