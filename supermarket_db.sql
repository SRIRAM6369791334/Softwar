-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2026 at 08:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `supermarket_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `id` int(11) NOT NULL,
  `original_table` varchar(50) NOT NULL,
  `original_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `total_hours` decimal(5,2) DEFAULT 0.00,
  `status` enum('present','absent','half_day','late') DEFAULT 'present',
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_overtime` tinyint(1) DEFAULT 0,
  `overtime_minutes` int(11) DEFAULT 0,
  `manager_approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `grace_minutes` int(11) DEFAULT 0,
  `overtime_approved` tinyint(1) DEFAULT 0,
  `overtime_rate` decimal(5,2) DEFAULT 1.50,
  `notes` text DEFAULT NULL,
  `clock_in_photo` varchar(255) DEFAULT NULL,
  `clock_out_photo` varchar(255) DEFAULT NULL,
  `clock_in_latitude` decimal(10,8) DEFAULT NULL,
  `clock_in_longitude` decimal(11,8) DEFAULT NULL,
  `clock_out_latitude` decimal(10,8) DEFAULT NULL,
  `clock_out_longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `user_id`, `clock_in`, `clock_out`, `total_hours`, `status`, `date`, `created_at`, `is_overtime`, `overtime_minutes`, `manager_approval_status`, `grace_minutes`, `overtime_approved`, `overtime_rate`, `notes`, `clock_in_photo`, `clock_out_photo`, `clock_in_latitude`, `clock_in_longitude`, `clock_out_latitude`, `clock_out_longitude`) VALUES
(1, 2, '2026-02-07 07:05:03', '2026-02-07 08:05:32', -3.49, 'present', '2026-02-07', '2026-02-07 07:05:03', 0, 0, 'pending', 0, 0, 1.50, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 42, '2026-02-10 02:49:24', '2026-02-10 02:49:24', 0.00, 'present', '2026-02-10', '2026-02-10 07:19:24', 0, 0, 'pending', 0, 0, 1.50, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'BACKTEST', 'Maintenance verification run', NULL, '2026-02-07 10:22:21'),
(2, 1, 'BACKTEST', 'Maintenance verification run', NULL, '2026-02-10 06:00:51'),
(3, 1, 'BACKTEST', 'Maintenance verification run', NULL, '2026-02-10 06:01:30'),
(4, 1, 'BACKTEST', 'Maintenance verification run', NULL, '2026-02-10 06:02:45'),
(5, 1, 'BACKTEST', 'Maintenance verification run', NULL, '2026-02-10 06:29:07'),
(6, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:37:51'),
(7, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:38:21'),
(8, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:42:27'),
(9, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:45:25'),
(10, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:45:42'),
(11, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:50:11'),
(12, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:51:42'),
(13, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 06:53:49'),
(14, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:08:47'),
(15, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:09:54'),
(16, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:14:05'),
(17, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:15:19'),
(18, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:15:24'),
(19, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:15:29'),
(20, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:15:59'),
(21, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:16:20'),
(22, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:17:24'),
(23, 1, 'BACKTEST', 'Function-level backtest run', NULL, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `automation_logs`
--

CREATE TABLE `automation_logs` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `trigger_context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trigger_context`)),
  `status` enum('success','failed','pending') DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `background_url` varchar(255) DEFAULT NULL,
  `region` varchar(50) DEFAULT 'General',
  `manager_id` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `geofence_radius` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `location`, `background_url`, `region`, `manager_id`, `phone`, `is_active`, `created_at`, `updated_at`, `latitude`, `longitude`, `geofence_radius`) VALUES
(1, 'Main Branch', 'Headquarters', NULL, 'North', NULL, NULL, 1, '2026-02-06 10:16:29', '2026-02-06 13:12:50', NULL, NULL, 100),
(2, 'North Branch', 'Mall Road', NULL, 'General', 0, '', 1, '2026-02-06 10:29:36', '2026-02-06 10:29:36', NULL, NULL, 100),
(3, 'Main Branch (HQ)', 'Mumbai Central', NULL, 'North', NULL, NULL, 1, '2026-02-07 05:14:06', '2026-02-07 05:14:06', NULL, NULL, 100),
(4, 'North Side Store', 'Andheri West', NULL, 'North', NULL, NULL, 1, '2026-02-07 05:14:06', '2026-02-07 05:14:06', NULL, NULL, 100),
(5, 'South Side Mall', 'Colaba Causeway', NULL, 'South', NULL, NULL, 1, '2026-02-07 05:14:06', '2026-02-07 05:14:06', NULL, NULL, 100),
(6, 'Main Branch (HQ)', 'Mumbai Central', NULL, 'North', NULL, NULL, 1, '2026-02-07 09:52:49', '2026-02-07 09:52:49', NULL, NULL, 100),
(7, 'North Side Store', 'Andheri West', NULL, 'North', NULL, NULL, 1, '2026-02-07 09:52:49', '2026-02-07 09:52:49', NULL, NULL, 100),
(8, 'South Side Mall', 'Colaba Causeway', NULL, 'South', NULL, NULL, 1, '2026-02-07 09:52:49', '2026-02-07 09:52:49', NULL, NULL, 100),
(9, 'Main Branch (HQ)', 'Mumbai Central', NULL, 'North', NULL, NULL, 1, '2026-02-07 09:54:56', '2026-02-07 09:54:56', NULL, NULL, 100),
(10, 'North Side Store', 'Andheri West', NULL, 'North', NULL, NULL, 1, '2026-02-07 09:54:56', '2026-02-07 09:54:56', NULL, NULL, 100),
(11, 'South Side Mall', 'Colaba Causeway', NULL, 'South', NULL, NULL, 1, '2026-02-07 09:54:56', '2026-02-07 09:54:56', NULL, NULL, 100),
(12, 'Test Branch 679', 'Test Location', NULL, 'Central', NULL, NULL, 1, '2026-02-07 10:22:20', '2026-02-07 10:22:20', NULL, NULL, 100),
(13, 'Test Branch 800', 'Test Location', NULL, 'Central', NULL, NULL, 1, '2026-02-10 06:00:50', '2026-02-10 06:00:50', NULL, NULL, 100),
(14, 'Test Branch 202', 'Test Location', NULL, 'Central', NULL, NULL, 1, '2026-02-10 06:01:28', '2026-02-10 06:01:28', NULL, NULL, 100),
(15, 'Main Branch (HQ)', 'Mumbai Central', NULL, 'North', NULL, NULL, 1, '2026-02-10 06:02:11', '2026-02-10 06:02:11', NULL, NULL, 100),
(16, 'North Side Store', 'Andheri West', NULL, 'North', NULL, NULL, 1, '2026-02-10 06:02:12', '2026-02-10 06:02:12', NULL, NULL, 100),
(17, 'South Side Mall', 'Colaba Causeway', NULL, 'South', NULL, NULL, 1, '2026-02-10 06:02:12', '2026-02-10 06:02:12', NULL, NULL, 100),
(18, 'Test Branch 734', 'Test Location', NULL, 'Central', NULL, NULL, 1, '2026-02-10 06:02:43', '2026-02-10 06:02:43', NULL, NULL, 100),
(19, 'Test Branch 310', 'Test Location', NULL, 'Central', NULL, NULL, 1, '2026-02-10 06:29:06', '2026-02-10 06:29:06', NULL, NULL, 100);

-- --------------------------------------------------------

--
-- Table structure for table `branch_product_settings`
--

CREATE TABLE `branch_product_settings` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `min_stock_alert` int(11) DEFAULT 10,
  `reorder_level` int(11) DEFAULT 20,
  `default_sale_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_product_settings`
--

INSERT INTO `branch_product_settings` (`id`, `branch_id`, `product_id`, `min_stock_alert`, `reorder_level`, `default_sale_price`, `created_at`, `updated_at`) VALUES
(17, 1, 33, 10, 20, NULL, '2026-02-10 07:19:24', '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clock_in` datetime NOT NULL,
  `clock_out` datetime DEFAULT NULL,
  `selfie_path` varchar(255) DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_attendance`
--

INSERT INTO `employee_attendance` (`id`, `user_id`, `clock_in`, `clock_out`, `selfie_path`, `location_lat`, `location_lng`, `branch_id`, `notes`, `created_at`) VALUES
(1, 1, '2026-02-10 11:54:23', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:24:23'),
(2, 1, '2026-02-10 11:57:01', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:27:01'),
(3, 1, '2026-02-10 11:59:06', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:29:06'),
(4, 1, '2026-02-10 12:07:51', '2026-02-10 12:07:51', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:37:51'),
(5, 1, '2026-02-10 12:08:21', '2026-02-10 12:08:21', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:38:21'),
(6, 1, '2026-02-10 12:12:27', '2026-02-10 12:12:27', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:42:27'),
(7, 1, '2026-02-10 12:15:25', '2026-02-10 12:15:25', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:45:25'),
(8, 1, '2026-02-10 12:15:42', '2026-02-10 12:15:42', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:45:42'),
(9, 1, '2026-02-10 12:20:11', '2026-02-10 12:20:11', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:50:11'),
(10, 1, '2026-02-10 12:21:42', '2026-02-10 12:21:42', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:51:42'),
(11, 1, '2026-02-10 12:23:49', '2026-02-10 12:23:49', NULL, NULL, NULL, NULL, NULL, '2026-02-10 06:53:49'),
(12, 1, '2026-02-10 12:38:47', '2026-02-10 12:38:47', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:08:47'),
(13, 1, '2026-02-10 12:39:55', '2026-02-10 12:39:55', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:09:55'),
(14, 1, '2026-02-10 12:44:05', '2026-02-10 12:44:05', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:14:05'),
(15, 1, '2026-02-10 12:45:19', '2026-02-10 12:45:19', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:15:19'),
(16, 1, '2026-02-10 12:45:24', '2026-02-10 12:45:24', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:15:24'),
(17, 1, '2026-02-10 12:45:29', '2026-02-10 12:45:29', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:15:29'),
(18, 1, '2026-02-10 12:45:59', '2026-02-10 12:45:59', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:15:59'),
(19, 1, '2026-02-10 12:46:20', '2026-02-10 12:46:20', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:16:20'),
(20, 1, '2026-02-10 12:47:24', '2026-02-10 12:47:24', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:17:24'),
(21, 1, '2026-02-10 12:49:24', '2026-02-10 12:49:24', NULL, NULL, NULL, NULL, NULL, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('sick','casual','earned','unpaid') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days` decimal(5,1) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_leaves`
--

INSERT INTO `employee_leaves` (`id`, `user_id`, `type`, `start_date`, `end_date`, `days`, `reason`, `status`, `approved_by`, `created_at`) VALUES
(1, 2, 'sick', '2026-02-07', '2026-02-08', 2.0, 'hello', 'approved', 1, '2026-02-07 08:05:03'),
(2, 1, 'sick', '2026-02-15', '2026-02-16', 0.0, 'Family Event', 'pending', NULL, '2026-02-10 06:24:23'),
(3, 1, 'sick', '2026-02-15', '2026-02-16', 0.0, 'Test', 'pending', NULL, '2026-02-10 06:25:48'),
(4, 1, 'casual', '2026-02-15', '2026-02-16', 1.0, 'Family Event', 'approved', 1, '2026-02-10 06:27:01'),
(5, 1, 'casual', '2026-02-15', '2026-02-16', 1.0, 'Family Event', 'approved', 1, '2026-02-10 06:29:06'),
(6, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:37:51'),
(8, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:38:21'),
(10, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:42:27'),
(12, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:45:25'),
(14, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:45:42'),
(16, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:50:11'),
(18, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:51:42'),
(20, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 06:53:49'),
(22, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:08:47'),
(24, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:09:54'),
(26, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:14:05'),
(28, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:15:19'),
(30, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:15:24'),
(32, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:15:29'),
(34, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:15:59'),
(36, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:16:20'),
(38, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:17:24'),
(40, 1, 'casual', '2026-03-12', '2026-03-12', 1.0, 'Backtest leave', 'rejected', 1, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `employee_messages`
--

CREATE TABLE `employee_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `target_role_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_urgent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_messages`
--

