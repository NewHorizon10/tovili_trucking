-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 28, 2023 at 03:03 PM
-- Server version: 10.6.12-MariaDB-0ubuntu0.22.04.1
-- PHP Version: 8.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tovilli_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `lookups`
--

CREATE TABLE `lookups` (
  `id` bigint(20) NOT NULL,
  `code` varchar(255) NOT NULL,
  `lookup_type` varchar(255) NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lookups`
--

INSERT INTO `lookups` (`id`, `code`, `lookup_type`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'Partnership', 'company-type', 1, '2023-04-13 05:09:52', '2023-04-14 07:11:09'),
(6, 'Corporation', 'company-type', 1, '2023-04-13 05:10:00', '2023-04-14 07:09:22'),
(7, 'Sole Proprietorshi', 'company-type', 1, '2023-04-14 07:11:55', '2023-04-14 07:11:55'),
(8, 'Small Truck', 'truck-type', 1, '2023-05-19 10:48:21', '2023-05-19 10:48:21'),
(9, 'Big Truck', 'truck-type', 1, '2023-05-19 10:48:48', '2023-05-19 10:48:48'),
(10, 'Medium Truck', 'truck-type', 1, '2023-05-19 10:49:18', '2023-05-19 10:49:18'),
(15, '1en', 'tidaluk-company-type', 1, '2023-05-31 10:21:22', '2023-05-31 10:21:22'),
(16, 'test en1', 'tidaluk-company-type', 1, '2023-05-31 11:09:39', '2023-05-31 11:09:39'),
(17, 'Moving An Apartment', 'shipment-service', 1, '2023-06-28 10:32:47', '2023-06-28 10:32:47'),
(18, 'Moving An Apartment Including A Crane', 'shipment-service', 1, '2023-06-28 10:33:29', '2023-06-28 10:33:29'),
(19, 'Moving A Number Of Items', 'shipment-service', 1, '2023-06-28 10:34:06', '2023-06-28 10:34:06'),
(20, 'Morning', 'shipment-time', 1, '2023-06-28 10:40:15', '2023-06-28 10:40:15'),
(21, 'Afternoon', 'shipment-time', 1, '2023-06-28 10:40:37', '2023-06-28 10:40:37'),
(22, 'Evening', 'shipment-time', 1, '2023-06-28 10:41:02', '2023-06-28 10:41:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lookups`
--
ALTER TABLE `lookups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lookups`
--
ALTER TABLE `lookups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
