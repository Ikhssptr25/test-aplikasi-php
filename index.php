<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Penggajian Karyawan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/heroicons@2.0.13/dist/heroicons.min.js"></script>

</head>


<body class="min-h-screen flex flex-col bg-gradient-to-b from-green-400 to-green-100 font-poppins">

  <!-- Header -->
  <header class="bg-white shadow-md flex justify-between items-center px-1 py-1 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 px-12">
      <span class=" text-gray-700">Z.</span><span class="text-green-600">Corporate</span>
    </h1>
    <div class="flex items-center gap-4 mr-2">
    <img src="../assets/logo.png" alt="Logo" class="w-110 h-14 px-10 mr-0 ">
    <a href="user/logout.php" class="flex items-center gap-2 text-black px-4 py-2 mt-5 rounded-lg text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        keluar
    </a>
</div>
  </header>

   <!-- Judul -->
<h2 class="text-3xl font-bold text-center md:text-center text-white drop-shadow-md tracking-wide mt-8">
  SISTEM PENGGAJIAN KARYAWAN
</h2>

<!-- Main Content -->
<main class="px-6 md:px-24">
  <div class="mx-auto max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
    
    <!-- Left Text Section -->
    <div class="max-w-xl">
      <p class="text-lg text-black">
        <span class="text-red-600 font-semibold">Welcome,</span>
        <a href="#" class="text-black-100 font-semibold underline">Admin !</a>
      </p>

      <p class="mt-3 text-gray-800 leading-relaxed">
        Pantau dan kelola data karyawan serta penggajian mereka dengan mudah. Melalui sistem ini,
        Anda dapat menambahkan, memperbarui, dan menghapus data karyawan, serta mencatat rincian
        penggajian setiap periode dengan akurat.
      </p>

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="data-karyawan/karyawan.php"
           class="bg-green-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-green-700 transition">
          Data Karyawan
        </a>
        <a href="gaji-karyawan/gaji.php"
           class="bg-green-600 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-green-700 transition">
          Gaji Karyawan
        </a>
      </div>
    </div>

    <!-- Right Image Section -->
    <div class="flex justify-center md:justify-end">
      <img src="assets/dashboard-illustration.png"
           alt="Gambar Dashboard"
           class="w-72 md:w-[450px] h-auto object-contain drop-shadow-lg">
    </div>

  </div>
</main>


  <!-- Footer -->
  <footer class="text-center text-gray-700 text-sm py-4">
    © 2025 Intern. All rights reserved.
  </footer>

</body>
</html>
