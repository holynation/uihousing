-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 10, 2022 at 04:49 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `equipro`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `user_platform` enum('mobile','web') NOT NULL DEFAULT 'mobile',
  `ip` varchar(50) NOT NULL,
  `events` enum('booking','extended','payment','chat','review','post_equip','withdrawal','login','logout') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'tracking activity',
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `address` text,
  `role_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `admin_path` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `firstname`, `middlename`, `lastname`, `email`, `phone_number`, `address`, `role_id`, `status`, `admin_path`) VALUES
(1, 'Holynation', 'Dev', 'Oluwaseun', 'admin@gmail.com', NULL, 'kwara road, UI', 1, 1, NULL),
(3, 'Codefixbug', 'Dev', 'Dev', 'developer@codefixbug.com', '08157853136', 'Abuja, Nigeria.', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `app_setting`
--

CREATE TABLE `app_setting` (
  `ID` int NOT NULL,
  `delivery_charge` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `ID` int NOT NULL,
  `user_sender` int NOT NULL,
  `user_receiver` int NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `earnings`
--

CREATE TABLE `earnings` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `owners_id` int NOT NULL,
  `amount` float NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `owners_id` int NOT NULL,
  `equip_name` varchar(255) NOT NULL,
  `equip_images_id` int DEFAULT NULL,
  `cost_of_hire` float DEFAULT NULL,
  `cost_of_hire_interval` int NOT NULL COMMENT 'reps in days',
  `avail_from` timestamp NOT NULL,
  `avail_to` timestamp NOT NULL,
  `quantity` int NOT NULL,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`ID`, `user_id`, `owners_id`, `equip_name`, `equip_images_id`, `cost_of_hire`, `cost_of_hire_interval`, `avail_from`, `avail_to`, `quantity`, `description`, `status`, `date_created`) VALUES
(1, 11, 1, 'Wheel Barrow', 1, 1000, 1, '2022-05-04 23:00:00', '2022-12-24 23:00:00', 12, 'This is a wheelbarrow useful for farming and item carriage', 1, '2022-05-05 01:47:52'),
(2, 11, 1, 'Bulldozer', 2, 5000, 7, '2022-05-04 23:00:00', '2022-12-24 23:00:00', 12, 'This is a bulldozer useful for farming and item carriage', 1, '2022-05-05 01:49:11'),
(3, 11, 1, 'Tractor', 3, 10000, 7, '2022-05-04 23:00:00', '2022-12-24 23:00:00', 12, 'This is a tractor useful for farming and item carriage', 1, '2022-05-05 01:50:08'),
(4, 11, 1, 'Wheel Barrow', 4, 1000, 7, '2022-05-04 23:00:00', '2022-12-24 23:00:00', 12, 'This is a wheelbarrow useful for farming and item carriage', 1, '2022-05-05 01:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `equip_delivery_status`
--

CREATE TABLE `equip_delivery_status` (
  `ID` int NOT NULL,
  `hirers_id` int NOT NULL,
  `equipments_id` int NOT NULL,
  `delivery_status` enum('pending','picked_from_owner','delivered_hirer','in_use','picked_from_hirer','returned') NOT NULL DEFAULT 'pending',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `equip_images`
--

CREATE TABLE `equip_images` (
  `ID` int NOT NULL,
  `equipments_id` int NOT NULL,
  `equip_images_path` varchar(150) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `equip_images`
--

INSERT INTO `equip_images` (`ID`, `equipments_id`, `equip_images_path`, `status`, `date_created`) VALUES
(1, 1, '/var/www/holynation/gig/equipro/writable/uploads/equipments/1651718872_84801fd3d1c2830c621f.png', 1, '2022-05-05 02:47:52'),
(2, 2, '/var/www/holynation/gig/equipro/writable/uploads/equipments/1651718951_6a63c19020a5fa15229e.png', 1, '2022-05-05 02:49:11'),
(3, 3, '/var/www/holynation/gig/equipro/writable/uploads/equipments/1651719008_62f86c3484822082a67a.png', 1, '2022-05-05 02:50:08'),
(4, 4, '/var/www/holynation/gig/equipro/writable/uploads/equipments/1651719451_bdebd019a0dc4d5f6b43.txt', 1, '2022-05-05 02:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `equip_order`
--

CREATE TABLE `equip_order` (
  `ID` int NOT NULL,
  `hirers_id` int NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `quantity` int NOT NULL,
  `discount` float DEFAULT NULL,
  `total_amount` float NOT NULL,
  `delivery_charge` double NOT NULL,
  `order_status` enum('pending','accepted','delivered','processing','rejected') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'pending',
  `order_type` enum('normal','extended') NOT NULL DEFAULT 'normal' COMMENT 'extended means equip extended',
  `pickup_date` timestamp NULL DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='tracking equip order';

--
-- Dumping data for table `equip_order`
--

INSERT INTO `equip_order` (`ID`, `hirers_id`, `order_number`, `quantity`, `discount`, `total_amount`, `delivery_charge`, `order_status`, `order_type`, `pickup_date`, `payment_status`, `date_created`) VALUES
(17, 9, '1000000011', 2, NULL, 1428.57, 0, 'pending', 'normal', NULL, 0, '2022-05-06 17:01:23'),
(18, 8, '1000000012', 1, NULL, 1428.57, 0, 'pending', 'normal', NULL, 0, '2022-05-06 17:01:23'),
(25, 9, '1000000013', 2, NULL, 1428.57, 0, 'pending', 'normal', NULL, 0, '2022-05-08 13:42:34'),
(26, 9, '1000000014', 3, NULL, 2142.86, 0, 'rejected', 'normal', '2022-05-09 23:00:00', 0, '2022-05-08 14:21:23');

-- --------------------------------------------------------

--
-- Table structure for table `equip_payment`
--

CREATE TABLE `equip_payment` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL COMMENT 'user who paid',
  `user_email` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'this was saved so that recurring pay can occur',
  `equip_order_id` int NOT NULL,
  `transaction_number` varchar(255) DEFAULT NULL,
  `payment_method` varchar(100) NOT NULL,
  `receipt_ref` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_number` varchar(255) NOT NULL,
  `gateway_reference` varchar(255) NOT NULL,
  `payment_status` tinyint(1) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_message` varchar(300) NOT NULL,
  `payment_date` timestamp NULL DEFAULT NULL COMMENT 'the time is 1hr behind based on paystack time',
  `next_payment_date` timestamp NULL DEFAULT NULL,
  `card` int DEFAULT NULL,
  `auth_code` varchar(50) NOT NULL,
  `auth_object` json NOT NULL,
  `prev_tranx_count` int NOT NULL,
  `current_tranx_count` int NOT NULL,
  `autorenew` tinyint(1) NOT NULL DEFAULT '1',
  `payment_channel` varchar(255) NOT NULL,
  `payment_log` text,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `equip_request`
--

CREATE TABLE `equip_request` (
  `ID` int NOT NULL,
  `equip_order_id` int NOT NULL,
  `hirers_id` int NOT NULL,
  `equipments_id` int NOT NULL,
  `quantity` int NOT NULL,
  `rental_from` timestamp NOT NULL,
  `rental_to` timestamp NOT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `request_status` enum('pending','booked','received','returned','accepted','rejected') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'pending',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `equip_request`
--

INSERT INTO `equip_request` (`ID`, `equip_order_id`, `hirers_id`, `equipments_id`, `quantity`, `rental_from`, `rental_to`, `delivery_location`, `request_status`, `date_created`) VALUES
(16, 17, 9, 2, 2, '2022-05-05 23:00:00', '2022-05-07 23:00:00', 'No 10, Kwara road, Ibadan', 'pending', '2022-05-06 18:01:23'),
(17, 18, 8, 3, 1, '2022-05-05 23:00:00', '2022-05-07 23:00:00', 'No 10, Kwara road, Ibadan', 'pending', '2022-05-06 18:01:23'),
(23, 25, 9, 2, 2, '2022-05-05 23:00:00', '2022-05-07 23:00:00', 'No 10, Kwara road, Ibadan', 'pending', '2022-05-08 14:42:34'),
(24, 26, 9, 2, 3, '2022-05-06 23:00:00', '2022-05-07 23:00:00', 'No 10, Kwara road, Ibadan', 'rejected', '2022-05-08 14:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `equip_stock`
--

CREATE TABLE `equip_stock` (
  `ID` int NOT NULL,
  `equipments_id` int NOT NULL,
  `total_avail` int NOT NULL,
  `total_used` int NOT NULL,
  `total_left` int NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='useful for report';

--
-- Dumping data for table `equip_stock`
--

INSERT INTO `equip_stock` (`ID`, `equipments_id`, `total_avail`, `total_used`, `total_left`, `date_created`) VALUES
(1, 1, 12, 0, 12, '2022-05-05 02:47:52'),
(2, 2, 10, 0, 10, '2022-05-05 02:49:11'),
(3, 3, 5, 0, 5, '2022-05-05 02:50:08'),
(4, 4, 12, 0, 12, '2022-05-05 02:57:31');

-- --------------------------------------------------------

--
-- Table structure for table `extend_equip_request`
--

CREATE TABLE `extend_equip_request` (
  `ID` int NOT NULL,
  `hirers_id` int NOT NULL,
  `equip_request_id` int NOT NULL,
  `equip_order_id` int NOT NULL,
  `rental_from` timestamp NOT NULL COMMENT 'start from prev booking ends',
  `rental_to` timestamp NOT NULL,
  `request_status` enum('pending','rejected','approved') NOT NULL DEFAULT 'pending',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `hirers`
--

CREATE TABLE `hirers` (
  `ID` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(70) NOT NULL,
  `phone_number` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gender` enum('','male','female','others') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `address_opt` text,
  `local_state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `hirers_path` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `hirers`
--

INSERT INTO `hirers` (`ID`, `fullname`, `email`, `phone_number`, `gender`, `address`, `address_opt`, `local_state`, `country`, `hirers_path`, `status`, `date_created`) VALUES
(8, 'Oluwaseun Alatise', 'holynationdevelopment@gmail.com', '+2348109994485', 'male', 'No 26, Gbemisola street, Allen Avenue, Ikeja', NULL, 'Oyo', 'Nigeria', NULL, 1, '2022-05-04 23:02:24'),
(9, 'Holynation developer', 'holynation667@gmail.com', '+2348109994486', '', NULL, NULL, NULL, NULL, NULL, 1, '2022-05-06 11:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2022-04-13-005932', 'App\\Database\\Migrations\\Owners', 'default', 'App', 1649815152, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting`
--