INSERT INTO `employee_messages` (`id`, `sender_id`, `target_role_id`, `title`, `message`, `is_urgent`, `created_at`) VALUES
(1, 1, NULL, 'hello', 'how are you ravanan', 1, '2026-02-07 08:03:18'),
(2, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:37:51'),
(3, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:37:51'),
(4, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:38:21'),
(5, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:38:21'),
(6, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:42:27'),
(7, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:42:27'),
(8, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:45:25'),
(9, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:45:25'),
(10, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:45:42'),
(11, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:45:42'),
(12, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:50:11'),
(13, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:50:11'),
(14, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:51:42'),
(15, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:51:42'),
(16, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 06:53:49'),
(17, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 06:53:49'),
(18, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:08:47'),
(19, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:08:47'),
(20, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:09:54'),
(21, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:09:54'),
(22, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:14:05'),
(23, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:14:05'),
(24, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:15:19'),
(25, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:15:19'),
(26, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:15:24'),
(27, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:15:24'),
(28, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:15:29'),
(29, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:15:29'),
(30, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:15:59'),
(31, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:15:59'),
(32, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:16:20'),
(33, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:16:20'),
(34, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:17:24'),
(35, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:17:24'),
(36, 1, NULL, 'Test Alert', 'Backtest message', 0, '2026-02-10 07:19:24'),
(37, 1, NULL, 'Urgent Test', 'Backtest urgent', 1, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `employee_roster`
--

CREATE TABLE `employee_roster` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('scheduled','completed','absent') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_roster`
--

INSERT INTO `employee_roster` (`id`, `user_id`, `branch_id`, `shift_date`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-02-11', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:24:23'),
(2, 1, 1, '2026-02-11', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:27:01'),
(3, 1, 1, '2026-02-11', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:29:06'),
(4, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:37:51'),
(5, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:38:21'),
(6, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:42:27'),
(7, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:45:25'),
(8, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:45:42'),
(9, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:50:11'),
(10, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:51:42'),
(11, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 06:53:49'),
(12, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:08:47'),
(13, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:09:55'),
(14, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:14:05'),
(15, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:15:19'),
(16, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:15:24'),
(17, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:15:29'),
(18, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:15:59'),
(19, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:16:20'),
(20, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:17:24'),
(21, 1, 1, '2026-02-10', '09:00:00', '17:00:00', 'scheduled', '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `employee_shifts`
--

CREATE TABLE `employee_shifts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `type` enum('morning','afternoon','night','general') DEFAULT 'general',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_open` tinyint(1) DEFAULT 0,
  `max_claimants` int(11) DEFAULT 1,
  `claimed_by` int(11) DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `grace_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grace_period_logs`
--

CREATE TABLE `grace_period_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `scheduled_time` time NOT NULL,
  `actual_time` time NOT NULL,
  `grace_minutes` int(11) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_mode` enum('cash','card','upi','split') DEFAULT 'cash',
  `status` enum('paid','cancelled','refunded') DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `branch_id`, `user_id`, `customer_name`, `customer_phone`, `invoice_no`, `sub_total`, `tax_total`, `discount_total`, `grand_total`, `payment_mode`, `status`, `created_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, NULL, 'INV-1770441247-399', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 00:44:07', NULL),
(2, 1, 1, NULL, NULL, 'INV-1770441247-126', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 00:44:07', NULL),
(3, 1, 1, NULL, NULL, 'INV-1770441247-713', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 00:44:07', NULL),
(4, 1, 1, NULL, NULL, 'INV-1770441247-316', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-01-31 00:44:07', NULL),
(5, 1, 1, NULL, NULL, 'INV-1770441247-598', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 00:44:07', NULL),
(6, 1, 1, NULL, NULL, 'INV-1770441247-302', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-01-31 00:44:07', NULL),
(7, 1, 1, NULL, NULL, 'INV-1770441247-910', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 00:44:07', NULL),
(8, 1, 1, NULL, NULL, 'INV-1770441247-858', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 00:44:07', NULL),
(9, 1, 1, NULL, NULL, 'INV-1770441247-401', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 00:44:07', NULL),
(10, 1, 1, NULL, NULL, 'INV-1770441247-559', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 00:44:07', NULL),
(11, 1, 1, NULL, NULL, 'INV-1770441247-959', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 00:44:07', NULL),
(12, 1, 1, NULL, NULL, 'INV-1770441247-165', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 00:44:07', NULL),
(13, 1, 1, NULL, NULL, 'INV-1770441247-466', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 00:44:07', NULL),
(14, 1, 1, NULL, NULL, 'INV-1770441247-163', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 00:44:07', NULL),
(15, 1, 1, NULL, NULL, 'INV-1770441247-761', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 00:44:07', NULL),
(16, 1, 1, NULL, NULL, 'INV-1770441247-414', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 00:44:07', NULL),
(17, 1, 1, NULL, NULL, 'INV-1770441247-221', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 00:44:07', NULL),
(18, 1, 1, NULL, NULL, 'INV-1770441247-970', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 00:44:07', NULL),
(19, 1, 1, NULL, NULL, 'INV-1770441247-599', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-01-31 00:44:07', NULL),
(20, 1, 1, NULL, NULL, 'INV-1770441247-871', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 00:44:07', NULL),
(21, 1, 1, NULL, NULL, 'INV-1770457969-929', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 05:22:49', NULL),
(22, 1, 1, NULL, NULL, 'INV-1770457969-303', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:22:49', NULL),
(23, 1, 1, NULL, NULL, 'INV-1770457969-862', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:22:49', NULL),
(24, 1, 1, NULL, NULL, 'INV-1770457969-680', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:22:49', NULL),
(25, 1, 1, NULL, NULL, 'INV-1770457969-230', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:22:49', NULL),
(26, 1, 1, NULL, NULL, 'INV-1770457969-897', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-01 05:22:49', NULL),
(27, 1, 1, NULL, NULL, 'INV-1770457969-518', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:22:49', NULL),
(28, 1, 1, NULL, NULL, 'INV-1770457969-241', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:22:49', NULL),
(29, 1, 1, NULL, NULL, 'INV-1770457969-983', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 05:22:49', NULL),
(30, 1, 1, NULL, NULL, 'INV-1770457969-200', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:22:49', NULL),
(31, 1, 1, NULL, NULL, 'INV-1770457969-261', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:22:49', NULL),
(32, 1, 1, NULL, NULL, 'INV-1770457969-316', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-01 05:22:49', NULL),
(33, 1, 1, NULL, NULL, 'INV-1770457969-606', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:22:49', NULL),
(34, 1, 1, NULL, NULL, 'INV-1770457969-221', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:22:49', NULL),
(35, 1, 1, NULL, NULL, 'INV-1770457969-976', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:22:49', NULL),
(36, 1, 1, NULL, NULL, 'INV-1770457969-688', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-01-31 05:22:49', NULL),
(37, 1, 1, NULL, NULL, 'INV-1770457969-369', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:22:49', NULL),
(38, 1, 1, NULL, NULL, 'INV-1770457969-571', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:22:49', NULL),
(39, 1, 1, NULL, NULL, 'INV-1770457969-576', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:22:49', NULL),
(40, 1, 1, NULL, NULL, 'INV-1770457969-760', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:22:49', NULL),
(41, 1, 1, NULL, NULL, 'TEST-1770458008', 190.00, 9.50, 0.00, 199.50, 'cash', 'paid', '2026-02-07 09:53:28', NULL),
(42, 1, 1, NULL, NULL, 'INV-1770458097-136', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-01-31 05:24:57', NULL),
(43, 1, 1, NULL, NULL, 'INV-1770458097-554', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 05:24:57', NULL),
(44, 1, 1, NULL, NULL, 'INV-1770458097-471', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:24:57', NULL),
(45, 1, 1, NULL, NULL, 'INV-1770458097-512', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 05:24:57', NULL),
(46, 1, 1, NULL, NULL, 'INV-1770458097-232', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 05:24:57', NULL),
(47, 1, 1, NULL, NULL, 'INV-1770458097-368', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 05:24:57', NULL),
(48, 1, 1, NULL, NULL, 'INV-1770458097-393', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:24:57', NULL),
(49, 1, 1, NULL, NULL, 'INV-1770458097-330', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:24:57', NULL),
(50, 1, 1, NULL, NULL, 'INV-1770458097-470', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:24:57', NULL),
(51, 1, 1, NULL, NULL, 'INV-1770458097-464', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 05:24:57', NULL),
(52, 1, 1, NULL, NULL, 'INV-1770458097-568', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 05:24:57', NULL),
(53, 1, 1, NULL, NULL, 'INV-1770458097-170', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:24:57', NULL),
(54, 1, 1, NULL, NULL, 'INV-1770458097-805', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 05:24:57', NULL),
(55, 1, 1, NULL, NULL, 'INV-1770458097-797', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:24:57', NULL),
(56, 1, 1, NULL, NULL, 'INV-1770458097-431', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-01 05:24:57', NULL),
(57, 1, 1, NULL, NULL, 'INV-1770458097-481', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-02 05:24:57', NULL),
(58, 1, 1, NULL, NULL, 'INV-1770458097-559', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:24:57', NULL),
(59, 1, 1, NULL, NULL, 'INV-1770458097-305', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 05:24:57', NULL),
(60, 1, 1, NULL, NULL, 'INV-1770458097-699', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-05 05:24:57', NULL),
(61, 1, 1, NULL, NULL, 'INV-1770458097-752', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 05:24:57', NULL),
(62, 1, 1, NULL, NULL, 'TEST-1770458098', 190.00, 9.50, 0.00, 199.50, 'cash', 'paid', '2026-02-07 09:54:58', NULL),
(63, 1, 1, NULL, NULL, 'TEST-1770459740', 190.00, 9.50, 0.00, 199.50, 'cash', 'paid', '2026-02-07 10:22:20', NULL),
(64, 1, 1, NULL, NULL, 'INV-1770703333-630', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 01:32:13', NULL),
(65, 1, 1, NULL, NULL, 'INV-1770703333-696', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-09 01:32:13', NULL),
(66, 1, 1, NULL, NULL, 'INV-1770703333-599', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-09 01:32:13', NULL),
(67, 1, 1, NULL, NULL, 'INV-1770703333-834', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 01:32:13', NULL),
(68, 1, 1, NULL, NULL, 'INV-1770703333-945', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 01:32:13', NULL),
(69, 1, 1, NULL, NULL, 'INV-1770703333-105', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-08 01:32:13', NULL),
(70, 1, 1, NULL, NULL, 'INV-1770703333-413', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 01:32:13', NULL),
(71, 1, 1, NULL, NULL, 'INV-1770703333-220', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-10 01:32:13', NULL),
(72, 1, 1, NULL, NULL, 'INV-1770703333-866', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 01:32:13', NULL),
(73, 1, 1, NULL, NULL, 'INV-1770703333-688', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-08 01:32:13', NULL),
(74, 1, 1, NULL, NULL, 'INV-1770703333-291', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 01:32:13', NULL),
(75, 1, 1, NULL, NULL, 'INV-1770703333-785', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 01:32:13', NULL),
(76, 1, 1, NULL, NULL, 'INV-1770703333-749', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 01:32:13', NULL),
(77, 1, 1, NULL, NULL, 'INV-1770703333-614', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-08 01:32:13', NULL),
(78, 1, 1, NULL, NULL, 'INV-1770703333-854', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-08 01:32:13', NULL),
(79, 1, 1, NULL, NULL, 'INV-1770703333-147', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-04 01:32:13', NULL),
(80, 1, 1, NULL, NULL, 'INV-1770703333-387', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 01:32:13', NULL),
(81, 1, 1, NULL, NULL, 'INV-1770703333-710', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-07 01:32:13', NULL),
(82, 1, 1, NULL, NULL, 'INV-1770703333-743', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-03 01:32:13', NULL),
(83, 1, 1, NULL, NULL, 'INV-1770703333-484', 400.00, 72.00, 0.00, 472.00, 'cash', 'paid', '2026-02-06 01:32:13', NULL),
(84, 1, 1, NULL, NULL, 'TEST-1770703358', 190.00, 9.50, 0.00, 199.50, 'cash', 'paid', '2026-02-10 06:02:38', NULL),
(85, 1, 1, NULL, NULL, 'TEST-1770704946', 190.00, 9.50, 0.00, 199.50, 'cash', 'paid', '2026-02-10 06:29:06', NULL),
(86, 1, 1, NULL, NULL, 'BT-INV-1770705471', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:37:51', NULL),
(88, 1, 1, NULL, NULL, 'BT-INV-1770705501', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:38:21', NULL),
(90, 1, 1, NULL, NULL, 'BT-INV-1770705747', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:42:27', NULL),
(92, 1, 1, NULL, NULL, 'BT-INV-1770705925', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:45:25', NULL),
(94, 1, 1, NULL, NULL, 'BT-INV-1770705942', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:45:42', NULL),
(96, 1, 1, NULL, NULL, 'BT-INV-1770706210', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:50:10', NULL),
(98, 1, 1, NULL, NULL, 'BT-INV-1770706302', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:51:42', NULL),
(100, 1, 1, NULL, NULL, 'BT-INV-1770706429', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 06:53:49', NULL),
(102, 1, 1, NULL, NULL, 'BT-INV-1770707327', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:08:47', NULL),
(104, 1, 1, NULL, NULL, 'BT-INV-1770707394', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:09:54', NULL),
(106, 1, 1, NULL, NULL, 'BT-INV-1770707645', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:14:05', NULL),
(107, 1, 1, NULL, NULL, 'BT-INV-1770707719', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:15:19', NULL),
(108, 1, 1, NULL, NULL, 'BT-INV-1770707724', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:15:24', NULL),
(109, 1, 1, NULL, NULL, 'BT-INV-1770707728', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:15:28', NULL),
(110, 1, 1, NULL, NULL, 'BT-INV-1770707759', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:15:59', NULL),
(111, 1, 1, NULL, NULL, 'BT-INV-1770707780', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:16:20', NULL),
(112, 1, 1, NULL, NULL, 'BT-INV-1770707844', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:17:24', NULL),
(113, 1, 1, NULL, NULL, 'INV-20260210-1770707854', 298.17, 0.00, 0.00, 298.17, 'cash', 'paid', '2026-02-10 07:17:34', NULL),
(114, 1, 1, NULL, NULL, 'INV-20260210-1770707865', 298.17, 0.00, 0.00, 298.17, 'cash', 'paid', '2026-02-10 07:17:45', NULL),
(115, 1, 1, NULL, NULL, 'INV-20260210-1770707908', 298.17, 0.00, 0.00, 298.17, 'cash', 'paid', '2026-02-10 07:18:28', NULL),
(116, 1, 1, NULL, NULL, 'INV-20260210-1770707912', 298.17, 0.00, 0.00, 298.17, 'cash', 'paid', '2026-02-10 07:18:32', NULL),
(117, 1, 1, NULL, NULL, 'INV-20260210-1770707952', 298.17, 0.00, 0.00, 298.17, 'cash', 'paid', '2026-02-10 07:19:12', NULL),
(118, 1, 1, NULL, NULL, 'BT-INV-1770707964', 100.00, 18.00, 0.00, 118.00, 'cash', 'cancelled', '2026-02-10 07:19:24', NULL),
(119, 1, 1, NULL, NULL, 'INV-20260210-1770708006', 327.99, 0.00, 0.00, 327.99, 'cash', 'paid', '2026-02-10 07:20:06', NULL),
(120, 1, 1, NULL, NULL, 'INV-20260210-1770708132', 327.99, 0.00, 0.00, 327.99, 'cash', 'paid', '2026-02-10 07:22:12', NULL),
(121, 1, 1, NULL, NULL, 'INV-20260210-1770708186', 327.99, 0.00, 0.00, 327.99, 'cash', 'paid', '2026-02-10 07:23:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `qty` decimal(10,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_percent` decimal(5,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_id`, `batch_id`, `qty`, `unit_price`, `tax_percent`, `tax_amount`, `total`) VALUES
(1, 1, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(2, 2, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(3, 3, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(4, 4, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(5, 5, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(6, 6, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(7, 7, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(8, 8, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(9, 9, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(10, 10, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(11, 11, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(12, 12, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(13, 13, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(14, 14, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(15, 15, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(16, 16, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(17, 17, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(18, 18, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(19, 19, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(20, 20, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(21, 21, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(22, 22, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(23, 23, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(24, 24, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(25, 25, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(26, 26, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(27, 27, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(28, 28, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(29, 29, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(30, 30, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(31, 31, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(32, 32, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(33, 33, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(34, 34, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(35, 35, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(36, 36, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(37, 37, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(38, 38, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(39, 39, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(40, 40, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(41, 41, 1, 10, 2.000, 95.00, 5.00, 9.50, 199.50),
(42, 42, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(43, 43, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(44, 44, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(45, 45, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(46, 46, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(47, 47, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(48, 48, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(49, 49, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(50, 50, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(51, 51, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(52, 52, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(53, 53, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(54, 54, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(55, 55, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(56, 56, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(57, 57, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(58, 58, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(59, 59, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(60, 60, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(61, 61, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(62, 62, 1, 10, 2.000, 95.00, 5.00, 9.50, 199.50),
(63, 63, 1, 10, 2.000, 95.00, 5.00, 9.50, 199.50),
(64, 64, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(65, 65, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(66, 66, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(67, 67, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(68, 68, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(69, 69, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(70, 70, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(71, 71, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(72, 72, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(73, 73, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(74, 74, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(75, 75, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(76, 76, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(77, 77, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(78, 78, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(79, 79, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(80, 80, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(81, 81, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(82, 82, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(83, 83, 1, 1, 4.000, 100.00, 18.00, 72.00, 472.00),
(84, 84, 1, 10, 2.000, 95.00, 5.00, 9.50, 199.50),
(85, 85, 1, 10, 2.000, 95.00, 5.00, 9.50, 199.50),
(86, 86, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(88, 88, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(90, 90, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(92, 92, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(94, 94, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(96, 96, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(98, 98, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(100, 100, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(102, 102, 2, 10, 2.000, 50.00, 18.00, 18.00, 118.00),
(104, 104, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(106, 106, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(107, 107, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(108, 108, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(109, 109, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(110, 110, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(111, 111, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(112, 112, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(113, 113, 4, 79, 1.000, 298.17, 0.00, 0.00, 298.17),
(114, 114, 4, 79, 1.000, 298.17, 0.00, 0.00, 298.17),
(115, 115, 4, 79, 1.000, 298.17, 0.00, 0.00, 298.17),
(116, 116, 4, 79, 1.000, 298.17, 0.00, 0.00, 298.17),
(117, 117, 4, 79, 1.000, 298.17, 0.00, 0.00, 298.17),
(118, 118, 1, 748, 2.000, 50.00, 18.00, 18.00, 118.00),
(119, 119, 4, 79, 1.000, 327.99, 0.00, 0.00, 327.99),
(120, 120, 4, 79, 1.000, 327.99, 0.00, 0.00, 327.99),
(121, 121, 4, 79, 1.000, 327.99, 0.00, 0.00, 327.99);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(11) NOT NULL,
  `equipment_name` varchar(150) NOT NULL,
  `issue` text NOT NULL,
  `status` enum('reported','in_progress','resolved') DEFAULT 'reported',
  `reported_by` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `map_sections`
--

CREATE TABLE `map_sections` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grid_width` int(11) DEFAULT 10,
  `grid_height` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map_sections`
--

INSERT INTO `map_sections` (`id`, `branch_id`, `name`, `grid_width`, `grid_height`, `created_at`) VALUES
(1, 1, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(2, 2, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(3, 3, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(4, 4, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(5, 5, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(6, 6, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(7, 7, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(8, 8, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(9, 9, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(10, 10, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(11, 11, 'Main Floor', 10, 10, '2026-02-07 09:54:57'),
(12, 1, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(13, 2, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(14, 3, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(15, 4, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(16, 5, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(17, 6, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(18, 7, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(19, 8, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(20, 9, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(21, 10, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(22, 11, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(23, 12, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(24, 13, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(25, 14, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(26, 15, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(27, 16, 'Main Floor', 10, 10, '2026-02-10 06:02:13'),
(28, 17, 'Main Floor', 10, 10, '2026-02-10 06:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `type` enum('stock','expiry','system','update') DEFAULT 'system',
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `branch_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, NULL, 1, 'expiry', 'Expiring Soon: Organic Whole Milk 1L', 'Batch EXP-270 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(2, NULL, 1, 'expiry', 'Expiring Soon: Premium Sliced Bread', 'Batch EXP-158 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(3, NULL, 1, 'expiry', 'Expiring Soon: Farm Fresh Eggs (Doz)', 'Batch EXP-175 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(4, NULL, 1, 'expiry', 'Expiring Soon: Red Delicious Apples 1kg', 'Batch EXP-936 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(5, NULL, 1, 'expiry', 'Expiring Soon: Basmati Extra Long Rice 5kg', 'Batch EXP-850 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(6, NULL, 1, 'expiry', 'Expiring Soon: Energy Drink 250ml', 'Batch EXP-934 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:14:23'),
(7, NULL, 1, 'expiry', 'Expiring Soon: Organic Whole Milk 1L', 'Batch EXP-270 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(8, NULL, 1, 'expiry', 'Expiring Soon: Premium Sliced Bread', 'Batch EXP-158 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(9, NULL, 1, 'expiry', 'Expiring Soon: Farm Fresh Eggs (Doz)', 'Batch EXP-175 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(10, NULL, 1, 'expiry', 'Expiring Soon: Red Delicious Apples 1kg', 'Batch EXP-936 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(11, NULL, 1, 'expiry', 'Expiring Soon: Basmati Extra Long Rice 5kg', 'Batch EXP-850 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(12, NULL, 1, 'expiry', 'Expiring Soon: Energy Drink 250ml', 'Batch EXP-934 expires on 2026-02-14', '/inventory', 1, '2026-02-07 05:23:11'),
(13, 1, NULL, 'system', 'System Alert', 'Backtest in progress', NULL, 1, '2026-02-07 10:22:21'),
(14, NULL, 1, 'expiry', 'Expiring Soon: Organic Whole Milk 1L', 'Batch EXP-270 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(15, NULL, 1, 'expiry', 'Expiring Soon: Premium Sliced Bread', 'Batch EXP-158 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(16, NULL, 1, 'expiry', 'Expiring Soon: Farm Fresh Eggs (Doz)', 'Batch EXP-175 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(17, NULL, 1, 'expiry', 'Expiring Soon: Red Delicious Apples 1kg', 'Batch EXP-936 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(18, NULL, 1, 'expiry', 'Expiring Soon: Basmati Extra Long Rice 5kg', 'Batch EXP-850 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(19, NULL, 1, 'expiry', 'Expiring Soon: Energy Drink 250ml', 'Batch EXP-934 expires on 2026-02-14', '/inventory', 1, '2026-02-10 05:38:02'),
(20, 1, NULL, 'system', 'System Alert', 'Backtest in progress', NULL, 1, '2026-02-10 06:00:51'),
(21, 1, NULL, 'system', 'System Alert', 'Backtest in progress', NULL, 1, '2026-02-10 06:01:30'),
(22, 1, NULL, 'system', 'System Alert', 'Backtest in progress', NULL, 1, '2026-02-10 06:02:45'),
(23, 1, NULL, 'system', 'System Alert', 'Backtest in progress', NULL, 1, '2026-02-10 06:29:07'),
(24, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:37:51'),
(25, NULL, 1, 'system', 'BT Push Test 1770705471', 'Static push test', NULL, 1, '2026-02-10 06:37:51'),
(26, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:38:21'),
(27, NULL, 1, 'system', 'BT Push Test 1770705501', 'Static push test', NULL, 1, '2026-02-10 06:38:21'),
(28, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:42:27'),
(29, NULL, 1, 'system', 'BT Push Test 1770705747', 'Static push test', NULL, 1, '2026-02-10 06:42:27'),
(30, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:45:25'),
(31, NULL, 1, 'system', 'BT Push Test 1770705925', 'Static push test', NULL, 1, '2026-02-10 06:45:25'),
(32, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:45:42'),
(33, NULL, 1, 'system', 'BT Push Test 1770705942', 'Static push test', NULL, 1, '2026-02-10 06:45:42'),
(34, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:50:11'),
(35, NULL, 1, 'system', 'BT Push Test 1770706211', 'Static push test', NULL, 1, '2026-02-10 06:50:11'),
(36, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:51:42'),
(37, NULL, 1, 'system', 'BT Push Test 1770706302', 'Static push test', NULL, 1, '2026-02-10 06:51:42'),
(38, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 06:53:49'),
(39, NULL, 1, 'system', 'BT Push Test 1770706429', 'Static push test', NULL, 1, '2026-02-10 06:53:49'),
(40, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:08:47'),
(41, NULL, 1, 'system', 'BT Push Test 1770707327', 'Static push test', NULL, 1, '2026-02-10 07:08:47'),
(42, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:09:54'),
(43, NULL, 1, 'system', 'BT Push Test 1770707394', 'Static push test', NULL, 1, '2026-02-10 07:09:54'),
(44, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:14:05'),
(45, NULL, 1, 'system', 'BT Push Test 1770707645', 'Static push test', NULL, 1, '2026-02-10 07:14:05'),
(46, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:15:19'),
(47, NULL, 1, 'system', 'BT Push Test 1770707719', 'Static push test', NULL, 1, '2026-02-10 07:15:19'),
(48, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:15:24'),
(49, NULL, 1, 'system', 'BT Push Test 1770707724', 'Static push test', NULL, 1, '2026-02-10 07:15:24'),
(50, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:15:29'),
(51, NULL, 1, 'system', 'BT Push Test 1770707729', 'Static push test', NULL, 1, '2026-02-10 07:15:29'),
(52, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:15:59'),
(53, NULL, 1, 'system', 'BT Push Test 1770707759', 'Static push test', NULL, 1, '2026-02-10 07:15:59'),
(54, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:16:20'),
(55, NULL, 1, 'system', 'BT Push Test 1770707780', 'Static push test', NULL, 1, '2026-02-10 07:16:20'),
(56, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:17:24'),
(57, NULL, 1, 'system', 'BT Push Test 1770707844', 'Static push test', NULL, 1, '2026-02-10 07:17:24'),
(58, NULL, 1, 'system', 'BT Alert', 'Backtest notification', NULL, 1, '2026-02-10 07:19:24'),
(59, NULL, 1, 'system', 'BT Push Test 1770707964', 'Static push test', NULL, 0, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `overtime_records`
--

CREATE TABLE `overtime_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attendance_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `overtime_hours` decimal(5,2) NOT NULL,
  `overtime_rate` decimal(5,2) DEFAULT 1.50,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `overtime_records`
--

INSERT INTO `overtime_records` (`id`, `user_id`, `attendance_id`, `date`, `overtime_hours`, `overtime_rate`, `status`, `reason`, `approved_by`, `approved_at`, `created_at`) VALUES
(1, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:37:51'),
(2, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:38:21'),
(3, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:42:27'),
(4, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:45:25'),
(5, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:45:42'),
(6, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:50:11'),
(7, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:51:42'),
(8, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 06:53:49'),
(9, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:08:47'),
(10, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:09:54'),
(11, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:14:05'),
(12, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:15:19'),
(13, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:15:24'),
(14, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:15:29'),
(15, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:15:59'),
(16, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:16:20'),
(17, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:17:24'),
(18, 1, 1, '2026-02-10', 2.50, 1.50, 'approved', NULL, 1, NULL, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(4,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `user_id`, `date`, `hours`, `reason`, `status`, `approved_by`, `created_at`) VALUES
(1, 1, '2026-02-10', 2.00, 'Peak hour rush', 'pending', NULL, '2026-02-10 06:27:01'),
(2, 1, '2026-02-10', 2.00, 'Peak hour rush', 'pending', NULL, '2026-02-10 06:29:06'),
(3, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:37:51'),
(4, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:38:21'),
(5, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:42:27'),
(6, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:45:25'),
(7, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:45:42'),
(8, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:50:11'),
(9, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:51:42'),
(10, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 06:53:49'),
(11, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:08:47'),
(12, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:09:55'),
(13, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:14:05'),
(14, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:15:19'),
(15, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:15:24'),
(16, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:15:29'),
(17, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:15:59'),
(18, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:16:20'),
(19, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:17:24'),
(20, 1, '2026-02-10', 3.50, 'Backtest OT', 'approved', 1, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `tax_group_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `hsn_code` varchar(20) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'Nos',
  `min_stock_alert` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branch_id`, `tax_group_id`, `name`, `sku`, `hsn_code`, `unit`, `min_stock_alert`, `is_active`, `created_at`, `deleted_at`) VALUES
(1, 1, 2, 'Organic Whole Milk 1L', 'MLK001', '0401', 'Nos', 20, 1, '2026-02-07 05:14:07', NULL),
(2, 1, 1, 'Premium Sliced Bread', 'BRD001', '1905', 'Nos', 15, 1, '2026-02-07 05:14:07', NULL),
(3, 1, 1, 'Farm Fresh Eggs (Doz)', 'EGG001', '0407', 'Nos', 10, 1, '2026-02-07 05:14:07', NULL),
(4, 1, 1, 'Red Delicious Apples 1kg', 'APL001', '0808', 'Kg', 30, 1, '2026-02-07 05:14:07', NULL),
(5, 1, 2, 'Basmati Extra Long Rice 5kg', 'RCE001', '1006', 'Nos', 5, 1, '2026-02-07 05:14:07', NULL),
(6, 1, 4, 'Energy Drink 250ml', 'EDK001', '2202', 'Nos', 50, 1, '2026-02-07 05:14:07', NULL),
(33, 1, 1, 'Backtest Product', 'BT-TEST-SKU', NULL, 'Nos', 5, 1, '2026-02-10 07:09:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_batches`
--

CREATE TABLE `product_batches` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `product_id` int(11) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `stock_qty` decimal(10,3) DEFAULT 0.000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_batches`
--

INSERT INTO `product_batches` (`id`, `branch_id`, `product_id`, `batch_no`, `expiry_date`, `purchase_price`, `mrp`, `sale_price`, `stock_qty`, `created_at`) VALUES
(1, 1, 2, 'BCH-353', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(2, 1, 2, 'LSB-412', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(3, 1, 2, 'EXP-158', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(4, 1, 3, 'BCH-540', '2026-08-07', 50.00, 100.00, 327.99, 62.000, '2026-02-07 05:14:07'),
(5, 1, 3, 'LSB-387', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(6, 1, 3, 'EXP-175', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(7, 1, 4, 'BCH-854', '2026-08-07', 50.00, 100.00, 327.99, 75.000, '2026-02-07 05:14:07'),
(8, 1, 4, 'LSB-156', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(9, 1, 4, 'EXP-936', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(10, 1, 1, 'BCH-729', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(11, 1, 1, 'LSB-132', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(12, 1, 1, 'EXP-270', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(13, 1, 5, 'BCH-362', '2026-08-07', 50.00, 100.00, 327.99, 94.000, '2026-02-07 05:14:07'),
(14, 1, 5, 'LSB-731', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(15, 1, 5, 'EXP-850', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(16, 1, 6, 'BCH-780', '2026-08-07', 50.00, 100.00, 327.99, 54.000, '2026-02-07 05:14:07'),
(17, 1, 6, 'LSB-246', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(18, 1, 6, 'EXP-934', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(19, 1, 2, 'BCH-197', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(20, 1, 2, 'LSB-670', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(21, 1, 2, 'EXP-468', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(22, 1, 3, 'BCH-525', '2026-08-07', 50.00, 100.00, 327.99, 95.000, '2026-02-07 05:14:07'),
(23, 1, 3, 'LSB-817', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(24, 1, 3, 'EXP-227', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(25, 1, 4, 'BCH-657', '2026-08-07', 50.00, 100.00, 327.99, 86.000, '2026-02-07 05:14:07'),
(26, 1, 4, 'LSB-248', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(27, 1, 4, 'EXP-681', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(28, 1, 1, 'BCH-392', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(29, 1, 1, 'LSB-598', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(30, 1, 1, 'EXP-909', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(31, 1, 5, 'BCH-886', '2026-08-07', 50.00, 100.00, 327.99, 78.000, '2026-02-07 05:14:07'),
(32, 1, 5, 'LSB-379', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(33, 1, 5, 'EXP-134', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(34, 1, 6, 'BCH-823', '2026-08-07', 50.00, 100.00, 327.99, 54.000, '2026-02-07 05:14:07'),
(35, 1, 6, 'LSB-214', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(36, 1, 6, 'EXP-425', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(37, 1, 2, 'BCH-280', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(38, 1, 2, 'LSB-266', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(39, 1, 2, 'EXP-286', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(40, 1, 3, 'BCH-474', '2026-08-07', 50.00, 100.00, 327.99, 69.000, '2026-02-07 05:14:07'),
(41, 1, 3, 'LSB-529', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(42, 1, 3, 'EXP-653', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(43, 1, 4, 'BCH-678', '2026-08-07', 50.00, 100.00, 327.99, 53.000, '2026-02-07 05:14:07'),
(44, 1, 4, 'LSB-204', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(45, 1, 4, 'EXP-944', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(46, 1, 1, 'BCH-725', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(47, 1, 1, 'LSB-961', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(48, 1, 1, 'EXP-826', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(49, 1, 5, 'BCH-617', '2026-08-07', 50.00, 100.00, 327.99, 88.000, '2026-02-07 05:14:07'),
(50, 1, 5, 'LSB-332', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(51, 1, 5, 'EXP-302', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(52, 1, 6, 'BCH-164', '2026-08-07', 50.00, 100.00, 327.99, 52.000, '2026-02-07 05:14:07'),
(53, 1, 6, 'LSB-330', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(54, 1, 6, 'EXP-327', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(55, 1, 2, 'BCH-180', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(56, 1, 2, 'LSB-860', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(57, 1, 2, 'EXP-703', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(58, 1, 3, 'BCH-218', '2026-08-07', 50.00, 100.00, 327.99, 54.000, '2026-02-07 05:14:07'),
(59, 1, 3, 'LSB-679', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(60, 1, 3, 'EXP-637', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(61, 1, 4, 'BCH-217', '2026-08-07', 50.00, 100.00, 327.99, 88.000, '2026-02-07 05:14:07'),
(62, 1, 4, 'LSB-834', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(63, 1, 4, 'EXP-763', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(64, 1, 1, 'BCH-898', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(65, 1, 1, 'LSB-947', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(66, 1, 1, 'EXP-218', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(67, 1, 5, 'BCH-757', '2026-08-07', 50.00, 100.00, 327.99, 53.000, '2026-02-07 05:14:07'),
(68, 1, 5, 'LSB-965', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(69, 1, 5, 'EXP-571', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(70, 1, 6, 'BCH-250', '2026-08-07', 50.00, 100.00, 327.99, 90.000, '2026-02-07 05:14:07'),
(71, 1, 6, 'LSB-892', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(72, 1, 6, 'EXP-324', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(73, 1, 2, 'BCH-780', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(74, 1, 2, 'LSB-143', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(75, 1, 2, 'EXP-583', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(76, 1, 3, 'BCH-570', '2026-08-07', 50.00, 100.00, 327.99, 53.000, '2026-02-07 05:14:07'),
(77, 1, 3, 'LSB-961', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(78, 1, 3, 'EXP-909', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(79, 1, 4, 'BCH-456', '2026-08-07', 50.00, 100.00, 327.99, 42.000, '2026-02-07 05:14:07'),
(80, 1, 4, 'LSB-508', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(81, 1, 4, 'EXP-221', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(82, 1, 1, 'BCH-632', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(83, 1, 1, 'LSB-468', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(84, 1, 1, 'EXP-691', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 05:14:07'),
(85, 1, 5, 'BCH-981', '2026-08-07', 50.00, 100.00, 327.99, 67.000, '2026-02-07 05:14:07'),
(86, 1, 5, 'LSB-370', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(87, 1, 5, 'EXP-346', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(88, 1, 6, 'BCH-282', '2026-08-07', 50.00, 100.00, 327.99, 57.000, '2026-02-07 05:14:07'),
(89, 1, 6, 'LSB-225', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 05:14:07'),
(90, 1, 6, 'EXP-177', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 05:14:07'),
(91, 1, 2, 'BCH-922', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(92, 1, 2, 'LSB-812', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(93, 1, 2, 'EXP-775', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(94, 1, 3, 'BCH-762', '2026-08-07', 50.00, 100.00, 327.99, 73.000, '2026-02-07 09:52:49'),
(95, 1, 3, 'LSB-875', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(96, 1, 3, 'EXP-386', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(97, 1, 4, 'BCH-534', '2026-08-07', 50.00, 100.00, 327.99, 52.000, '2026-02-07 09:52:49'),
(98, 1, 4, 'LSB-230', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(99, 1, 4, 'EXP-678', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(100, 1, 1, 'BCH-126', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(101, 1, 1, 'LSB-779', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(102, 1, 1, 'EXP-391', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(103, 1, 5, 'BCH-385', '2026-08-07', 50.00, 100.00, 327.99, 50.000, '2026-02-07 09:52:49'),
(104, 1, 5, 'LSB-125', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(105, 1, 5, 'EXP-309', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(106, 1, 6, 'BCH-405', '2026-08-07', 50.00, 100.00, 327.99, 51.000, '2026-02-07 09:52:49'),
(107, 1, 6, 'LSB-932', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(108, 1, 6, 'EXP-692', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(109, 1, 2, 'BCH-667', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(110, 1, 2, 'LSB-309', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(111, 1, 2, 'EXP-479', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(112, 1, 3, 'BCH-894', '2026-08-07', 50.00, 100.00, 327.99, 77.000, '2026-02-07 09:52:49'),
(113, 1, 3, 'LSB-465', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(114, 1, 3, 'EXP-936', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(115, 1, 4, 'BCH-768', '2026-08-07', 50.00, 100.00, 327.99, 87.000, '2026-02-07 09:52:49'),
(116, 1, 4, 'LSB-940', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(117, 1, 4, 'EXP-958', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(118, 1, 1, 'BCH-357', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(119, 1, 1, 'LSB-256', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(120, 1, 1, 'EXP-744', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(121, 1, 5, 'BCH-804', '2026-08-07', 50.00, 100.00, 327.99, 58.000, '2026-02-07 09:52:49'),
(122, 1, 5, 'LSB-162', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(123, 1, 5, 'EXP-783', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(124, 1, 6, 'BCH-653', '2026-08-07', 50.00, 100.00, 327.99, 69.000, '2026-02-07 09:52:49'),
(125, 1, 6, 'LSB-433', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(126, 1, 6, 'EXP-680', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(127, 1, 2, 'BCH-798', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(128, 1, 2, 'LSB-265', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(129, 1, 2, 'EXP-551', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(130, 1, 3, 'BCH-385', '2026-08-07', 50.00, 100.00, 327.99, 95.000, '2026-02-07 09:52:49'),
(131, 1, 3, 'LSB-906', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(132, 1, 3, 'EXP-409', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(133, 1, 4, 'BCH-593', '2026-08-07', 50.00, 100.00, 327.99, 69.000, '2026-02-07 09:52:49'),
(134, 1, 4, 'LSB-790', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(135, 1, 4, 'EXP-216', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(136, 1, 1, 'BCH-867', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(137, 1, 1, 'LSB-252', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(138, 1, 1, 'EXP-219', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(139, 1, 5, 'BCH-910', '2026-08-07', 50.00, 100.00, 327.99, 76.000, '2026-02-07 09:52:49'),
(140, 1, 5, 'LSB-947', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(141, 1, 5, 'EXP-691', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(142, 1, 6, 'BCH-775', '2026-08-07', 50.00, 100.00, 327.99, 60.000, '2026-02-07 09:52:49'),
(143, 1, 6, 'LSB-983', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(144, 1, 6, 'EXP-225', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(145, 1, 2, 'BCH-561', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(146, 1, 2, 'LSB-542', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(147, 1, 2, 'EXP-546', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(148, 1, 3, 'BCH-459', '2026-08-07', 50.00, 100.00, 327.99, 99.000, '2026-02-07 09:52:49'),
(149, 1, 3, 'LSB-587', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(150, 1, 3, 'EXP-856', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(151, 1, 4, 'BCH-871', '2026-08-07', 50.00, 100.00, 327.99, 60.000, '2026-02-07 09:52:49'),
(152, 1, 4, 'LSB-237', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(153, 1, 4, 'EXP-204', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(154, 1, 1, 'BCH-838', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(155, 1, 1, 'LSB-562', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(156, 1, 1, 'EXP-300', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(157, 1, 5, 'BCH-280', '2026-08-07', 50.00, 100.00, 327.99, 89.000, '2026-02-07 09:52:49'),
(158, 1, 5, 'LSB-530', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(159, 1, 5, 'EXP-746', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(160, 1, 6, 'BCH-411', '2026-08-07', 50.00, 100.00, 327.99, 100.000, '2026-02-07 09:52:49'),
(161, 1, 6, 'LSB-154', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(162, 1, 6, 'EXP-991', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(163, 1, 2, 'BCH-116', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(164, 1, 2, 'LSB-143', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(165, 1, 2, 'EXP-716', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(166, 1, 3, 'BCH-693', '2026-08-07', 50.00, 100.00, 327.99, 77.000, '2026-02-07 09:52:49'),
(167, 1, 3, 'LSB-598', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(168, 1, 3, 'EXP-834', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(169, 1, 4, 'BCH-889', '2026-08-07', 50.00, 100.00, 327.99, 92.000, '2026-02-07 09:52:49'),
(170, 1, 4, 'LSB-697', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(171, 1, 4, 'EXP-796', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(172, 1, 1, 'BCH-614', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(173, 1, 1, 'LSB-787', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(174, 1, 1, 'EXP-260', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(175, 1, 5, 'BCH-870', '2026-08-07', 50.00, 100.00, 327.99, 73.000, '2026-02-07 09:52:49'),
(176, 1, 5, 'LSB-278', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(177, 1, 5, 'EXP-785', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(178, 1, 6, 'BCH-286', '2026-08-07', 50.00, 100.00, 327.99, 58.000, '2026-02-07 09:52:49'),
(179, 1, 6, 'LSB-377', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(180, 1, 6, 'EXP-521', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(181, 1, 2, 'BCH-166', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(182, 1, 2, 'LSB-285', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(183, 1, 2, 'EXP-139', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(184, 1, 3, 'BCH-114', '2026-08-07', 50.00, 100.00, 327.99, 80.000, '2026-02-07 09:52:49'),
(185, 1, 3, 'LSB-482', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(186, 1, 3, 'EXP-673', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(187, 1, 4, 'BCH-562', '2026-08-07', 50.00, 100.00, 327.99, 55.000, '2026-02-07 09:52:49'),
(188, 1, 4, 'LSB-658', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(189, 1, 4, 'EXP-137', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(190, 1, 1, 'BCH-267', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(191, 1, 1, 'LSB-225', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(192, 1, 1, 'EXP-761', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(193, 1, 5, 'BCH-686', '2026-08-07', 50.00, 100.00, 327.99, 83.000, '2026-02-07 09:52:49'),
(194, 1, 5, 'LSB-466', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(195, 1, 5, 'EXP-527', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(196, 1, 6, 'BCH-583', '2026-08-07', 50.00, 100.00, 327.99, 84.000, '2026-02-07 09:52:49'),
(197, 1, 6, 'LSB-481', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(198, 1, 6, 'EXP-531', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(199, 1, 2, 'BCH-282', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(200, 1, 2, 'LSB-638', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(201, 1, 2, 'EXP-748', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(202, 1, 3, 'BCH-511', '2026-08-07', 50.00, 100.00, 327.99, 74.000, '2026-02-07 09:52:49'),
(203, 1, 3, 'LSB-557', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(204, 1, 3, 'EXP-564', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(205, 1, 4, 'BCH-569', '2026-08-07', 50.00, 100.00, 327.99, 90.000, '2026-02-07 09:52:49'),
(206, 1, 4, 'LSB-606', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(207, 1, 4, 'EXP-247', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(208, 1, 1, 'BCH-463', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(209, 1, 1, 'LSB-259', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(210, 1, 1, 'EXP-225', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(211, 1, 5, 'BCH-669', '2026-08-07', 50.00, 100.00, 327.99, 87.000, '2026-02-07 09:52:49'),
(212, 1, 5, 'LSB-656', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(213, 1, 5, 'EXP-367', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(214, 1, 6, 'BCH-452', '2026-08-07', 50.00, 100.00, 327.99, 80.000, '2026-02-07 09:52:49'),
(215, 1, 6, 'LSB-363', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(216, 1, 6, 'EXP-918', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(217, 1, 2, 'BCH-239', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(218, 1, 2, 'LSB-623', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(219, 1, 2, 'EXP-141', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(220, 1, 3, 'BCH-917', '2026-08-07', 50.00, 100.00, 327.99, 93.000, '2026-02-07 09:52:49'),
(221, 1, 3, 'LSB-437', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(222, 1, 3, 'EXP-118', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(223, 1, 4, 'BCH-191', '2026-08-07', 50.00, 100.00, 327.99, 72.000, '2026-02-07 09:52:49'),
(224, 1, 4, 'LSB-838', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(225, 1, 4, 'EXP-700', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(226, 1, 1, 'BCH-712', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(227, 1, 1, 'LSB-217', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(228, 1, 1, 'EXP-902', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:52:49'),
(229, 1, 5, 'BCH-895', '2026-08-07', 50.00, 100.00, 327.99, 61.000, '2026-02-07 09:52:49'),
(230, 1, 5, 'LSB-948', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(231, 1, 5, 'EXP-196', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(232, 1, 6, 'BCH-680', '2026-08-07', 50.00, 100.00, 327.99, 75.000, '2026-02-07 09:52:49'),
(233, 1, 6, 'LSB-479', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:52:49'),
(234, 1, 6, 'EXP-248', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:52:49'),
(235, 1, 2, 'BCH-993', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(236, 1, 2, 'LSB-140', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(237, 1, 2, 'EXP-497', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(238, 1, 3, 'BCH-562', '2026-08-07', 50.00, 100.00, 327.99, 86.000, '2026-02-07 09:54:56'),
(239, 1, 3, 'LSB-545', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(240, 1, 3, 'EXP-425', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(241, 1, 4, 'BCH-788', '2026-08-07', 50.00, 100.00, 327.99, 64.000, '2026-02-07 09:54:56'),
(242, 1, 4, 'LSB-394', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(243, 1, 4, 'EXP-750', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(244, 1, 1, 'BCH-735', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(245, 1, 1, 'LSB-367', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(246, 1, 1, 'EXP-967', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(247, 1, 5, 'BCH-650', '2026-08-07', 50.00, 100.00, 327.99, 77.000, '2026-02-07 09:54:56'),
(248, 1, 5, 'LSB-835', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(249, 1, 5, 'EXP-778', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(250, 1, 6, 'BCH-929', '2026-08-07', 50.00, 100.00, 327.99, 71.000, '2026-02-07 09:54:56'),
(251, 1, 6, 'LSB-257', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(252, 1, 6, 'EXP-685', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(253, 1, 2, 'BCH-473', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(254, 1, 2, 'LSB-831', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(255, 1, 2, 'EXP-945', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(256, 1, 3, 'BCH-137', '2026-08-07', 50.00, 100.00, 327.99, 84.000, '2026-02-07 09:54:56'),
(257, 1, 3, 'LSB-222', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(258, 1, 3, 'EXP-186', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(259, 1, 4, 'BCH-156', '2026-08-07', 50.00, 100.00, 327.99, 100.000, '2026-02-07 09:54:56'),
(260, 1, 4, 'LSB-272', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(261, 1, 4, 'EXP-563', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(262, 1, 1, 'BCH-799', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(263, 1, 1, 'LSB-269', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(264, 1, 1, 'EXP-735', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(265, 1, 5, 'BCH-208', '2026-08-07', 50.00, 100.00, 327.99, 56.000, '2026-02-07 09:54:56'),
(266, 1, 5, 'LSB-515', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(267, 1, 5, 'EXP-554', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(268, 1, 6, 'BCH-784', '2026-08-07', 50.00, 100.00, 327.99, 79.000, '2026-02-07 09:54:56'),
(269, 1, 6, 'LSB-733', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(270, 1, 6, 'EXP-780', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(271, 1, 2, 'BCH-480', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(272, 1, 2, 'LSB-722', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(273, 1, 2, 'EXP-233', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(274, 1, 3, 'BCH-432', '2026-08-07', 50.00, 100.00, 327.99, 89.000, '2026-02-07 09:54:56'),
(275, 1, 3, 'LSB-422', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(276, 1, 3, 'EXP-244', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(277, 1, 4, 'BCH-268', '2026-08-07', 50.00, 100.00, 327.99, 100.000, '2026-02-07 09:54:56'),
(278, 1, 4, 'LSB-254', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(279, 1, 4, 'EXP-412', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(280, 1, 1, 'BCH-678', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(281, 1, 1, 'LSB-133', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(282, 1, 1, 'EXP-108', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(283, 1, 5, 'BCH-649', '2026-08-07', 50.00, 100.00, 327.99, 62.000, '2026-02-07 09:54:56'),
(284, 1, 5, 'LSB-494', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(285, 1, 5, 'EXP-123', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(286, 1, 6, 'BCH-267', '2026-08-07', 50.00, 100.00, 327.99, 69.000, '2026-02-07 09:54:56'),
(287, 1, 6, 'LSB-678', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(288, 1, 6, 'EXP-512', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(289, 1, 2, 'BCH-311', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(290, 1, 2, 'LSB-848', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(291, 1, 2, 'EXP-597', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(292, 1, 3, 'BCH-132', '2026-08-07', 50.00, 100.00, 327.99, 65.000, '2026-02-07 09:54:56'),
(293, 1, 3, 'LSB-298', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(294, 1, 3, 'EXP-942', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(295, 1, 4, 'BCH-474', '2026-08-07', 50.00, 100.00, 327.99, 96.000, '2026-02-07 09:54:56'),
(296, 1, 4, 'LSB-927', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(297, 1, 4, 'EXP-713', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(298, 1, 1, 'BCH-998', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(299, 1, 1, 'LSB-771', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(300, 1, 1, 'EXP-632', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(301, 1, 5, 'BCH-143', '2026-08-07', 50.00, 100.00, 327.99, 52.000, '2026-02-07 09:54:56'),
(302, 1, 5, 'LSB-308', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(303, 1, 5, 'EXP-533', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(304, 1, 6, 'BCH-854', '2026-08-07', 50.00, 100.00, 327.99, 62.000, '2026-02-07 09:54:56'),
(305, 1, 6, 'LSB-163', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(306, 1, 6, 'EXP-308', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(307, 1, 2, 'BCH-540', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(308, 1, 2, 'LSB-822', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(309, 1, 2, 'EXP-629', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(310, 1, 3, 'BCH-168', '2026-08-07', 50.00, 100.00, 327.99, 74.000, '2026-02-07 09:54:56'),
(311, 1, 3, 'LSB-189', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(312, 1, 3, 'EXP-810', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(313, 1, 4, 'BCH-105', '2026-08-07', 50.00, 100.00, 327.99, 71.000, '2026-02-07 09:54:56'),
(314, 1, 4, 'LSB-136', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(315, 1, 4, 'EXP-552', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(316, 1, 1, 'BCH-510', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(317, 1, 1, 'LSB-676', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(318, 1, 1, 'EXP-800', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(319, 1, 5, 'BCH-339', '2026-08-07', 50.00, 100.00, 327.99, 93.000, '2026-02-07 09:54:56'),
(320, 1, 5, 'LSB-430', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(321, 1, 5, 'EXP-515', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(322, 1, 6, 'BCH-779', '2026-08-07', 50.00, 100.00, 327.99, 87.000, '2026-02-07 09:54:56'),
(323, 1, 6, 'LSB-448', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(324, 1, 6, 'EXP-287', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(325, 1, 2, 'BCH-362', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(326, 1, 2, 'LSB-541', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(327, 1, 2, 'EXP-461', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(328, 1, 3, 'BCH-115', '2026-08-07', 50.00, 100.00, 327.99, 54.000, '2026-02-07 09:54:56'),
(329, 1, 3, 'LSB-208', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(330, 1, 3, 'EXP-690', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(331, 1, 4, 'BCH-929', '2026-08-07', 50.00, 100.00, 327.99, 73.000, '2026-02-07 09:54:56'),
(332, 1, 4, 'LSB-333', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(333, 1, 4, 'EXP-600', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(334, 1, 1, 'BCH-193', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(335, 1, 1, 'LSB-608', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(336, 1, 1, 'EXP-413', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(337, 1, 5, 'BCH-658', '2026-08-07', 50.00, 100.00, 327.99, 82.000, '2026-02-07 09:54:56'),
(338, 1, 5, 'LSB-148', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(339, 1, 5, 'EXP-235', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(340, 1, 6, 'BCH-882', '2026-08-07', 50.00, 100.00, 327.99, 58.000, '2026-02-07 09:54:56'),
(341, 1, 6, 'LSB-668', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(342, 1, 6, 'EXP-238', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(343, 1, 2, 'BCH-167', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(344, 1, 2, 'LSB-612', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(345, 1, 2, 'EXP-882', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(346, 1, 3, 'BCH-783', '2026-08-07', 50.00, 100.00, 327.99, 71.000, '2026-02-07 09:54:56'),
(347, 1, 3, 'LSB-381', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(348, 1, 3, 'EXP-149', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(349, 1, 4, 'BCH-472', '2026-08-07', 50.00, 100.00, 327.99, 88.000, '2026-02-07 09:54:56'),
(350, 1, 4, 'LSB-826', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(351, 1, 4, 'EXP-696', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(352, 1, 1, 'BCH-186', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(353, 1, 1, 'LSB-714', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(354, 1, 1, 'EXP-652', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(355, 1, 5, 'BCH-692', '2026-08-07', 50.00, 100.00, 327.99, 60.000, '2026-02-07 09:54:56'),
(356, 1, 5, 'LSB-332', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(357, 1, 5, 'EXP-200', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(358, 1, 6, 'BCH-539', '2026-08-07', 50.00, 100.00, 327.99, 90.000, '2026-02-07 09:54:56'),
(359, 1, 6, 'LSB-456', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(360, 1, 6, 'EXP-859', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(361, 1, 2, 'BCH-660', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(362, 1, 2, 'LSB-522', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(363, 1, 2, 'EXP-877', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(364, 1, 3, 'BCH-875', '2026-08-07', 50.00, 100.00, 327.99, 57.000, '2026-02-07 09:54:56'),
(365, 1, 3, 'LSB-231', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(366, 1, 3, 'EXP-451', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(367, 1, 4, 'BCH-232', '2026-08-07', 50.00, 100.00, 327.99, 54.000, '2026-02-07 09:54:56'),
(368, 1, 4, 'LSB-995', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(369, 1, 4, 'EXP-333', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(370, 1, 1, 'BCH-407', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(371, 1, 1, 'LSB-713', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(372, 1, 1, 'EXP-149', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(373, 1, 5, 'BCH-832', '2026-08-07', 50.00, 100.00, 327.99, 67.000, '2026-02-07 09:54:56'),
(374, 1, 5, 'LSB-268', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(375, 1, 5, 'EXP-104', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(376, 1, 6, 'BCH-835', '2026-08-07', 50.00, 100.00, 327.99, 75.000, '2026-02-07 09:54:56'),
(377, 1, 6, 'LSB-918', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(378, 1, 6, 'EXP-495', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(379, 1, 2, 'BCH-266', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(380, 1, 2, 'LSB-970', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(381, 1, 2, 'EXP-577', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(382, 1, 3, 'BCH-920', '2026-08-07', 50.00, 100.00, 327.99, 50.000, '2026-02-07 09:54:56'),
(383, 1, 3, 'LSB-742', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(384, 1, 3, 'EXP-899', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(385, 1, 4, 'BCH-467', '2026-08-07', 50.00, 100.00, 327.99, 62.000, '2026-02-07 09:54:56'),
(386, 1, 4, 'LSB-821', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:56'),
(387, 1, 4, 'EXP-628', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:56'),
(388, 1, 1, 'BCH-874', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(389, 1, 1, 'LSB-253', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:56'),
(390, 1, 1, 'EXP-773', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(391, 1, 5, 'BCH-894', '2026-08-07', 50.00, 100.00, 327.99, 63.000, '2026-02-07 09:54:57'),
(392, 1, 5, 'LSB-838', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(393, 1, 5, 'EXP-750', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(394, 1, 6, 'BCH-543', '2026-08-07', 50.00, 100.00, 327.99, 80.000, '2026-02-07 09:54:57'),
(395, 1, 6, 'LSB-434', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(396, 1, 6, 'EXP-341', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(397, 1, 2, 'BCH-823', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(398, 1, 2, 'LSB-433', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(399, 1, 2, 'EXP-943', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(400, 1, 3, 'BCH-709', '2026-08-07', 50.00, 100.00, 327.99, 94.000, '2026-02-07 09:54:57'),
(401, 1, 3, 'LSB-660', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(402, 1, 3, 'EXP-474', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(403, 1, 4, 'BCH-284', '2026-08-07', 50.00, 100.00, 327.99, 92.000, '2026-02-07 09:54:57'),
(404, 1, 4, 'LSB-359', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(405, 1, 4, 'EXP-376', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(406, 1, 1, 'BCH-309', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(407, 1, 1, 'LSB-995', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(408, 1, 1, 'EXP-730', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(409, 1, 5, 'BCH-354', '2026-08-07', 50.00, 100.00, 327.99, 96.000, '2026-02-07 09:54:57'),
(410, 1, 5, 'LSB-545', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(411, 1, 5, 'EXP-741', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(412, 1, 6, 'BCH-849', '2026-08-07', 50.00, 100.00, 327.99, 60.000, '2026-02-07 09:54:57'),
(413, 1, 6, 'LSB-878', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(414, 1, 6, 'EXP-842', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(415, 1, 2, 'BCH-645', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(416, 1, 2, 'LSB-341', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(417, 1, 2, 'EXP-636', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(418, 1, 3, 'BCH-626', '2026-08-07', 50.00, 100.00, 327.99, 52.000, '2026-02-07 09:54:57'),
(419, 1, 3, 'LSB-819', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(420, 1, 3, 'EXP-286', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(421, 1, 4, 'BCH-363', '2026-08-07', 50.00, 100.00, 327.99, 68.000, '2026-02-07 09:54:57'),
(422, 1, 4, 'LSB-325', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(423, 1, 4, 'EXP-157', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(424, 1, 1, 'BCH-439', '2026-08-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(425, 1, 1, 'LSB-647', '2026-05-07', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(426, 1, 1, 'EXP-934', '2026-02-14', 50.00, 100.00, 327.99, 0.000, '2026-02-07 09:54:57'),
(427, 1, 5, 'BCH-968', '2026-08-07', 50.00, 100.00, 327.99, 88.000, '2026-02-07 09:54:57'),
(428, 1, 5, 'LSB-410', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(429, 1, 5, 'EXP-588', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(430, 1, 6, 'BCH-909', '2026-08-07', 50.00, 100.00, 327.99, 56.000, '2026-02-07 09:54:57'),
(431, 1, 6, 'LSB-879', '2026-05-07', 50.00, 100.00, 327.99, 2.000, '2026-02-07 09:54:57'),
(432, 1, 6, 'EXP-294', '2026-02-14', 50.00, 100.00, 327.99, 10.000, '2026-02-07 09:54:57'),
(433, 1, 2, 'BCH-710', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(434, 1, 2, 'LSB-833', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(435, 1, 2, 'EXP-279', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(436, 1, 3, 'BCH-161', '2026-08-10', 50.00, 100.00, 327.99, 94.000, '2026-02-10 06:02:12'),
(437, 1, 3, 'LSB-852', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(438, 1, 3, 'EXP-503', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(439, 1, 4, 'BCH-990', '2026-08-10', 50.00, 100.00, 327.99, 69.000, '2026-02-10 06:02:12'),
(440, 1, 4, 'LSB-598', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(441, 1, 4, 'EXP-735', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(442, 1, 1, 'BCH-405', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(443, 1, 1, 'LSB-803', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(444, 1, 1, 'EXP-127', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(445, 1, 5, 'BCH-553', '2026-08-10', 50.00, 100.00, 327.99, 81.000, '2026-02-10 06:02:12'),
(446, 1, 5, 'LSB-663', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(447, 1, 5, 'EXP-427', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(448, 1, 6, 'BCH-256', '2026-08-10', 50.00, 100.00, 327.99, 76.000, '2026-02-10 06:02:12'),
(449, 1, 6, 'LSB-243', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(450, 1, 6, 'EXP-242', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(451, 1, 2, 'BCH-918', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(452, 1, 2, 'LSB-720', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(453, 1, 2, 'EXP-941', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(454, 1, 3, 'BCH-948', '2026-08-10', 50.00, 100.00, 327.99, 80.000, '2026-02-10 06:02:12'),
(455, 1, 3, 'LSB-499', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(456, 1, 3, 'EXP-612', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(457, 1, 4, 'BCH-125', '2026-08-10', 50.00, 100.00, 327.99, 83.000, '2026-02-10 06:02:12'),
(458, 1, 4, 'LSB-378', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(459, 1, 4, 'EXP-894', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(460, 1, 1, 'BCH-486', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(461, 1, 1, 'LSB-887', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(462, 1, 1, 'EXP-985', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(463, 1, 5, 'BCH-369', '2026-08-10', 50.00, 100.00, 327.99, 71.000, '2026-02-10 06:02:12'),
(464, 1, 5, 'LSB-819', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(465, 1, 5, 'EXP-112', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(466, 1, 6, 'BCH-137', '2026-08-10', 50.00, 100.00, 327.99, 98.000, '2026-02-10 06:02:12'),
(467, 1, 6, 'LSB-331', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(468, 1, 6, 'EXP-370', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(469, 1, 2, 'BCH-545', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(470, 1, 2, 'LSB-506', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(471, 1, 2, 'EXP-521', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(472, 1, 3, 'BCH-461', '2026-08-10', 50.00, 100.00, 327.99, 80.000, '2026-02-10 06:02:12'),
(473, 1, 3, 'LSB-923', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(474, 1, 3, 'EXP-638', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(475, 1, 4, 'BCH-277', '2026-08-10', 50.00, 100.00, 327.99, 73.000, '2026-02-10 06:02:12'),
(476, 1, 4, 'LSB-500', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(477, 1, 4, 'EXP-357', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(478, 1, 1, 'BCH-601', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(479, 1, 1, 'LSB-435', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(480, 1, 1, 'EXP-942', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(481, 1, 5, 'BCH-958', '2026-08-10', 50.00, 100.00, 327.99, 60.000, '2026-02-10 06:02:12'),
(482, 1, 5, 'LSB-563', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(483, 1, 5, 'EXP-824', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(484, 1, 6, 'BCH-452', '2026-08-10', 50.00, 100.00, 327.99, 61.000, '2026-02-10 06:02:12'),
(485, 1, 6, 'LSB-270', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(486, 1, 6, 'EXP-589', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(487, 1, 2, 'BCH-937', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(488, 1, 2, 'LSB-410', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(489, 1, 2, 'EXP-929', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(490, 1, 3, 'BCH-297', '2026-08-10', 50.00, 100.00, 327.99, 67.000, '2026-02-10 06:02:12'),
(491, 1, 3, 'LSB-849', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(492, 1, 3, 'EXP-346', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(493, 1, 4, 'BCH-508', '2026-08-10', 50.00, 100.00, 327.99, 67.000, '2026-02-10 06:02:12'),
(494, 1, 4, 'LSB-717', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(495, 1, 4, 'EXP-409', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(496, 1, 1, 'BCH-158', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(497, 1, 1, 'LSB-873', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(498, 1, 1, 'EXP-795', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(499, 1, 5, 'BCH-124', '2026-08-10', 50.00, 100.00, 327.99, 52.000, '2026-02-10 06:02:12'),
(500, 1, 5, 'LSB-876', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(501, 1, 5, 'EXP-216', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(502, 1, 6, 'BCH-209', '2026-08-10', 50.00, 100.00, 327.99, 97.000, '2026-02-10 06:02:12'),
(503, 1, 6, 'LSB-857', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(504, 1, 6, 'EXP-477', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(505, 1, 2, 'BCH-924', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(506, 1, 2, 'LSB-219', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(507, 1, 2, 'EXP-309', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(508, 1, 3, 'BCH-440', '2026-08-10', 50.00, 100.00, 327.99, 68.000, '2026-02-10 06:02:12'),
(509, 1, 3, 'LSB-844', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(510, 1, 3, 'EXP-468', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(511, 1, 4, 'BCH-388', '2026-08-10', 50.00, 100.00, 327.99, 98.000, '2026-02-10 06:02:12'),
(512, 1, 4, 'LSB-339', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(513, 1, 4, 'EXP-984', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(514, 1, 1, 'BCH-379', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(515, 1, 1, 'LSB-920', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(516, 1, 1, 'EXP-384', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(517, 1, 5, 'BCH-794', '2026-08-10', 50.00, 100.00, 327.99, 96.000, '2026-02-10 06:02:12'),
(518, 1, 5, 'LSB-513', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(519, 1, 5, 'EXP-542', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(520, 1, 6, 'BCH-618', '2026-08-10', 50.00, 100.00, 327.99, 59.000, '2026-02-10 06:02:12'),
(521, 1, 6, 'LSB-653', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(522, 1, 6, 'EXP-590', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(523, 1, 2, 'BCH-175', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(524, 1, 2, 'LSB-308', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(525, 1, 2, 'EXP-349', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(526, 1, 3, 'BCH-406', '2026-08-10', 50.00, 100.00, 327.99, 97.000, '2026-02-10 06:02:12'),
(527, 1, 3, 'LSB-337', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(528, 1, 3, 'EXP-274', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(529, 1, 4, 'BCH-628', '2026-08-10', 50.00, 100.00, 327.99, 89.000, '2026-02-10 06:02:12'),
(530, 1, 4, 'LSB-978', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(531, 1, 4, 'EXP-314', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(532, 1, 1, 'BCH-766', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(533, 1, 1, 'LSB-722', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(534, 1, 1, 'EXP-748', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(535, 1, 5, 'BCH-880', '2026-08-10', 50.00, 100.00, 327.99, 96.000, '2026-02-10 06:02:12'),
(536, 1, 5, 'LSB-985', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(537, 1, 5, 'EXP-391', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(538, 1, 6, 'BCH-477', '2026-08-10', 50.00, 100.00, 327.99, 59.000, '2026-02-10 06:02:12'),
(539, 1, 6, 'LSB-566', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(540, 1, 6, 'EXP-450', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(541, 1, 2, 'BCH-401', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(542, 1, 2, 'LSB-790', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(543, 1, 2, 'EXP-148', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(544, 1, 3, 'BCH-377', '2026-08-10', 50.00, 100.00, 327.99, 97.000, '2026-02-10 06:02:12'),
(545, 1, 3, 'LSB-578', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(546, 1, 3, 'EXP-336', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(547, 1, 4, 'BCH-885', '2026-08-10', 50.00, 100.00, 327.99, 77.000, '2026-02-10 06:02:12'),
(548, 1, 4, 'LSB-152', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(549, 1, 4, 'EXP-501', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(550, 1, 1, 'BCH-351', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(551, 1, 1, 'LSB-449', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(552, 1, 1, 'EXP-371', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(553, 1, 5, 'BCH-396', '2026-08-10', 50.00, 100.00, 327.99, 99.000, '2026-02-10 06:02:12'),
(554, 1, 5, 'LSB-307', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(555, 1, 5, 'EXP-358', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(556, 1, 6, 'BCH-819', '2026-08-10', 50.00, 100.00, 327.99, 90.000, '2026-02-10 06:02:12'),
(557, 1, 6, 'LSB-942', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(558, 1, 6, 'EXP-697', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12');
INSERT INTO `product_batches` (`id`, `branch_id`, `product_id`, `batch_no`, `expiry_date`, `purchase_price`, `mrp`, `sale_price`, `stock_qty`, `created_at`) VALUES
(559, 1, 2, 'BCH-761', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(560, 1, 2, 'LSB-580', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(561, 1, 2, 'EXP-681', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(562, 1, 3, 'BCH-858', '2026-08-10', 50.00, 100.00, 327.99, 82.000, '2026-02-10 06:02:12'),
(563, 1, 3, 'LSB-862', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(564, 1, 3, 'EXP-127', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(565, 1, 4, 'BCH-498', '2026-08-10', 50.00, 100.00, 327.99, 88.000, '2026-02-10 06:02:12'),
(566, 1, 4, 'LSB-627', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(567, 1, 4, 'EXP-855', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(568, 1, 1, 'BCH-671', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(569, 1, 1, 'LSB-128', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(570, 1, 1, 'EXP-904', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(571, 1, 5, 'BCH-152', '2026-08-10', 50.00, 100.00, 327.99, 71.000, '2026-02-10 06:02:12'),
(572, 1, 5, 'LSB-693', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(573, 1, 5, 'EXP-352', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(574, 1, 6, 'BCH-492', '2026-08-10', 50.00, 100.00, 327.99, 69.000, '2026-02-10 06:02:12'),
(575, 1, 6, 'LSB-975', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(576, 1, 6, 'EXP-801', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(577, 1, 2, 'BCH-793', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(578, 1, 2, 'LSB-577', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(579, 1, 2, 'EXP-269', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(580, 1, 3, 'BCH-223', '2026-08-10', 50.00, 100.00, 327.99, 70.000, '2026-02-10 06:02:12'),
(581, 1, 3, 'LSB-773', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(582, 1, 3, 'EXP-989', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(583, 1, 4, 'BCH-973', '2026-08-10', 50.00, 100.00, 327.99, 72.000, '2026-02-10 06:02:12'),
(584, 1, 4, 'LSB-266', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(585, 1, 4, 'EXP-207', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(586, 1, 1, 'BCH-842', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(587, 1, 1, 'LSB-233', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(588, 1, 1, 'EXP-487', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(589, 1, 5, 'BCH-207', '2026-08-10', 50.00, 100.00, 327.99, 64.000, '2026-02-10 06:02:12'),
(590, 1, 5, 'LSB-911', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(591, 1, 5, 'EXP-468', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(592, 1, 6, 'BCH-983', '2026-08-10', 50.00, 100.00, 327.99, 99.000, '2026-02-10 06:02:12'),
(593, 1, 6, 'LSB-801', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(594, 1, 6, 'EXP-917', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(595, 1, 2, 'BCH-726', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(596, 1, 2, 'LSB-600', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(597, 1, 2, 'EXP-695', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(598, 1, 3, 'BCH-282', '2026-08-10', 50.00, 100.00, 327.99, 58.000, '2026-02-10 06:02:12'),
(599, 1, 3, 'LSB-746', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(600, 1, 3, 'EXP-659', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(601, 1, 4, 'BCH-543', '2026-08-10', 50.00, 100.00, 327.99, 93.000, '2026-02-10 06:02:12'),
(602, 1, 4, 'LSB-214', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(603, 1, 4, 'EXP-987', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(604, 1, 1, 'BCH-763', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(605, 1, 1, 'LSB-450', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(606, 1, 1, 'EXP-797', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(607, 1, 5, 'BCH-356', '2026-08-10', 50.00, 100.00, 327.99, 66.000, '2026-02-10 06:02:12'),
(608, 1, 5, 'LSB-644', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(609, 1, 5, 'EXP-356', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(610, 1, 6, 'BCH-745', '2026-08-10', 50.00, 100.00, 327.99, 62.000, '2026-02-10 06:02:12'),
(611, 1, 6, 'LSB-744', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(612, 1, 6, 'EXP-326', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(613, 1, 2, 'BCH-706', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(614, 1, 2, 'LSB-420', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(615, 1, 2, 'EXP-208', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(616, 1, 3, 'BCH-977', '2026-08-10', 50.00, 100.00, 327.99, 77.000, '2026-02-10 06:02:12'),
(617, 1, 3, 'LSB-998', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(618, 1, 3, 'EXP-572', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(619, 1, 4, 'BCH-166', '2026-08-10', 50.00, 100.00, 327.99, 95.000, '2026-02-10 06:02:12'),
(620, 1, 4, 'LSB-116', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(621, 1, 4, 'EXP-649', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(622, 1, 1, 'BCH-836', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(623, 1, 1, 'LSB-704', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(624, 1, 1, 'EXP-985', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(625, 1, 5, 'BCH-402', '2026-08-10', 50.00, 100.00, 327.99, 70.000, '2026-02-10 06:02:12'),
(626, 1, 5, 'LSB-188', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(627, 1, 5, 'EXP-602', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(628, 1, 6, 'BCH-992', '2026-08-10', 50.00, 100.00, 327.99, 63.000, '2026-02-10 06:02:12'),
(629, 1, 6, 'LSB-493', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(630, 1, 6, 'EXP-228', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(631, 1, 2, 'BCH-618', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(632, 1, 2, 'LSB-897', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(633, 1, 2, 'EXP-336', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(634, 1, 3, 'BCH-963', '2026-08-10', 50.00, 100.00, 327.99, 51.000, '2026-02-10 06:02:12'),
(635, 1, 3, 'LSB-922', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(636, 1, 3, 'EXP-132', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(637, 1, 4, 'BCH-308', '2026-08-10', 50.00, 100.00, 327.99, 87.000, '2026-02-10 06:02:12'),
(638, 1, 4, 'LSB-311', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(639, 1, 4, 'EXP-706', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(640, 1, 1, 'BCH-452', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(641, 1, 1, 'LSB-690', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(642, 1, 1, 'EXP-824', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(643, 1, 5, 'BCH-286', '2026-08-10', 50.00, 100.00, 327.99, 84.000, '2026-02-10 06:02:12'),
(644, 1, 5, 'LSB-464', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(645, 1, 5, 'EXP-320', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(646, 1, 6, 'BCH-308', '2026-08-10', 50.00, 100.00, 327.99, 65.000, '2026-02-10 06:02:12'),
(647, 1, 6, 'LSB-488', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(648, 1, 6, 'EXP-249', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(649, 1, 2, 'BCH-411', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(650, 1, 2, 'LSB-854', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(651, 1, 2, 'EXP-273', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(652, 1, 3, 'BCH-123', '2026-08-10', 50.00, 100.00, 327.99, 68.000, '2026-02-10 06:02:12'),
(653, 1, 3, 'LSB-355', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(654, 1, 3, 'EXP-606', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(655, 1, 4, 'BCH-818', '2026-08-10', 50.00, 100.00, 327.99, 54.000, '2026-02-10 06:02:12'),
(656, 1, 4, 'LSB-574', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(657, 1, 4, 'EXP-973', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(658, 1, 1, 'BCH-886', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(659, 1, 1, 'LSB-126', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(660, 1, 1, 'EXP-987', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(661, 1, 5, 'BCH-193', '2026-08-10', 50.00, 100.00, 327.99, 54.000, '2026-02-10 06:02:12'),
(662, 1, 5, 'LSB-669', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(663, 1, 5, 'EXP-444', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(664, 1, 6, 'BCH-530', '2026-08-10', 50.00, 100.00, 327.99, 84.000, '2026-02-10 06:02:12'),
(665, 1, 6, 'LSB-141', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(666, 1, 6, 'EXP-422', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(667, 1, 2, 'BCH-809', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(668, 1, 2, 'LSB-833', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(669, 1, 2, 'EXP-440', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(670, 1, 3, 'BCH-312', '2026-08-10', 50.00, 100.00, 327.99, 84.000, '2026-02-10 06:02:12'),
(671, 1, 3, 'LSB-618', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(672, 1, 3, 'EXP-338', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(673, 1, 4, 'BCH-296', '2026-08-10', 50.00, 100.00, 327.99, 96.000, '2026-02-10 06:02:12'),
(674, 1, 4, 'LSB-243', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(675, 1, 4, 'EXP-404', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:12'),
(676, 1, 1, 'BCH-106', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(677, 1, 1, 'LSB-415', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(678, 1, 1, 'EXP-277', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:12'),
(679, 1, 5, 'BCH-783', '2026-08-10', 50.00, 100.00, 327.99, 100.000, '2026-02-10 06:02:12'),
(680, 1, 5, 'LSB-695', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:12'),
(681, 1, 5, 'EXP-776', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(682, 1, 6, 'BCH-708', '2026-08-10', 50.00, 100.00, 327.99, 88.000, '2026-02-10 06:02:13'),
(683, 1, 6, 'LSB-565', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(684, 1, 6, 'EXP-530', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(685, 1, 2, 'BCH-619', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(686, 1, 2, 'LSB-481', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(687, 1, 2, 'EXP-723', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(688, 1, 3, 'BCH-645', '2026-08-10', 50.00, 100.00, 327.99, 57.000, '2026-02-10 06:02:13'),
(689, 1, 3, 'LSB-486', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(690, 1, 3, 'EXP-959', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(691, 1, 4, 'BCH-705', '2026-08-10', 50.00, 100.00, 327.99, 96.000, '2026-02-10 06:02:13'),
(692, 1, 4, 'LSB-848', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(693, 1, 4, 'EXP-866', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(694, 1, 1, 'BCH-393', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(695, 1, 1, 'LSB-348', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(696, 1, 1, 'EXP-764', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(697, 1, 5, 'BCH-408', '2026-08-10', 50.00, 100.00, 327.99, 82.000, '2026-02-10 06:02:13'),
(698, 1, 5, 'LSB-731', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(699, 1, 5, 'EXP-733', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(700, 1, 6, 'BCH-580', '2026-08-10', 50.00, 100.00, 327.99, 95.000, '2026-02-10 06:02:13'),
(701, 1, 6, 'LSB-982', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(702, 1, 6, 'EXP-150', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(703, 1, 2, 'BCH-326', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(704, 1, 2, 'LSB-501', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(705, 1, 2, 'EXP-446', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(706, 1, 3, 'BCH-510', '2026-08-10', 50.00, 100.00, 327.99, 95.000, '2026-02-10 06:02:13'),
(707, 1, 3, 'LSB-618', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(708, 1, 3, 'EXP-932', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(709, 1, 4, 'BCH-624', '2026-08-10', 50.00, 100.00, 327.99, 67.000, '2026-02-10 06:02:13'),
(710, 1, 4, 'LSB-284', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(711, 1, 4, 'EXP-634', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(712, 1, 1, 'BCH-696', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(713, 1, 1, 'LSB-846', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(714, 1, 1, 'EXP-780', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(715, 1, 5, 'BCH-629', '2026-08-10', 50.00, 100.00, 327.99, 89.000, '2026-02-10 06:02:13'),
(716, 1, 5, 'LSB-355', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(717, 1, 5, 'EXP-597', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(718, 1, 6, 'BCH-728', '2026-08-10', 50.00, 100.00, 327.99, 79.000, '2026-02-10 06:02:13'),
(719, 1, 6, 'LSB-202', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(720, 1, 6, 'EXP-723', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(721, 1, 2, 'BCH-821', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(722, 1, 2, 'LSB-132', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(723, 1, 2, 'EXP-925', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(724, 1, 3, 'BCH-298', '2026-08-10', 50.00, 100.00, 327.99, 99.000, '2026-02-10 06:02:13'),
(725, 1, 3, 'LSB-660', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(726, 1, 3, 'EXP-569', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(727, 1, 4, 'BCH-299', '2026-08-10', 50.00, 100.00, 327.99, 50.000, '2026-02-10 06:02:13'),
(728, 1, 4, 'LSB-621', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(729, 1, 4, 'EXP-235', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(730, 1, 1, 'BCH-673', '2026-08-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(731, 1, 1, 'LSB-692', '2026-05-10', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(732, 1, 1, 'EXP-314', '2026-02-17', 50.00, 100.00, 327.99, 0.000, '2026-02-10 06:02:13'),
(733, 1, 5, 'BCH-568', '2026-08-10', 50.00, 100.00, 327.99, 54.000, '2026-02-10 06:02:13'),
(734, 1, 5, 'LSB-101', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(735, 1, 5, 'EXP-989', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(736, 1, 6, 'BCH-895', '2026-08-10', 50.00, 100.00, 327.99, 56.000, '2026-02-10 06:02:13'),
(737, 1, 6, 'LSB-247', '2026-05-10', 50.00, 100.00, 327.99, 2.000, '2026-02-10 06:02:13'),
(738, 1, 6, 'EXP-555', '2026-02-17', 50.00, 100.00, 327.99, 10.000, '2026-02-10 06:02:13'),
(748, 1, 33, 'BT-B001', NULL, 50.00, 80.00, 176.86, 55.000, '2026-02-10 07:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `product_locations`
--

CREATE TABLE `product_locations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `x_coord` int(11) NOT NULL,
  `y_coord` int(11) NOT NULL,
  `z_layer` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','ordered','partially_delivered','delivered','cancelled') DEFAULT 'pending',
  `delivery_schedule` date DEFAULT NULL,
  `invoice_pdf` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `backorder_status` enum('all_active','partial_backorder','full_backorder') DEFAULT 'all_active',
  `grn_signature` text DEFAULT NULL,
  `grn_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `vendor_id`, `branch_id`, `order_no`, `total_amount`, `status`, `delivery_schedule`, `invoice_pdf`, `created_by`, `created_at`, `backorder_status`, `grn_signature`, `grn_photo`) VALUES
(1, 2, 1, 'PO-3252', 15000.00, 'delivered', NULL, 'backtest_invoice.pdf', 1, '2026-02-07 05:14:07', 'all_active', 'signed_data', NULL),
(2, 4, 1, 'PO-6074', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(3, 2, 2, 'PO-9661', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(4, 4, 2, 'PO-8483', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(5, 2, 3, 'PO-4812', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(6, 4, 3, 'PO-5561', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(7, 2, 4, 'PO-1995', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(8, 4, 4, 'PO-6802', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(9, 2, 5, 'PO-5128', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(10, 4, 5, 'PO-1637', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 05:14:07', 'all_active', NULL, NULL),
(11, 2, 1, 'PO-8501', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(12, 4, 1, 'PO-2198', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(13, 2, 2, 'PO-1360', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(14, 4, 2, 'PO-2218', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(15, 2, 3, 'PO-6407', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(16, 4, 3, 'PO-6533', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(17, 2, 4, 'PO-5388', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(18, 4, 4, 'PO-3895', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(19, 2, 5, 'PO-2760', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(20, 4, 5, 'PO-7597', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(21, 2, 6, 'PO-4847', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(22, 4, 6, 'PO-2338', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(23, 2, 7, 'PO-7269', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(24, 4, 7, 'PO-5274', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(25, 2, 8, 'PO-2282', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(26, 4, 8, 'PO-9467', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:52:49', 'all_active', NULL, NULL),
(27, 2, 1, 'PO-9835', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(28, 4, 1, 'PO-1742', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(29, 2, 2, 'PO-9980', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(30, 4, 2, 'PO-3945', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(31, 2, 3, 'PO-3794', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(32, 4, 3, 'PO-3766', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(33, 2, 4, 'PO-4319', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(34, 4, 4, 'PO-1505', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(35, 2, 5, 'PO-8133', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(36, 4, 5, 'PO-6712', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(37, 2, 6, 'PO-7859', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(38, 4, 6, 'PO-4656', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(39, 2, 7, 'PO-5626', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(40, 4, 7, 'PO-1732', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(41, 2, 8, 'PO-6642', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(42, 4, 8, 'PO-4565', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(43, 2, 9, 'PO-9410', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(44, 4, 9, 'PO-2196', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(45, 2, 10, 'PO-7104', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(46, 4, 10, 'PO-7426', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(47, 2, 11, 'PO-9012', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(48, 4, 11, 'PO-9909', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 09:54:57', 'all_active', NULL, NULL),
(49, 2, 1, 'PO-TEST-1770459740', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-07 10:22:20', 'all_active', NULL, NULL),
(50, 2, 1, 'PO-TEST-1770703251', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:00:51', 'all_active', NULL, NULL),
(51, 2, 1, 'PO-TEST-1770703290', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:01:30', 'all_active', NULL, NULL),
(52, 2, 1, 'PO-8808', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(53, 4, 1, 'PO-9954', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(54, 2, 2, 'PO-7511', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(55, 4, 2, 'PO-7790', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(56, 2, 3, 'PO-6766', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(57, 4, 3, 'PO-4288', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(58, 2, 4, 'PO-7589', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(59, 4, 4, 'PO-7136', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(60, 2, 5, 'PO-7969', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(61, 4, 5, 'PO-9994', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(62, 2, 6, 'PO-1845', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(63, 4, 6, 'PO-6027', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(64, 2, 7, 'PO-5675', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(65, 4, 7, 'PO-6328', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(66, 2, 8, 'PO-5403', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(67, 4, 8, 'PO-5108', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(68, 2, 9, 'PO-6208', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(69, 4, 9, 'PO-7439', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(70, 2, 10, 'PO-5666', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(71, 4, 10, 'PO-3542', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(72, 2, 11, 'PO-2150', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(73, 4, 11, 'PO-9374', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(74, 2, 12, 'PO-5747', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(75, 4, 12, 'PO-2552', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(76, 2, 13, 'PO-3497', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(77, 4, 13, 'PO-3148', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(78, 2, 14, 'PO-8927', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(79, 4, 14, 'PO-6848', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(80, 2, 15, 'PO-7613', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(81, 4, 15, 'PO-4201', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(82, 2, 16, 'PO-5879', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(83, 4, 16, 'PO-1350', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(84, 2, 17, 'PO-2534', 15000.00, 'delivered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(85, 4, 17, 'PO-5060', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:13', 'all_active', NULL, NULL),
(86, 2, 1, 'PO-TEST-1770703365', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:02:45', 'all_active', NULL, NULL),
(87, 2, 1, 'PO-TEST-1770704657', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:24:17', 'all_active', NULL, NULL),
(88, 2, 1, 'PO-TEST-1770704828', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:27:08', 'all_active', NULL, NULL),
(89, 2, 1, 'PO-TEST-1770704929', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:28:49', 'all_active', NULL, NULL),
(90, 2, 1, 'PO-TEST-1770704946', 5000.00, 'ordered', NULL, NULL, 1, '2026-02-10 06:29:06', 'all_active', NULL, NULL),
(91, 2, 1, 'BT-PO-1770705471', 5000.00, '', NULL, NULL, 1, '2026-02-10 06:37:51', 'all_active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` decimal(10,3) NOT NULL,
  `estimated_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Administrator', 'admin', '2026-02-06 09:55:33'),
(2, 'Store Manager', 'manager', '2026-02-06 09:55:33'),
(3, 'Cashier', 'cashier', '2026-02-06 09:55:33'),
(4, 'Stock Keeper', 'inventory', '2026-02-06 09:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `input_type` varchar(20) DEFAULT 'text',
  `label` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `input_type`, `label`, `created_at`, `updated_at`) VALUES
(1, 'theme_primary_color', '#0d6efd', 'branding', 'color', 'Primary Color', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(2, 'theme_accent_color', '#6610f2', 'branding', 'color', 'Accent Color', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(3, 'theme_sidebar_color', '#212529', 'branding', 'color', 'Sidebar Background', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(4, 'store_name', 'Supermarket OS', 'store', 'text', 'Store Name', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(5, 'store_address', '123 Main St, City', 'store', 'textarea', 'Address', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(6, 'store_phone', '+1 234 567 890', 'store', 'text', 'Phone Number', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(7, 'store_gstin', '', 'store', 'text', 'GSTIN / Tax ID', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(8, 'maintenance_mode', '0', 'system', 'boolean', 'Maintenance Mode', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(9, 'debug_mode', '0', 'system', 'boolean', 'Debug Mode', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(10, 'smtp_host', 'smtp.example.com', 'email', 'text', 'SMTP Host', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(11, 'smtp_port', '587', 'email', 'number', 'SMTP Port', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(12, 'smtp_user', 'user@example.com', 'email', 'text', 'SMTP Username', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(13, 'smtp_pass', '', 'email', 'password', 'SMTP Password', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(14, 'smtp_encryption', 'tls', 'email', 'select', 'Encryption (tls/ssl)', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(15, 'invoice_footer_text', 'Thank you for your business!', 'invoice', 'textarea', 'Invoice Footer', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(16, 'invoice_show_logo', '1', 'invoice', 'boolean', 'Show Logo on Invoice', '2026-02-07 07:17:47', '2026-02-07 07:17:47'),
(17, 'grace_period_enabled', '1', 'attendance', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(18, 'grace_period_default_minutes', '15', 'attendance', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(19, 'grace_period_max_monthly_uses', '5', 'attendance', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(20, 'overtime_rate_standard', '1.5', 'payroll', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(21, 'overtime_rate_holiday', '2.0', 'payroll', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(22, 'overtime_auto_approve_threshold', '2', 'payroll', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(23, 'open_shifts_enabled', '1', 'roster', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25'),
(24, 'open_shifts_approval_required', '0', 'roster', 'text', NULL, '2026-02-07 08:06:25', '2026-02-07 08:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `shift_templates`
--

CREATE TABLE `shift_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_minutes` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_templates`
--

INSERT INTO `shift_templates` (`id`, `name`, `start_time`, `end_time`, `break_minutes`, `description`, `created_at`) VALUES
(1, 'Morning Shift', '06:00:00', '14:00:00', 30, 'Standard morning shift with 30-min break', '2026-02-07 08:06:25'),
(2, 'Day Shift', '09:00:00', '17:00:00', 60, 'Standard day shift with 1-hour lunch', '2026-02-07 08:06:25'),
(3, 'Evening Shift', '14:00:00', '22:00:00', 30, 'Evening shift with 30-min break', '2026-02-07 08:06:25'),
(4, 'Night Shift', '22:00:00', '06:00:00', 45, 'Overnight shift with 45-min break', '2026-02-07 08:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL,
  `from_branch` int(11) DEFAULT NULL,
  `from_branch_id` int(11) NOT NULL,
  `to_branch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `status` enum('requested','pending','transit','completed','cancelled') DEFAULT 'requested',
  `transfer_no` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `to_branch` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transfers`
--

INSERT INTO `stock_transfers` (`id`, `from_branch`, `from_branch_id`, `to_branch_id`, `product_id`, `batch_id`, `qty`, `status`, `transfer_no`, `created_by`, `received_by`, `remarks`, `created_at`, `updated_at`, `to_branch`) VALUES
(6, NULL, 1, 2, 1, 10, 5.00, 'pending', 'TRN-TEST-1770704929', 1, NULL, NULL, '2026-02-10 06:28:49', '2026-02-10 06:28:49', NULL),
(7, NULL, 1, 2, 1, 10, 5.00, 'pending', 'TRN-TEST-1770704946', 1, NULL, NULL, '2026-02-10 06:29:06', '2026-02-10 06:29:06', NULL),
(8, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770705471', 1, NULL, NULL, '2026-02-10 06:37:51', '2026-02-10 06:37:51', NULL),
(9, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770705501', 1, NULL, NULL, '2026-02-10 06:38:21', '2026-02-10 06:38:21', NULL),
(10, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770705747', 1, NULL, NULL, '2026-02-10 06:42:27', '2026-02-10 06:42:27', NULL),
(11, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770705925', 1, NULL, NULL, '2026-02-10 06:45:25', '2026-02-10 06:45:25', NULL),
(12, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770705942', 1, NULL, NULL, '2026-02-10 06:45:42', '2026-02-10 06:45:42', NULL),
(13, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770706211', 1, NULL, NULL, '2026-02-10 06:50:11', '2026-02-10 06:50:11', NULL),
(14, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770706302', 1, NULL, NULL, '2026-02-10 06:51:42', '2026-02-10 06:51:42', NULL),
(15, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770706429', 1, NULL, NULL, '2026-02-10 06:53:49', '2026-02-10 06:53:49', NULL),
(16, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707327', 1, NULL, NULL, '2026-02-10 07:08:47', '2026-02-10 07:08:47', NULL),
(17, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707394', 1, NULL, NULL, '2026-02-10 07:09:54', '2026-02-10 07:09:54', NULL),
(18, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707645', 1, NULL, NULL, '2026-02-10 07:14:05', '2026-02-10 07:14:05', NULL),
(19, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707719', 1, NULL, NULL, '2026-02-10 07:15:19', '2026-02-10 07:15:19', NULL),
(20, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707724', 1, NULL, NULL, '2026-02-10 07:15:24', '2026-02-10 07:15:24', NULL),
(21, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707729', 1, NULL, NULL, '2026-02-10 07:15:29', '2026-02-10 07:15:29', NULL),
(22, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707759', 1, NULL, NULL, '2026-02-10 07:15:59', '2026-02-10 07:15:59', NULL),
(23, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707780', 1, NULL, NULL, '2026-02-10 07:16:20', '2026-02-10 07:16:20', NULL),
(24, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707844', 1, NULL, NULL, '2026-02-10 07:17:24', '2026-02-10 07:17:24', NULL),
(25, NULL, 1, 2, 1, 10, 3.00, 'completed', 'BT-TRN-1770707964', 1, NULL, NULL, '2026-02-10 07:19:24', '2026-02-10 07:19:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax_groups`
--

CREATE TABLE `tax_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_groups`
--

INSERT INTO `tax_groups` (`id`, `name`, `percentage`, `created_at`) VALUES
(1, 'Exempt', 0.00, '2026-02-06 09:55:33'),
(2, 'GST 5%', 5.00, '2026-02-06 09:55:33'),
(3, 'GST 12%', 12.00, '2026-02-06 09:55:33'),
(4, 'GST 18%', 18.00, '2026-02-06 09:55:33'),
(5, 'GST 28%', 28.00, '2026-02-06 09:55:33'),
(36, 'Exempt', 0.00, '2026-02-07 09:27:58'),
(37, 'GST 5%', 5.00, '2026-02-07 09:27:58'),
(38, 'GST 12%', 12.00, '2026-02-07 09:27:58'),
(39, 'GST 18%', 18.00, '2026-02-07 09:27:58'),
(40, 'GST 28%', 28.00, '2026-02-07 09:27:58'),
(41, 'Exempt', 0.00, '2026-02-07 09:28:13'),
(42, 'GST 5%', 5.00, '2026-02-07 09:28:13'),
(43, 'GST 12%', 12.00, '2026-02-07 09:28:13'),
(44, 'GST 18%', 18.00, '2026-02-07 09:28:13'),
(45, 'GST 28%', 28.00, '2026-02-07 09:28:13'),
(46, 'Exempt', 0.00, '2026-02-07 09:29:50'),
(47, 'GST 5%', 5.00, '2026-02-07 09:29:50'),
(48, 'GST 12%', 12.00, '2026-02-07 09:29:50'),
(49, 'GST 18%', 18.00, '2026-02-07 09:29:50'),
(50, 'GST 28%', 28.00, '2026-02-07 09:29:50'),
(51, 'Exempt', 0.00, '2026-02-07 09:30:39'),
(52, 'GST 5%', 5.00, '2026-02-07 09:30:39'),
(53, 'GST 12%', 12.00, '2026-02-07 09:30:39'),
(54, 'GST 18%', 18.00, '2026-02-07 09:30:39'),
(55, 'GST 28%', 28.00, '2026-02-07 09:30:39'),
(56, 'Exempt', 0.00, '2026-02-07 09:31:16'),
(57, 'GST 5%', 5.00, '2026-02-07 09:31:16'),
(58, 'GST 12%', 12.00, '2026-02-07 09:31:16'),
(59, 'GST 18%', 18.00, '2026-02-07 09:31:16'),
(60, 'GST 28%', 28.00, '2026-02-07 09:31:16'),
(61, 'Exempt', 0.00, '2026-02-07 09:31:29'),
(62, 'GST 5%', 5.00, '2026-02-07 09:31:29'),
(63, 'GST 12%', 12.00, '2026-02-07 09:31:29'),
(64, 'GST 18%', 18.00, '2026-02-07 09:31:29'),
(65, 'GST 28%', 28.00, '2026-02-07 09:31:29'),
(66, 'Exempt', 0.00, '2026-02-07 09:32:27'),
(67, 'GST 5%', 5.00, '2026-02-07 09:32:27'),
(68, 'GST 12%', 12.00, '2026-02-07 09:32:27'),
(69, 'GST 18%', 18.00, '2026-02-07 09:32:27'),
(70, 'GST 28%', 28.00, '2026-02-07 09:32:27'),
(71, 'Exempt', 0.00, '2026-02-07 09:33:16'),
(72, 'GST 5%', 5.00, '2026-02-07 09:33:16'),
(73, 'GST 12%', 12.00, '2026-02-07 09:33:16'),
(74, 'GST 18%', 18.00, '2026-02-07 09:33:16'),
(75, 'GST 28%', 28.00, '2026-02-07 09:33:16'),
(76, 'Exempt', 0.00, '2026-02-07 09:34:17'),
(77, 'GST 5%', 5.00, '2026-02-07 09:34:17'),
(78, 'GST 12%', 12.00, '2026-02-07 09:34:17'),
(79, 'GST 18%', 18.00, '2026-02-07 09:34:17'),
(80, 'GST 28%', 28.00, '2026-02-07 09:34:17'),
(81, 'Exempt', 0.00, '2026-02-07 09:36:53'),
(82, 'GST 5%', 5.00, '2026-02-07 09:36:53'),
(83, 'GST 12%', 12.00, '2026-02-07 09:36:53'),
(84, 'GST 18%', 18.00, '2026-02-07 09:36:53'),
(85, 'GST 28%', 28.00, '2026-02-07 09:36:53'),
(86, 'Special GST 20', 7.50, '2026-02-07 10:22:20'),
(87, 'Exempt', 0.00, '2026-02-10 05:31:11'),
(88, 'GST 5%', 5.00, '2026-02-10 05:31:11'),
(89, 'GST 12%', 12.00, '2026-02-10 05:31:11'),
(90, 'GST 18%', 18.00, '2026-02-10 05:31:11'),
(91, 'GST 28%', 28.00, '2026-02-10 05:31:11'),
(92, 'Special GST 51', 7.50, '2026-02-10 06:00:50'),
(93, 'Special GST 75', 7.50, '2026-02-10 06:01:29'),
(94, 'Special GST 97', 7.50, '2026-02-10 06:02:43'),
(95, 'Special GST 44', 7.50, '2026-02-10 06:29:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `role_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `last_password_change` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `grace_period_minutes` int(11) DEFAULT 15,
  `overtime_eligible` tinyint(1) DEFAULT 1,
  `max_grace_uses_per_month` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `role_id`, `username`, `password_hash`, `full_name`, `email`, `phone`, `status`, `last_login`, `created_at`, `failed_login_attempts`, `locked_until`, `last_password_change`, `deleted_at`, `grace_period_minutes`, `overtime_eligible`, `max_grace_uses_per_month`) VALUES
(1, 1, 1, 'admin', '$2y$10$iZeDB4wMpWeWehnC6j5szelvovB2nt6J6UvDHc/NYt.kaDdyEO85a', 'Super Admin', NULL, NULL, 'active', NULL, '2026-02-06 09:55:33', 0, NULL, '2026-02-07 07:20:15', NULL, 15, 1, 5),
(2, 4, 4, 'Sriram', '$2y$10$gn4xKq1gZcrPbxkcAGdfyuC1BvW18uN88KuT8SrCLi6o.hPLNGQRW', 'Sriram', NULL, NULL, 'active', NULL, '2026-02-07 07:01:08', 0, NULL, '2026-02-07 07:20:15', NULL, 15, 1, 5),
(3, 1, 3, 'tester_921', '$2y$10$wgSdEqOTdGvs9qPYoZPEwOGZGvI1ZH7MAixvQVYy2CyA0naNudzsi', 'Test User', NULL, NULL, 'active', NULL, '2026-02-07 10:22:20', 0, NULL, '2026-02-07 10:22:20', NULL, 15, 1, 5),
(4, 1, 3, 'tester_215', '$2y$10$8t9ox4zTC71yM62MUMXhiO.B5ooy/0k2Yt66UOV6FPnz5uv4146uO', 'Test User', NULL, NULL, 'active', NULL, '2026-02-10 06:00:50', 0, NULL, '2026-02-10 06:00:50', NULL, 15, 1, 5),
(5, 1, 3, 'tester_344', '$2y$10$OqS1/I8L29.fh6Jn0SzFpuqB1wjylmOMMe0Lm/rch.60pRhcZXMR6', 'Test User', NULL, NULL, 'active', NULL, '2026-02-10 06:01:28', 0, NULL, '2026-02-10 06:01:28', NULL, 15, 1, 5),
(6, 1, 3, 'tester_620', '$2y$10$STms2/6JlrSTpOObOFupdOHjUFrHpHm0kjRhhn4rffnm1yYACEkYO', 'Test User', NULL, NULL, 'active', NULL, '2026-02-10 06:02:43', 0, NULL, '2026-02-10 06:02:43', NULL, 15, 1, 5),
(7, 1, 3, 'tester_249', '$2y$10$Uw82UR3OhXP1Ww20IGWSH.y.09KoYrb/Vm2zvaGUnfdbU1J7y8CPC', 'Test User', NULL, NULL, 'active', NULL, '2026-02-10 06:29:06', 0, NULL, '2026-02-10 06:29:06', NULL, 15, 1, 5),
(42, 1, 3, 'bt_test_user', '$2y$10$qaWNVtdAPXeS50jgA1sIyOQAvtOkGK1OAA2aSH556MieI56rP.sa2', 'Backtest User', NULL, NULL, 'active', '2026-02-10 12:49:24', '2026-02-10 07:19:24', 0, NULL, '2026-02-10 07:19:24', NULL, 15, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user_biometrics`
--

CREATE TABLE `user_biometrics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `credential_id` text NOT NULL,
  `public_key` text DEFAULT NULL,
  `label` varchar(100) DEFAULT 'My Device',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_biometrics`
--

INSERT INTO `user_biometrics` (`id`, `user_id`, `credential_id`, `public_key`, `label`, `created_at`) VALUES
(1, 1, 'cred_8907673069dd1e8c', NULL, 'Test Device', '2026-02-07 06:20:52'),
(2, 1, 'DdwAazHFp95m64rAiPGZfA', NULL, 'My Device (Feb 07)', '2026-02-07 06:30:19'),
(3, 1, 'H-61Kk3s8pW_3KcjU2vsMCeq44OPsNm87X-olcP5wSE', NULL, 'My Device (Feb 07)', '2026-02-07 06:30:35'),
(4, 1, 'cred_fd3eb2b4a41b29da', NULL, 'Test Device', '2026-02-07 09:52:51'),
(5, 1, 'cred_2151d15eb08a558d', NULL, 'Test Device', '2026-02-07 09:53:28'),
(6, 1, 'cred_39dc1755f1deb2a5', NULL, 'Test Device', '2026-02-07 09:54:58'),
(7, 1, 'cred_c2cadc65efd1daab', NULL, 'Test Device', '2026-02-07 10:22:19'),
(8, 1, 'cred_38d7547262028943', NULL, 'Test Device', '2026-02-10 05:59:59'),
(9, 1, 'cred_048bb58ee1d49b75', NULL, 'Test Device', '2026-02-10 06:02:32'),
(10, 1, 'cred_a0f4dbdacd7cdf37', NULL, 'Test Device', '2026-02-10 06:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `tin_no`, `is_active`, `created_at`) VALUES
(1, 'Global Suppliers', 'supplier@example.com', '$2y$10$A5nFU/i1eVe5CR0rgGJGf.wegi67uDfpOBfFijYW4DklgDAr6j4TC', '9876543210', NULL, NULL, 1, '2026-02-06 12:15:52'),
(2, 'Global Supplies Inc', 'contact@globalsupply.com', '$2y$10$.5icdI9RDVFphv9v68rslePeRyxxrhuRUtym36eSbgziBbOTRupxO', '9876543210', NULL, NULL, 1, '2026-02-07 05:14:07'),
(3, 'Local Fresh produce', 'local@produce.com', '$2y$10$9OWsmMGiraZhAWpIvFvYv.dikX5MRWHNQBRB/t8PpFi5QtuKTtGyy', '9123456789', NULL, NULL, 1, '2026-02-07 05:14:07'),
(4, 'Tech Logistics', 'info@techlogistics.com', '$2y$10$qvYxIoe5.iqsKJuYbApzqeBIn1XUvbiUHhaWd3kX3YwNyDhdc70uq', '7788990011', NULL, NULL, 1, '2026-02-07 05:14:07'),
(31, 'BT Vendor', 'bt_vendor@test.com', '', '9999999999', NULL, NULL, 1, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_broadcasts`
--

CREATE TABLE `vendor_broadcasts` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ledger`
--

CREATE TABLE `vendor_ledger` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_quotations`
--

CREATE TABLE `vendor_quotations` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `proposed_price` decimal(10,2) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `trigger_event` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `name`, `trigger_event`, `description`, `is_active`, `created_at`) VALUES
(1, 'New Employee Welcome', 'user_created', 'Send welcome email when new staff is added', 1, '2026-02-07 07:39:57'),
(2, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:37:51'),
(3, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:38:21'),
(4, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:42:27'),
(5, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:45:25'),
(6, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:45:42'),
(7, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:50:11'),
(8, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:51:42'),
(9, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 06:53:49'),
(10, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:08:47'),
(11, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:09:55'),
(12, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:14:05'),
(13, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:15:19'),
(14, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:15:24'),
(15, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:15:29'),
(16, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:15:59'),
(17, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:16:20'),
(18, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:17:24'),
(19, 'BT Workflow', 'test_event', 'Backtest workflow', 1, '2026-02-10 07:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_actions`
--

CREATE TABLE `workflow_actions` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_payload`)),
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_actions`
--

INSERT INTO `workflow_actions` (`id`, `workflow_id`, `action_type`, `action_payload`, `sort_order`) VALUES
(1, 1, 'send_email', '{\"template_key\":\"email_welcome\",\"recipient_field\":\"email\"}', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_table` (`original_table`),
  ADD KEY `archived_at` (`archived_at`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`date`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user_id` (`user_id`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `branch_product_settings`
--
ALTER TABLE `branch_product_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_branch_product` (`branch_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `employee_messages`
--
ALTER TABLE `employee_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `employee_roster`
--
ALTER TABLE `employee_roster`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grace_period_logs`
--
ALTER TABLE `grace_period_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `idx_date` (`created_at`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_invoices_user_id` (`user_id`),
  ADD KEY `idx_invoices_date` (`created_at`),
  ADD KEY `idx_invoices_inv_no` (`invoice_no`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `idx_inv_items_invoice_id` (`invoice_id`),
  ADD KEY `idx_inv_items_product_id` (`product_id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map_sections`
--
ALTER TABLE `map_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_branch` (`branch_id`);

--
-- Indexes for table `overtime_records`
--
ALTER TABLE `overtime_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_products_tax_group` (`tax_group_id`),
  ADD KEY `idx_products_name` (`name`),
  ADD KEY `idx_products_sku` (`sku`);

--
-- Indexes for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_expiry` (`expiry_date`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_batches_product_id` (`product_id`),
  ADD KEY `idx_batches_stock` (`stock_qty`);

--
-- Indexes for table `product_locations`
--
ALTER TABLE `product_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pos` (`section_id`,`x_coord`,`y_coord`,`z_layer`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `shift_templates`
--
ALTER TABLE `shift_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_no` (`transfer_no`),
  ADD KEY `from_branch_id` (`from_branch_id`),
  ADD KEY `to_branch_id` (`to_branch_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `tax_groups`
--
ALTER TABLE `tax_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_users_role_id` (`role_id`),
  ADD KEY `idx_users_status` (`status`);

--
-- Indexes for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_broadcasts`
--
ALTER TABLE `vendor_broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `vendor_ledger`
--
ALTER TABLE `vendor_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `vendor_quotations`
--
ALTER TABLE `vendor_quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_id` (`workflow_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `automation_logs`
--
ALTER TABLE `automation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `branch_product_settings`
--
ALTER TABLE `branch_product_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `employee_messages`
--
ALTER TABLE `employee_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `employee_roster`
--
ALTER TABLE `employee_roster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `grace_period_logs`
--
ALTER TABLE `grace_period_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `map_sections`
--
ALTER TABLE `map_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `overtime_records`
--
ALTER TABLE `overtime_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=749;

--
-- AUTO_INCREMENT for table `product_locations`
--
ALTER TABLE `product_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `shift_templates`
--
ALTER TABLE `shift_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tax_groups`
--
ALTER TABLE `tax_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `vendor_broadcasts`
--
ALTER TABLE `vendor_broadcasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_ledger`
--
ALTER TABLE `vendor_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_quotations`
--
ALTER TABLE `vendor_quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `branch_product_settings`
--
ALTER TABLE `branch_product_settings`
  ADD CONSTRAINT `branch_product_settings_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `branch_product_settings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD CONSTRAINT `employee_leaves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employee_leaves_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `employee_messages`
--
ALTER TABLE `employee_messages`
  ADD CONSTRAINT `employee_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `employee_shifts`
--
ALTER TABLE `employee_shifts`
  ADD CONSTRAINT `employee_shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `grace_period_logs`
--
ALTER TABLE `grace_period_logs`
  ADD CONSTRAINT `grace_period_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `invoice_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`);

--
-- Constraints for table `map_sections`
--
ALTER TABLE `map_sections`
  ADD CONSTRAINT `map_sections_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `overtime_records`
--
ALTER TABLE `overtime_records`
  ADD CONSTRAINT `overtime_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_records_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`tax_group_id`) REFERENCES `tax_groups` (`id`);

--
-- Constraints for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD CONSTRAINT `product_batches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_locations`
--
ALTER TABLE `product_locations`
  ADD CONSTRAINT `product_locations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_locations_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `map_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_ibfk_1` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `stock_transfers_ibfk_2` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `stock_transfers_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_transfers_ibfk_4` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`),
  ADD CONSTRAINT `stock_transfers_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_transfers_ibfk_6` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD CONSTRAINT `user_biometrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_broadcasts`
--
ALTER TABLE `vendor_broadcasts`
  ADD CONSTRAINT `vendor_broadcasts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `vendor_ledger`
--
ALTER TABLE `vendor_ledger`
  ADD CONSTRAINT `vendor_ledger_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  ADD CONSTRAINT `vendor_ledger_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`);

--
-- Constraints for table `vendor_quotations`
--
ALTER TABLE `vendor_quotations`
  ADD CONSTRAINT `vendor_quotations_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  ADD CONSTRAINT `vendor_quotations_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  ADD CONSTRAINT `workflow_actions_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
