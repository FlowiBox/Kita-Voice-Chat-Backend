-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 16, 2022 at 01:03 AM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yai-chat`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Dashboard', 'fa-bar-chart', '/', NULL, NULL, NULL),
(2, 0, 2, 'Admin', 'fa-tasks', '', NULL, NULL, NULL),
(3, 2, 3, 'Users', 'fa-users', 'auth/users', NULL, NULL, NULL),
(4, 2, 4, 'Roles', 'fa-user', 'auth/roles', NULL, NULL, NULL),
(5, 2, 5, 'Permission', 'fa-ban', 'auth/permissions', NULL, NULL, NULL),
(6, 2, 6, 'Menu', 'fa-bars', 'auth/menu', NULL, NULL, NULL),
(7, 2, 7, 'Operation log', 'fa-history', 'auth/logs', NULL, NULL, NULL),
(8, 0, 7, 'Helpers', 'fa-gears', '', NULL, '2022-11-15 19:07:39', '2022-11-15 19:07:39'),
(9, 8, 8, 'Scaffold', 'fa-keyboard-o', 'helpers/scaffold', NULL, '2022-11-15 19:07:39', '2022-11-15 19:07:39'),
(10, 8, 9, 'Database terminal', 'fa-database', 'helpers/terminal/database', NULL, '2022-11-15 19:07:39', '2022-11-15 19:07:39'),
(11, 8, 10, 'Laravel artisan', 'fa-terminal', 'helpers/terminal/artisan', NULL, '2022-11-15 19:07:39', '2022-11-15 19:07:39'),
(12, 8, 11, 'Routes', 'fa-list-alt', 'helpers/routes', NULL, '2022-11-15 19:07:39', '2022-11-15 19:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `admin_operation_log`
--

CREATE TABLE `admin_operation_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_operation_log`
--

INSERT INTO `admin_operation_log` (`id`, `user_id`, `path`, `method`, `ip`, `input`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:05:58', '2022-11-15 19:05:58'),
(2, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:07:18', '2022-11-15 19:07:18'),
(3, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:07:47', '2022-11-15 19:07:47'),
(4, 1, 'admin/helpers/scaffold', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:07:51', '2022-11-15 19:07:51'),
(5, 1, 'admin/auth/roles', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:08:41', '2022-11-15 19:08:41'),
(6, 1, 'admin/auth/roles', 'GET', '::1', '[]', '2022-11-15 19:12:29', '2022-11-15 19:12:29'),
(7, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:12:36', '2022-11-15 19:12:36'),
(8, 1, 'admin/auth/roles', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:12:48', '2022-11-15 19:12:48'),
(9, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:12:54', '2022-11-15 19:12:54'),
(10, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:14:53', '2022-11-15 19:14:53'),
(11, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:15:24', '2022-11-15 19:15:24'),
(12, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:15:56', '2022-11-15 19:15:56'),
(13, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:16:22', '2022-11-15 19:16:22'),
(14, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:16:25', '2022-11-15 19:16:25'),
(15, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:17:23', '2022-11-15 19:17:23'),
(16, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:17:41', '2022-11-15 19:17:41'),
(17, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:20:16', '2022-11-15 19:20:16'),
(18, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:20:51', '2022-11-15 19:20:51'),
(19, 1, 'admin/auth/roles/1/edit', 'GET', '::1', '[]', '2022-11-15 19:22:07', '2022-11-15 19:22:07'),
(20, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:22:21', '2022-11-15 19:22:21'),
(21, 1, 'admin/auth/menu', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:26:13', '2022-11-15 19:26:13'),
(22, 1, 'admin/auth/menu/1/edit', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:26:17', '2022-11-15 19:26:17'),
(23, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:31:21', '2022-11-15 19:31:21'),
(24, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 19:31:21', '2022-11-15 19:31:21'),
(25, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:32:06', '2022-11-15 19:32:06'),
(26, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:33:34', '2022-11-15 19:33:34'),
(27, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:34:16', '2022-11-15 19:34:16'),
(28, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:39:09', '2022-11-15 19:39:09'),
(29, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:43:26', '2022-11-15 19:43:26'),
(30, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:43:49', '2022-11-15 19:43:49'),
(31, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:48:54', '2022-11-15 19:48:54'),
(32, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:50:28', '2022-11-15 19:50:28'),
(33, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:50:34', '2022-11-15 19:50:34'),
(34, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:50:36', '2022-11-15 19:50:36'),
(35, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:50:38', '2022-11-15 19:50:38'),
(36, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:56:51', '2022-11-15 19:56:51'),
(37, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:58:15', '2022-11-15 19:58:15'),
(38, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:59:26', '2022-11-15 19:59:26'),
(39, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 19:59:36', '2022-11-15 19:59:36'),
(40, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:00:26', '2022-11-15 20:00:26'),
(41, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:01:09', '2022-11-15 20:01:09'),
(42, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:03:01', '2022-11-15 20:03:01'),
(43, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:04:02', '2022-11-15 20:04:02'),
(44, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:04:25', '2022-11-15 20:04:25'),
(45, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:05:08', '2022-11-15 20:05:08'),
(46, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:05:52', '2022-11-15 20:05:52'),
(47, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:06:23', '2022-11-15 20:06:23'),
(48, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:07:09', '2022-11-15 20:07:09'),
(49, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:07:58', '2022-11-15 20:07:58'),
(50, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 20:08:26', '2022-11-15 20:08:26'),
(51, 1, 'admin/helpers/scaffold', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 20:10:59', '2022-11-15 20:10:59'),
(52, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:12:37', '2022-11-15 21:12:37'),
(53, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:13:27', '2022-11-15 21:13:27'),
(54, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 21:13:46', '2022-11-15 21:13:46'),
(55, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 21:17:09', '2022-11-15 21:17:09'),
(56, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 21:20:17', '2022-11-15 21:20:17'),
(57, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 21:21:31', '2022-11-15 21:21:31'),
(58, 1, 'admin/helpers/scaffold', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 21:21:50', '2022-11-15 21:21:50'),
(59, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:28:12', '2022-11-15 21:28:12'),
(60, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:28:45', '2022-11-15 21:28:45'),
(61, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:29:55', '2022-11-15 21:29:55'),
(62, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:30:07', '2022-11-15 21:30:07'),
(63, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:30:18', '2022-11-15 21:30:18'),
(64, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:31:07', '2022-11-15 21:31:07'),
(65, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:32:32', '2022-11-15 21:32:32'),
(66, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:33:05', '2022-11-15 21:33:05'),
(67, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:35:26', '2022-11-15 21:35:26'),
(68, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:35:54', '2022-11-15 21:35:54'),
(69, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:37:57', '2022-11-15 21:37:57'),
(70, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:38:51', '2022-11-15 21:38:51'),
(71, 1, 'admin/helpers/scaffold', 'GET', '::1', '[]', '2022-11-15 21:41:23', '2022-11-15 21:41:23'),
(72, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 21:41:31', '2022-11-15 21:41:31'),
(73, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 21:42:59', '2022-11-15 21:42:59'),
(74, 1, 'admin/auth/users', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 21:43:16', '2022-11-15 21:43:16'),
(75, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 22:01:30', '2022-11-15 22:01:30'),
(76, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:02:31', '2022-11-15 22:02:31'),
(77, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:05:30', '2022-11-15 22:05:30'),
(78, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:06:03', '2022-11-15 22:06:03'),
(79, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:06:46', '2022-11-15 22:06:46'),
(80, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:07:46', '2022-11-15 22:07:46'),
(81, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:11:40', '2022-11-15 22:11:40'),
(82, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:11:57', '2022-11-15 22:11:57'),
(83, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:12:12', '2022-11-15 22:12:12'),
(84, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:14:27', '2022-11-15 22:14:27'),
(85, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:17:55', '2022-11-15 22:17:55'),
(86, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:20:59', '2022-11-15 22:20:59'),
(87, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:22:36', '2022-11-15 22:22:36'),
(88, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:25:47', '2022-11-15 22:25:47'),
(89, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:31:41', '2022-11-15 22:31:41'),
(90, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:32:48', '2022-11-15 22:32:48'),
(91, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:33:35', '2022-11-15 22:33:35'),
(92, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:37:46', '2022-11-15 22:37:46'),
(93, 1, 'admin', 'GET', '::1', '[]', '2022-11-15 22:50:11', '2022-11-15 22:50:11'),
(94, 1, 'admin/auth/setting', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 22:51:37', '2022-11-15 22:51:37'),
(95, 1, 'admin/auth/setting', 'GET', '::1', '[]', '2022-11-15 22:52:43', '2022-11-15 22:52:43'),
(96, 1, 'admin/auth/setting', 'GET', '::1', '[]', '2022-11-15 22:53:23', '2022-11-15 22:53:23'),
(97, 1, 'admin/auth/setting', 'GET', '::1', '[]', '2022-11-15 22:59:35', '2022-11-15 22:59:35'),
(98, 1, 'admin', 'GET', '::1', '{\"_pjax\":\"#pjax-container\"}', '2022-11-15 23:02:27', '2022-11-15 23:02:27');

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
(1, 'All permission', '*', '', '*', NULL, NULL),
(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
(6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2022-11-15 19:07:39', '2022-11-15 19:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', '2022-11-15 19:05:14', '2022-11-15 19:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_menu`
--

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_menu`
--

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_users`
--

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_users`
--

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$6kzJpo6IGKelCzSCmy3tMefvS7K.hpTNGmMBZjFtEJZp/ivjHYxeW', 'Administrator', NULL, 'VPSj4ICZ1gNxkFRAsw0hv6P4XU3EXi0ZtGM03igerUf6eb57l9hXA0zqegPF', '2022-11-15 19:05:14', '2022-11-15 19:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_04_173148_create_admin_tables', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_operation_log_user_id_index` (`user_id`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_permissions_name_unique` (`name`),
  ADD UNIQUE KEY `admin_permissions_slug_unique` (`slug`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_roles_name_unique` (`name`),
  ADD UNIQUE KEY `admin_roles_slug_unique` (`slug`);

--
-- Indexes for table `admin_role_menu`
--
ALTER TABLE `admin_role_menu`
  ADD KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`);

--
-- Indexes for table `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`);

--
-- Indexes for table `admin_role_users`
--
ALTER TABLE `admin_role_users`
  ADD KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`);

--
-- Indexes for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
