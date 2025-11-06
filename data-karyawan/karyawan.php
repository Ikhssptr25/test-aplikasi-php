<?php
include_once "../database/koneksi.php";
$result = mysqli_query($koneksi, "SELECT * FROM data_karyawan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Data Karyawan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-b from-green-400 to-green-100 font-sans">

    <!-- Header -->
  <header class="bg-white shadow-md flex justify-between items-center px-1 py-1 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 px-12">
      <span class=" text-gray-700">Z.</span><span class="text-green-600">Corporate</span>
    </h1>
    <div class="flex items-center gap-4 mr-2">
    <img src="../assets/logo.png" alt="Logo" class="w-110 h-14 px-10 mr-0 ">
    <a href="../user/logout.php" class="flex items-center gap-2 text-black px-4 py-2 mt-5 rounded-lg text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        keluar
    </a>
</div>
  </header>

<!-- Main -->
<main class="flex-1 px-4 md:px-10 py-10">
  <div class="w-full max-w-6xl mx-auto">

    <!-- Tombol Navigasi -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
      <a href="../index.php"
         class="w-full sm:w-auto text-center bg-black text-white px-5 py-2 font-semibold hover:bg-gray-800 transition rounded-sm">
        Kembali
      </a>

      <a href="../gaji-karyawan/gaji.php"
         class="w-full sm:w-auto text-center bg-white text-green-600 border border-green-600 px-5 py-2 font-semibold hover:bg-green-50 transition rounded-sm">
        Gaji Karyawan
      </a>
    </div>

    <!-- Card Tabel -->
    <div class="bg-white shadow-lg w-full p-8 rounded-md">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="text-lg font-bold tracking-widest text-gray-800 border-b pb-2">
          KELOLA DATA KARYAWAN
        </h2>
        <button onclick="openModalTambah()"
                class="bg-green-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-green-700 transition">
          Add Data
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-gray-100 text-gray-700 border-b font-bold">
              <th class="py-3 px-4 font-semibold">No</th>
              <th class="py-3 px-4 font-semibold">Nama</th>
              <th class="py-3 px-4 font-semibold">Jabatan</th>
              <th class="py-3 px-4 font-semibold">Alamat</th>
              <th class="py-3 px-4 font-semibold">No Telp</th>
              <th class="py-3 px-4 text-center font-semibold">Action</th>
            </tr>
          </thead>
          <tbody class="text-gray-800">
            <?php
            $no = 1;
            while ($data = mysqli_fetch_array($result)) {
              echo "
              <tr class='border-b hover:bg-gray-50 transition'>
                <td class='py-2 px-3 font-semibold'>{$no}</td>
                <td class='py-2 px-3'>{$data['nama']}</td>
                <td class='py-2 px-3'>{$data['jabatan']}</td>
                <td class='py-2 px-3'>{$data['alamat']}</td>
                <td class='py-2 px-3'>{$data['no_telp']}</td>
                <td class='py-2 px-3 text-center'>
                  <button onclick='editData({$data['id']}, \"{$data['nama']}\", \"{$data['jabatan']}\", \"{$data['alamat']}\", \"{$data['no_telp']}\")'
                          class='text-green-600 hover:text-green-800 mx-1'>
                    <i class=\"ri-edit-2-fill text-xl\"></i>
                  </button>
                  <button onclick='hapusData({$data['id']})'
                          class='text-red-600 hover:text-red-800 mx-1'>
                    <i class=\"ri-delete-bin-5-fill text-xl\"></i>
                  </button>
                </td>
              </tr>";
              $no++;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

  <!-- Footer -->
  <footer class="text-center text-gray-700 text-sm py-4">
    © 2025 Intern. All rights reserved.
  </footer>

<!-- Modal Tambah Data -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-full max-w-md rounded-xl shadow-xl">
    <div class="bg-green-600 text-white text-center py-3 rounded-t-xl text-lg font-semibold shadow-md shadow-green-300">
      Tambah Data Karyawan
    </div>

    <form id="formTambah" class="p-6 space-y-4 flex flex-col">
      <div>
        <label class="block font-semibold mb-1 text-black">Nama
          <input type="text" name="nama" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">Jabatan
          <input type="text" name="jabatan" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">Alamat
          <input type="text" name="alamat" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">No Telp
          <input type="text" name="no_telp" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>

      <div class="flex justify-end space-x-3 mt-2">
        <button type="button" onclick="closeModalTambah()" class="px-4 py-2 bg-black text-white border border-green-500 rounded-full font-semibold">Kembali</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Data -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-md shadow-lg overflow-hidden">
    <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold shadow-md">
      Edit Data Karyawan
    </div>

    <form id="formEdit" class="p-6 space-y-4 flex flex-col">
      <input type="hidden" name="id" id="edit_id">
      <div>
        <label class="block font-semibold mb-1 text-black">Nama
          <input type="text" name="nama" id="edit_nama" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">Jabatan
          <input type="text" name="jabatan" id="edit_jabatan" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">Alamat
          <input type="text" name="alamat" id="edit_alamat" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>
      <div>
        <label class="block font-semibold mb-1 text-black">No Telp
          <input type="text" name="no_telp" id="edit_no_telp" class="border border-green-400 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
        </label>
      </div>

      <div class="flex justify-end space-x-3 mt-2">
        <button type="button" onclick="closeModalEdit()" class="px-4 py-2 bg-black text-white border border-green-500 rounded-full font-semibold">Kembali</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModalTambah() { document.getElementById('modalTambah').classList.remove('hidden'); }
  function closeModalTambah() { document.getElementById('modalTambah').classList.add('hidden'); }

  function editData(id, nama, jabatan, alamat, no_telp) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jabatan').value = jabatan;
    document.getElementById('edit_alamat').value = alamat;
    document.getElementById('edit_no_telp').value = no_telp;
    document.getElementById('modalEdit').classList.remove('hidden');
  }
  function closeModalEdit() { document.getElementById('modalEdit').classList.add('hidden'); }

  // ===== Tambah Data =====
  document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const noTelp = this.querySelector('input[name="no_telp"]').value.trim();
    if (!/^628\d{7,10}$/.test(noTelp)) {
      alert("Nomor telepon harus diawali dengan 628 dan diikuti 6-12 digit angka.");
      return;
    }
    const formData = new FormData(this);
    fetch('tambah_karyawan.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil ditambahkan!');
          closeModalTambah();
          location.reload();
        } else { alert('Gagal menambah data: ' + response); }
      });
  });

  // ===== Edit Data =====
  document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const noTelp = this.querySelector('input[name="no_telp"]').value.trim();
    if (!/^628\d{7,13}$/.test(noTelp)) {
      alert("Nomor telepon harus diawali dengan 628 dan diikuti 6-12 digit angka.");
      return;
    }
    const formData = new FormData(this);
    fetch('edit_karyawan.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil diperbarui!');
          closeModalEdit();
          location.reload();
        } else { alert('Gagal memperbarui data: ' + response); }
      });
  });

  // ===== Hapus Data =====
  function hapusData(id) {
    if (confirm("Yakin ingin menghapus data ini?")) {
      fetch('hapus_karyawan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
      })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil dihapus!');
          location.reload();
        } else {
          alert('Gagal menghapus data: ' + response);
        }
      });
    }
  }
</script>

</body>
</html>
