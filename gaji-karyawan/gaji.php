<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

include_once "../database/koneksi.php";

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Data Gaji Karyawan</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-b from-green-400 to-green-100 font-sans overflow-hidden">

<header class="bg-white shadow-md flex justify-between items-center px-1 py-1 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 px-12">
      <span class="text-gray-700">Z.</span><span class="text-green-600">Corporate</span>
    </h1>
    <div class="flex items-center gap-4 mr-2">
        <img src="../assets/logo.png" alt="Logo" class="w-110 h-14 px-10 mr-0">
        <a href="../user/logout.php" class="flex items-center gap-2 text-black px-4 py-2 mt-5 rounded-lg text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
            keluar
        </a>
    </div>
</header>

<main class="flex-1 px-4 md:px-10 py-10">
    <div class="w-full max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
            <a href="../index.php" class="w-full sm:w-auto text-center bg-black text-white px-5 py-2 font-semibold hover:bg-gray-800 rounded-sm">Kembali</a>
            <a href="../data-karyawan/karyawan.php" class="w-full sm:w-auto text-center bg-white text-green-600 border border-green-600 px-5 py-2 font-semibold hover:bg-green-50 rounded-sm">Data Karyawan</a>
        </div>

        <div class="bg-white shadow-lg w-full p-6 rounded-md">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <h2 class="text-lg font-bold tracking-widest text-gray-800 border-b pb-2">KELOLA GAJI KARYAWAN</h2>
                <button onclick="openModalTambah()" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-full shadow">Add Gaji</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b font-bold">
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Nama</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">No. Telp</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Periode</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Gaji Pokok</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Tunjangan</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Potongan</th>
                            <th class="py-2 px-2 w-1/6 whitespace-nowrap truncate">Total Gaji</th>
                            <th class="py-2 px-2 w-1/12 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT g.*, d.nama, d.no_telp
                            FROM gaji_karyawan g
                            JOIN data_karyawan d ON g.id_karyawan = d.id
                            ORDER BY g.tahun DESC, FIELD(g.bulan,
                            'Januari','Februari','Maret','April','Mei','Juni','Juli',
                            'Agustus','September','Oktober','November','Desember') DESC
                        ");
                        while ($data = mysqli_fetch_assoc($query)):
                            $total = max(0, (float)$data['gaji_pokok'] + (float)$data['tunjangan'] - (float)$data['potongan']);
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-2 whitespace-nowrap truncate"><?= htmlspecialchars($data['nama']) ?></td>
                            <td class="py-2 px-2 whitespace-nowrap truncate"><?= htmlspecialchars($data['no_telp']) ?></td>
                            <td class="py-2 px-2 whitespace-nowrap"><?= htmlspecialchars($data['bulan'] . ' ' . $data['tahun']) ?></td>
                            <td class="py-2 px-2 whitespace-nowrap">Rp. <?= number_format((float)$data['gaji_pokok'],2,',','.') ?></td>
                            <td class="py-2 px-2 whitespace-nowrap">Rp. <?= number_format((float)$data['tunjangan'],2,',','.') ?></td>
                            <td class="py-2 px-2 whitespace-nowrap">Rp. <?= number_format((float)$data['potongan'],2,',','.') ?></td>
                            <td class="py-2 px-2 whitespace-nowrap font-semibold">Rp. <?= number_format($total,2,',','.') ?></td>
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button onclick='openModalEdit(<?= $data['id_gaji'] ?>, <?= json_encode($data['id_karyawan']) ?>, <?= json_encode($data['bulan']) ?>, <?= $data['tahun'] ?>, <?= (float)$data['gaji_pokok'] ?>, <?= (float)$data['tunjangan'] ?>, <?= (float)$data['potongan'] ?>)' class='text-green-600 hover:text-green-800 mx-1'>
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

