-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 07, 2025 at 11:13 AM
-- Server version: 10.11.11-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopdb1`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `buy_itm` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `item_id`, `user_name`, `buy_itm`, `item_price`, `item_type`, `created_at`) VALUES
(802, 5, 1320, 'da', 'Card_id: 1320', 5.00, 'Cards', '2025-03-21 02:26:58'),
(803, 5, 849, 'da', 'dump_id: 849', 5.00, 'Dumps', '2025-03-21 02:27:03'),
(804, 5, 883, 'da', 'dump_id: 883', 5.00, 'Dumps', '2025-03-22 08:26:47'),
(805, 5, 1352, 'da', 'Card_id=1352', 5.00, 'Cards', '2025-03-23 17:32:14'),
(806, 5, 1351, 'da', 'Card_id=1351', 5.00, 'Cards', '2025-03-23 17:32:14'),
(807, 5, 1350, 'da', 'Card_id: 1350', 5.00, 'Cards', '2025-03-23 17:32:20'),
(808, 5, 1355, 'da', 'Card_id=1355', 5.00, 'Cards', '2025-03-23 17:39:33'),
(809, 5, 1354, 'da', 'Card_id=1354', 5.00, 'Cards', '2025-03-23 17:39:33'),
(810, 5, 1353, 'da', 'Card_id=1353', 5.00, 'Cards', '2025-03-23 17:39:33'),
(811, 5, 1358, 'da', 'Card_id=1358', 5.00, 'Cards', '2025-03-23 17:46:34'),
(812, 5, 1357, 'da', 'Card_id=1357', 5.00, 'Cards', '2025-03-23 17:46:34'),
(813, 5, 1356, 'da', 'Card_id=1356', 5.00, 'Cards', '2025-03-23 17:46:34'),
(814, 5, 1361, 'da', 'Card_id=1361', 5.00, 'Cards', '2025-03-23 18:01:26'),
(815, 5, 1360, 'da', 'Card_id=1360', 5.00, 'Cards', '2025-03-23 18:01:26'),
(816, 5, 1359, 'da', 'Card_id=1359', 5.00, 'Cards', '2025-03-23 18:01:26'),
(817, 5, 1319, 'da', 'Card_id=1319', 5.00, 'Cards', '2025-03-23 22:29:12'),
(818, 5, 1318, 'da', 'Card_id: 1318', 5.00, 'Cards', '2025-03-23 22:38:04'),
(819, 5, 1317, 'da', 'Card_id: 1317', 5.00, 'Cards', '2025-03-24 14:31:42'),
(820, 5, 1316, 'da', 'Card_id: 1316', 5.00, 'Cards', '2025-03-24 14:32:13'),
(821, 5, 1364, 'da', 'Card_id=1364', 5.00, 'Cards', '2025-03-24 20:56:30'),
(822, 5, 1363, 'da', 'Card_id=1363', 5.00, 'Cards', '2025-03-24 20:56:30'),
(823, 5, 1362, 'da', 'Card_id=1362', 5.00, 'Cards', '2025-03-24 20:56:30'),
(824, 5, 1315, 'da', 'Card_id: 1315', 5.00, 'Cards', '2025-03-24 22:51:34'),
(825, 5, 1313, 'da', 'Card_id=1313', 5.00, 'Cards', '2025-03-24 22:51:40'),
(826, 5, 1365, 'da', 'Card_id=1365', 5.00, 'Cards', '2025-03-29 01:17:40'),
(827, 5, 890, 'da', 'dump_id: 890', 1.00, 'Dumps', '2025-04-06 00:20:01'),
(828, 5, 891, 'da', 'dump_id: 891', 2.00, 'Dumps', '2025-04-06 01:29:22'),
(829, 5, 892, 'da', 'dump_id: 892', 1.00, 'Dumps', '2025-04-06 01:41:15'),
(830, 5, 893, 'da', 'dump_id: 893', 1.00, 'Dumps', '2025-04-06 03:28:49'),
(831, 5, 1314, 'da', 'Card_id: 1314', 5.00, 'Cards', '2025-04-06 03:37:20'),
(832, 5, 83, 'da', 'test', 12.00, 'leads', '2025-04-06 09:48:55'),
(833, 5, 84, 'da', 'test', 12.00, 'tools', '2025-04-06 09:50:07'),
(834, 5, 85, 'da', 'laraib', 15.00, 'pages', '2025-04-06 10:03:51'),
(835, 5, 86, 'da', 'shakeeb', 18.00, 'leads', '2025-04-06 10:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'caca', '$2y$10$zNB7sdUV5lpgEyoZ71BrQuXEwG1.BtAS7qVPvkGSiclBhnDTzGu62');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` datetime NOT NULL,
  `status` enum('success','failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `username`, `ip_address`, `login_time`, `status`) VALUES
(1, 1, NULL, '116.71.163.224', '2025-03-18 19:56:02', 'success'),
(2, 1, NULL, '39.34.148.221', '2025-03-18 21:13:28', 'success'),
(3, 1, NULL, '2.58.73.32', '2025-03-18 22:27:14', 'success'),
(4, NULL, 'admin', '116.71.162.128', '2025-03-18 22:36:19', 'failed'),
(5, 1, NULL, '116.71.162.128', '2025-03-18 22:36:25', 'success'),
(6, 1, NULL, '2.58.73.32', '2025-03-18 23:27:24', 'success'),
(7, 1, NULL, '2.58.73.32', '2025-03-18 23:28:25', 'success'),
(8, 1, NULL, '2.58.73.32', '2025-03-19 00:10:16', 'success'),
(9, 1, NULL, '39.34.144.136', '2025-03-19 00:28:21', 'success'),
(10, 1, NULL, '116.71.161.152', '2025-03-19 02:20:32', 'success'),
(11, 1, NULL, '178.132.108.181', '2025-03-19 07:01:35', 'success'),
(12, NULL, 'caca', '2.58.72.245', '2025-03-19 15:02:50', 'failed'),
(13, 1, NULL, '2.58.72.245', '2025-03-19 15:02:53', 'success'),
(14, 1, NULL, '2.58.72.245', '2025-03-19 15:10:17', 'success'),
(15, 1, NULL, '86.62.30.36', '2025-03-19 15:36:55', 'success'),
(16, 1, NULL, '116.71.167.152', '2025-03-19 15:56:15', 'success'),
(17, 1, NULL, '86.62.30.36', '2025-03-19 16:00:36', 'success'),
(18, 1, NULL, '116.71.167.152', '2025-03-19 16:24:21', 'success'),
(19, 1, NULL, '86.62.30.36', '2025-03-19 17:14:05', 'success'),
(20, 1, NULL, '39.34.144.115', '2025-03-19 18:04:30', 'success'),
(21, 1, NULL, '86.62.30.36', '2025-03-19 18:44:26', 'success'),
(22, 1, NULL, '116.71.162.8', '2025-03-19 18:54:04', 'success'),
(23, 1, NULL, '86.62.30.36', '2025-03-19 19:13:55', 'success'),
(24, 1, NULL, '116.71.162.8', '2025-03-19 19:27:30', 'success'),
(25, 1, NULL, '116.71.162.8', '2025-03-19 19:31:25', 'success'),
(26, 1, NULL, '116.71.162.8', '2025-03-19 19:33:30', 'success'),
(27, 1, NULL, '116.71.162.8', '2025-03-19 19:35:24', 'success'),
(28, 1, NULL, '116.71.162.8', '2025-03-19 19:44:07', 'success'),
(29, 1, NULL, '86.62.30.36', '2025-03-19 20:27:20', 'success'),
(30, 1, NULL, '86.62.30.36', '2025-03-19 20:48:57', 'success'),
(31, 1, NULL, '39.34.151.173', '2025-03-19 22:44:52', 'success'),
(32, 1, NULL, '39.34.151.173', '2025-03-19 22:47:37', 'success'),
(33, 1, NULL, '39.34.151.173', '2025-03-19 23:09:23', 'success'),
(34, 1, NULL, '86.62.30.33', '2025-03-20 07:53:15', 'success'),
(35, 1, NULL, '86.62.30.33', '2025-03-20 07:55:03', 'success'),
(36, 1, NULL, '178.132.108.145', '2025-03-20 15:39:59', 'success'),
(37, 1, NULL, '185.187.243.173', '2025-03-20 20:29:55', 'success'),
(38, 1, NULL, '39.34.150.154', '2025-03-20 21:51:54', 'success'),
(39, 1, NULL, '119.155.192.215', '2025-03-21 02:26:23', 'success'),
(40, 1, NULL, '39.34.151.52', '2025-03-21 09:07:36', 'success'),
(41, 1, NULL, '39.34.151.52', '2025-03-21 10:15:18', 'success'),
(42, 1, NULL, '39.34.151.52', '2025-03-21 10:17:07', 'success'),
(43, 1, NULL, '39.34.151.52', '2025-03-21 10:17:27', 'success'),
(44, 1, NULL, '39.34.151.52', '2025-03-21 10:58:01', 'success'),
(45, 1, NULL, '39.34.151.52', '2025-03-21 11:05:26', 'success'),
(46, 1, NULL, '39.34.151.52', '2025-03-21 11:29:24', 'success'),
(47, 1, NULL, '152.89.160.131', '2025-03-21 12:31:36', 'success'),
(48, 1, NULL, '39.34.151.52', '2025-03-21 12:31:37', 'success'),
(49, NULL, 'caca', '39.34.151.52', '2025-03-21 12:31:48', 'failed'),
(50, 1, NULL, '39.34.151.52', '2025-03-21 12:31:53', 'success'),
(51, 1, NULL, '39.34.151.52', '2025-03-21 12:40:33', 'success'),
(52, 1, NULL, '39.34.151.52', '2025-03-21 12:41:04', 'success'),
(53, 1, NULL, '39.34.151.52', '2025-03-21 12:41:30', 'success'),
(54, 1, NULL, '39.34.151.52', '2025-03-21 12:52:13', 'success'),
(55, 1, NULL, '185.137.39.66', '2025-03-21 13:35:03', 'success'),
(56, 1, NULL, '185.137.39.66', '2025-03-21 13:40:28', 'success'),
(57, 1, NULL, '98.142.240.246', '2025-03-21 18:16:37', 'success'),
(58, 1, NULL, '185.137.39.66', '2025-03-21 20:41:08', 'success'),
(59, 1, NULL, '185.137.39.66', '2025-03-21 20:47:55', 'success'),
(60, 1, NULL, '185.137.39.66', '2025-03-21 20:50:49', 'success'),
(61, 1, NULL, '116.71.176.226', '2025-03-21 20:52:33', 'success'),
(62, 1, NULL, '116.71.176.226', '2025-03-21 20:57:09', 'success'),
(63, 1, NULL, '185.137.39.66', '2025-03-21 22:25:56', 'success'),
(64, NULL, 'admin', '39.34.148.73', '2025-03-22 00:07:40', 'failed'),
(65, 1, NULL, '39.34.148.73', '2025-03-22 00:07:44', 'success'),
(66, 1, NULL, '39.34.148.73', '2025-03-22 01:00:04', 'success'),
(67, NULL, 'caca', '116.71.183.154', '2025-03-22 01:22:27', 'failed'),
(68, 1, NULL, '116.71.183.154', '2025-03-22 01:22:33', 'success'),
(69, 1, NULL, '116.71.183.154', '2025-03-22 01:24:37', 'success'),
(70, 1, NULL, '178.132.108.115', '2025-03-22 08:17:23', 'success'),
(71, 1, NULL, '178.132.108.115', '2025-03-22 08:58:29', 'success'),
(72, 1, NULL, '194.35.123.6', '2025-03-22 17:36:32', 'success'),
(73, NULL, 'caca', '39.34.148.134', '2025-03-22 17:39:51', 'failed'),
(74, 1, NULL, '39.34.148.134', '2025-03-22 17:39:55', 'success'),
(75, 1, NULL, '194.35.232.120', '2025-03-22 20:25:07', 'success'),
(76, 1, NULL, '194.35.232.120', '2025-03-22 21:48:50', 'success'),
(77, 1, NULL, '39.34.145.180', '2025-03-22 22:44:21', 'success'),
(78, 1, NULL, '39.34.145.180', '2025-03-22 22:58:09', 'success'),
(79, 1, NULL, '39.34.145.180', '2025-03-23 01:18:47', 'success'),
(80, 1, NULL, '116.71.178.202', '2025-03-23 02:00:10', 'success'),
(81, 1, NULL, '194.35.232.120', '2025-03-23 11:40:04', 'success'),
(82, 1, NULL, '39.34.150.246', '2025-03-23 12:03:31', 'success'),
(83, NULL, 'caca', '39.34.150.246', '2025-03-23 14:13:02', 'failed'),
(84, 1, NULL, '39.34.150.246', '2025-03-23 14:13:06', 'success'),
(85, 1, NULL, '39.34.150.246', '2025-03-23 14:13:50', 'success'),
(86, 1, NULL, '39.34.150.246', '2025-03-23 14:21:03', 'success'),
(87, 1, NULL, '39.34.150.246', '2025-03-23 14:32:42', 'success'),
(88, 1, NULL, '39.34.150.246', '2025-03-23 14:39:14', 'success'),
(89, 1, NULL, '194.156.224.35', '2025-03-23 17:29:29', 'success'),
(90, 1, NULL, '39.34.144.182', '2025-03-23 17:32:59', 'success'),
(91, 1, NULL, '194.156.224.35', '2025-03-23 18:26:09', 'success'),
(92, 1, NULL, '194.156.224.35', '2025-03-23 18:28:16', 'success'),
(93, 1, NULL, '116.71.178.76', '2025-03-23 19:13:31', 'success'),
(94, 1, NULL, '116.71.178.76', '2025-03-23 19:49:14', 'success'),
(95, 1, NULL, '178.132.109.16', '2025-03-23 22:34:44', 'success'),
(96, 1, NULL, '39.34.150.28', '2025-03-24 14:01:43', 'success'),
(97, 1, NULL, '146.19.88.154', '2025-03-24 17:02:18', 'success'),
(98, 1, NULL, '192.42.116.197', '2025-03-24 17:53:18', 'success'),
(99, 1, NULL, '146.19.88.154', '2025-03-24 20:55:35', 'success'),
(100, 1, NULL, '39.34.151.199', '2025-03-25 23:18:13', 'success'),
(101, 1, NULL, '39.34.151.199', '2025-03-25 23:30:43', 'success'),
(102, 1, NULL, '39.34.151.199', '2025-03-25 23:31:06', 'success'),
(103, 1, NULL, '39.34.151.199', '2025-03-25 23:33:04', 'success'),
(104, 1, NULL, '39.34.151.199', '2025-03-25 23:33:49', 'success'),
(105, 1, NULL, '185.137.36.186', '2025-03-26 09:11:21', 'success'),
(106, 1, NULL, '39.34.148.2', '2025-04-06 00:11:09', 'success'),
(107, 1, NULL, '39.34.148.2', '2025-04-06 00:12:42', 'success'),
(108, 1, NULL, '39.34.150.171', '2025-04-06 05:17:09', 'success'),
(109, 1, NULL, '38.10.174.111', '2025-04-06 08:21:38', 'success'),
(110, 1, NULL, '185.61.158.41', '2025-04-06 10:41:18', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `card_activity_log`
--

CREATE TABLE `card_activity_log` (
  `id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_checked` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_activity_log`
--

INSERT INTO `card_activity_log` (`id`, `card_id`, `card_number`, `status`, `user_id`, `date_checked`, `deleted`) VALUES
(181, 1270, '4155722742275400', 'DEAD', 5, '2025-03-10 10:22:41', 1),
(182, 1264, '4124300645065719', 'DEAD', 5, '2025-03-10 10:25:42', 1),
(183, 1265, '4747879752100071', 'DEAD', 5, '2025-03-10 10:25:52', 1),
(184, 1268, '4973619455960073', 'DEAD', 5, '2025-03-10 10:25:53', 1),
(185, 1269, '4179735109259346', 'DEAD', 5, '2025-03-10 10:25:53', 1),
(186, 1267, '4233958321273172', 'DEAD', 5, '2025-03-10 10:25:55', 1),
(187, 1266, '4990592197892232', 'DEAD', 5, '2025-03-10 10:25:55', 1),
(188, 1261, '4798159648100092', 'DEAD', 5, '2025-03-10 20:48:31', 1),
(189, 1262, '4308327762029777', 'DEAD', 5, '2025-03-10 20:48:32', 1),
(190, 1263, '4443983341224148', 'DEAD', 5, '2025-03-10 20:48:33', 1),
(191, 1286, '2221288619088455', 'DEAD', 5, '2025-03-10 20:57:36', 1),
(192, 1287, '2720685232015505', 'DEAD', 5, '2025-03-10 20:57:37', 1),
(193, 1289, '5285608047392016', 'DEAD', 5, '2025-03-10 20:57:37', 1),
(194, 1288, '5142769694740149', 'DEAD', 5, '2025-03-10 20:57:39', 1),
(195, 1290, '5344207665730638', 'DEAD', 5, '2025-03-10 20:57:39', 1),
(196, 1283, '5361038905176304', 'DEAD', 5, '2025-03-10 20:59:05', 1),
(197, 1284, '5477100348631244', 'DEAD', 5, '2025-03-10 20:59:10', 1),
(198, 1285, '5502462917620579', 'DEAD', 5, '2025-03-10 20:59:11', 1),
(199, 1280, '4725681033628304', 'DEAD', 5, '2025-03-10 21:08:08', 1),
(200, 1281, '5105783653099695', 'DEAD', 5, '2025-03-10 21:08:08', 1),
(201, 1279, '4134175542089532', 'DEAD', 5, '2025-03-10 21:08:09', 1),
(202, 1282, '5229811495248365', 'DEAD', 5, '2025-03-10 21:08:21', 1),
(203, 1278, '4497275532543955', 'DEAD', 5, '2025-03-10 21:08:21', 1),
(204, 1298, '6499850243033912', 'DEAD', 5, '2025-03-10 23:21:43', 1),
(205, 1299, '6011458031782655', 'DEAD', 5, '2025-03-10 23:21:44', 1),
(206, 1300, '6544144035140667', 'DEAD', 5, '2025-03-10 23:21:45', 1),
(207, 1301, '4347695526187639', 'DEAD', 5, '2025-03-10 23:47:09', 1),
(208, 1302, '4610460296890486', 'LIVE', 5, '2025-03-10 23:48:15', 1),
(209, 1303, '4833120168184217', 'LIVE', 5, '2025-03-10 23:49:05', 1),
(210, 1296, '6471964643147000', 'DEAD', 5, '2025-03-13 03:44:55', 1),
(211, 1295, '6468558302869251', 'DEAD', 5, '2025-03-13 03:51:40', 1),
(212, 1291, '6011643020653487', 'DEAD', 5, '2025-03-13 04:13:21', 1),
(213, 1276, '4982586749741031', 'DEAD', 5, '2025-03-13 08:36:05', 1),
(214, 1274, '4193846770869961', 'DEAD', 5, '2025-03-13 08:38:12', 1),
(215, 1272, '4608991099613629', 'DEAD', 5, '2025-03-13 18:52:48', 1),
(216, 1340, '6485080798468376', 'DEAD', 5, '2025-03-14 08:02:44', 1),
(217, 1339, '6474624438510382', 'DEAD', 5, '2025-03-14 09:20:11', 0),
(218, 1336, '6443290799602467', 'DEAD', 5, '2025-03-14 17:33:36', 0),
(219, 1333, '373397674622545', 'DEAD', 5, '2025-03-14 18:01:43', 0),
(220, 1332, '343073215387933', 'DEAD', 5, '2025-03-14 18:01:45', 0),
(221, 1330, '345029177282357', 'DEAD', 5, '2025-03-14 18:01:47', 0),
(222, 1329, '379439218655024', 'DEAD', 5, '2025-03-14 21:01:49', 0),
(223, 1326, '348169651290911', 'DEAD', 5, '2025-03-14 22:20:50', 0),
(224, 1324, '344758042650810', 'DEAD', 5, '2025-03-16 17:42:01', 0),
(225, 1347, '4427562017619368', 'DEAD', 5, '2025-03-20 20:31:49', 0),
(226, 1345, '4411035526016077', 'LIVE', 5, '2025-03-20 20:32:11', 0),
(227, 1346, '4347695528874812', 'LIVE', 5, '2025-03-20 20:32:22', 0),
(228, 1356, '4610460210735569', 'LIVE', 5, '2025-03-23 17:53:51', 0),
(229, 1318, '5534373685818457', 'DEAD', 5, '2025-03-23 22:42:59', 0),
(230, 1317, '5465387427442847', 'DEAD', 5, '2025-03-24 14:31:53', 0),
(231, 1362, '4610460210735569', 'LIVE', 5, '2025-03-24 20:57:05', 0),
(232, 1363, '4060425578934216', 'LIVE', 5, '2025-03-24 20:57:37', 0),
(233, 1364, '4060425573226824', 'LIVE', 5, '2025-03-24 20:58:26', 0),
(234, 1313, '4741753386327493', 'DEAD', 5, '2025-03-24 22:52:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `credit_cards`
--

CREATE TABLE `credit_cards` (
  `id` int(11) NOT NULL,
  `card_number` varbinary(255) DEFAULT NULL,
  `mm_exp` varchar(10) DEFAULT NULL,
  `yyyy_exp` varchar(10) DEFAULT NULL,
  `cvv` varbinary(255) DEFAULT NULL,
  `name_on_card` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `mmn` varchar(100) DEFAULT 'N/A',
  `account_number` varchar(100) DEFAULT 'N/A',
  `sort_code` varchar(100) DEFAULT 'N/A',
  `cardholder_name` varchar(100) DEFAULT 'N/A',
  `phone_number` varchar(70) DEFAULT NULL,
  `date_of_birth` varchar(70) DEFAULT NULL,
  `base_name` varchar(100) NOT NULL DEFAULT 'NA',
  `full_name` varchar(255) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `otherinfo` varchar(255) NOT NULL DEFAULT 'NA',
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'unsold',
  `is_view` int(11) NOT NULL DEFAULT 0,
  `buyer_id` int(11) DEFAULT NULL,
  `sinssn` varchar(255) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `drivers` varchar(255) DEFAULT NULL,
  `purchased_at` timestamp NULL DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `cc_status` varchar(10) DEFAULT NULL,
  `refundable` varchar(50) NOT NULL DEFAULT 'Non-Refundable',
  `deleted` tinyint(1) DEFAULT 0,
  `seller_reversed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_cards`
--

INSERT INTO `credit_cards` (`id`, `card_number`, `mm_exp`, `yyyy_exp`, `cvv`, `name_on_card`, `address`, `city`, `zip`, `country`, `mmn`, `account_number`, `sort_code`, `cardholder_name`, `phone_number`, `date_of_birth`, `base_name`, `full_name`, `seller_id`, `section`, `created_at`, `price`, `seller_name`, `state`, `card_type`, `otherinfo`, `email`, `status`, `is_view`, `buyer_id`, `sinssn`, `pin`, `drivers`, `purchased_at`, `checked_at`, `cc_status`, `refundable`, `deleted`, `seller_reversed`) VALUES
(1261, 0x1e6ceb3d6999c03fc649a3ec7a4eb9d546375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:48:21', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:48:21', '2025-03-10 20:48:31', 'dead', '5 Minutes', 0, 1),
(1262, 0xdf2fb7d698f32274e8c79fa65a35109b46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:48:21', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:48:21', '2025-03-10 20:48:32', 'dead', '5 Minutes', 0, 1),
(1263, 0xbd8068023789672481475966ec5eb0a646375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:48:21', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:48:21', '2025-03-10 20:48:33', 'dead', '5 Minutes', 0, 1),
(1264, 0x3b6daed22df16404f8be540976b238b846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:25:42', 'dead', '5 Minutes', 0, 1),
(1265, 0xd1ff604ac0494b1ed7d3fd7edf6739cd46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:26:13', 'DISABLED', '5 Minutes', 0, 1),
(1266, 0x58412b73be9cfa1198c748a8f640533446375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:26:14', 'DISABLED', '5 Minutes', 0, 1),
(1267, 0x31227bbcfa6d18fe3dd240d5da8b37c946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:26:15', 'DISABLED', '5 Minutes', 0, 1),
(1268, 0x2d2550f4a123ee9dde815c188799b37a46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:26:15', 'DISABLED', '5 Minutes', 0, 1),
(1269, 0x7a2e89dbaaba90381ba360018988f01446375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:25:12', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:25:12', '2025-03-10 10:26:16', 'DISABLED', '5 Minutes', 0, 1),
(1270, 0x77fcc5a50974dfee2ffc50140c7fe9fa46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 10:21:53', 1.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 10:22:25', '2025-03-10 10:22:41', 'dead', '5 Minutes', 0, 1),
(1271, 0x12e5ae434d3f62fe8ae2293a2857d2f446375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:05', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1272, 0xb99075bd7f34a71f5d920763ab8f543d46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:05', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 18:52:41', '2025-03-13 18:52:48', 'dead', '5 Minutes', 0, 1),
(1273, 0x1369fa30e24dae467ab6e067e7a680c546375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 08:54:36', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 08:54:36', NULL, 'unchecked', '5 Minutes', 1, 0),
(1274, 0x1594bdc0934c76340a3226264399031b46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 08:36:58', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 08:36:58', '2025-03-13 08:38:12', 'dead', '5 Minutes', 0, 1),
(1275, 0xe28254d6eda657747bdec9f6f9f91d8c46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 08:36:58', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 08:36:58', NULL, 'unchecked', '5 Minutes', 1, 0),
(1276, 0x5b792d690c723dbd18115cf861a74f8446375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:05', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 08:35:32', '2025-03-13 08:36:05', 'dead', '5 Minutes', 0, 1),
(1277, 0xa92bd06d4d74f41e5c086ebd5994bf1b46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 04:13:36', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 04:13:36', NULL, 'unchecked', '5 Minutes', 1, 0),
(1278, 0x2fc4f7d7caf85750cea4a0ced5f027b246375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 21:07:59', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 21:07:59', '2025-03-10 21:08:21', 'dead', '5 Minutes', 0, 1),
(1279, 0x5f568919bc9bb3b18222bae73755657946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 21:07:59', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 21:07:59', '2025-03-10 21:08:09', 'dead', '5 Minutes', 0, 1),
(1280, 0x7bc65dc1699c60fe155bc145bb2900a346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 21:07:59', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 21:07:59', '2025-03-10 21:08:08', 'dead', '5 Minutes', 0, 1),
(1281, 0x1b3abcd1a971859756cec0b67cd67d1e46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 21:07:59', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 21:07:59', '2025-03-10 21:08:08', 'dead', '5 Minutes', 0, 1),
(1282, 0x8c85e3085f2f8e7e2298e42dcc5a1fa946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 21:07:59', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 21:07:59', '2025-03-10 21:08:21', 'dead', '5 Minutes', 0, 1),
(1283, 0x42fe9e9a9786236b7368eed88218dd1246375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:58:54', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:58:54', '2025-03-10 20:59:05', 'dead', '5 Minutes', 0, 1),
(1284, 0x9284b918e889f18c76d64f335682d57546375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:58:54', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:58:54', '2025-03-10 20:59:10', 'dead', '5 Minutes', 0, 1),
(1285, 0x68b279828b898c41a5bddcf905f717cd46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:58:54', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:58:54', '2025-03-10 20:59:11', 'dead', '5 Minutes', 0, 1),
(1286, 0x5fdd5899cda40ad2d71b2ddc9107bfd746375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:26', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:57:26', '2025-03-10 20:57:36', 'dead', '5 Minutes', 0, 1),
(1287, 0x415474c3b638dc5e4fd2b32f8be4917846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:26', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:57:26', '2025-03-10 20:57:37', 'dead', '5 Minutes', 0, 1),
(1288, 0x842af2dd049597bc66093dd0cecde26846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:26', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:57:26', '2025-03-10 20:57:39', 'dead', '5 Minutes', 0, 1),
(1289, 0x1c916b61d62ab41a7e7059cb2861fc1046375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:26', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:57:26', '2025-03-10 20:57:37', 'dead', '5 Minutes', 0, 1),
(1290, 0x5ac47281071f36c36deda2c9e8a6a8c746375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 20:57:26', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 20:57:26', '2025-03-10 20:57:39', 'dead', '5 Minutes', 0, 1),
(1291, 0xd9012512ed88972ec1a9289ceb2ab2e346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 04:13:14', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 04:13:14', '2025-03-13 04:13:21', 'dead', '5 Minutes', 0, 1),
(1292, 0x355d1204433abe31ecda5a32cc20690546375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 04:07:14', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 04:07:14', NULL, 'unchecked', '5 Minutes', 1, 0),
(1293, 0x29dacf70a28dd168f3ace2a37b7b2eac46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 04:01:56', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 04:01:56', NULL, 'unchecked', '5 Minutes', 1, 0),
(1294, 0xd0ca06b8ee15d8937e95c26dfef0266d46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 03:54:06', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 03:54:06', NULL, 'unchecked', '20 Minutes', 1, 0),
(1295, 0x8ab2bb88afd11c158c29e59fc355628346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 03:46:48', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 03:46:48', '2025-03-13 03:51:40', 'dead', '5 Minutes', 0, 1),
(1296, 0x9b5d00a65dca0874f3e003e34e98501346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 03:44:20', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 03:44:20', '2025-03-13 03:44:55', 'dead', '5 Minutes', 0, 1),
(1297, 0xd7d4c6c53f46136ea9bd44817155399946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-12 19:45:11', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-12 19:45:11', NULL, 'unchecked', '5 Minutes', 1, 0),
(1298, 0x47f0d69671e604e7cf703a75c2c770aa46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 23:21:20', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 23:21:20', '2025-03-10 23:21:43', 'dead', '5 Minutes', 0, 1),
(1299, 0x3078c1731ad2ddf64bde561a4b00e3b346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 23:21:20', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 23:21:20', '2025-03-10 23:21:44', 'dead', '5 Minutes', 0, 1),
(1300, 0xadd0d08946b1da6e191dcef330c09e3646375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-10 23:21:20', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-10 23:21:20', '2025-03-10 23:21:45', 'dead', '5 Minutes', 0, 1),
(1301, 0x74b0ad7750d79e8d9173b1ab73ffcef446375c9d7b811e5cd14998055885d94b, '11', '25', 0x545e050b39ad525f36950ee1926789f0, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'N/A', 'N/A', 5, 'credit_cards', '2025-03-10 23:46:34', 5.00, 'da', 'N/A', 'Visa', 'No', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-10 23:46:44', '2025-03-10 23:47:09', 'dead', '5 Minutes', 0, 1),
(1302, 0x99034d2494a57b5cd54574a5e7f7d76946375c9d7b811e5cd14998055885d94b, '02', '28', 0xb0a31af9cd52b90245c9382e0bac95c6, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'N/A', 'N/A', 5, 'credit_cards', '2025-03-10 23:47:59', 5.00, 'da', 'N/A', 'Visa', 'No', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-10 23:48:06', '2025-03-10 23:48:15', 'Live', '5 Minutes', 1, 0),
(1303, 0xb2e236052ce3313749c2651cee37207546375c9d7b811e5cd14998055885d94b, '12', '27', 0x4dff87da5370ce77f54525e1af5ffe50, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'N/A', 'N/A', 5, 'credit_cards', '2025-03-10 23:48:48', 5.00, 'da', 'N/A', 'Visa', 'No', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-10 23:48:53', '2025-03-10 23:49:05', 'Live', '5 Minutes', 1, 0),
(1304, 0x83361481537a055da11dbbd7bb047bd346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1305, 0x3c55e7c429080198eda6c68fe4e30cef46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1306, 0x4bd5e97c3064dec54030db5b415ef25246375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1307, 0x94fd0868e20b9df94e567294c81f957146375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1308, 0xeae8b70548bdf47aae0687434767182046375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1309, 0x7fd546efa727e325fb3614cbb921909846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1310, 0x4533f1a8969a14a7d7139614e90a1f2e46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1311, 0x66db482695a16a454028304ba10e876a46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1312, 0xce1708510c5979e4c3c64af04152fae946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'unsold', 0, NULL, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', NULL, NULL, 'unchecked', '5 Minutes', 0, 0),
(1313, 0xc0f859110fa95a3ad2c88900502a5ce346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-24 22:51:40', 5.00, 'da', 'London', 'Visa', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-24 22:51:40', '2025-03-24 22:52:30', 'dead', '5 Minutes', 0, 1),
(1314, 0xd3f58efb73d13997cbe0c41fea8a751846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-04-06 03:37:20', NULL, 'unchecked', '5 Minutes', 0, 0),
(1315, 0x5508267fbe24a14f1223b5ea77bf53de46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-24 22:51:34', NULL, 'unchecked', '5 Minutes', 0, 0),
(1316, 0xac50bc88b0dc0f78bb715b1dba3e988e46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-24 14:32:13', NULL, 'unchecked', '5 Minutes', 0, 0),
(1317, 0x6636e1ffba28d655178bfdb4915c330946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-24 14:31:42', '2025-03-24 14:31:53', 'dead', '5 Minutes', 0, 1),
(1318, 0x462c86febd995d06579364353626cac746375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-23 22:38:04', '2025-03-23 22:42:59', 'dead', '5 Minutes', 0, 1),
(1319, 0xfacb9af8ec6f223af392f1256ecc6f3646375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-23 22:29:12', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-23 22:29:12', NULL, 'unchecked', '5 Minutes', 1, 0),
(1320, 0x4e101c9420340552ec1169fd0c46c2bf46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-21 02:40:58', NULL, 'unchecked', '5 Minutes', 1, 0),
(1321, 0xf9f5a701b17a578d0ed8626b17f3dfc846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-18 02:29:22', NULL, 'unchecked', '5 Minutes', 1, 0),
(1322, 0x9ae3f734e86fd6a0007bf42c1f2d50c346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-16 22:47:05', NULL, 'unchecked', '5 Minutes', 1, 0),
(1323, 0x305e2b81e93f9b0189d8d5aa6abaeef846375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'MasterCard', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-17 14:18:06', NULL, 'unchecked', '5 Minutes', 1, 0),
(1324, 0x7deb704981226a73854dfaa88e1649a4, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-16 17:41:12', '2025-03-16 17:42:01', 'dead', '5 Minutes', 0, 1),
(1325, 0x9c292f1b9b4c37ca771dc4f23e86bb8a, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-15 05:49:14', NULL, 'unchecked', '5 Minutes', 1, 0),
(1326, 0xa3f495da001287ec4bfa842f37de8c28, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 22:20:33', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 22:20:33', '2025-03-14 22:21:09', 'DISABLED', '5 Minutes', 0, 1),
(1327, 0x3d03f90f8bdbf5ba334ce2a6af43eb95, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 22:20:33', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 22:20:33', NULL, 'unchecked', '5 Minutes', 1, 0),
(1328, 0x932e7718d3da014cdd40e463a34f5ee5, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 22:20:33', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 22:20:33', NULL, 'unchecked', '5 Minutes', 1, 0),
(1329, 0xb1bdfdea17f7ebca07b13adcfd832465, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 21:01:31', '2025-03-14 21:02:14', 'DISABLED', '5 Minutes', 0, 1),
(1330, 0x05469934eefefcf0953bce8934c5c7e6, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 18:01:30', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 18:01:30', '2025-03-14 18:01:47', 'dead', '5 Minutes', 0, 1),
(1331, 0x4110e4dd1b6d8884ee851a51fceb4af1, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 18:14:17', NULL, 'unchecked', '5 Minutes', 1, 0),
(1332, 0x3f53e6aa5d57211424d9da1ceac6acc3, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 18:01:30', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 18:01:30', '2025-03-14 18:01:45', 'dead', '5 Minutes', 0, 1),
(1333, 0xf4e05e6ef37348a00dd1297fe490a7cf, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 18:01:30', 5.00, 'da', 'London', 'American Express', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 18:01:30', '2025-03-14 18:01:43', 'dead', '5 Minutes', 0, 1),
(1334, 0x379378a6b3006ab7f2423561d0d1e31a46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 17:49:25', NULL, 'unchecked', '5 Minutes', 1, 0),
(1335, 0x59827ee65cdb4e65795448cbed324a1346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 17:40:20', NULL, 'unchecked', '20 Minutes', 1, 0),
(1336, 0x7bb4aec7c843715abeebca8090b756a946375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 17:33:23', '2025-03-14 17:33:36', 'dead', '5 Minutes', 0, 1),
(1337, 0x24ba763470952015ed395744d0a2aed446375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 09:19:48', NULL, 'unchecked', '5 Minutes', 1, 0),
(1338, 0x551f6044fccacbd9cf7e6be8bdc0950646375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 09:19:33', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 09:19:33', NULL, 'unchecked', '5 Minutes', 1, 0),
(1339, 0x99d4c1379f48743511980e1e83137f0e46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 09:19:33', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 09:19:33', '2025-03-14 09:20:11', 'dead', '5 Minutes', 0, 1),
(1340, 0x465c8ee0f95376628650a294503408a346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 08:00:51', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 08:00:51', '2025-03-14 08:02:44', 'dead', '5 Minutes', 0, 1),
(1341, 0xf21004a9c51b2cbe2a2f4df4ec2904fc46375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 08:00:51', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 08:00:51', NULL, 'unchecked', '5 Minutes', 1, 0),
(1342, 0x972a837787495210e07c5736b233899746375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-14 08:00:51', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941\r', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-14 08:00:51', NULL, 'unchecked', '5 Minutes', 1, 0),
(1343, 0x7eadec3bab48b60944dd59071a6e9dc346375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-13 19:12:43', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 22:37:12', NULL, 'unchecked', '5 Minutes', 1, 0),
(1346, 0xb2e236052ce3313749c2651cee37207546375c9d7b811e5cd14998055885d94b, '04', '29', 0x003e7701579d8e4672f5cc0d71d998ab, 'James', '182 roadway', 'London', 'CV1 2BG', 'UK', 'MMN: carolina', 'acc: 92-04-19', 'sort: 04-92-11', 'James', '07462281921', '1955-10-04', 'UK_CVV_2025', 'N/A', 5, 'credit_cards', '2025-03-20 20:32:22', 5.00, 'da', 'London', 'Discover', 'Yes', 'james@yahoo.com', 'sold', 1, 5, 'N/A', '9941', 'Driver: 8932F98D2BE78D81N8D291 21', '2025-03-13 22:37:12', NULL, 'live', '5 Minutes', 1, 0),
(1362, 0x34cd6fac48fd720e6e2854846c2a409c46375c9d7b811e5cd14998055885d94b, '04', '27', 0x7ed6b88a59f72cf742d9936b41304f1c, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'US_BASE', NULL, 5, 'credit_cards', '2025-03-24 20:56:30', 5.00, 'da', 'N/A', 'Visa', 'Yes', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-24 20:56:30', '2025-03-24 20:57:05', 'Live', '5 Minutes', 0, 0),
(1363, 0x48547c9fe50a282618aafe4b1dd6e59a46375c9d7b811e5cd14998055885d94b, '06', '28', 0xe69342bb6efa34e5480c52698ae65616, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'US_BASE', NULL, 5, 'credit_cards', '2025-03-24 20:56:30', 5.00, 'da', 'N/A', 'Visa', 'Yes', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-24 20:56:30', '2025-03-24 20:57:37', 'Live', '5 Minutes', 0, 0),
(1364, 0xd7520172f987d8f363bbf140ea48986246375c9d7b811e5cd14998055885d94b, '06', '26', 0x3fbf35eba9814df3b5451040c83186bc, 'N/A', 'N/A', 'N/A', 'N/A', 'US', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'US_BASE', NULL, 5, 'credit_cards', '2025-03-24 20:56:30', 5.00, 'da', 'N/A', 'Visa', 'Yes', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-24 20:56:30', '2025-03-24 20:58:26', 'Live', '5 Minutes', 0, 0),
(1365, 0x6df31b6360f15914dd4d6ec44624dece46375c9d7b811e5cd14998055885d94b, '04', '25', 0x86c7c0735675e047565e6c19f12f7f2f, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', NULL, 'N/A', NULL, 5, 'credit_cards', '2025-03-29 01:17:40', 5.00, 'da', 'N/A', 'Visa', 'Yes', 'N/A', 'sold', 1, 5, 'N/A', 'N/A', 'N/A', '2025-03-29 01:17:40', NULL, '', 'Non-Refundable', 0, 0),
(1366, 0x33182e1130842e9fba5f7912f4bf5e62, '12', '28', 0xc08780d8f7fd81403c6abf5055894d12, 'LALA', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'LALA', 'N/A', NULL, 'N/A', NULL, 43, 'credit_cards', '2025-04-06 05:17:50', 1.00, 'LaraibR', 'N/A', 'N/A', 'Yes', 'N/A', 'unsold', 0, NULL, 'N/A', 'N/A', 'N/A', NULL, NULL, '', 'Non-Refundable', 0, 0);

--
-- Triggers `credit_cards`
--
DELIMITER $$
CREATE TRIGGER `before_insert_credit_cards` BEFORE INSERT ON `credit_cards` FOR EACH ROW BEGIN
    SET NEW.country = UPPER(TRIM(NEW.country));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_credit_cards` BEFORE UPDATE ON `credit_cards` FOR EACH ROW BEGIN
    SET NEW.country = UPPER(TRIM(NEW.country));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `set_buyer_id_to_null` BEFORE UPDATE ON `credit_cards` FOR EACH ROW BEGIN
    IF NEW.status = 'unsold' THEN
        SET NEW.buyer_id = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_credit_cards_balance` AFTER UPDATE ON `credit_cards` FOR EACH ROW BEGIN
    -- Check if the status has changed to "unsold"
    IF NEW.status = 'unsold' AND OLD.status != 'unsold' THEN
        UPDATE users
        SET credit_cards_balance = credit_cards_balance - (NEW.price * (seller_percentage / 100))
        WHERE id = NEW.seller_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dumps`
--

CREATE TABLE `dumps` (
  `id` int(11) NOT NULL,
  `track1` varbinary(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT 'NA',
  `track2` varbinary(255) DEFAULT NULL,
  `monthexp` varchar(10) DEFAULT NULL,
  `yearexp` varchar(10) DEFAULT NULL,
  `pin` varchar(20) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `base_name` varchar(100) NOT NULL DEFAULT 'NA',
  `seller_name` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'unsold',
  `card_type` varchar(50) DEFAULT NULL,
  `is_view` int(11) NOT NULL DEFAULT 0,
  `buyer_id` int(11) DEFAULT NULL,
  `Refundable` varchar(255) DEFAULT NULL,
  `dump_status` varchar(255) NOT NULL DEFAULT 'unchecked',
  `purchased_at` datetime DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `seller_reversed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dumps`
--

INSERT INTO `dumps` (`id`, `track1`, `code`, `track2`, `monthexp`, `yearexp`, `pin`, `seller_id`, `created_at`, `price`, `country`, `base_name`, `seller_name`, `status`, `card_type`, `is_view`, `buyer_id`, `Refundable`, `dump_status`, `purchased_at`, `checked_at`, `deleted`, `seller_reversed`) VALUES
(837, NULL, '101', 0x84c1dc89644080f15bcdd0982cc26c7ac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(838, NULL, '101', 0x48747434750990255329742f1ab3dcc1c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(839, NULL, '101', 0x136539964ad9541a9b5d149fe43d2afac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(840, NULL, '101', 0x312a285b422930d03e9ec6516681fdf8c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(841, NULL, '101', 0x617b1eda291df5d3a972ec0d90d183cac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(842, NULL, '101', 0xe8e69ed4f3f51839c27d98578559b826c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(843, NULL, '101', 0xec372206f5bb4bf9b5ddb1d62b128807c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(844, NULL, '101', 0x3ddac893534722379ad0fc390b79692ec9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(845, NULL, '101', 0x71048eed8af0c78db6a5b62d3ed2d498c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(846, NULL, '101', 0xf10c834b982a11100155982431758d7ac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'Visa', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(847, NULL, '101', 0x270a961939b4408bea49fb0f4064e6dec9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'mastercard', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(848, NULL, '101', 0xc1e58b3475c619683aa2e312a3de6f82c9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '0421', 5, '2025-03-13 19:14:45', 5.00, 'UK', 'Dumps_uk_base_2025\r', 'da', 'unsold', 'mastercard', 0, NULL, '5 Minutes', 'unchecked', NULL, NULL, 0, 0),
(884, 0x600bd600cebe5acfc17921a21962affea9b0be930011bd467a0a4f3d29dad2610af61d72b8a4e13c5bc716addef235d9c51b69e969c929e905d8e2299af225e9, '121', 0x842755c75236f10f11e00a6ba8b5abe23c3b65636b26117ff6dbfc9ca53aff82ff1ff8a3f5f08edb36546b49f208308f, '01', '24', '635', 43, '2025-03-22 22:58:44', 1.00, 'UK', '8761', 'LaraibR', 'unsold', 'N/A', 0, NULL, 'Not-Refundable', 'unchecked', NULL, NULL, 0, 0),
(893, 0x49ec7851a5dd54b91622ae7bbdc555bac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '101', 0x49ec7851a5dd54b91622ae7bbdc555bac9dfb760d589a8443a288b1fac8cc2065ae9d6b30f692243db2319b4269ed8c4, '04', '29', '6666', 43, '2025-04-06 02:13:20', 1.00, 'PK', '666ECRYPT', 'LaraibR', 'sold', 'N/A', 1, 5, 'Not-Refundable', 'unchecked', '2025-04-06 03:28:49', NULL, 0, 0);

--
-- Triggers `dumps`
--
DELIMITER $$
CREATE TRIGGER `before_insert_dumps` BEFORE INSERT ON `dumps` FOR EACH ROW BEGIN
    SET NEW.country = UPPER(TRIM(NEW.country));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_dumps` BEFORE UPDATE ON `dumps` FOR EACH ROW BEGIN
    SET NEW.country = UPPER(TRIM(NEW.country));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `set_buyer_id_to_null_in_dumps` BEFORE UPDATE ON `dumps` FOR EACH ROW BEGIN
    IF NEW.status = 'unsold' THEN
        SET NEW.buyer_id = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update2_credit_cards_balance` AFTER UPDATE ON `dumps` FOR EACH ROW BEGIN
    -- Check if the status has changed to "unsold"
    IF NEW.status = 'unsold' AND OLD.status != 'unsold' THEN
        -- Deduct the calculated amount from the seller's dumps_balance
        UPDATE users
        SET dumps_balance = dumps_balance - (NEW.price * (seller_percentage / 100))
        WHERE id = NEW.seller_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dumps_activity_log`
--

CREATE TABLE `dumps_activity_log` (
  `id` int(11) NOT NULL,
  `dump_id` int(11) NOT NULL,
  `track1` varchar(255) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `date_checked` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `created_at`) VALUES
(17, 'Shop update', 'tradalalalala', '2024-10-30 11:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `tool_id`, `created_at`) VALUES
(103, 5, 84, '2025-04-06 09:50:07'),
(104, 5, 85, '2025-04-06 10:03:51'),
(105, 5, 86, '2025-04-06 10:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `btc_address` varchar(64) NOT NULL,
  `amount_usd` float NOT NULL,
  `amount_usd_margin` float NOT NULL,
  `amount_btc` decimal(16,8) NOT NULL,
  `received_payment` varchar(100) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `qr_uri` varchar(255) NOT NULL,
  `tx_hash` varchar(255) DEFAULT NULL,
  `request_id` varchar(255) NOT NULL,
  `status` enum('PENDING','RECEIVING','CONFIRMED','EXPIRED','INSUFFICIENT','CANCELLED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `section_view` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `section_view`) VALUES
(1, 'Credit Cards', 1),
(2, 'Dumps', 1),
(3, 'My Cards', 1),
(4, 'My Dumps', 1),
(5, 'Tools', 1),
(6, 'Leads', 1),
(7, 'Pages', 1),
(8, 'My Orders', 1);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `input_type` varchar(50) NOT NULL DEFAULT 'text',
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `key`, `value`, `input_type`, `url`) VALUES
(20, 'checker_name', 'Lux Checker', 'dropdown', NULL),
(21, 'checker_status', '1', 'dropdown', NULL),
(22, 'margin', '0.02', 'input', NULL),
(26, 'Mirror', 'Mirror2', 'text', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `support_replies`
--

CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_replies`
--

INSERT INTO `support_replies` (`id`, `ticket_id`, `sender`, `message`, `is_read`, `created_at`) VALUES
(533, 123, 'user', 'kP+4CpZTU1RVEQmwSGpeazQ3UkJHa2p0TlkrNXV2TXZwRXM3WEE9PQ==', 1, '2025-03-18 00:47:52'),
(534, 123, 'admin', 'KlS7bgEROz53PHL2q36jWkxURmFLQnFKZ1FpNzhGMTVmTUZ4Z2c9PQ==', 1, '2025-03-18 01:20:22'),
(535, 123, 'user', 'Y6CaZQRfqjLESB/XpM2kX3Vvc2JJT25aaHp6MUJJRy9lWlNPRXc9PQ==', 1, '2025-03-18 01:23:14'),
(536, 123, 'admin', 'YI61jpb1rggP4KX3LJhtMXd3aERrQy91Y3N5dW1WaUVYRUlSR3c9PQ==', 1, '2025-03-18 01:23:39'),
(538, 123, 'admin', 'ZcpuMwBI2C9oWlIfHWkPxXRmUUE1RnJNdTdEK25TSVgxNGxqV3c9PQ==', 1, '2025-03-18 02:20:17'),
(539, 123, 'admin', 'q3R6VoHWK0UjZsa0c/3GOERMVXNvQ1IrU25Tb21ra0tGUFNWQkE9PQ==', 1, '2025-03-18 02:20:55'),
(540, 123, 'admin', 'kqpglZiV2pyoQgotPGI3g0lpSEk4aDYrZnU0Umdjd1Q5MWN2eDVqWkNkRTBDY0xNc213RmZEYWVmR0krOFlvWDlXc0dsdVF3b24wbFRLNlVMOHVZLzBjTnRIVFgwb3hkak9nNS83SzFOeUtQQUMxNW5xUnZtM3pNYmpHWWdvaGRyM1JtQ0xlWXlQK0VuTUo1', 1, '2025-03-18 02:22:36'),
(541, 123, 'admin', 'OAmANe3luY7TL13aJfrOr0RWVEFBbHZrUVlTQ2JJTEtVc3NwUVQrQlpqelZxODQ5d1JwSDZWUWYyMm5XemVpK0tHaStwVUllekFaNTVEUWU=', 1, '2025-03-18 02:27:10'),
(542, 123, 'user', 'H5DboEHUfJ0Y/isK7WZY6TU1L2RLcERkeUJsKytWRklUalNXWVE9PQ==', 1, '2025-03-18 02:29:33'),
(543, 124, 'user', 'buc+NblSL3EXnbTnErLqtGh1ZTdpeW9OUElHeEo5SjdhaE4zMGc9PQ==', 1, '2025-03-18 09:37:24'),
(544, 124, 'admin', 'VAY3AAEO15tYy/uNv6IlPGRuS3dUZkdKRk5Oam5NT1hqZHA1bWc9PQ==', 1, '2025-03-18 09:37:51'),
(545, 124, 'admin', 'z4CkxXkRHnmg855rIlg66TExQVZwZEY4UWRhbm9GQkRFMkNxdFZIOVYwamFWK0xmcGk4QnBYVndrZUk9', 1, '2025-03-18 09:38:16'),
(546, 125, 'user', 'CPmqNqYyzYkVUWORhyqQJFg3Y1l1RDdxRmd5TXZkd3BINGtFV3c9PQ==', 1, '2025-03-18 22:05:04'),
(547, 126, 'user', '05Rx3lkoT69tLu59OZZlYWtkSFZjdU9qSk1iRGhLaGdiUm9BNHc9PQ==', 1, '2025-03-18 23:31:36'),
(548, 126, 'admin', 'u/h5asySaslItrSKPtqp9W5Lc1dpVHFTUnBYN051a0RoVDk5Rmc9PQ==', 1, '2025-03-18 23:31:47'),
(549, 126, 'user', 'EP6X+ez82C7tf/0BmOSy7XVTQjBETmpSSSswbkduRHZ3RnRndXc9PQ==', 1, '2025-03-19 16:01:25'),
(550, 126, 'admin', 'hxUr1ei+x4OIWbqhQJt6Tm14TVN5V2pxLzRLWmdwcDB2TndxZEE9PQ==', 1, '2025-03-19 16:01:32'),
(551, 126, 'user', 'kEr2oqJs0mMHjCOCw1IcdEp3Mll5clJOeTl6dDluRmtZWm5uU0E9PQ==', 1, '2025-03-19 17:14:22'),
(552, 126, 'admin', 'CXxc9oha33E+Nt6JYkOrNFBRUHNRM2VmcFJvM3J4eEpDd2Q0bFE9PQ==', 1, '2025-03-19 17:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text DEFAULT '',
  `response` text DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_unread` tinyint(4) DEFAULT 0,
  `last_user_reply` tinyint(4) DEFAULT 1,
  `unread` tinyint(1) DEFAULT 0,
  `admin_unread` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `message`, `response`, `status`, `created_at`, `username`, `subject`, `updated_at`, `user_unread`, `last_user_reply`, `unread`, `admin_unread`) VALUES
(123, 5, '', '<p><strong>Testing123</strong></p>', 'closed', '2025-03-18 00:47:52', 'da', 'Support Ticket', '2025-03-18 09:35:23', 0, 1, 1, 1),
(124, 5, '', NULL, 'closed', '2025-03-18 09:37:24', 'da', 'Support Ticket', '2025-03-18 10:09:22', 0, 1, 1, 1),
(125, 5, '', NULL, 'closed', '2025-03-18 22:05:04', 'da', 'Support Ticket', '2025-03-18 22:36:39', 0, 1, 1, 1),
(126, 5, '', NULL, 'closed', '2025-03-18 23:31:36', 'da', 'Support Ticket', '2025-03-19 17:56:20', 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Updates`
--

CREATE TABLE `Updates` (
  `id` int(11) NOT NULL,
  `Text` text DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `Gif` varchar(255) DEFAULT NULL,
  `Video` varchar(255) DEFAULT NULL,
  `Tag` varchar(255) DEFAULT NULL,
  `Milestone` varchar(255) DEFAULT NULL,
  `Event` varchar(255) DEFAULT NULL,
  `Active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `name` varbinary(255) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `file_path` varbinary(255) DEFAULT NULL,
  `price` varbinary(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `section` enum('leads','tools','pages') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `name`, `description`, `file_path`, `price`, `created_at`, `section`) VALUES
(77, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012fb5831fd5d4a12984c429b06d5ecb89149f59bdc3476d234a94a8e8a7bfe15fa7, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:12:13', 'pages'),
(78, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f6fdd953857471fbc476328c2cd8fab4743780db655f4a06d6845ad395affdb34, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:33:52', 'leads'),
(79, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f8460bfeb389f119b10079ca8eed4293d1b5f5ebdf59f1502770eada3514f1a23, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:35:03', 'leads'),
(80, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f35649b9ac8904f6cb8a6ba9d990007865f9e62976e7f90ca731dcff63b510f66, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:38:27', 'leads'),
(81, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f3fee82d7c8d7246962b02f3c8fcc10499301305f5344b223391c99df96ac8a17, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:38:58', 'leads'),
(82, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f8460bfeb389f119b10079ca8eed4293d17406bbbba0f4ffbd4554cb681dac46a, 0xb8fa1cc9ff866ebaac2f04b056bbf601, '2025-04-06 09:40:54', 'leads'),
(83, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012f35649b9ac8904f6cb8a6ba9d9900078606d6a1571440fd7b4c1b8013efbe5bae, 0x205c4d3725b089fe939481e7199b7817, '2025-04-06 09:42:56', 'leads'),
(84, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5405d4e0b85a043a4cde4c8fdba55ee0c1c5badcc35a68ff19b8fcdd3ccff55a380acc459ff6bc2872e69f0e2a269d84e, 0x205c4d3725b089fe939481e7199b7817, '2025-04-06 09:49:55', 'tools'),
(85, 0x7827b8483699d48dfbfc31e5a5e6be43, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a590f7724d6ae04c7d9fa3a51c9385e28ccb74e3ea0894f9694b3685ed419d7f9f335b0f9216ad9491a04edc6a416ff381, 0xf1865472b0874b0a7b985d1d93f37059, '2025-04-06 10:03:37', 'pages'),
(86, 0xe48a5ae9cbd142d51180016c6b3f4329, 0xb8fa1cc9ff866ebaac2f04b056bbf601, 0xe246f51538bf856a9cf40363eb261d2991a330d2fb7434f4650ab8e8edd3e9a5c228f1173111d093a49c237908f2012fd18bd9c8d9bb221ec9ef0fb893a89abcdbe54345ef84b699be0b746d6b77ac43, 0x9857b6f110919aae2d4dd7eb2e7b7763, '2025-04-06 10:05:04', 'leads');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabber` varchar(255) DEFAULT NULL,
  `telegram` varchar(255) DEFAULT NULL,
  `secret_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `seller` tinyint(1) NOT NULL DEFAULT 0,
  `seller_percentage` int(11) DEFAULT 0,
  `credit_cards_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credit_cards_total_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dumps_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dumps_total_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `active` int(11) NOT NULL DEFAULT 0,
  `total_earned` decimal(10,2) DEFAULT 0.00,
  `seller_actual_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `jabber`, `telegram`, `secret_code`, `created_at`, `balance`, `seller`, `seller_percentage`, `credit_cards_balance`, `credit_cards_total_earned`, `dumps_balance`, `dumps_total_earned`, `banned`, `role`, `status`, `active`, `total_earned`, `seller_actual_balance`) VALUES
(5, 'da', '$2y$10$qfm8/ykLZWPHIIsbhoRev.VEq9f9imBwdDgjc3CTi5GfGnnYeaZG2', 'caca1@jabber.ru', '', 'TVRFeE1URXg=', '2024-10-18 11:44:42', 9810.00, 1, 50, 217.00, 65.00, 7.50, 7.50, 0, 1, 'active', 1, 72.50, 72.50),
(13, 'xagua123', '$2a$12$Zx8/1cJyeEeqFfWvdWM5oOg0noBuwXUgIntHXsNoVbfyVkEdFkQZe', '', '', 'TVRFeE1URXg=', '2024-10-19 21:19:51', 11.50, 1, 50, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 1, 0.00, 0.00),
(33, 'dadada', '$2y$10$/Ga.U5H1LeYpRedJgJrbQO9KjuyxCeAepw.Wmzkocm91SUa/l17g6', 'dada@dada.com', NULL, 'TVRFeE1URXg=', '2025-01-15 19:44:52', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(34, 'jajaja', '$2y$10$WLJQgtgCyBhvRSS24oT3Re46/v.P892e7FEW98OfbneJx2HiCDDbK', NULL, '@jajaja', 'TVRFeE1URXg=', '2025-01-16 09:36:12', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(36, 'hackruffff', '$2y$10$/q4Y1CSagvlIiG91q79DtOGYwlISfdDt/ZVPlitfJR8xa/NzTtOCe', NULL, NULL, 'TVRFeE1URXg=', '2025-01-29 22:07:56', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(37, 'asdasdasdasd', '$2y$10$uZSkvw/SKgS32PF0531gvOOfcY9OupMRz6oAJmvSTrR7bazHdb0S6', 'asdasdasd@gmail.com', NULL, 'TVRFeE1URXg=', '2025-01-29 22:13:22', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(38, 'johnjohn', '$2y$10$m8giI.b0ilFnqa2MFobNQ.udmfLUaZ2Nz4CUocAlAK4rYKy0MA7eq', NULL, '@fewfwefew', 'TVRFeE1URXg=', '2025-02-07 22:28:24', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(39, 'yuyuyu', '$2y$10$JHGbrXX08Tv/YV1k8pJKhO3uiArLtr4dTWnOdCbXmoqybfrrxrUOa', NULL, '@yuyuy', 'TVRFeE1URXg=', '2025-02-12 21:45:45', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(41, 'jamie123', '$2y$10$6jvxo3s2eFflNp.qOsDp3OfV014YwN/ZasZaJsLORBRAglp7TxWf6', NULL, '@fewfwefew', 'TVRFeE1URXg=', '2025-02-14 09:59:20', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(43, 'LaraibR', '$2y$10$fO2/s5SQytM.XSqM6X4tLuveGU5puQA7dXdCd5k5r.zYblhEraChO', 'Laraib', 'Laraib', 'TmpZMk5qWTI=', '2025-02-14 11:18:48', 500.50, 1, 50, 4.00, 4.00, 2.50, 2.50, 0, 0, 'active', 1, 4.50, 6.50),
(44, 'jojojo', '$2y$10$tWeYZfPhzyqqvJ7UEL6/eeoS8FKGXWyeLIgpNBvDYAIGoQHrTiXOS', NULL, 'fwefwef', 'TVRFeE1URXg=', '2025-02-16 00:18:44', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(45, 'julien123', '$2y$10$BPqa4ulQpRo10Fzk2WdzXOGyN01V3NcuAz/lsUY8NuV1oiXRGX.5a', NULL, '@fewfew', 'TVRFeE1URXg=', '2025-02-17 08:27:00', 15.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(46, 'cracra', '$2y$10$V9.GtRajyuIlWz11d.m7bOe7lv7KcgfuT0q60wCWdaQacHP6lmFNi', NULL, '@fewfwef', 'TVRFeE1URXg=', '2025-02-17 19:48:33', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(48, 'nununu', '$2y$10$cKkxKl4GCagtYGNsi82zRuNpvORjfIB8P50QrGa8juVgXknA1tG5y', NULL, '@da', 'TVRFeE1URXg=', '2025-02-18 17:03:26', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(49, 'hahadadaaa', '$2y$10$yOC8GoZptAI1wP.0jSu3SuzqCvfhIA93LYy8hlWDMmTeQOypyruZG', '', '@hahadada', 'TVRFeU1qTXo=', '2025-02-18 17:11:30', 1.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'inactive', 0, 0.00, 0.00),
(50, 'childsplay', '$2y$10$fjhKDE3fk2dqpLUdFZCSJOGze0Gf4ZQmY6dFMtm6qd0doSBMVx7NG', 'chucky@jabber.ru', NULL, 'TmpZMk5qWTI=', '2025-02-20 00:27:33', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(51, 'jamesjames', '$2y$10$uQV.kVmj23HLIyoR0k4sFOT/7ov0sZ6vQp4mhz9XK/49kGpfcIPNi', 'fwejfiew@jabber.ru', '', 'TVRFeE1URXg=', '2025-03-13 14:23:14', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00),
(53, 'cacacaca', '$2y$10$Ht8Xuv8l9zle2c9ly9oAF.D/1nUD3ZZzrtFg0NRzJcRuF0fW1sj2e', 'caca@caca.com', '1', 'TVRFeE1URXg=', '2025-03-19 20:48:45', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 1.00, 0.00),
(57, 'dadadada', '$2y$10$fGJRLyjUPobddXrutHaOo.nj5KePGlXVaalvHjj06EWHzkvfkrPPG', 'fewfwefew', '', 'TkRFeU16RXk=', '2025-03-20 07:49:31', 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, 0, 'active', 0, 0.00, 0.00);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `update_seller_actual_balance` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
  SET NEW.seller_actual_balance = NEW.credit_cards_total_earned + NEW.dumps_total_earned;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_support_tickets_username_after_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    UPDATE support_tickets
    SET username = NEW.username
    WHERE user_id = NEW.id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `card_activity_log`
--
ALTER TABLE `card_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_cards`
--
ALTER TABLE `credit_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit_cards_ibfk_1` (`seller_id`);

--
-- Indexes for table `dumps`
--
ALTER TABLE `dumps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `dumps_activity_log`
--
ALTER TABLE `dumps_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tool_id` (`tool_id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Updates`
--
ALTER TABLE `Updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=836;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `card_activity_log`
--
ALTER TABLE `card_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `credit_cards`
--
ALTER TABLE `credit_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1367;

--
-- AUTO_INCREMENT for table `dumps`
--
ALTER TABLE `dumps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=894;

--
-- AUTO_INCREMENT for table `dumps_activity_log`
--
ALTER TABLE `dumps_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=596;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `Updates`
--
ALTER TABLE `Updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `credit_cards`
--
ALTER TABLE `credit_cards`
  ADD CONSTRAINT `credit_cards_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dumps`
--
ALTER TABLE `dumps`
  ADD CONSTRAINT `dumps_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD CONSTRAINT `support_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
