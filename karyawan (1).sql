-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 05, 2025 at 02:04 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `karyawan`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_karyawan`
--

CREATE TABLE `data_karyawan` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_telp` varchar(20) NOT NULL
) ;

--
-- Dumping data for table `data_karyawan`
--

INSERT INTO `data_karyawan` (`id`, `nama`, `jabatan`, `alamat`, `no_telp`) VALUES
(14, 'bayu ramadhan', 'staff', 'Lampung Tengah', '08531668214'),
(17, 'ikhsan ', 'magang', 'lampung', '085609694039'),
(20, 'ikhsan saputra', 'magang', 'lampung', '085609694039');

-- --------------------------------------------------------

--
-- Table structure for table `gaji_karyawan`
--

CREATE TABLE `gaji_karyawan` (
  `id_gaji` int NOT NULL,
  `id_karyawan` int NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `gaji_pokok` double NOT NULL,
  `tunjangan` double NOT NULL,
  `potongan` double NOT NULL,
  `total_gaji` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gaji_karyawan`
--

INSERT INTO `gaji_karyawan` (`id_gaji`, `id_karyawan`, `bulan`, `gaji_pokok`, `tunjangan`, `potongan`, `total_gaji`) VALUES
(28, 14, 'agustus', 1, 1, 3, 0),
(29, 14, 'september 2025', 1, 9.223372036854777e68, 1, 9.2233720368548e68),
(30, 14, 'agustus', 1, 100, 1, 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_karyawan`
--
ALTER TABLE `data_karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gaji_karyawan`
--
ALTER TABLE `gaji_karyawan`
  ADD PRIMARY KEY (`id_gaji`),
  ADD KEY `gaji_karyawan_ibfk_1` (`id_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_karyawan`
--
ALTER TABLE `data_karyawan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gaji_karyawan`
--
ALTER TABLE `gaji_karyawan`
  MODIFY `id_gaji` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gaji_karyawan`
--
ALTER TABLE `gaji_karyawan`
  ADD CONSTRAINT `gaji_karyawan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `data_karyawan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