<!-- Modal Tambah & Edit (sama seperti versi sebelumnya, sudah include no_telp otomatis) -->

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
    <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">Tambah Data Gaji</div>
    <form id="formTambah" method="POST" class="p-6 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div>
        <label for="id_karyawan" class="block font-semibold mb-1">Nama Karyawan</label>
        <select id="id_karyawan" name="id_karyawan" class="border border-gray-400 rounded w-full px-3 py-2" required onchange="isiOtomatisTambah()">
          <option value="">-- Pilih Karyawan --</option>
          <?php
          $karyawan = mysqli_query($koneksi, "SELECT id, nama, no_telp FROM data_karyawan ORDER BY nama ASC");
          while ($k = mysqli_fetch_assoc($karyawan)) {
              echo "<option value='".(int)$k['id']."' data-no='".htmlspecialchars($k['no_telp'])."'>".htmlspecialchars($k['nama'])."</option>";
          }
          ?>
        </select>
      </div>
      <div>
        <label class="block font-semibold mb-1">No. Telepon</label>
        <input id="no_telp_tambah" type="text" class="border border-gray-400 rounded w-full px-3 py-2 bg-gray-100" readonly placeholder="No Telepon otomatis terisi">
      </div>
      <div class="flex gap-2">
        <div class="flex-1">
          <label for="bulan" class="block font-semibold mb-1">Bulan</label>
          <select id="bulan" name="bulan" required class="border border-gray-400 rounded w-full px-3 py-2">
            <?php
            $bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            foreach ($bulan_list as $b) echo "<option value='$b'>$b</option>";
            ?>
          </select>
        </div>
        <div class="flex-1">
          <label for="tahun" class="block font-semibold mb-1">Tahun</label>
          <input id="tahun" name="tahun" type="number" min="2000" max="2100" value="<?= date('Y') ?>" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
      </div>
      <div>
        <label for="gaji_pokok" class="block font-semibold mb-1">Gaji Pokok</label>
        <input id="gaji_pokok" name="gaji_pokok" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div>
        <label for="tunjangan" class="block font-semibold mb-1">Tunjangan</label>
        <input id="tunjangan" name="tunjangan" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div>
        <label for="potongan" class="block font-semibold mb-1">Potongan</label>
        <input id="potongan" name="potongan" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div class="flex justify-end space-x-3 mt-4">
        <button type="button" onclick="closeModalTambah()" class="bg-black text-white px-4 py-2 rounded-full">Kembali</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit (sama dengan Tambah, sudah disesuaikan) -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
    <div class="bg-green-600 text-white text-center py-3 text-lg font-semibold">Edit Data Gaji</div>
    <form id="formEdit" method="POST" class="p-6 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <input type="hidden" name="id_gaji" id="edit_id_gaji">
      <div>
        <label for="edit_id_karyawan" class="block font-semibold mb-1">Nama Karyawan</label>
        <select id="edit_id_karyawan" name="id_karyawan" class="border border-gray-400 rounded w-full px-3 py-2" required onchange="isiOtomatisEdit()">
          <option value="">-- Pilih Karyawan --</option>
          <?php
          mysqli_data_seek($karyawan, 0);
          while ($k = mysqli_fetch_assoc($karyawan)) {
              echo "<option value='".(int)$k['id']."' data-no='".htmlspecialchars($k['no_telp'])."'>".htmlspecialchars($k['nama'])."</option>";
          }
          ?>
        </select>
      </div>
      <div>
        <label class="block font-semibold mb-1">No. Telepon</label>
        <input id="no_telp_edit" type="text" class="border border-gray-400 rounded w-full px-3 py-2 bg-gray-100" readonly placeholder="Otomatis terisi">
      </div>
      <div class="flex gap-2">
        <div class="flex-1">
          <label for="edit_bulan" class="block font-semibold mb-1">Bulan</label>
          <select id="edit_bulan" name="bulan" required class="border border-gray-400 rounded w-full px-3 py-2">
            <?php foreach ($bulan_list as $b) echo "<option value='$b'>$b</option>"; ?>
          </select>
        </div>
        <div class="flex-1">
          <label for="edit_tahun" class="block font-semibold mb-1">Tahun</label>
          <input id="edit_tahun" name="tahun" type="number" min="2000" max="2100" value="<?= date('Y') ?>" class="border border-gray-400 rounded w-full px-3 py-2" required>
        </div>
      </div>
      <div>
        <label for="edit_gaji_pokok" class="block font-semibold mb-1">Gaji Pokok</label>
        <input id="edit_gaji_pokok" name="gaji_pokok" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div>
        <label for="edit_tunjangan" class="block font-semibold mb-1">Tunjangan</label>
        <input id="edit_tunjangan" name="tunjangan" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div>
        <label for="edit_potongan" class="block font-semibold mb-1">Potongan</label>
        <input id="edit_potongan" name="potongan" type="number" min="0" step="0.01" value="0" class="border border-gray-400 rounded w-full px-3 py-2" required>
      </div>
      <div class="flex justify-end space-x-3 mt-4">
        <button type="button" onclick="closeModalEdit()" class="bg-black text-white px-4 py-2 rounded-full">Kembali</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-full">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
