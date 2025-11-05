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

// 🧪 Negative Test Case Section

test('cannot insert karyawan dengan data kosong', function () {
    $query = "INSERT INTO data_karyawan (nama, jabatan, alamat, no_telp)
              VALUES ('', '', '', '')";

    // KITA HARAPKAN INI GAGAL!
    try {
        mysqli_query($this->koneksi, $query);

        // Jika kode sampai sini, berarti INSERT berhasil (ITU BUG!)
        $this->fail('Query INSERT seharusnya gagal karena check constraint');

    } catch (mysqli_sql_exception $e) {
        
        // Jika kode sampai sini, INSERT GAGAL (INI YANG KITA MAU!)
        // Kita pastikan gagalnya karena alasan yang benar.
        expect($e->getMessage())->toContain('Check constraint');
        expect($e->getMessage())->toContain('is violated');
    }
});

test('cannot update karyawan dengan id tidak valid', function () {
    $id_tidak_ada = 999999;
    $query = "UPDATE data_karyawan SET nama='Nama Tidak Valid' WHERE id='$id_tidak_ada'";

    $update = mysqli_query($this->koneksi, $query);
    // mysqli_query akan return true walau tidak ada baris berubah, jadi kita cek affected rows
    expect(mysqli_affected_rows($this->koneksi))->toBe(0);
});

test('cannot delete karyawan dengan id tidak valid', function () {
    $id_tidak_ada = 999999;
    $delete = mysqli_query($this->koneksi, "DELETE FROM data_karyawan WHERE id='$id_tidak_ada'");

    expect(mysqli_affected_rows($this->koneksi))->toBe(0);
});
