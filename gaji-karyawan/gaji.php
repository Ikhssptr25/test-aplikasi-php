<?php
include_once "../database/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
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
    <div class="w-full max-w-6xl mx-auto">
      <!-- Nav -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
        <a href="../index.php" class="w-full sm:w-auto text-center bg-black text-white px-5 py-2 font-semibold hover:bg-gray-800 transition rounded-sm">Kembali</a>
        <a href="../data-karyawan/karyawan.php" class="w-full sm:w-auto text-center bg-white text-green-600 border border-green-600 px-5 py-2 font-semibold hover:bg-green-50 transition rounded-sm">Data Karyawan</a>
      </div>

      <!-- Card -->
      <div class="bg-white shadow-lg w-full p-8 rounded-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
          <h2 class="text-lg font-bold tracking-widest text-gray-800 border-b pb-2">KELOLA GAJI KARYAWAN</h2>
          <button onclick="openModalTambah()" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-full shadow">Add Gaji</button>
        </div>

        <!-- Table -->
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
                SELECT g.*, d.nama
                FROM gaji_karyawan g
                JOIN data_karyawan d ON g.id_karyawan = d.id
                ORDER BY g.id_gaji DESC
              ");
              while ($data = mysqli_fetch_assoc($query)):
                $total = max(0, (int)$data['gaji_pokok'] + (int)$data['tunjangan'] - (int)$data['potongan']);
              ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4"><?= htmlspecialchars($data['nama']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($data['bulan']) ?></td>
                <td class="py-3 px-4">Rp. <?= number_format((int)$data['gaji_pokok'],0,',','.') ?></td>
                <td class="py-3 px-4">Rp. <?= number_format((int)$data['tunjangan'],0,',','.') ?></td>
                <td class="py-3 px-4">Rp. <?= number_format((int)$data['potongan'],0,',','.') ?></td>
                <td class="py-3 px-4 font-semibold">Rp. <?= number_format($total,0,',','.') ?></td>
                <td class="py-2 px-3 text-center">
                  <button onclick='openModalEdit(<?= $data['id_gaji'] ?>, "<?= htmlspecialchars(addslashes($data['bulan'])) ?>", <?= (int)$data['gaji_pokok'] ?>, <?= (int)$data['tunjangan'] ?>, <?= (int)$data['potongan'] ?>)' class='text-green-600 hover:text-green-800 mx-1'>
                    <i class="ri-edit-2-fill text-xl"></i>
                  </button>
                  <button onclick='hapusData(<?= $data['id_gaji'] ?>)' class='text-red-600 hover:text-red-800 mx-1'>
                    <i class="ri-delete-bin-5-fill text-xl"></i>
                  </button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <footer class="mt-10 text-gray-600 text-sm mb-6 text-center">© 2025 Intern. All rights reserved.</footer>

  <!-- Modal Tambah -->
  <div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
      <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">Tambah Data Gaji</div>

      <form id="formTambah" method="POST" class="p-6 space-y-4">
        <div>
          <label for="id_karyawan" class="block font-semibold mb-1">Nama Karyawan</label>
          <select id="id_karyawan" name="id_karyawan" class="border border-gray-400 rounded w-full px-3 py-2" required>
            <option value="">-- Pilih Karyawan --</option>
            <?php
            $karyawan = mysqli_query($koneksi, "SELECT id, nama FROM data_karyawan ORDER BY nama ASC");
            while ($k = mysqli_fetch_assoc($karyawan)) {
              echo "<option value='".(int)$k['id']."'>".htmlspecialchars($k['nama'])."</option>";
            }
            ?>
          </select>
        </div>

        <div>
          <label for="bulan" class="block font-semibold mb-1">Bulan</label>
          <input id="bulan" name="bulan" type="text" placeholder="Mis: Januari 2025" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>

        <div>
          <label for="gaji_pokok" class="block font-semibold mb-1">Gaji Pokok</label>
          <input id="gaji_pokok" name="gaji_pokok" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>

        <div>
          <label for="tunjangan" class="block font-semibold mb-1">Tunjangan</label>
          <input id="tunjangan" name="tunjangan" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>

        <div>
          <label for="potongan" class="block font-semibold mb-1">Potongan</label>
          <input id="potongan" name="potongan" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>

        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" onclick="closeModalTambah()" class="bg-black text-white px-4 py-2 rounded-full">Kembali</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit -->
  <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
      <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">Edit Data Gaji</div>

      <form id="formEdit" method="POST" class="p-6 space-y-4">
        <input type="hidden" name="id_gaji" id="edit_id_gaji">
        <div>
          <label for="edit_bulan" class="block font-semibold mb-1">Bulan</label>
          <input id="edit_bulan" name="bulan" type="text" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
        <div>
          <label for="edit_gaji_pokok" class="block font-semibold mb-1">Gaji Pokok</label>
          <input id="edit_gaji_pokok" name="gaji_pokok" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
        <div>
          <label for="edit_tunjangan" class="block font-semibold mb-1">Tunjangan</label>
          <input id="edit_tunjangan" name="tunjangan" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
        <div>
          <label for="edit_potongan" class="block font-semibold mb-1">Potongan</label>
          <input id="edit_potongan" name="potongan" type="number" min="0" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
        <div class="flex justify-end space-x-3 mt-4">
          <button type="button" onclick="closeModalEdit()" class="bg-black text-white px-4 py-2 rounded-full">Kembali</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Hapus -->
  <div id="modalHapus" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
      <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini?</p>
      <div class="flex justify-center space-x-4">
        <button onclick="closeModalHapus()" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">Batal</button>
        <button id="btnConfirmHapus" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
      </div>
    </div>
  </div>

