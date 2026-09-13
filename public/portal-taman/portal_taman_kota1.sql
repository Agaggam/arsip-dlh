-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2026 at 05:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portal_taman_kota1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$F7w9t.KHFW.lhEMZi1Z8ROCOemCC23IOi6hsiEF9fZfMBLDBXTG5W');

-- --------------------------------------------------------

--
-- Table structure for table `city_info`
--

CREATE TABLE `city_info` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `about` text DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city_info`
--

INSERT INTO `city_info` (`id`, `city_name`, `description`, `about`, `contact`) VALUES
(1, 'Batu', 'Portal informasi untuk menemukan taman, ruang hijau, dan tempat rekreasi di kota.', 'Website ini mengumpulkan informasi berbagai taman dalam satu kota agar masyarakat lebih mudah menemukan tempat untuk bersantai, berolahraga, dan berkegiatan.', 'Kontak Dinas/administrator kota');

-- --------------------------------------------------------

--
-- Table structure for table `parks`
--

CREATE TABLE `parks` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(80) NOT NULL,
  `address` text NOT NULL,
  `opening_hours` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `plant_species` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `image` text NOT NULL,
  `employee_count` int(11) NOT NULL DEFAULT 0,
  `area` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Aktif','Dalam Perawatan','Pasif') NOT NULL DEFAULT 'Aktif',
  `map_url` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parks`
--