CREATE TABLE `notification_setting` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `activity_log` tinyint(1) NOT NULL DEFAULT '1',
  `unusual_activity` tinyint(1) NOT NULL DEFAULT '1',
  `new_browser` tinyint(1) NOT NULL DEFAULT '1',
  `lastest_news` tinyint(1) NOT NULL DEFAULT '1',
  `new_features` tinyint(1) NOT NULL DEFAULT '1',
  `account_tips` tinyint(1) NOT NULL DEFAULT '1',
  `date_modififed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `notification_setting`
--

INSERT INTO `notification_setting` (`ID`, `user_id`, `activity_log`, `unusual_activity`, `new_browser`, `lastest_news`, `new_features`, `account_tips`, `date_created`) VALUES
(5, 11, 1, 1, 1, 1, 1, 1, '2022-05-03 11:06:14'),
(6, 12, 1, 1, 1, 1, 1, 1, '2022-05-06 11:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `ID` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `hirers_id` int UNSIGNED NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`ID`, `user_id`, `hirers_id`, `status`, `date_created`) VALUES
(1, 11, 8, 1, '2022-05-04 23:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_otp`
--

CREATE TABLE `password_otp` (
  `ID` int NOT NULL,
  `otp` varchar(10) NOT NULL,
  `user_table_id` int NOT NULL,
  `user_type` enum('hirers') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'hirers',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `password_otp`
--

INSERT INTO `password_otp` (`ID`, `otp`, `user_table_id`, `user_type`, `date_created`, `status`) VALUES
(1, '388306', 8, 'hirers', '2022-05-03 11:48:07', 1),
(2, '524624', 8, 'hirers', '2022-05-03 11:56:03', 1),
(3, '633999', 8, 'hirers', '2022-05-03 12:46:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `ID` int NOT NULL,
  `role_id` int NOT NULL,
  `path` varchar(100) DEFAULT NULL,
  `permission` enum('r','w') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`ID`, `role_id`, `path`, `permission`) VALUES
(1, 1, 'vc/admin/dashboard', 'w'),
(2, 1, 'vc/admin/profile', 'w'),
(3, 1, 'vc/create/admin', 'w'),
(4, 1, 'vc/create/customer', 'w'),
(5, 1, 'vc/create/role', 'w'),
(9, 1, 'vc/admin/plan', 'w'),
(273, 1, 'vc/create/plan', 'w'),
(2474, 1, 'vc/create/business_type', 'w'),
(2775, 1, 'vc/admin/view_model/devices?type=customer', 'w'),
(2776, 1, 'vc/admin/view_model/devices?type=company', 'w'),
(2777, 1, 'vc/create/payment', 'w'),
(2778, 1, 'vc/create/company_package_payment', 'w'),
(2779, 1, 'vc/create/package_payment_history', 'w'),
(2780, 1, 'vc/create/company_package_payment_history', 'w'),
(3415, 1, 'vc/create/package_payment', 'w'),
(9920, 1, 'vc/create/claims_repairs', 'w'),
(9999, 1, 'vc/create/company_claims_repairs', 'w'),
(10897, 3, 'vc/admin/dashboard', 'w'),
(10898, 3, 'vc/create/devices', 'w'),
(11211, 1, 'vc/create/subscription', 'w'),
(11238, 1, 'vc/create/device_type', 'w'),
(11265, 1, 'vc/create/company', 'w'),
(11266, 1, 'vc/create/coupon_code', 'w'),
(11267, 1, 'vc/create/referral', 'w'),
(11268, 1, 'vc/create/promo_bonus_users', 'w'),
(11269, 1, 'vc/create/coupon_users', 'w'),
(11271, 1, 'vc/create/hirers', 'w'),
(11275, 1, 'vc/create/devices', 'w'),
(11277, 1, 'vc/create/reviews', 'w'),
(11278, 1, 'vc/create/equipment', 'w'),
(11279, 1, 'vc/create/equip_payment', 'w'),
(11281, 1, 'vc/create/chat', 'w'),
(11282, 1, 'vc/create/activity_log', 'w'),
(11324, 1, 'vc/create/owners', 'w'),
(11326, 1, 'vc/create/users_payment_details', 'w'),
(11327, 1, 'vc/create/app_setting', 'w'),
(11328, 1, 'vc/create/notification_setting', 'w'),
(11329, 1, 'vc/create/users_setting', 'w'),
(11330, 1, 'vc/create/equipments', 'w'),
(11334, 1, 'vc/create/equip_images', 'w'),
(11335, 1, 'vc/create/equip_order', 'w'),
(11336, 1, 'vc/create/equip_request', 'w'),
(11337, 1, 'vc/create/extend_equip_request', 'w'),
(11338, 1, 'vc/create/equip_delivery_status', 'w'),
(11340, 1, 'vc/create/withdrawal_request', 'w'),
(39649, 1, 'vc/create/chats', 'w');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ID` int NOT NULL,
  `hirers_id` int NOT NULL,
  `equipments_id` int NOT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `ID` int NOT NULL,
  `role_title` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`ID`, `role_title`, `status`) VALUES
(1, 'superadmin', 1),
(2, 'Member', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int NOT NULL,
  `username` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `user_type` enum('admin','hirers','app_hirers') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'app_hirers',
  `user_table_id` int NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activity_log` tinyint(1) NOT NULL DEFAULT '1',
  `fcm_token` varchar(255) DEFAULT NULL,
  `last_logout` timestamp NULL DEFAULT NULL,
  `last_change_password` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `username`, `password`, `user_type`, `user_table_id`, `activity_log`, `fcm_token`, `last_logout`, `last_change_password`, `date_created`, `status`) VALUES