<script>
  // Modal controls
  function openModalTambah() { document.getElementById('modalTambah').classList.remove('hidden'); }
  function closeModalTambah() { document.getElementById('modalTambah').classList.add('hidden'); }
  function openModalEdit(id, bulan, gaji_pokok, tunjangan, potongan) {
    document.getElementById('edit_id_gaji').value = id;
    document.getElementById('edit_bulan').value = bulan;
    document.getElementById('edit_gaji_pokok').value = gaji_pokok;
    document.getElementById('edit_tunjangan').value = tunjangan;
    document.getElementById('edit_potongan').value = potongan;
    document.getElementById('modalEdit').classList.remove('hidden');
  }
  function closeModalEdit() { document.getElementById('modalEdit').classList.add('hidden'); }

  // Hapus modal
  let idGajiHapus = null;
  function hapusData(id) {
    idGajiHapus = id;
    document.getElementById('modalHapus').classList.remove('hidden');
  }
  function closeModalHapus() { document.getElementById('modalHapus').classList.add('hidden'); idGajiHapus = null; }

  // ===== AJAX: Tambah =====
  document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();

    // client-side validation
    const idKaryawan = this.id_karyawan.value;
    const bulan = this.bulan.value.trim();
    const gp = Number(this.gaji_pokok.value);
    const tun = Number(this.tunjangan.value);
    const pot = Number(this.potongan.value);

    if (!idKaryawan) { alert('Pilih karyawan'); return; }
    if (!bulan) { alert('Isi bulan'); return; }
    if (gp < 0 || tun < 0 || pot < 0) { alert('Nilai tidak boleh negatif'); return; }

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
      }).catch(err => alert('Terjadi kesalahan: ' + err));
  });

  // ===== AJAX: Edit =====
  document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const gp = Number(this.gaji_pokok.value);
    const tun = Number(this.tunjangan.value);
    const pot = Number(this.potongan.value);
    if (gp < 0 || tun < 0 || pot < 0) { alert('Nilai tidak boleh negatif'); return; }

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
      }).catch(err => alert('Terjadi kesalahan: ' + err));
  });

  // ===== AJAX: Hapus =====
  document.getElementById('btnConfirmHapus').addEventListener('click', () => {
    if (!idGajiHapus) return;
    fetch('hapus_gaji.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id_gaji=' + idGajiHapus
    }).then(res => res.text())
      .then(response => {
        if (response.includes('success')) {
          alert('Data berhasil dihapus!');
          closeModalHapus();
          location.reload();
        } else {
          alert('Gagal menghapus data: ' + response);
        }
      }).catch(err => alert('Terjadi kesalahan: ' + err));
  });
</script>

</body>
</html>