INSERT INTO `parks` (`id`, `name`, `category`, `address`, `opening_hours`, `description`, `plant_species`, `facilities`, `image`, `employee_count`, `area`, `status`, `map_url`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Taman Kota Utama', 'Taman Kota', 'Jl. Pusat Kota No. 1', '06.00 - 21.00', 'Ruang hijau yang cocok untuk bersantai dan menikmati suasana kota.', 'Pohon trembesi, Pucuk merah, Rumput gajah', 'Jogging track, Gazebo, Taman bermain, Toilet', 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=1000&q=80', 8, 2500.00, 'Aktif', 'https://maps.google.com/', NULL, NULL, '2026-08-11 06:37:41'),
(2, 'Taman Keluarga', 'Taman Keluarga', 'Jl. Melati No. 12', '07.00 - 20.00', 'Taman ramah keluarga dengan area bermain dan ruang terbuka.', 'Palem, Bougenville, Rumput jepang', 'Playground, Gazebo, Area piknik, Parkir', 'https://images.unsplash.com/photo-1598902108854-10e335adac99?auto=format&fit=crop&w=1000&q=80', 5, 1800.00, 'Aktif', 'https://maps.google.com/', NULL, NULL, '2026-08-11 06:37:41'),
(3, 'Taman Olahraga', 'Taman Olahraga', 'Jl. Merdeka No. 20', '05.30 - 21.00', 'Ruang terbuka untuk aktivitas olahraga dan komunitas.', 'Ketapang kencana, Rumput gajah, Pucuk merah', 'Lapangan, Jogging track, Fitness outdoor, Toilet', 'https://images.unsplash.com/photo-1473445361085-b9a07f55608b?auto=format&fit=crop&w=1000&q=80', 10, 3200.00, 'Aktif', 'https://maps.google.com/', NULL, NULL, '2026-08-11 06:37:41'),
(4, 'Taman Bunga', 'Taman Tematik', 'Jl. Mawar No. 8', '07.00 - 19.00', 'Taman tematik dengan berbagai tanaman dan area foto.', 'Mawar, Bougenville, Kamboja', 'Taman bunga, Gazebo, Spot foto, Parkir', 'https://images.unsplash.com/photo-1558904541-efa843a96f01?auto=format&fit=crop&w=1000&q=80', 4, 1200.00, 'Dalam Perawatan', 'https://maps.google.com/', NULL, NULL, '2026-08-11 06:37:41'),
(5, 'Taman Sungai', 'Taman Kota', 'Jl. Sudiro, Sisir, Kec. Batu, Kota Batu, Jawa Timur, 65314', '06.00 - 20.00', 'Ruang terbuka di sekitar aliran sungai untuk jalan santai.', 'Bambu, Ketapang, Rumput gajah', 'Jalur pejalan kaki, Bangku, Gazebo', 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80', 6, 2100.00, 'Aktif', 'https://maps.app.goo.gl/1pB3bgjMeoTC6xyUA', 6.1680600, 106.8238900, '2026-08-11 06:37:41'),
(6, 'aaaaa', 'Taman Alamak', 'aaaa', 'aaaa', 'aaa', 'ssssss', 'aaaa', 'uploads/parks/park_5ebb4b1a82a2a92bf961.png', 0, 0.00, 'Aktif', 'https://maps.app.goo.gl/sQWZyAUvJPx1WynZ8', -6.1680600, 106.8238900, '2026-08-12 02:25:15'),
(7, 'Alun-Alun Kota Wisata Batu', 'Taman Kota', 'Jl. Sudiro, Sisir, Kec. Batu, Kota Batu, Jawa Timur, 65314', '05.00 - 22.00 WIB', 'Alun-Alun Kota Batu merupakan salah satu ruang publik dan ikon wisata yang berada di pusat Kota Batu, Jawa Timur. Alun-alun ini menjadi tempat berkumpul dan bersantai bagi masyarakat maupun wisatawan. Dengan suasana yang sejuk dan nyaman, kawasan ini dilengkapi berbagai fasilitas seperti area bermain anak, taman, tempat duduk, toilet, serta berbagai pilihan kuliner di sekitarnya. Salah satu daya tarik utama Alun-Alun Kota Batu adalah bianglala yang menjadi ikon khas kawasan ini. Keindahan taman dan lokasinya yang strategis menjadikan Alun-Alun Kota Batu sebagai salah satu destinasi favorit untuk berwisata, bersantai, dan menikmati suasana kota.', 'Rumput, Pohon, Perdu, Ground Cover', 'Biang Lala, Taman Bermain, Air Mancur, Tempat Duduk', 'uploads/parks/park_3026ad089727835fb4bc.jpg', 45, 10000.00, 'Aktif', 'https://maps.app.goo.gl/mC9sqGK2jf9JKP728', -7.8711350, 112.5269370, '2026-08-26 02:34:45');

-- --------------------------------------------------------

--
-- Table structure for table `park_images`
--

CREATE TABLE `park_images` (
  `id` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `park_images`
--

INSERT INTO `park_images` (`id`, `park_id`, `image`, `sort_order`, `created_at`) VALUES
(2, 7, 'uploads/parks/park_c35b960bd1bf79f5a45f.jpg', 0, '2026-08-26 02:39:54'),
(3, 7, 'uploads/parks/park_f72092cef417801b0f3b.jpg', 1, '2026-08-26 02:40:06'),
(4, 7, 'uploads/parks/park_e015b5ab21788bacb5a7.jpg', 2, '2026-08-26 02:40:35'),
(5, 6, 'uploads/parks/park_5dc6ffdf5a6af07c9b9e.jpg', 0, '2026-08-26 02:49:20'),
(6, 6, 'uploads/parks/park_e4eff8e308ebc19f818a.jpg', 1, '2026-08-26 02:49:20'),
(7, 6, 'uploads/parks/park_39544a0b3bd4ad0a35a8.jpg', 2, '2026-08-26 02:49:20'),
(8, 6, 'uploads/parks/park_6da99f095f6764c87e80.jpg', 3, '2026-08-26 02:49:20'),
(9, 6, 'uploads/parks/park_d3edd3fe1a44b47b2bfe.jpg', 4, '2026-08-26 02:49:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `city_info`
--
ALTER TABLE `city_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parks`
--
ALTER TABLE `parks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `park_images`
--
ALTER TABLE `park_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_park_images_park_id` (`park_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `city_info`
--
ALTER TABLE `city_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parks`
--
ALTER TABLE `parks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `park_images`
--
ALTER TABLE `park_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