(1, 'admin@gmail.com', '$2y$10$RZHEbRze1mD4JHc0Hk1j6uvctU.uJrjtYvlxUkUlPh5LxN7IEJx/2', 'admin', 1, 1, NULL, NULL, '2021-09-22 13:47:07', '2018-04-05 20:26:09', 1),
(3, 'developer@gmail.com', '$2y$10$RZHEbRze1mD4JHc0Hk1j6uvctU.uJrjtYvlxUkUlPh5LxN7IEJx/2', 'hirers', 2, 1, NULL, NULL, '2021-09-22 13:56:22', '2021-09-10 14:26:49', 1),
(11, 'holynationdevelopment@gmail.com', '$2y$10$LG9HPQf1JRPgsS4Na5PkX.rjqbpY2YvMHhR.9TynKJadKSdICro0C', 'app_hirers', 8, 1, 'uni75y6nfdifn9s', NULL, '2022-05-03 12:47:13', '2022-05-03 10:37:06', 1),
(12, 'holynation667@gmail.com', '$2y$10$rPK/C57ntRG4s9TotSohxeQZ8NjiXtkOV4thzlXaLmUlAfltUyyvi', 'app_hirers', 9, 1, 'uni75y6n45df6', NULL, '2022-05-06 11:45:44', '2022-05-06 11:45:44', 1),
(13, 'developer@codefixbug.com', '$2y$10$RZHEbRze1mD4JHc0Hk1j6uvctU.uJrjtYvlxUkUlPh5LxN7IEJx/2', 'admin', 3, 1, NULL, NULL, '2022-05-10 15:21:02', '2022-05-10 15:21:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_location`
--

CREATE TABLE `users_location` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `ip` varchar(50) NOT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `location` varchar(225) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users_location`
--

INSERT INTO `users_location` (`ID`, `user_id`, `ip`, `latitude`, `longitude`, `location`, `date_created`) VALUES
(1, 11, '::1', 2.17403, 41.4034, 'No 26, Gbemisola street, Allen Avenue', '2022-05-06 11:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `users_payment_details`
--

CREATE TABLE `users_payment_details` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_number` varchar(30) NOT NULL,
  `bank_name` varchar(70) NOT NULL,
  `bank_code` varchar(15) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users_setting`
--

CREATE TABLE `users_setting` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `location` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_modifies` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_request`
--

CREATE TABLE `withdrawal_request` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `owners_id` int NOT NULL,
  `request_number` varchar(30) NOT NULL,
  `amount` float NOT NULL,
  `request_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `app_setting`
--
ALTER TABLE `app_setting`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `earnings`
--
ALTER TABLE `earnings`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equip_delivery_status`
--
ALTER TABLE `equip_delivery_status`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equip_images`
--
ALTER TABLE `equip_images`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equip_order`
--
ALTER TABLE `equip_order`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equip_payment`
--
ALTER TABLE `equip_payment`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD UNIQUE KEY `reference_number_2` (`reference_number`),
  ADD UNIQUE KEY `payment_key` (`transaction_number`),
  ADD UNIQUE KEY `transaction_number` (`transaction_number`);

--
-- Indexes for table `equip_request`
--
ALTER TABLE `equip_request`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `equip_stock`
--
ALTER TABLE `equip_stock`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `extend_equip_request`
--
ALTER TABLE `extend_equip_request`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `hirers`
--
ALTER TABLE `hirers`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `hirers_id` (`hirers_id`);

--
-- Indexes for table `password_otp`
--
ALTER TABLE `password_otp`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `otp` (`otp`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `role_id` (`role_id`,`path`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `role_title` (`role_title`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users_location`
--
ALTER TABLE `users_location`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users_payment_details`
--
ALTER TABLE `users_payment_details`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users_setting`
--
ALTER TABLE `users_setting`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `app_setting`
--
ALTER TABLE `app_setting`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `earnings`
--
ALTER TABLE `earnings`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `equip_delivery_status`
--
ALTER TABLE `equip_delivery_status`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equip_images`
--
ALTER TABLE `equip_images`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `equip_order`
--
ALTER TABLE `equip_order`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `equip_payment`
--
ALTER TABLE `equip_payment`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equip_request`
--
ALTER TABLE `equip_request`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `equip_stock`
--
ALTER TABLE `equip_stock`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `extend_equip_request`
--
ALTER TABLE `extend_equip_request`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hirers`
--
ALTER TABLE `hirers`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `ID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_otp`
--
ALTER TABLE `password_otp`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42465;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users_location`
--
ALTER TABLE `users_location`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_payment_details`
--
ALTER TABLE `users_payment_details`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_setting`
--
ALTER TABLE `users_setting`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