const CSRF_TOKEN = <?= json_encode($csrf_token) ?>;

function isiOtomatisTambah(){
  const select = document.getElementById('id_karyawan');
  document.getElementById('no_telp_tambah').value = select.options[select.selectedIndex]?.dataset.no || '';
}

function isiOtomatisEdit(){
  const select = document.getElementById('edit_id_karyawan');
  document.getElementById('no_telp_edit').value = select.options[select.selectedIndex]?.dataset.no || '';
}

function openModalTambah(){ document.getElementById('modalTambah').classList.remove('hidden'); }
function closeModalTambah(){ document.getElementById('modalTambah').classList.add('hidden'); }

function openModalEdit(id_gaji, id_karyawan, bulan, tahun, gaji_pokok, tunjangan, potongan){
  document.getElementById('modalEdit').classList.remove('hidden');
  document.getElementById('edit_id_gaji').value = id_gaji;
  document.getElementById('edit_id_karyawan').value = id_karyawan;
  isiOtomatisEdit();
  document.getElementById('edit_bulan').value = bulan;
  document.getElementById('edit_tahun').value = tahun;
  document.getElementById('edit_gaji_pokok').value = gaji_pokok;
  document.getElementById('edit_tunjangan').value = tunjangan;
  document.getElementById('edit_potongan').value = potongan;
}
function closeModalEdit(){ document.getElementById('modalEdit').classList.add('hidden'); }

function hapusData(id_gaji){
  if(confirm("Apakah Anda yakin ingin menghapus gaji ini?")){
    fetch('hapus_gaji.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'id_gaji='+encodeURIComponent(id_gaji)+'&csrf_token='+encodeURIComponent(CSRF_TOKEN)
    })
    .then(res=>res.text()).then(r=>{
      if(r.includes('success')){ alert('Data berhasil dihapus!'); location.reload(); }
      else alert('Gagal hapus: '+r);
    }).catch(err=>alert('Error: '+err));
  }
}

document.getElementById('formTambah').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);
  fetch('tambah_gaji.php',{method:'POST',body:formData})
  .then(res=>res.text()).then(r=>{
    if(r.includes('success')){ alert('Data berhasil ditambahkan!'); closeModalTambah(); location.reload(); }
    else alert('Gagal: '+r);
  }).catch(err=>alert('Error: '+err));
});

document.getElementById('formEdit').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);
  fetch('edit_gaji.php',{method:'POST',body:formData})
  .then(res=>res.text()).then(r=>{
    if(r.includes('success')){ alert('Data berhasil diperbarui!'); closeModalEdit(); location.reload(); }
    else alert('Gagal: '+r);
  }).catch(err=>alert('Error: '+err));
});
</script>

</body>
</html>
