-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2026 at 05:23 AM
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
-- Database: `portal_taman_kota`
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
(1, 'admin', '$2y$10$lMSS6ewL4cb3jPlnLYvHC.rwT7W1bTawdwU91puWY5TscGQL4zudK'),
(2, 'atmin', '$2y$10$7ATy2iybfFzsHgJcvSS.YOXGD2JUgAorU8sC/9cCskWSQfwN94Obe');

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
(1, 'Kota Batu', 'Portal informasi untuk menemukan taman, ruang hijau, dan tempat rekreasi di kota.', 'Website ini mengumpulkan informasi berbagai taman dalam satu kota agar masyarakat lebih mudah menemukan tempat untuk bersantai, berolahraga, dan berkegiatan.', 'Kontak Dinas/administrator kota');

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
  `status` enum('Aktif','Pasif','Dalam Perawatan') NOT NULL DEFAULT 'Aktif',
  `map_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parks`
--

INSERT INTO `parks` (`id`, `name`, `category`, `address`, `opening_hours`, `description`, `plant_species`, `facilities`, `image`, `employee_count`, `area`, `status`, `map_url`, `created_at`) VALUES
(1, 'Taman Kota Utama', 'Taman Kota', 'Jl. Pusat Kota No. 1', '06.00 - 21.00', 'Ruang hijau yang cocok untuk bersantai dan menikmati suasana kota.', 'Pohon trembesi, Pucuk merah, Rumput gajah', 'Jogging track, Gazebo, Taman bermain, Toilet', 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=1000&q=80', 8, 2500.00, 'Aktif', 'https://maps.google.com/', '2026-08-11 06:37:41'),
(2, 'Taman Pedestrian Gajah Mada', 'Taman Kota', 'Jl. Semeru I, Kota Batu, Jawa Timur 65314', '05.00 - 22.00', 'tamanakjhkh', 'pohon, semak, perdu, ground cover, rumput', 'Bangku Taman', 'uploads/parks/park_591ff0029af524c793d3.jpg', 5, 1800.00, 'Pasif', 'https://maps.app.goo.gl/sQWZyAUvJPx1WynZ8', '2026-08-11 06:37:41'),
(3, 'Taman Olahraga', 'Taman Olahraga', 'Jl. Merdeka No. 20', '05.30 - 21.00', 'Ruang terbuka untuk aktivitas olahraga dan komunitas.', 'Ketapang kencana, Rumput gajah, Pucuk merah', 'Lapangan, Jogging track, Fitness outdoor, Toilet', 'https://images.unsplash.com/photo-1473445361085-b9a07f55608b?auto=format&fit=crop&w=1000&q=80', 10, 3200.00, 'Aktif', 'https://maps.google.com/', '2026-08-11 06:37:41'),
(4, 'Taman Wilis', 'Taman Kota', 'JL. Wilis, Kelurahan Sisir, Kecamatan Batu, Kota Batu, Jawa Timur 65314', '05.00 - 22.00', 'OOI', 'pohon, semak, perdu, ground cover, rumput', 'Alat Permainan Anak, Gazebo', 'uploads/parks/park_e523824d29df17fa2a6e.jpg', 10, 8455.72, 'Dalam Perawatan', 'https://maps.app.goo.gl/mC9sqGK2jf9JKP728', '2026-08-11 06:37:41'),
(7, 'yuyutttyt', 'Taman Keluarga', 'tyty', 'tyuyutyu', 'tyututu', 'tyutyu', 'tyutyutyu', 'uploads/parks/park_8b8e8c5385a954ee1fec.jpg', 0, 0.00, 'Aktif', '', '2026-08-19 04:25:17'),
(8, 'Alun-Alun Kota Wisata Batu', 'Taman Kota', 'Jl. Sudiro, Sisir, Kec. Batu, Kota Batu, Jawa Timur, 65314', '05.00 - 22.00', 'ssss', 'pohon, semak, perdu, ground cover, rumput', 'Biang Lala, Taman Bermain, Air Mancur, Ferishwheel, Lampion, Carousel, Jogging Track, Gazebo Feature, Bangunan Toilet, Gardu Pandang, Pagar, Kantor Informasi, Bangku Taman', 'uploads/parks/park_7d53e817db44e36ba74a.jpg', 10, 8455.73, 'Aktif', 'https://maps.app.goo.gl/mC9sqGK2jf9JKP728', '2026-08-19 04:30:57');

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
(2, 9, 'uploads/parks/park_dc9ccdc42929b0b555d9.jpg', 0, '2026-08-19 06:59:56'),
(3, 9, 'uploads/parks/park_625950571d72169c8c96.jpg', 1, '2026-08-19 07:00:08'),
(4, 9, 'uploads/parks/park_e35d8023a791c14ecdfc.jpg', 2, '2026-08-19 07:03:24'),
(5, 9, 'uploads/parks/park_e60c7d2449b22aae824f.jpg', 3, '2026-08-20 06:53:23'),
(6, 8, 'uploads/parks/park_4a9ef61e4e7fed77b96d.jpg', 0, '2026-08-21 01:48:23'),
(7, 8, 'uploads/parks/park_22e18e35f8112c94b7d2.jpg', 1, '2026-08-21 01:48:23'),
(8, 10, 'uploads/parks/park_90d9c8b82a260cd35b2b.jpg', 0, '2026-08-21 02:08:16'),
(9, 5, 'uploads/parks/park_65a70ebb7f3f59ff6209.jpg', 0, '2026-08-21 02:26:17'),
(10, 4, 'uploads/parks/park_f969d089f6f127e99944.jpg', 0, '2026-08-21 02:27:47'),
(11, 2, 'uploads/parks/park_3c122faf01c84e6e24fa.jpg', 0, '2026-08-21 02:36:50');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `city_info`
--
ALTER TABLE `city_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parks`
--
ALTER TABLE `parks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `park_images`
--
ALTER TABLE `park_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
