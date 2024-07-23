-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 18, 2023 at 06:35 PM
-- Server version: 10.4.25-MariaDB-1:10.4.25+maria~bionic
-- PHP Version: 7.2.34-28+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `astonsport_liven`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_actions`
--

CREATE TABLE `email_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_actions`
--

INSERT INTO `email_actions` (`id`, `action`, `options`, `created_at`, `updated_at`) VALUES
(2, 'forgot_password', 'EMAIL,FORGOT_PASSWORD_LINK', NULL, NULL),
(3, 'reset_password', 'USER_NAME', NULL, NULL),
(4, 'contract_mail', 'EMAIL,SPORT_NAME,PERSONE_COUNT,DESTINATION_COUNTRY,START_DATE,END_DATE,PERSONE_NAME,SUBSCRIBER_NAME,CIVILITY,SUBSCRIBER_FAMILY_NAME,PHONE,ADDRESS_1,ADDRESS_2,ZIP_CODE,CITY,USER_COUNTRY,SM_CONTRACT_ID,SM_ESTIMATION_ID,TOTAL_PRICE,SUB_TOTAL,MANAGEMENT_FEE,BAGAGE_FEE,BAGAGE_OPTION_FEE,TRANSACTION_DATE,COMPANY', NULL, NULL),
(5, 'estimation_mail', 'EMAIL,SPORT_NAME,PERSONE_COUNT,DESTINATION_COUNTRY,START_DATE,END_DATE,PERSONE_NAME,SUBSCRIBER_NAME,CIVILITY,SUBSCRIBER_FAMILY_NAME,PHONE,ADDRESS_1,ADDRESS_2,ZIP_CODE,CITY,USER_COUNTRY,SM_CONTRACT_ID,SM_ESTIMATION_ID,TOTAL_PRICE,SUB_TOTAL,MANAGEMENT_FEE,BAGAGE_FEE,BAGAGE_OPTION_FEE,TRANSACTION_DATE,COMPANY', NULL, NULL),
(6, 'account_created', 'EMAIL,PASSWORD', NULL, NULL),
(7, 'contact_replies', 'EMAIL,MESSAGE', NULL, NULL),
(8, 'send_login_credentials', 'USER_NAME,EMAIL,PASSWORD', '2022-11-18 00:00:00', '2022-11-18 00:00:00'),
(9, 'end_of_your_contract', 'CIVILITY,LAST_NAME,FIRST_NAME', NULL, NULL),
(10, 'contract_will_stop_in_1_week', 'CIVILITY,LAST_NAME,FIRST_NAME,SM_CONTRACT_ID', NULL, NULL),
(11, 'contract_will_stop_in_1_month', 'CIVILITY,LAST_NAME,FIRST_NAME,SM_CONTRACT_ID', NULL, NULL),
(12, 'contract_has_begun', 'CIVILITY,LAST_NAME,FIRST_NAME,CONTRACT_START_DATE,CONTRACT_END_DATE', NULL, NULL),
(13, 'contact_enquiry', 'NAME,EMAIL,MESSAGE,MOBILE_NUMBER,CONTACT_NUMBER', NULL, NULL),
(14, 'otp_verification', 'NAME,EMAIL,OTP', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_actions`
--
ALTER TABLE `email_actions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_actions`
--
ALTER TABLE `email_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
