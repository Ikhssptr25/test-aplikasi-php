<?php
include_once "../database/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Gaji Karyawan</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-b from-green-400 to-green-100 font-sans">

  <!-- Header -->
    <header class="bg-white shadow-md flex justify-between items-center px-1 py-1 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 px-12">
      <span class=" text-gray-700">Z.</span><a href="../index.php" class="text-green-600">Corporate</a>
    </h1>
    <img src="../assets/logo.png" alt="Logo" class="w-110 h-14 px-10">
  </header>


 <!-- Main -->
<main class="flex-1 px-4 md:px-10 py-10">
  <!-- Container utama: tombol + card di dalam sini -->
  <div class="w-full max-w-6xl mx-auto">

    <!-- Tombol navigasi -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
      <a href="../index.php"
         class="w-full sm:w-auto text-center bg-black text-white px-5 py-2 font-semibold hover:bg-gray-800 transition rounded-sm">
        Kembali
      </a>

      <a href="../data-karyawan/karyawan.php"
         class="w-full sm:w-auto text-center bg-white text-green-600 border border-green-600 px-5 py-2 font-semibold hover:bg-green-50 transition rounded-sm">
        Data Karyawan
      </a>
    </div>

    <!-- Card Kelola Gaji -->
    <div class="bg-white shadow-lg w-full p-8 rounded-md">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="text-lg font-bold tracking-widest text-gray-800 border-b pb-2">
          KELOLA GAJI KARYAWAN
        </h2>
        <button onclick="openModalTambah()"
                class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-full shadow">
          Add Gaji
        </button>
      </div>

      <!-- Tabel Data Gaji -->
      <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="bg-gray-100 text-gray-700 border-b font-bold">
              <th class="py-3 px-4 font-semibold">Nama</th>
              <th class="py-3 px-4 font-semibold">Bulan</th>
              <th class="py-3 px-4 font-semibold">Gaji Pokok</th>
              <th class="py-3 px-4 font-semibold">Tunjangan</th>
              <th class="py-3 px-4 font-semibold">Potongan</th>
              <th class="py-3 px-4 font-semibold">Total Gaji</th>
              <th class="py-3 px-4 text-center font-semibold">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = mysqli_query($koneksi, "
              SELECT gaji_karyawan.*, data_karyawan.nama
              FROM gaji_karyawan
              JOIN data_karyawan ON gaji_karyawan.id_karyawan = data_karyawan.id
            ");
            while ($data = mysqli_fetch_array($query)) {
              $total = $data['gaji_pokok'] + $data['tunjangan'] - $data['potongan'];
              echo "
                <tr class='border-b hover:bg-gray-50'>
                  <td class='py-3 px-4'>{$data['nama']}</td>
                  <td class='py-3 px-4'>{$data['bulan']}</td>
                  <td class='py-3 px-4'>Rp. " . number_format($data['gaji_pokok'], 0, ',', '.') . "</td>
                  <td class='py-3 px-4'>Rp. " . number_format($data['tunjangan'], 0, ',', '.') . "</td>
                  <td class='py-3 px-4'>Rp. " . number_format($data['potongan'], 0, ',', '.') . "</td>
                  <td class='py-3 px-4'>Rp. " . number_format($total, 0, ',', '.') . "</td>
                  <td class='py-2 px-3 text-center'>
                    <button onclick='openModalEdit({$data['id_gaji']}, \"{$data['bulan']}\", {$data['gaji_pokok']}, {$data['tunjangan']}, {$data['potongan']})'
                            class='text-green-600 hover:text-green-800 mx-1'>
                      <i class=\"ri-edit-2-fill text-xl\"></i>
                    </button>
                    <button onclick='hapusData({$data['id_gaji']})'
                            class='text-red-600 hover:text-red-800 mx-1'>
                      <i class=\"ri-delete-bin-5-fill text-xl\"></i>
                    </button>
                  </td>
                </tr>
              ";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>


  <footer class="mt-10 text-gray-600 text-sm mb-6 text-center">
    © 2025 Intern. All rights reserved.
  </footer>

  <!-- Modal Tambah -->
  <div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg">
      <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">
        Tambah Data Gaji
      </div>
      <form id="formTambah" method="POST" action="tambah_gaji.php" class="p-6 space-y-4">
  <div>
    <label class="block font-semibold mb-1">Nama Karyawan
    <select name="nama_karyawan" class="border border-gray-400 rounded w-full px-3 py-2" required>
      <option value="">-- Pilih Karyawan --</option>
    </label>

      <?php
      $query = mysqli_query($koneksi, "SELECT nama FROM data_karyawan");
      while ($row = mysqli_fetch_assoc($query)) {
          echo "<option value='{$row['nama']}'>{$row['nama']}</option>";
      }
      ?>
    </select>
  </div>
  <div>
    <label class="block font-semibold mb-1">Bulan
    <input type="text" name="bulan" class="border border-gray-400 rounded w-full px-3 py-2" required>
    </label>
  </div>
  <div>
    <label class="block font-semibold mb-1">Gaji Pokok
    <input type="number" name="gaji_pokok" class="border border-gray-400 rounded w-full px-3 py-2" required>
    </label>
  </div>
  <div>
    <label class="block font-semibold mb-1">Tunjangan
    <input type="number" name="tunjangan" class="border border-gray-400 rounded w-full px-3 py-2" required>
    </label>
  </div>
  <div>
    <label class="block font-semibold mb-1">Potongan
    <input type="number" name="potongan" class="border border-gray-400 rounded w-full px-3 py-2" required>
    </label>
  </div>
  <div class="flex justify-end space-x-3 mt-4">
    <button type="button" onclick="closeModalTambah()" class="bg-black text-white px-4 py-2 rounded-full ">Kembali</button>
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full ">Simpan</button>
  </div>
</form>

    </div>
  </div>

  <!-- Modal Edit -->
  <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg">
      <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">
        Edit Data Gaji
      </div>
      <form id="formEdit" method="POST" action="edit_gaji.php" class="p-6 space-y-4">
        <input type="hidden" name="id_gaji" id="edit_id_gaji">
        <div>
          <label class="block font-semibold mb-1">Bulan
          <input type="text" name="bulan" id="edit_bulan" class="border border-gray-400 rounded w-full px-3 py-2" required>
          </label>
        </div>
        <div>
          <label class="block font-semibold mb-1">Gaji Pokok
          <input type="number" name="gaji_pokok" id="edit_gaji_pokok" class="border border-gray-400 rounded w-full px-3 py-2" required>
          </label>
        </div>
        <div>
          <label class="block font-semibold mb-1">Tunjangan
          <input type="number" name="tunjangan" id="edit_tunjangan" class="border border-gray-400 rounded w-full px-3 py-2" required>
          </label>
        </div>
        <div>
          <label class="block font-semibold mb-1">Potongan
          <input type="number" name="potongan" id="edit_potongan" class="border border-gray-400 rounded w-full px-3 py-2" required>
          </label>
        </div>
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" onclick="closeModalEdit()" class="bg-black text-white px-4 py-2 rounded-full ">Kembali</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full ">Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div id="modalHapus" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
      <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini?</p>
      <div class="flex justify-center space-x-4">
        <button onclick="closeModalHapus()" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">Batal</button>
        <button id="btnConfirmHapus" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
      </div>
    </div>
  </div>

  <!-- SCRIPT -->
  <script>
  function openModalTambah() {
    document.getElementById('modalTambah').classList.remove('hidden');
  }
  function closeModalTambah() {
    document.getElementById('modalTambah').classList.add('hidden');
  }

  function openModalEdit(id, bulan, gaji_pokok, tunjangan, potongan) {
    document.getElementById('edit_id_gaji').value = id;
    document.getElementById('edit_bulan').value = bulan;
    document.getElementById('edit_gaji_pokok').value = gaji_pokok;
    document.getElementById('edit_tunjangan').value = tunjangan;
    document.getElementById('edit_potongan').value = potongan;
    document.getElementById('modalEdit').classList.remove('hidden');
  }
  function closeModalEdit() {
    document.getElementById('modalEdit').classList.add('hidden');
  }

  let idGajiHapus = null;
  function hapusData(id) {
    idGajiHapus = id;
    document.getElementById('modalHapus').classList.remove('hidden');
  }
  function closeModalHapus() {
    document.getElementById('modalHapus').classList.add('hidden');
    idGajiHapus = null;
  }

  // 🔹 Tambah Data Gaji (AJAX)
  document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('tambah_gaji.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil ditambahkan!');
          closeModalTambah();
          location.reload();
        } else {
          alert('Gagal menambah data: ' + response);
        }
      })
      .catch(err => {
        alert('Terjadi kesalahan: ' + err);
      });
  });

  // 🔹 Edit Data Gaji (AJAX)
  document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('edit_gaji.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil diperbarui!');
          closeModalEdit();
          location.reload();
        } else {
          alert('Gagal mengupdate data: ' + response);
        }
      })
      .catch(err => {
        alert('Terjadi kesalahan: ' + err);
      });
  });

  // 🔹 Hapus Data Gaji (AJAX)
  document.getElementById('btnConfirmHapus').addEventListener('click', () => {
    if (idGajiHapus) {
      fetch('hapus_gaji.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_gaji=' + idGajiHapus
      })
      .then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil dihapus!');
          closeModalHapus();
          location.reload();
        } else {
          alert('Gagal menghapus data: ' + response);
        }
      })
      .catch(err => {
        alert('Terjadi kesalahan: ' + err);
      });
    }
  });
</script>

</body>
</html>
