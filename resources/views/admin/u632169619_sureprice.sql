-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 06, 2025 at 01:59 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u632169619_sureprice`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `attachable_type` varchar(255) NOT NULL,
  `attachable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Roofing Materials', 'roofing-materials', 'Sunt veritatis illum velit dolores. Veritatis in nostrum dignissimos quod. Nihil sit aut corporis molestiae non quibusdam aut voluptas.', '2025-04-05 15:18:39', '2024-10-19 04:04:44'),
(2, 'Flooring Materials', 'flooring-materials', 'Consequatur vero qui a totam earum. In suscipit nam eos nulla.', '2024-12-28 06:09:47', '2024-07-03 21:17:16'),
(3, 'Safety Equipment', 'safety-equipment', 'Eveniet perferendis rerum culpa. Sint ipsa adipisci quis doloribus odit placeat. Ad autem quae repellat dolore quos voluptatum.', '2025-01-07 22:05:58', '2024-08-07 17:26:23'),
(4, 'Windows and Doors', 'windows-and-doors', 'Id explicabo laudantium et molestias cupiditate eum qui. Consectetur quo non qui et. Accusamus consequatur provident eos qui vel sed sit.', '2024-07-02 11:19:56', '2025-06-02 08:26:05'),
(5, 'Tools and Hardware', 'tools-and-hardware', 'Assumenda iste qui et neque aspernatur. Iure velit blanditiis et velit.', '2025-03-11 19:34:40', '2024-07-14 22:25:25'),
(6, 'Electrical Supplies', 'electrical-supplies', 'Velit temporibus repellendus maiores velit qui. Tenetur aut exercitationem dolore ea ea vero.', '2024-11-04 07:29:45', '2025-01-25 23:01:49'),
(7, 'Plumbing Materials', 'plumbing-materials', 'Fugit quam omnis maiores et laudantium modi et. Dolorem et ullam aut officiis tempore cum eum voluptas. Nulla repellat voluptas suscipit omnis vel commodi adipisci.', '2025-03-22 01:41:31', '2024-10-18 09:21:00'),
(8, 'Construction Materials', 'construction-materials', 'Est veritatis consequatur illum sunt. Dolorem enim excepturi aperiam et pariatur asperiores.', '2024-08-04 00:36:00', '2025-04-27 17:27:55'),
(9, 'Insulation Materials', 'insulation-materials', 'Molestiae ea quos doloremque dolorum. Autem quisquam debitis minima porro perspiciatis.', '2025-04-12 20:01:37', '2025-01-04 04:12:24'),
(10, 'Structural Materials', 'structural-materials', 'Iure et mollitia recusandae ut voluptatibus accusantium dolorum. Facilis veniam quis autem et eos.', '2025-05-05 01:03:54', '2024-10-08 03:59:13'),
(11, 'Paint and Coatings', 'paint-and-coatings', 'Rerum qui pariatur sint qui dicta ipsam earum. Minima earum accusamus rerum praesentium quod.', '2025-04-08 00:11:14', '2025-03-24 19:52:38'),
(12, 'Finishing Materials', 'finishing-materials', 'Illum praesentium voluptatum ea aliquam. Nihil deleniti voluptas nihil cumque.', '2025-05-18 16:03:26', '2025-04-07 02:59:48'),
(13, 'Lighting Fixtures', 'lighting-fixtures', 'Odit mollitia aliquam velit excepturi possimus et distinctio. Accusantium doloremque exercitationem rerum ut vero hic iure. Aut omnis sed tempora.', '2025-04-06 21:42:34', '2025-05-26 00:25:25'),
(14, 'Landscaping Supplies', 'landscaping-supplies', 'Eligendi cupiditate iusto est quis perferendis. Consequatur fugit nihil quia repellendus.', '2024-10-25 18:01:20', '2024-08-26 03:26:37'),
(15, 'HVAC Equipment', 'hvac-equipment', 'Ea corporis sed magnam minus qui inventore. Numquam dolorum voluptates et veritatis fuga.', '2025-05-14 21:13:46', '2025-04-01 02:15:24');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `supplier_type` enum('Individual','Contractor','Material Supplier','Equipment Rental','Other') NOT NULL,
  `other_supplier_type` varchar(100) DEFAULT NULL,
  `business_reg_no` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `telephone_number` varchar(20) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `years_operation` int(11) DEFAULT NULL,
  `business_size` enum('Solo','Small Enterprise','Medium','Large') DEFAULT NULL,
  `service_areas` text DEFAULT NULL,
  `primary_products_services` varchar(500) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `vat_registered` tinyint(4) NOT NULL DEFAULT 0,
  `use_sureprice` tinyint(4) NOT NULL DEFAULT 0,
  `payment_terms` enum('7 days','15 days','30 days') DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `created_at`, `updated_at`, `user_id`, `username`, `email`, `company_name`, `supplier_type`, `other_supplier_type`, `business_reg_no`, `contact_person`, `designation`, `mobile_number`, `telephone_number`, `street`, `city`, `province`, `zip_code`, `years_operation`, `business_size`, `service_areas`, `primary_products_services`, `bank_name`, `bank_account_name`, `bank_account_number`, `vat_registered`, `use_sureprice`, `payment_terms`, `status`) VALUES
(1, '2025-02-01 06:40:08', '2025-05-30 21:18:11', 103, 'oschaden', 'herminia48@example.com', 'Company 8576', 'Individual', NULL, '860-686-055', 'Kailey Veum', 'Engine Assembler', '+639071235131', NULL, '52693 Yundt Square Apt. 641', 'Pasig City', 'Metro Manila', '1635', 30, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'nesciunt et magni', 'Security Bank', NULL, NULL, 0, 0, NULL, 'pending'),
(2, '2025-05-13 07:14:27', '2025-03-15 07:37:27', 104, 'sedrick43', 'suzanne.bernier@example.org', 'Company 2633', 'Individual', NULL, '878-660-495', 'Simone Reinger', 'Carpenter', '+639173121590', NULL, '6069 Arely Avenue', 'Pasay City', 'Metro Manila', '1253', 49, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'mollitia voluptatibus labore', 'BDO', 'Kutch PLC', '3910005166', 1, 1, NULL, 'rejected'),
(3, '2025-01-14 19:25:44', '2024-09-17 16:55:55', 105, 'blair.turner', 'olson.dorothea@example.com', 'Company 3864', 'Contractor', NULL, '462-609-452', 'Marilyne Leannon Jr.', 'Automotive Body Repairer', '+639794366625', '(02) 20042010', '2081 Kemmer Creek', 'Makati City', 'Metro Manila', '1688', 23, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'eius ea sed', NULL, NULL, NULL, 0, 0, NULL, 'pending'),
(4, '2024-11-23 12:01:44', '2024-08-29 10:56:37', 106, 'phills', 'samantha60@example.com', 'Company 9420', 'Contractor', NULL, '860-334-415', 'Mrs. Shanna Weber', 'Physical Therapist Aide', '+639102237992', NULL, '212 Stiedemann Crossroad Suite 602', 'Manila City', 'Metro Manila', '1544', 3, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'repellat praesentium sunt', 'Security Bank', 'Schmeler, Daniel and Bauch', NULL, 0, 0, NULL, 'rejected'),
(5, '2025-04-13 01:56:17', '2025-04-27 21:41:18', 107, 'jwalter', 'grimes.frida@example.net', 'Company 6030', 'Individual', NULL, '438-005-060', 'Karine Morar IV', 'Electrical Parts Reconditioner', '+639504815879', '(02) 00809403', '83744 Moen Ridges', 'Muntinlupa City', 'Metro Manila', '1431', 20, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'molestias delectus perspiciatis', 'BPI', NULL, '4441978673', 1, 1, NULL, 'approved'),
(6, '2025-02-09 18:25:12', '2024-08-23 22:36:37', 108, 'ryley23', 'preynolds@example.org', 'Company 8052', 'Contractor', NULL, '001-379-899', 'Maymie Yundt', 'Order Filler', '+639483070391', NULL, '612 Breitenberg Lakes', 'Mandaluyong City', 'Metro Manila', '1046', 34, 'Small Enterprise', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'aut quasi beatae', NULL, 'Stoltenberg Group', NULL, 1, 0, NULL, 'pending'),
(7, '2025-05-24 16:18:04', '2025-04-21 03:05:06', 109, 'jordane07', 'ervin.renner@example.net', 'Company 2214', 'Material Supplier', NULL, '588-356-523', 'Dr. Myrna Gerhold', 'Maintenance Supervisor', '+639350773006', NULL, '651 Ratke Walks Apt. 826', 'San Juan City', 'Metro Manila', '1055', 39, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'voluptatem adipisci necessitatibus', NULL, 'McGlynn-Gaylord', NULL, 0, 1, '7 days', 'rejected'),
(8, '2025-04-27 00:26:57', '2024-08-08 01:06:55', 110, 'beth34', 'amira.pfeffer@example.org', 'Company 7357', 'Other', 'et', '431-034-663', 'Simeon Ernser', 'Dredge Operator', '+639814379387', NULL, '83316 Laila Coves', 'Muntinlupa City', 'Metro Manila', '1511', 32, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'quas quasi aut', NULL, NULL, '5474529048', 0, 0, NULL, 'approved'),
(9, '2025-05-12 18:46:09', '2024-08-10 08:25:11', 111, 'klocko.arno', 'zblock@example.org', 'Company 2572', 'Individual', NULL, '734-659-446', 'Eliseo McCullough', 'Welder-Fitter', '+639475136248', NULL, '4170 Mertz Center', 'Makati City', 'Metro Manila', '1562', 33, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'occaecati libero vero', NULL, 'Larkin, Wolff and Heidenreich', '7324185959', 1, 1, NULL, 'rejected'),
(10, '2025-02-11 05:42:47', '2025-01-11 08:38:17', 112, 'crona.grover', 'athena56@example.org', 'Company 8379', 'Contractor', NULL, '716-530-637', 'Gussie Herzog DVM', 'Pharmaceutical Sales Representative', '+639147964546', NULL, '165 Jenkins Islands', 'Muntinlupa City', 'Metro Manila', '1709', 29, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'maxime qui amet', 'Metrobank', 'Baumbach, Gerhold and Hoeger', NULL, 0, 1, '15 days', 'approved'),
(11, '2024-07-05 01:10:55', '2025-02-08 06:49:55', 113, 'aaufderhar', 'tlittle@example.net', 'Company 2211', 'Contractor', NULL, '999-943-876', 'Nick Parisian Jr.', 'Scientific Photographer', '+639907384002', NULL, '6322 Hegmann Island Apt. 924', 'Quezon City', 'Metro Manila', '1389', 42, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'autem necessitatibus ut', NULL, 'Mann PLC', '3731654628', 1, 1, '7 days', 'approved'),
(12, '2025-05-10 21:37:25', '2024-10-21 02:58:39', 114, 'fritsch.desiree', 'simonis.kadin@example.com', 'Company 4326', 'Equipment Rental', NULL, '504-063-372', 'Hertha Kub', 'Psychologist', '+639121344496', '(02) 01994779', '794 Stroman Crossing', 'Mandaluyong City', 'Metro Manila', '1197', 47, 'Small Enterprise', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'id et eos', 'Metrobank', NULL, '5243438228', 0, 0, NULL, 'pending'),
(13, '2025-04-20 05:35:32', '2025-04-12 23:36:10', 115, 'ruben14', 'ahowell@example.net', 'Company 9694', 'Equipment Rental', NULL, '450-634-682', 'Dannie Hilpert', 'Educational Psychologist', '+639841138443', '(02) 66028854', '4810 Marks Trail Suite 148', 'Pasig City', 'Metro Manila', '1029', 30, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'voluptate ad alias', NULL, NULL, NULL, 1, 0, '15 days', 'pending'),
(14, '2024-11-08 04:25:11', '2024-07-31 19:40:09', 116, 'maritza.grady', 'vivian.bartoletti@example.com', 'Company 5525', 'Contractor', NULL, '797-341-568', 'Estelle Connelly MD', 'Annealing Machine Operator', '+639250667791', '(02) 63924999', '781 Troy Forge Suite 924', 'Makati City', 'Metro Manila', '1385', 21, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'corporis in est', NULL, 'Lockman Inc', NULL, 0, 1, NULL, 'pending'),
(15, '2024-10-02 17:24:21', '2025-01-01 12:08:28', 117, 'mohr.mitchel', 'olson.kylee@example.net', 'Company 2492', 'Material Supplier', NULL, '791-838-103', 'Heloise Durgan PhD', 'Separating Machine Operators', '+639618229181', NULL, '92208 Rutherford Stravenue Apt. 100', 'Parañaque City', 'Metro Manila', '1153', 21, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'fugit odit aut', 'BDO', 'Auer, Kris and Schmitt', NULL, 0, 1, '30 days', 'rejected'),
(16, '2024-06-15 11:39:20', '2024-09-08 14:04:33', 118, 'izabella88', 'fhaley@example.net', 'Company 7889', 'Other', 'ut', '074-150-030', 'Gayle Hamill', 'Materials Inspector', '+639795310288', NULL, '48666 Macejkovic Valleys', 'Pasig City', 'Metro Manila', '1660', 24, 'Small Enterprise', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'soluta aspernatur odio', 'Security Bank', NULL, NULL, 1, 1, NULL, 'rejected'),
(17, '2025-05-24 17:14:36', '2025-03-24 20:34:24', 119, 'nicolas.ally', 'uhudson@example.net', 'Company 7957', 'Contractor', NULL, '920-384-131', 'Cody Denesik DDS', 'Oral Surgeon', '+639399358991', '(02) 61208767', '337 Kulas Prairie Apt. 097', 'Pasay City', 'Metro Manila', '1145', 16, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'quia autem qui', NULL, NULL, NULL, 1, 0, NULL, 'rejected'),
(18, '2024-07-25 05:18:24', '2024-06-10 23:51:46', 120, 'johns.ericka', 'swilkinson@example.com', 'Company 8751', 'Individual', NULL, '169-864-617', 'Maximillia Dibbert I', 'Real Estate Appraiser', '+639627431448', '(02) 46803763', '26694 Arely Tunnel', 'Manila City', 'Metro Manila', '1244', 50, 'Small Enterprise', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'iusto neque est', 'Security Bank', 'Von PLC', NULL, 1, 0, NULL, 'pending'),
(19, '2024-12-11 20:43:57', '2024-11-22 03:42:29', 121, 'jfeeney', 'strosin.maudie@example.net', 'Company 8741', 'Equipment Rental', NULL, '112-099-479', 'Prof. Dolly Jenkins', 'Maintenance Worker', '+639095021963', NULL, '709 Trever Gardens', 'Mandaluyong City', 'Metro Manila', '1099', 47, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'beatae a minus', NULL, NULL, NULL, 1, 0, '15 days', 'approved'),
(20, '2024-12-31 01:52:08', '2025-01-27 06:58:36', 122, 'giles.kulas', 'marina45@example.net', 'Company 5250', 'Contractor', NULL, '499-472-087', 'Daren Feil', 'Automotive Mechanic', '+639647268011', '(02) 50926570', '9289 Sincere Gateway', 'Taguig City', 'Metro Manila', '1263', 37, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'dolores praesentium cumque', 'BPI', 'Collier LLC', NULL, 0, 1, '30 days', 'approved'),
(21, '2024-10-26 21:56:57', '2024-12-29 08:45:07', 123, 'alek95', 'emely.hegmann@example.org', 'Company 3190', 'Other', 'et', '508-161-993', 'Riley Ernser I', 'Government Service Executive', '+639860967861', '(02) 11760701', '6118 Jakubowski Lights Apt. 447', 'Parañaque City', 'Metro Manila', '1658', 37, 'Small Enterprise', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'cum porro velit', 'Security Bank', 'Cole-Renner', NULL, 0, 1, NULL, 'pending'),
(22, '2024-07-31 04:22:51', '2025-03-25 16:08:23', 124, 'angeline20', 'emile.windler@example.org', 'Company 2160', 'Individual', NULL, '988-482-232', 'Elizabeth Hyatt DDS', 'Insulation Installer', '+639978767918', NULL, '9011 Bergstrom Crescent', 'Pasay City', 'Metro Manila', '1775', 46, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'et dolor soluta', 'BPI', 'Tremblay, Lebsack and Stanton', '3099114232', 1, 1, '7 days', 'approved'),
(23, '2024-11-22 19:56:58', '2024-12-12 22:44:45', 125, 'tommie.kulas', 'zward@example.org', 'Company 3304', 'Individual', NULL, '633-630-126', 'Ms. Claudine Crona Jr.', 'Waste Treatment Plant Operator', '+639288305237', '(02) 84701049', '793 Harris Course Apt. 372', 'Mandaluyong City', 'Metro Manila', '1461', 36, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'eum quod perferendis', 'Security Bank', 'Bartoletti-Schmitt', '1627546191', 1, 1, NULL, 'approved'),
(24, '2024-11-24 22:25:41', '2024-06-14 14:59:07', 126, 'gzemlak', 'mason.bruen@example.org', 'Company 9407', 'Equipment Rental', NULL, '377-717-118', 'Mrs. Nona Reinger Jr.', 'Power Generating Plant Operator', '+639537405015', NULL, '731 Deonte Villages', 'Makati City', 'Metro Manila', '1067', 38, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'accusamus consequatur quia', 'Metrobank', 'Doyle Inc', NULL, 1, 1, '30 days', 'pending'),
(25, '2024-08-04 20:38:54', '2024-10-21 10:20:59', 127, 'herminio12', 'lora.franecki@example.net', 'Company 9746', 'Individual', NULL, '146-713-415', 'Gordon Buckridge', 'Arbitrator', '+639976334795', NULL, '4545 Vincenza Walk Suite 547', 'Parañaque City', 'Metro Manila', '1526', 3, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'esse rerum sapiente', NULL, NULL, NULL, 1, 0, NULL, 'approved'),
(26, '2025-05-16 03:03:30', '2024-10-30 09:42:45', 128, 'hrice', 'thora50@example.net', 'Company 7491', 'Contractor', NULL, '902-616-967', 'Otis Harvey V', 'Short Order Cook', '+639476588418', NULL, '47862 Gleichner Station Apt. 329', 'Parañaque City', 'Metro Manila', '1458', 8, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'perspiciatis voluptatum totam', NULL, 'Schroeder, Yost and Heller', '9664370456', 1, 1, '15 days', 'approved'),
(27, '2024-08-15 20:58:31', '2024-12-12 05:00:56', 129, 'qhane', 'fkilback@example.com', 'Company 7113', 'Individual', NULL, '855-939-925', 'Prof. Meaghan Gleason', 'Engineering Technician', '+639447639878', NULL, '113 Weissnat Manors', 'Parañaque City', 'Metro Manila', '1486', 12, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'odio inventore consequatur', NULL, 'Kertzmann Group', '1992928902', 1, 1, NULL, 'rejected'),
(28, '2024-12-14 23:34:48', '2024-11-29 13:40:18', 130, 'hcummings', 'srenner@example.net', 'Company 4437', 'Individual', NULL, '624-004-767', 'Ms. Melody Conroy V', 'Personal Home Care Aide', '+639622629050', '(02) 10988577', '829 Keira Ford', 'Pasig City', 'Metro Manila', '1368', 11, 'Large', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'fugit nemo et', 'Security Bank', 'Littel, Bradtke and Cremin', '3364889472', 0, 1, '7 days', 'rejected'),
(29, '2024-11-06 15:18:53', '2025-02-04 01:39:13', 131, 'gprohaska', 'schoen.earline@example.com', 'Company 4203', 'Other', 'aut', '050-825-364', 'Bryce Thompson V', 'Middle School Teacher', '+639827497871', '(02) 58900522', '4521 Ahmed Stream', 'Makati City', 'Metro Manila', '1067', 45, 'Medium', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'quia voluptatum veniam', 'Security Bank', NULL, '9545111116', 0, 0, '15 days', 'pending'),
(30, '2025-01-21 23:20:15', '2024-12-19 22:07:48', 132, 'schultz.noble', 'harvey.felicity@example.org', 'Company 2088', 'Equipment Rental', NULL, '453-798-629', 'Leonard Anderson', 'Shampooer', '+639890171832', NULL, '42865 Annamarie Harbor', 'Manila City', 'Metro Manila', '1331', 2, 'Solo', '[\"Metro Manila\",\"Luzon\",\"Visayas\",\"Mindanao\"]', 'optio aut ducimus', 'BPI', 'Stehr Group', NULL, 0, 1, '7 days', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `company_docs`
--

CREATE TABLE `company_docs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `type` enum('DTI_SEC_REGISTRATION','BUSINESS_PERMIT_MAYOR_PERMIT','VALID_ID_OWNER_REP','ACCREDITATIONS_CERTIFICATIONS','COMPANY_PROFILE_PORTFOLIO','SAMPLE_PRICE_LIST') NOT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` varchar(255) NOT NULL,
  `contractor_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `scope_of_work` varchar(255) NOT NULL,
  `scope_description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `budget_allocation` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payment_terms` text NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `check_number` varchar(255) DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `check_image` varchar(255) DEFAULT NULL,
  `jurisdiction` varchar(255) NOT NULL,
  `contract_terms` text NOT NULL,
  `client_signature` varchar(255) DEFAULT NULL,
  `contractor_signature` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `contract_id`, `contractor_id`, `client_id`, `property_id`, `scope_of_work`, `scope_description`, `start_date`, `end_date`, `total_amount`, `budget_allocation`, `payment_method`, `payment_terms`, `bank_name`, `bank_account_name`, `bank_account_number`, `check_number`, `check_date`, `check_image`, `jurisdiction`, `contract_terms`, `client_signature`, `contractor_signature`, `status`, `purchase_order_id`, `created_at`, `updated_at`) VALUES
(1, 'CT20250001', 13, 14, 7, 'renovation, repair, construction', 'zxxzxZXZXzXzX', '2025-06-07', '2025-06-21', 2340.00, 3000.00, 'cash', 'S', NULL, NULL, NULL, NULL, NULL, NULL, 'Manila, Philippines', 'Standard terms and conditions apply', 'signatures/client_6841528995320.png', 'signatures/contractor_684152899823f.png', 'approved', 2, '2025-06-05 08:17:13', '2025-06-05 08:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `contract_items`
--

CREATE TABLE `contract_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `material_name` varchar(255) NOT NULL,
  `material_unit` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_items`
--

INSERT INTO `contract_items` (`id`, `contract_id`, `material_id`, `material_name`, `material_unit`, `supplier_id`, `supplier_name`, `quantity`, `amount`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 27, 'Concrete Hollow Blocks 6\" #688', 'pcs', 1, NULL, 90.00, 26.00, 2340.00, '2025-06-05 08:17:13', '2025-06-05 08:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('procurement','warehousing') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 133, 'stan.lebsack', 'Alan', 'Gerlach', 'kelly88@example.org', 'procurement', '2025-03-27 23:32:53', '2024-07-31 17:40:24'),
(2, 134, 'koch.pamela', 'Christy', 'Stroman', 'rbins@example.net', 'procurement', '2025-05-13 16:32:42', '2024-06-07 13:09:25'),
(3, 135, 'rempel.marisol', 'Estrella', 'Schamberger', 'bo.larkin@example.com', 'procurement', '2024-11-30 02:21:25', '2024-07-13 14:14:40'),
(4, 136, 'bosco.blaise', 'Mandy', 'Lang', 'emard.sylvan@example.org', 'warehousing', '2025-04-21 15:21:44', '2024-06-06 01:54:42'),
(5, 137, 'flavie88', 'Domingo', 'Hagenes', 'fay.cydney@example.org', 'procurement', '2024-07-02 12:05:23', '2024-11-11 20:59:16'),
(6, 138, 'wpadberg', 'Juana', 'Jenkins', 'jodie47@example.com', 'warehousing', '2024-10-12 10:50:32', '2024-11-28 17:14:52'),
(7, 139, 'joanny.haag', 'Daisha', 'Pouros', 'tiffany.funk@example.org', 'procurement', '2024-11-01 07:10:18', '2024-12-06 14:14:26'),
(8, 140, 'uwalter', 'Liliane', 'Jacobs', 'skyla.klocko@example.com', 'warehousing', '2024-09-30 07:15:51', '2025-06-02 10:59:11'),
(9, 141, 'nschoen', 'Dustin', 'Medhurst', 'nichole.deckow@example.com', 'procurement', '2025-01-29 08:45:48', '2024-09-30 06:36:22'),
(10, 142, 'chester58', 'Raphael', 'Collins', 'lenore19@example.com', 'procurement', '2025-02-19 00:21:51', '2025-05-24 23:10:59'),
(11, 143, 'hubert.schaefer', 'Etha', 'Medhurst', 'kreichert@example.net', 'procurement', '2024-08-05 12:43:26', '2025-02-05 09:34:29'),
(12, 144, 'davis.celestino', 'Pablo', 'Streich', 'vito.champlin@example.com', 'warehousing', '2025-06-02 19:58:59', '2024-10-27 02:51:21'),
(13, 145, 'breana38', 'Alfonzo', 'Daniel', 'geovanny.miller@example.net', 'warehousing', '2025-01-11 11:08:57', '2024-09-21 21:57:52'),
(14, 146, 'boehm.mattie', 'Bernita', 'Hahn', 'dedrick.fahey@example.net', 'procurement', '2024-10-08 04:14:07', '2025-05-04 18:05:48'),
(15, 147, 'dana88', 'Urban', 'Ryan', 'emayer@example.org', 'procurement', '2024-06-13 04:45:49', '2024-11-29 09:38:35'),
(16, 148, 'lloyd96', 'Benton', 'Kirlin', 'jewel.wisozk@example.org', 'warehousing', '2025-02-17 21:05:37', '2025-05-07 08:47:44'),
(17, 149, 'juliana67', 'Reina', 'Fadel', 'uklocko@example.com', 'warehousing', '2024-07-11 09:13:04', '2025-01-16 09:46:25'),
(18, 150, 'lenora19', 'Earl', 'Mills', 'mhowe@example.org', 'procurement', '2024-11-13 22:12:43', '2025-01-25 17:00:29'),
(19, 151, 'earnest.daugherty', 'Bridgette', 'Konopelski', 'feest.gerda@example.org', 'procurement', '2024-12-25 03:43:02', '2025-02-18 23:08:39'),
(20, 152, 'ekreiger', 'Belle', 'Metz', 'nitzsche.jaylon@example.net', 'warehousing', '2025-03-28 14:03:57', '2024-12-06 03:25:55'),
(21, 153, 'okuneva.yesenia', 'Frances', 'Kessler', 'veronica.huel@example.org', 'warehousing', '2025-02-23 22:06:48', '2025-01-30 09:39:57'),
(22, 154, 'orion.rau', 'Kenyatta', 'Waters', 'gisselle.kub@example.net', 'warehousing', '2024-08-29 15:08:26', '2024-11-23 16:58:34'),
(23, 155, 'hand.lyric', 'Alysa', 'Lakin', 'marques04@example.org', 'warehousing', '2024-12-12 23:17:56', '2024-10-13 08:28:10'),
(24, 156, 'mparker', 'Amber', 'Altenwerth', 'mckayla53@example.org', 'warehousing', '2024-09-05 08:40:49', '2024-08-04 21:03:23'),
(25, 157, 'donato80', 'Estefania', 'Dare', 'kunze.fausto@example.org', 'warehousing', '2025-03-03 23:55:31', '2024-09-20 03:09:52'),
(26, 158, 'larson.alba', 'Alisa', 'Willms', 'xbalistreri@example.org', 'warehousing', '2024-08-03 20:34:59', '2024-12-27 22:14:20'),
(27, 159, 'mante.sigurd', 'Mateo', 'Larkin', 'brendan79@example.net', 'procurement', '2024-10-17 03:12:50', '2025-04-03 15:15:47'),
(28, 160, 'kihn.taryn', 'Hubert', 'Rath', 'elsa.reichel@example.net', 'procurement', '2025-02-18 17:48:40', '2024-10-17 03:27:58'),
(29, 161, 'langosh.nat', 'Adelle', 'Gulgowski', 'marlon.welch@example.org', 'warehousing', '2025-02-13 14:05:54', '2024-07-26 17:16:01'),
(30, 162, 'schmidt.tamara', 'Luz', 'Hyatt', 'adelia.weissnat@example.com', 'procurement', '2024-08-23 17:39:48', '2024-06-25 19:31:10'),
(31, 163, 'vhaag', 'Laisha', 'Rowe', 'selena16@example.org', 'warehousing', '2024-08-07 11:53:58', '2025-03-11 07:11:37'),
(32, 164, 'andreanne03', 'Betty', 'Wehner', 'wbailey@example.net', 'procurement', '2025-01-29 10:30:49', '2025-05-18 06:19:06'),
(33, 165, 'herzog.isaiah', 'Edwina', 'Windler', 'weber.carolyne@example.net', 'warehousing', '2025-01-19 04:21:47', '2024-07-27 17:49:28'),
(34, 166, 'mckenna.cassin', 'Enoch', 'Lind', 'quinn.ernser@example.net', 'procurement', '2024-11-16 03:41:20', '2025-04-19 22:08:23'),
(35, 167, 'christine16', 'Devonte', 'Watsica', 'abbott.reymundo@example.org', 'procurement', '2024-08-23 11:40:46', '2024-07-14 07:49:31'),
(36, 168, 'fadel.domenico', 'Marcelino', 'Leuschke', 'pgerlach@example.org', 'warehousing', '2025-03-03 11:55:56', '2024-11-29 15:11:20'),
(37, 169, 'chaim.miller', 'Jacey', 'Lind', 'hulda77@example.org', 'procurement', '2025-05-16 19:45:42', '2024-12-27 23:49:15'),
(38, 170, 'rjerde', 'Lilian', 'Tillman', 'ona70@example.com', 'procurement', '2025-05-17 17:01:24', '2025-03-14 17:25:52'),
(39, 171, 'macejkovic.elissa', 'Maybell', 'Bergnaum', 'abahringer@example.org', 'procurement', '2024-09-15 12:49:33', '2025-02-11 21:11:11'),
(40, 172, 'salvador.schumm', 'Katherine', 'Block', 'ulices59@example.net', 'procurement', '2025-05-28 03:44:30', '2025-03-02 20:33:40'),
(41, 173, 'owuckert', 'Jarret', 'McLaughlin', 'franecki.vida@example.org', 'procurement', '2024-08-05 23:33:17', '2024-11-16 21:31:01'),
(42, 174, 'acarter', 'Armani', 'Mraz', 'martin54@example.org', 'procurement', '2024-12-13 14:33:16', '2025-01-14 16:11:49'),
(43, 175, 'bailee54', 'Hannah', 'Bartoletti', 'kade37@example.org', 'warehousing', '2024-11-13 02:11:05', '2024-12-05 00:38:05'),
(44, 176, 'zula.labadie', 'Noemy', 'Oberbrunner', 'dereck31@example.org', 'procurement', '2024-09-06 01:27:15', '2024-06-05 15:01:07'),
(45, 177, 'ferry.laurence', 'Humberto', 'Pouros', 'watsica.davion@example.net', 'procurement', '2025-04-07 07:08:47', '2025-02-23 03:43:20'),
(46, 178, 'king.amalia', 'Tierra', 'Reichert', 'gaylord.tod@example.net', 'procurement', '2025-02-10 13:24:11', '2025-03-22 04:10:54'),
(47, 179, 'xschaden', 'Britney', 'O\'Reilly', 'brohan@example.com', 'procurement', '2025-06-01 17:03:43', '2025-04-12 10:34:22'),
(48, 180, 'abby.baumbach', 'Maribel', 'Bradtke', 'michelle34@example.net', 'procurement', '2024-07-05 19:42:22', '2024-07-19 18:50:53'),
(49, 181, 'pinkie.gerhold', 'Lola', 'Bernhard', 'rebeca.stehr@example.com', 'procurement', '2025-04-10 05:03:51', '2024-09-01 23:26:56'),
(50, 182, 'arlo65', 'Hailee', 'Harvey', 'shaina80@example.org', 'procurement', '2025-01-17 14:17:36', '2025-03-05 01:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL,
  `required_date` date NOT NULL,
  `department` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_material`
--

CREATE TABLE `inquiry_material` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `specifications` text DEFAULT NULL,
  `minimum_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `code`, `description`, `unit`, `category_id`, `base_price`, `specifications`, `minimum_stock`, `current_stock`, `created_at`, `updated_at`) VALUES
(1, 'Deformed Steel Bars 16mm #703', 'MAT940915', 'Nisi est officia maxime sit ratione aut eos. Commodi rem necessitatibus totam architecto omnis sunt eius. Voluptatem ut ab veritatis enim aliquid cupiditate facere. Nemo repellendus sit sapiente eum quos. Voluptatum at sed saepe dignissimos.', 'pcs', 8, 579.00, 'Quisquam asperiores quia quidem aliquid culpa quam. Perspiciatis aut nemo laborum adipisci ab. Quam omnis quia vero quisquam vel.', 51.00, 74.00, '2024-09-06 11:14:49', '2024-11-09 06:39:37'),
(2, 'GI Sheet Roofing #588', 'MAT309045', 'Ipsa quia voluptas inventore non iure et ipsam. Sed ex maiores autem debitis qui. Eos voluptate reprehenderit maxime aut dicta ipsa.', 'pcs', 1, 583.00, NULL, 69.00, 96.00, '2024-12-20 14:54:01', '2025-02-12 22:48:28'),
(3, 'Paint Latex #78', 'MAT613459', 'Nulla aut quia rerum placeat. Fugit molestias perferendis qui minus. Minima voluptas id voluptates distinctio doloremque aut iusto.', 'gallons', 9, 1080.00, NULL, 41.00, 15.00, '2024-08-20 18:26:02', '2025-02-08 08:44:23'),
(4, 'GI Sheet Roofing #238', 'MAT565795', 'Vero natus totam consequuntur sint quidem veritatis vel vel. Quia sit eius sint culpa sequi deserunt. Commodi numquam alias molestias fuga atque facilis ipsam ipsa. Ex deleniti nemo nihil quasi repellat tempore beatae.', 'pcs', 1, 512.00, NULL, 16.00, 33.00, '2025-02-15 01:57:41', '2024-12-13 14:31:00'),
(5, 'Paint Latex #981', 'MAT744594', 'Architecto qui libero voluptatum qui perspiciatis quaerat et aspernatur. Repellat cupiditate assumenda nemo maiores quia. Accusantium exercitationem eum suscipit rerum est repellat sed.', 'gallons', 2, 954.00, 'Mollitia autem qui quia unde. Quaerat eos veritatis laboriosam voluptatibus. Quam ex sit suscipit.', 64.00, 195.00, '2024-07-30 03:49:14', '2024-08-06 16:16:36'),
(6, 'GI Sheet Roofing #773', 'MAT784147', 'Beatae voluptas facere et non dolorem ex. Est at ad qui est culpa. Illo minus quos consequatur error tenetur et praesentium. Numquam dolores est dicta blanditiis totam corporis quibusdam. Repellendus et cumque tempora numquam.', 'pcs', 13, 524.00, 'Maxime dolores autem atque ad quas iste. Ab minima labore odit eum. Adipisci ab molestias magni a debitis. Fuga nemo soluta quam minus.', 24.00, 32.00, '2025-03-16 05:48:33', '2024-07-08 06:55:37'),
(7, 'Ceramic Floor Tiles 60x60 #965', 'MAT689485', 'Fuga hic repellendus quibusdam sunt reiciendis qui quis. Exercitationem ea molestias distinctio corporis adipisci libero. Vitae vel dolores veniam sit ipsum dolorum nisi.', 'boxes', 5, 1567.00, 'Suscipit esse sit pariatur et dolore et dolores. Omnis cupiditate numquam molestiae tenetur aliquid. Maxime voluptas aperiam quisquam ratione eos alias incidunt.', 79.00, 14.00, '2025-03-31 21:54:22', '2024-09-13 22:07:24'),
(8, 'GI Sheet Roofing #927', 'MAT670877', 'Non quia quo inventore earum. Unde cupiditate molestiae officiis et doloremque omnis. Dolores dolores et nulla qui perferendis.', 'pcs', 4, 457.00, NULL, 40.00, 156.00, '2024-12-13 21:58:12', '2024-12-13 03:47:16'),
(9, 'Concrete Hollow Blocks 6\" #787', 'MAT917792', 'Sed est alias dolor unde repudiandae vitae dolores. Iure id fugiat et reiciendis laudantium harum pariatur pariatur. Qui dolore illum distinctio enim cumque quasi.', 'pcs', 7, 26.00, 'Ut nam ab in autem eum veritatis. Suscipit vel debitis fugit corporis repellat enim provident. Excepturi qui nobis ratione laudantium sapiente id inventore. Perspiciatis accusamus voluptas soluta eum eos aut quidem at.', 72.00, 78.00, '2024-07-30 18:05:49', '2024-08-06 21:58:34'),
(10, 'Gravel 3/4\" #564', 'MAT838042', 'Nemo nulla eum est distinctio. Et error quam et quia. Maiores voluptates aut illum non deleniti.', 'cu.m', 9, 1591.00, NULL, 49.00, 58.00, '2024-11-11 20:01:31', '2024-07-03 11:27:54'),
(11, 'Gravel 3/4\" #740', 'MAT159531', 'Iusto vel quia consequuntur necessitatibus alias voluptatem ab. Ut incidunt enim aut voluptatibus repellendus quibusdam aperiam. Ut sit provident ut et id enim rerum.', 'cu.m', 10, 1757.00, 'Adipisci dolor aliquid corrupti voluptates facere ullam cumque. Adipisci repellat sapiente maxime fugit qui sed. Vel illum voluptatem mollitia praesentium amet vitae. Maiores amet veritatis officia expedita quo sed dolores.', 39.00, 18.00, '2024-08-09 07:14:37', '2025-04-29 05:45:18'),
(12, 'Door Hinges Stainless #226', 'MAT255720', 'Qui facere aperiam quis itaque. Omnis et reprehenderit ut ut. Iusto quam nesciunt culpa natus impedit. Et eos soluta repellat tempore ut quia. Animi praesentium quis accusamus exercitationem inventore necessitatibus alias.', 'sets', 9, 214.00, NULL, 13.00, 35.00, '2024-09-11 06:09:01', '2025-04-21 12:20:03'),
(13, 'Portland Cement #404', 'MAT391809', 'Sit amet culpa dolor velit assumenda numquam vero numquam. Quasi error quo unde quasi ullam repellat et. Aliquid inventore aut molestiae voluptas voluptas.', 'bag', 11, 335.00, NULL, 23.00, 1.00, '2024-10-20 02:28:22', '2024-08-22 09:15:11'),
(14, 'Paint Latex #296', 'MAT180507', 'Consectetur et velit eum rerum. Doloremque asperiores consequuntur at accusantium expedita eligendi. Omnis rerum sint reprehenderit corporis consectetur.', 'gallons', 3, 1064.00, 'Inventore sunt nihil non non. Qui aperiam dolorem quae quis non sapiente voluptatibus. Omnis voluptas reiciendis sunt voluptas corrupti.', 11.00, 89.00, '2025-05-09 09:55:28', '2025-03-04 17:26:40'),
(15, 'Marine Plywood 1/2\" #797', 'MAT123152', 'Et neque sed porro dolor. Sit iusto commodi doloremque doloremque ipsa eaque dolor. Et omnis fugiat rerum nisi ratione nobis ut.', 'pcs', 6, 1001.00, 'Earum autem ut et deleniti et accusantium. Porro quae ipsum vero et dolorem. Officia accusamus facilis minima qui sed temporibus culpa.', 43.00, 171.00, '2024-06-13 11:00:46', '2025-02-27 23:08:19'),
(16, 'Concrete Hollow Blocks 6\" #906', 'MAT962395', 'Sunt natus autem veritatis suscipit perferendis delectus iste molestiae. Modi consequuntur dolorem vero et. Vel facere consequatur error laudantium vero temporibus ut.', 'pcs', 2, 25.00, 'Odit quia et natus suscipit laborum. Ut maxime qui libero esse quis voluptatibus qui. Exercitationem quia et deleniti. Quidem quo ea iste necessitatibus qui dolorum aperiam porro. Quidem ut dolores non.', 60.00, 189.00, '2024-06-15 01:03:43', '2025-04-24 12:16:13'),
(17, 'Portland Cement #697', 'MAT532775', 'Sed veniam minus nulla nostrum nihil laborum. Earum et eum ut provident enim molestiae id ut. Repellat voluptatem culpa velit eveniet.', 'bag', 13, 347.00, NULL, 23.00, 122.00, '2025-01-28 16:22:10', '2025-04-28 02:33:49'),
(18, 'Concrete Hollow Blocks 6\" #512', 'MAT359454', 'Expedita eaque nobis quia deserunt. Voluptatem occaecati ipsum consequatur et itaque blanditiis aut. Cum iste quis labore rerum fugit id.', 'pcs', 8, 26.00, NULL, 24.00, 151.00, '2024-08-16 17:37:01', '2024-10-29 04:31:33'),
(19, 'Window Glass 6mm #287', 'MAT168427', 'Sed ut dolor qui. Sit quo voluptatibus voluptas iure occaecati corporis. Veniam est placeat sint ex omnis quo. Distinctio nihil dolore provident qui.', 'sq.m', 6, 454.00, 'Autem qui est delectus nisi. Cupiditate eius rerum at asperiores. Itaque incidunt mollitia tempora aut officiis. Vel voluptas quod dolorem ipsam eos earum exercitationem pariatur.', 82.00, 84.00, '2024-11-16 07:10:36', '2025-03-06 18:16:50'),
(20, 'Marine Plywood 1/2\" #862', 'MAT471581', 'Accusamus perspiciatis accusantium voluptatem ut architecto. Repellat vero sed iste atque rerum consequatur. Qui rerum quia eaque corrupti perferendis. Voluptatem qui id eaque rem aut est.', 'pcs', 1, 1074.00, 'Reiciendis dolores sed fugit culpa. Distinctio repudiandae qui illum in quis nobis quam. Ut qui asperiores ea dolor. Deserunt tempore quo eius optio.', 58.00, 103.00, '2025-04-22 11:07:13', '2024-12-03 15:01:12'),
(21, 'Ceramic Floor Tiles 60x60 #376', 'MAT670228', 'Dolores eius suscipit aliquid dolores qui dolores voluptatem. Cumque aut accusamus a laboriosam nihil dolor. Fuga et eos quia error voluptatibus illo. Laborum rerum officia sunt tempora et consequatur ullam. Ipsam alias sint sed odit.', 'boxes', 11, 1567.00, 'Tempora voluptatem ab soluta sunt eos ullam ipsa. Blanditiis quisquam ut minus laborum. Doloribus assumenda quaerat et esse. Et commodi delectus eos dolore aut sit.', 30.00, 144.00, '2025-03-17 12:09:15', '2024-12-24 19:36:55'),
(22, 'Window Glass 6mm #535', 'MAT517972', 'Sed iure non maxime qui animi et. Voluptatem dolorem eum dolorem nobis vitae. Nesciunt voluptatem nihil quis omnis similique ad.', 'sq.m', 9, 472.00, 'Et mollitia est perferendis ut ad. Doloremque velit unde autem ullam. Ullam quia doloribus ex est dolore adipisci aut qui.', 37.00, 161.00, '2024-12-15 13:17:16', '2024-08-14 18:03:40'),
(23, 'Portland Cement #912', 'MAT129253', 'Amet illum id eum sequi provident. Est possimus eius blanditiis voluptas. Autem sequi repellat eligendi a blanditiis quibusdam.', 'bag', 10, 279.00, NULL, 67.00, 94.00, '2024-10-21 15:47:44', '2024-08-27 21:09:20'),
(24, 'Concrete Hollow Blocks 6\" #776', 'MAT498176', 'Numquam non qui ex laboriosam sint nesciunt qui aut. Adipisci assumenda perferendis molestiae aut. Quos sit quos quia et corrupti.', 'pcs', 5, 34.00, 'Voluptas dolor voluptatem at pariatur molestiae est quia. Suscipit non reprehenderit tempora eum consectetur. Ut veritatis delectus labore a corrupti similique. Molestiae velit voluptatem et earum non consectetur perspiciatis. Facilis explicabo laborum et sint.', 11.00, 55.00, '2025-02-04 04:30:06', '2024-07-11 06:30:35'),
(25, 'Door Hinges Stainless #523', 'MAT930143', 'Itaque hic nihil omnis molestias velit tempore dolorem. Similique atque ullam et. Rem consequatur dolore pariatur tempore omnis. Ut molestias laudantium sint iusto et.', 'sets', 9, 209.00, 'Totam sit deserunt repellendus consequatur. Aspernatur non molestiae atque et aliquam assumenda illum necessitatibus. Libero recusandae id nam quis deserunt.', 62.00, 105.00, '2024-08-28 22:30:36', '2024-06-10 22:48:21'),
(26, 'PVC Pipes 4\" #344', 'MAT457178', 'Reprehenderit sed qui unde. Quasi non illum libero officia debitis inventore maxime. Asperiores non sint mollitia. Vero et officia quia debitis officia.', 'pcs', 1, 373.00, 'Est voluptas odio numquam exercitationem alias. Ducimus consequatur ad minus. Rerum ab sapiente sunt qui cumque. Non sapiente mollitia recusandae dolor error facilis.', 18.00, 154.00, '2025-02-05 23:38:57', '2025-01-02 04:15:39'),
(27, 'Concrete Hollow Blocks 6\" #688', 'MAT313290', 'Eaque reiciendis est et aut sunt. Recusandae officia voluptate tempora ut mollitia molestiae et. Nisi qui vel quasi ut quo.', 'pcs', 11, 26.00, NULL, 36.00, 166.00, '2024-10-31 06:44:36', '2025-05-26 11:18:38'),
(28, 'Deformed Steel Bars 16mm #358', 'MAT709083', 'Molestias harum ut illo nemo repellendus. Unde totam amet repudiandae iure. Ut et dolor possimus expedita velit possimus sapiente. Facilis voluptas qui deleniti illum.', 'pcs', 7, 540.00, 'Eligendi minus fugiat consequatur optio sint. Exercitationem velit saepe qui qui sunt ipsum animi.', 41.00, 141.00, '2024-09-27 09:31:54', '2024-11-05 09:57:15'),
(29, 'Ceramic Floor Tiles 60x60 #755', 'MAT666818', 'Et nulla voluptatibus nobis repellendus voluptatum ullam. Aliquam sed corrupti autem sed at temporibus deleniti. Nemo sint ipsam dolorem iure. Et autem vel in ut et sed perspiciatis.', 'boxes', 15, 1658.00, NULL, 54.00, 162.00, '2024-06-11 02:23:05', '2024-12-19 10:14:04'),
(30, 'Concrete Hollow Blocks 6\" #44', 'MAT523029', 'Vero eaque voluptatum omnis excepturi voluptatum et architecto. Est dolorem aut ratione aut dignissimos. Exercitationem rerum asperiores cupiditate praesentium molestiae nobis nemo. Ab dolorum ullam sit est suscipit mollitia repellendus.', 'pcs', 8, 33.00, 'Fugiat molestiae alias consectetur voluptas voluptas ex sapiente. A non possimus exercitationem. Iste vero qui consequatur dicta autem iure.', 61.00, 175.00, '2025-01-23 03:02:27', '2025-05-31 22:00:54'),
(31, 'Electrical Wires 3.5mm² #918', 'MAT149015', 'Ab omnis ratione quia laborum. Nulla incidunt qui voluptatem occaecati. Perferendis quidem laborum optio nihil laudantium sequi. Beatae repudiandae nihil at voluptatibus illo labore nemo.', 'rolls', 13, 2743.00, 'Est earum a tempora dolore ipsam ut. Molestiae delectus et et exercitationem omnis beatae vel veritatis. Alias alias non eos quo deleniti corrupti. Rerum dolorum fugit harum voluptatem rerum nesciunt dolores.', 46.00, 41.00, '2024-11-04 08:44:15', '2024-10-22 16:28:45'),
(32, 'Kitchen Sink Stainless #518', 'MAT601568', 'Aut consectetur vitae qui facilis ullam vero qui. Aut ea quis ex optio nostrum qui. Aliquid velit dolores sapiente voluptatem rerum.', 'pcs', 13, 3437.00, 'Est id id porro reiciendis. Nulla assumenda voluptatem est eum nesciunt autem alias. Voluptatum possimus ea dolor itaque nihil. Perferendis sint voluptas consequuntur et.', 30.00, 32.00, '2025-05-21 18:06:42', '2024-09-01 14:27:32'),
(33, 'Kitchen Sink Stainless #504', 'MAT876371', 'Esse numquam distinctio id et non. Quasi totam dignissimos sequi sed laboriosam. Autem et qui provident eligendi.', 'pcs', 5, 3647.00, NULL, 31.00, 55.00, '2025-03-18 02:29:47', '2024-08-10 20:36:21'),
(34, 'Paint Latex #601', 'MAT296675', 'Aut dolor est libero provident. Omnis suscipit facere praesentium culpa. Nisi repellendus ea officia nostrum debitis recusandae.', 'gallons', 12, 1030.00, 'Qui consequatur aut tempora et libero possimus modi. Soluta officiis ea voluptatum similique sed temporibus. At maxime molestiae vero voluptatem quia rerum repellendus.', 87.00, 48.00, '2025-05-13 13:46:10', '2024-09-12 23:41:06'),
(35, 'Sand #126', 'MAT468017', 'Et beatae magnam nihil et pariatur quis qui aut. Incidunt quia quisquam cupiditate eius consectetur nihil dolor possimus. Possimus earum labore error facere porro.', 'cu.m', 12, 1301.00, 'Minima iusto nobis sed illo nisi dolorum et. Nam ratione quidem autem porro. Dolorem sint pariatur porro vel rem alias laborum.', 20.00, 185.00, '2024-12-03 07:22:58', '2024-06-26 21:25:10'),
(36, 'Gravel 3/4\" #10', 'MAT964080', 'Impedit tempora ex cupiditate laborum aut totam totam. Sed aut et nam perspiciatis.', 'cu.m', 9, 1907.00, 'Dolorem dicta doloribus sequi ipsa facere architecto. Nobis veniam corrupti neque voluptatem. Sit commodi ab quaerat deserunt omnis molestiae neque consequuntur. Rerum magni eum adipisci harum perspiciatis amet autem.', 22.00, 66.00, '2024-11-10 06:35:16', '2025-02-07 08:39:19'),
(37, 'Electrical Wires 3.5mm² #993', 'MAT722566', 'Eligendi maiores quia cupiditate qui placeat sint rerum. Velit voluptas pariatur aut quas quaerat sapiente dolore eos. In omnis reiciendis repudiandae natus modi vel. Dolorum rem quis fugiat omnis nesciunt.', 'rolls', 2, 3067.00, 'Animi quaerat culpa qui doloremque quasi aliquam. Velit nemo minus in itaque. Cum vitae iure et eaque rerum reiciendis ullam porro.', 83.00, 176.00, '2024-07-28 05:48:31', '2024-12-01 22:16:57'),
(38, 'Deformed Steel Bars 16mm #974', 'MAT719890', 'Nostrum hic maxime placeat et quo. Eaque eos soluta velit natus. Porro expedita ut voluptatibus quam neque.', 'pcs', 14, 516.00, NULL, 87.00, 111.00, '2025-01-21 03:36:05', '2024-06-29 03:59:21'),
(39, 'Window Glass 6mm #424', 'MAT714398', 'Praesentium deleniti quibusdam labore quisquam eum qui. Magni deserunt ut ex aut consequatur ipsam.', 'sq.m', 8, 495.00, NULL, 67.00, 53.00, '2024-12-11 08:22:52', '2025-02-08 09:15:44'),
(40, 'Electrical Wires 3.5mm² #488', 'MAT884863', 'Alias temporibus aut doloremque suscipit dolorem voluptatem. Eum voluptatem possimus non omnis quasi et delectus. Voluptatem optio sed autem voluptatibus deleniti. Eius sequi vel consequatur quo voluptas.', 'rolls', 2, 2686.00, NULL, 41.00, 196.00, '2025-01-15 15:44:32', '2024-06-30 20:50:22'),
(41, 'PVC Pipes 4\" #945', 'MAT113001', 'Autem voluptatum nulla voluptatem repellat. Consectetur velit enim rem aut eaque. Atque cum sint ratione nulla nemo saepe sed. Odio non amet ratione placeat voluptates.', 'pcs', 2, 378.00, NULL, 39.00, 4.00, '2024-11-03 21:28:06', '2024-08-01 02:31:47'),
(42, 'Window Glass 6mm #676', 'MAT884585', 'Vitae eos vitae autem est molestiae. Sit libero architecto quia aperiam mollitia. Suscipit odio ipsam nulla tenetur. Quos esse hic dolore.', 'sq.m', 12, 545.00, NULL, 50.00, 4.00, '2024-07-05 12:59:08', '2024-11-20 12:18:03'),
(43, 'Portland Cement #477', 'MAT653234', 'Inventore deserunt sunt ab repellat. Nobis omnis sed iure aut recusandae beatae. Dolore quidem quae voluptates. Laborum dignissimos ipsa debitis nesciunt id nam.', 'bag', 3, 350.00, 'Consequatur aliquam corporis ab qui quia quos molestiae. Dolor quod modi quo eos qui rem recusandae. Sed architecto sequi doloribus cupiditate voluptatibus laborum. Ipsa impedit enim quidem dolores non eaque.', 74.00, 75.00, '2025-04-01 05:45:33', '2025-01-09 02:43:50'),
(44, 'Portland Cement #692', 'MAT583745', 'Commodi repellat adipisci aliquid. Illum molestias sint exercitationem possimus mollitia est commodi aperiam. Expedita ut architecto qui ut velit eum.', 'bag', 10, 327.00, NULL, 28.00, 197.00, '2025-02-12 12:49:05', '2025-02-06 07:08:25'),
(45, 'Window Glass 6mm #331', 'MAT679920', 'Temporibus voluptatem laboriosam praesentium ea eos. Qui nihil esse est ipsum atque et in. Voluptates et explicabo nam rerum saepe id non. Quod quaerat sint tempora veniam.', 'sq.m', 15, 527.00, NULL, 27.00, 8.00, '2024-07-06 10:29:16', '2024-06-09 01:22:49'),
(46, 'GI Sheet Roofing #673', 'MAT962125', 'Impedit atque ullam nihil ea expedita ducimus. Cupiditate veniam aut ipsum non at. Perferendis harum exercitationem ad qui aliquam.', 'pcs', 1, 511.00, 'Eius impedit possimus quibusdam rerum. Aliquam dolores saepe non sunt recusandae qui et consequatur. Laborum quia beatae pariatur veniam.', 96.00, 103.00, '2024-06-19 21:42:43', '2024-08-29 06:54:13'),
(47, 'Portland Cement #842', 'MAT133171', 'In saepe quas in expedita. Optio rerum adipisci commodi similique. Laudantium tenetur saepe atque libero.', 'bag', 12, 251.00, 'Minus maxime adipisci et minima facere quaerat nobis. Rerum alias numquam aut expedita ea. Sed odit sit cum et voluptas. Accusamus doloremque aut incidunt ea ab nostrum.', 100.00, 60.00, '2025-02-09 05:56:17', '2025-04-27 18:05:47'),
(48, 'Electrical Wires 3.5mm² #631', 'MAT499041', 'Placeat debitis quia qui eaque alias. Voluptas rerum repellat cumque ut sequi. Itaque sit sunt veritatis velit.', 'rolls', 12, 3019.00, 'Facilis voluptas perferendis nulla nam nobis perspiciatis molestiae. Facilis aut aut et omnis explicabo amet. Incidunt ea quam aut consequuntur ab. Iure necessitatibus est veniam eum nisi minima nisi.', 95.00, 51.00, '2025-02-02 12:15:41', '2024-09-17 07:11:09'),
(49, 'Deformed Steel Bars 16mm #539', 'MAT226993', 'Enim perspiciatis adipisci molestiae enim nemo excepturi eligendi adipisci. Optio distinctio voluptas aut omnis ut quam illo. Dolores voluptas molestiae voluptatum repudiandae illo repellat commodi numquam. Ullam deleniti voluptatem et labore alias vel cumque.', 'pcs', 14, 776.00, NULL, 66.00, 175.00, '2024-07-19 07:40:37', '2024-11-22 10:33:25'),
(50, 'Bathroom Fixtures Set #550', 'MAT458284', 'Odit modi laudantium rem quia dolores voluptatem. Dolorem deleniti ratione error velit velit maxime exercitationem. Quae aut porro ducimus cupiditate iure excepturi voluptas sed.', 'sets', 12, 4858.00, 'Qui omnis dolores ullam repudiandae. Ullam nihil molestias consequuntur distinctio. Nesciunt illum illum doloribus recusandae.', 17.00, 186.00, '2024-12-18 19:39:35', '2024-11-12 10:25:20'),
(51, 'Paint Latex #531', 'MAT587805', 'Et in aspernatur suscipit est ea. Sit repellat et illum in quibusdam. Qui sunt voluptatem est illum.', 'gallons', 7, 1025.00, 'Nihil reprehenderit et et ut cumque. Est ut deserunt sed sint qui. Voluptas ea ut quis iusto in excepturi. Porro delectus quas facilis quis.', 86.00, 37.00, '2024-09-20 08:27:40', '2024-07-03 09:59:27'),
(52, 'Portland Cement #43', 'MAT876856', 'Natus occaecati impedit similique magni inventore culpa odio. Numquam eius dolor illo et suscipit ratione. Et doloribus ut ut est dolorum.', 'bag', 12, 283.00, 'Quae doloremque nihil consectetur qui tempore magnam. Ut possimus omnis tempora dolores. Aspernatur maiores nobis aperiam ipsa saepe veritatis. Pariatur necessitatibus in fugiat.', 95.00, 165.00, '2024-10-10 03:59:52', '2025-02-21 10:56:18'),
(53, 'Marine Plywood 1/2\" #970', 'MAT633445', 'Iusto quos recusandae magni sed eos. Eligendi tempore tempora deserunt est nesciunt et id. Animi dignissimos sequi ut aperiam ipsa saepe. Atque vero placeat mollitia ut.', 'pcs', 4, 1050.00, 'Quia recusandae similique aut provident minus quaerat ea. Accusamus velit quod cupiditate ex. Dignissimos et et reprehenderit ullam. Et consectetur dolores soluta est ipsa.', 47.00, 7.00, '2024-09-04 13:21:21', '2025-01-26 15:26:26'),
(54, 'Window Glass 6mm #621', 'MAT714701', 'Assumenda maxime perspiciatis tempora veritatis voluptatem aut deserunt. Et quidem in distinctio et et mollitia. Corporis aut provident cumque soluta ut et.', 'sq.m', 2, 555.00, 'Possimus recusandae qui magnam. Qui vitae fuga qui optio repellendus tempore enim. Quas suscipit corrupti praesentium veritatis repellat sit.', 44.00, 155.00, '2024-11-25 12:32:36', '2025-04-17 15:47:06'),
(55, 'Marine Plywood 1/2\" #156', 'MAT705109', 'Ut provident rerum perferendis ratione commodi. Omnis nam explicabo aliquam aliquid perspiciatis officia saepe. Voluptatibus iusto fuga ipsum possimus recusandae sequi error. Modi quia repellendus sed hic nemo.', 'pcs', 7, 979.00, NULL, 41.00, 10.00, '2025-05-07 11:28:17', '2025-05-13 09:23:30'),
(56, 'Gravel 3/4\" #610', 'MAT565419', 'Provident pariatur est optio qui ea maiores. Vel non aut dolorum at minima ut. At ducimus doloremque quam nemo.', 'cu.m', 6, 1762.00, NULL, 99.00, 124.00, '2024-08-19 22:44:04', '2024-11-18 07:13:11'),
(57, 'Sand #608', 'MAT670486', 'Incidunt error nulla placeat esse atque debitis. Iure eius harum omnis similique blanditiis dolorem. Eius et et facilis in.', 'cu.m', 4, 1397.00, NULL, 15.00, 8.00, '2024-11-13 15:49:42', '2025-02-17 09:30:44'),
(58, 'Bathroom Fixtures Set #700', 'MAT608629', 'Facilis ut non amet dolore et quia. Alias delectus voluptatum qui aliquid totam. Rerum eos excepturi odit ratione amet ut harum sit.', 'sets', 6, 4982.00, 'Omnis qui harum soluta magnam quo. Perferendis quo est et debitis saepe et itaque. Molestias reprehenderit consectetur accusantium. Quae eum animi quo architecto a nulla.', 34.00, 173.00, '2024-10-16 20:11:19', '2024-12-09 01:01:41'),
(59, 'Portland Cement #655', 'MAT149437', 'Ex consequatur dolor qui optio consectetur id nihil porro. Qui eos consequuntur est aut numquam quis repellat. Sed et neque quae possimus molestiae veniam. Amet amet consequatur est incidunt quaerat sunt deserunt.', 'bag', 6, 267.00, NULL, 44.00, 132.00, '2025-01-16 17:55:08', '2024-08-04 08:29:24'),
(60, 'Marine Plywood 1/2\" #734', 'MAT138793', 'Qui iste recusandae recusandae eum quis perspiciatis qui quam. Est illum at velit dolorem quo doloribus dolores. Vel ut vel assumenda non illo. Dicta ea molestias odio ullam.', 'pcs', 11, 1175.00, NULL, 66.00, 16.00, '2025-05-22 04:12:46', '2024-11-11 05:23:08'),
(61, 'Portland Cement #968', 'MAT895663', 'Rerum expedita reiciendis hic aliquid cumque. Illo quidem voluptate saepe dolorum. Sint nostrum sed dolorum ducimus aut aut. Praesentium ipsam est praesentium ut provident.', 'bag', 6, 303.00, NULL, 28.00, 113.00, '2024-11-07 12:29:53', '2024-10-27 14:56:18'),
(62, 'Gravel 3/4\" #933', 'MAT830520', 'Omnis omnis omnis tempore totam molestiae dolore iusto in. Commodi consequatur est et ducimus quaerat ex. Nostrum aut modi sed. Est voluptatem reiciendis cumque inventore accusantium. Est non laboriosam sint est blanditiis aliquam a.', 'cu.m', 3, 1889.00, 'Vitae voluptatem sint impedit suscipit ut reiciendis. Minus fugit cum sapiente facilis. Et sit quisquam odit ut numquam. Inventore veritatis omnis mollitia voluptatum placeat.', 99.00, 119.00, '2025-01-23 15:53:10', '2024-07-25 18:20:47'),
(63, 'Marine Plywood 1/2\" #598', 'MAT328733', 'Eos et illum cupiditate temporibus. Nam voluptas id repellat quasi eum nihil repellendus quod. Fuga adipisci accusamus numquam praesentium.', 'pcs', 12, 1053.00, NULL, 83.00, 200.00, '2025-01-31 16:47:24', '2024-09-01 16:15:03'),
(64, 'Window Glass 6mm #464', 'MAT302035', 'Voluptatibus quo facere et est aperiam atque autem omnis. Id eius rem et quis voluptates voluptatem magni. Voluptate excepturi sunt ab ratione.', 'sq.m', 5, 636.00, NULL, 51.00, 156.00, '2025-03-20 15:14:00', '2024-10-05 13:29:59'),
(65, 'Door Hinges Stainless #216', 'MAT357481', 'Distinctio qui corrupti suscipit qui voluptates aliquid. Sunt rem debitis ab placeat magnam distinctio sit. Qui dolorum voluptatum suscipit eligendi voluptatem enim. Molestias corporis et aut accusamus et reiciendis nisi.', 'sets', 11, 173.00, 'Reiciendis dicta ex sed pariatur. Quis odit et voluptatem voluptatibus maiores quis voluptatem. Voluptatem esse aut adipisci ducimus eveniet.', 36.00, 109.00, '2025-03-18 20:35:06', '2025-04-14 17:09:35'),
(66, 'Paint Latex #479', 'MAT883895', 'Nesciunt at explicabo omnis error. Necessitatibus animi occaecati possimus voluptate enim saepe natus praesentium. Dolor vel at explicabo voluptate. Voluptatem soluta tempora voluptates atque dolorem rerum qui.', 'gallons', 13, 1152.00, 'Repellat facere doloremque quidem ipsam est. Voluptas consequatur libero vero earum labore id quo. Optio quibusdam et ex et. Non tempore in rerum eius autem totam molestiae. Accusantium natus quisquam voluptas itaque et.', 35.00, 37.00, '2024-08-24 23:43:18', '2024-06-16 06:52:14'),
(67, 'GI Sheet Roofing #272', 'MAT866278', 'Sint molestiae officia sint ipsa quos qui. Sint dolores corrupti et quasi rerum dolores rerum.', 'pcs', 9, 594.00, NULL, 63.00, 4.00, '2025-01-16 11:46:05', '2024-10-25 08:15:30'),
(68, 'Concrete Hollow Blocks 6\" #224', 'MAT502532', 'Error quisquam nam animi dolore et nostrum. Magnam nulla architecto porro quia. Assumenda earum quia voluptates minus.', 'pcs', 4, 31.00, NULL, 20.00, 84.00, '2024-11-28 23:16:59', '2024-09-25 22:25:23'),
(69, 'Kitchen Sink Stainless #183', 'MAT776406', 'Ex possimus earum ut eaque quia reprehenderit. Vel ullam magni nam omnis. Ratione dolor quis dolorem distinctio eius odio provident.', 'pcs', 8, 2900.00, 'Sunt alias nihil provident excepturi. Ut nulla et sunt. Cumque et beatae dolores a totam magnam. Sunt suscipit dolorem a sint qui.', 93.00, 189.00, '2024-10-06 02:12:53', '2025-04-21 21:18:19'),
(70, 'Concrete Hollow Blocks 6\" #954', 'MAT386219', 'Sit aut voluptatibus est. Distinctio maxime facilis tempore earum labore vel. Fugiat voluptatem quos autem. Quis sapiente sint dolorum id commodi ea.', 'pcs', 7, 25.00, 'Et aliquam eius velit qui. Ex quidem est ut. Sequi assumenda magni magni omnis at expedita atque. Beatae quas eaque itaque ipsum velit ut.', 95.00, 102.00, '2024-09-14 12:31:52', '2024-06-22 23:43:15'),
(71, 'GI Sheet Roofing #710', 'MAT865656', 'Provident dolores molestiae sequi vero excepturi id qui. Ex voluptas fuga sit. Vitae aliquid soluta in est deserunt quisquam.', 'pcs', 11, 565.00, NULL, 31.00, 163.00, '2025-03-10 10:00:28', '2024-06-21 05:08:17'),
(72, 'Door Hinges Stainless #737', 'MAT706571', 'Eum omnis impedit amet qui reiciendis est rerum. Neque vel excepturi quibusdam.', 'sets', 12, 201.00, 'Qui animi suscipit est vel quia unde molestias. In cupiditate nostrum pariatur minus non. Quos nostrum enim sint ut ut ipsam. Dolor doloremque nesciunt officia pariatur quas.', 67.00, 25.00, '2024-09-04 18:39:08', '2025-04-08 04:28:06'),
(73, 'Electrical Wires 3.5mm² #215', 'MAT824990', 'Eligendi eum saepe id omnis nobis omnis reiciendis. Qui corrupti ut iusto eum eum. Libero odit tenetur sed delectus.', 'rolls', 11, 3006.00, 'Deserunt labore nostrum quo quia voluptas quos. Vel vero non odio perspiciatis. Sint blanditiis mollitia ut omnis. Rem a autem molestiae earum alias.', 80.00, 130.00, '2024-07-04 03:24:04', '2025-01-09 22:23:42'),
(74, 'Door Hinges Stainless #474', 'MAT857163', 'Aut qui blanditiis aut adipisci aut est fugiat. Qui quaerat quia quo ab quos. Provident eum quas debitis et possimus quia dolorem eos. Unde dicta sit minima.', 'sets', 8, 248.00, 'Perferendis debitis dolorem omnis sunt maiores. Voluptatibus quia dicta eos id at. Doloremque praesentium aliquam mollitia dolorum eos libero voluptatem.', 100.00, 137.00, '2024-09-18 05:10:09', '2025-01-13 03:40:46'),
(75, 'GI Sheet Roofing #814', 'MAT448182', 'Et aut modi laborum aut ab quam. Reprehenderit ut provident cupiditate qui et omnis eum reiciendis. Vel reprehenderit ut quas nesciunt tenetur.', 'pcs', 14, 471.00, NULL, 95.00, 1.00, '2024-10-14 14:51:26', '2024-06-26 04:54:40'),
(76, 'PVC Pipes 4\" #334', 'MAT591791', 'Repellat voluptatum amet ducimus dicta. Delectus ut nihil ab saepe quasi. Ea est laborum sunt ipsa. Dolores eveniet voluptates perferendis porro nesciunt.', 'pcs', 15, 354.00, NULL, 37.00, 93.00, '2025-02-05 00:22:07', '2024-09-10 18:46:54'),
(77, 'Door Hinges Stainless #867', 'MAT569324', 'Dolores porro in vel dignissimos. Corrupti enim in nemo adipisci id. Quia dolorem odit voluptatem officiis est consectetur rerum. Totam occaecati aut sequi ipsam.', 'sets', 2, 174.00, NULL, 60.00, 198.00, '2024-10-15 09:47:58', '2024-07-28 18:58:52'),
(78, 'Sand #943', 'MAT660852', 'Inventore et fugit eos maxime explicabo voluptas. Minima autem quasi excepturi. Quia nobis sed non tenetur qui et. Numquam et deserunt exercitationem quia perferendis enim voluptatum.', 'cu.m', 11, 1351.00, 'Perferendis doloremque rem tempore ut ipsa omnis. Maxime sapiente tenetur et perspiciatis nam voluptas velit. Quisquam vel ducimus sint libero consequuntur quam consequatur.', 19.00, 18.00, '2025-03-20 02:22:31', '2024-12-06 18:17:53'),
(79, 'GI Sheet Roofing #749', 'MAT558915', 'Eaque deserunt quaerat vel velit temporibus reprehenderit. Eius inventore aliquam at. Voluptas est eos est soluta sunt.', 'pcs', 9, 552.00, NULL, 23.00, 21.00, '2025-05-26 02:34:17', '2024-12-30 20:41:56'),
(80, 'Portland Cement #383', 'MAT676891', 'Sit officiis asperiores atque nesciunt blanditiis mollitia qui. Qui cupiditate suscipit commodi earum ut aut. Quis enim molestiae dicta iste quo.', 'bag', 1, 253.00, 'Velit rerum dolor in aut repellendus omnis. Repellendus eum velit est sit. Inventore adipisci voluptatem et id temporibus.', 71.00, 66.00, '2024-12-20 11:58:51', '2024-12-29 08:12:19'),
(81, 'Electrical Wires 3.5mm² #112', 'MAT186351', 'Dolorum quisquam qui et in impedit est vel. Ut incidunt rerum aut voluptatibus odio. Itaque reiciendis autem eum ipsa enim quam aut officia. Consequatur repellendus aliquid eos quasi ad qui dolorem enim.', 'rolls', 4, 2856.00, 'Voluptate velit vel quo quam. Est et unde veniam dolorum atque molestiae. Omnis quas a assumenda. Nostrum quam facilis quisquam repellendus rem rem.', 34.00, 125.00, '2024-06-04 20:50:36', '2025-01-14 14:00:23'),
(82, 'Deformed Steel Bars 16mm #679', 'MAT663182', 'Accusantium minima corrupti magni voluptates aperiam ipsum error. Eius mollitia tenetur repellat voluptatem. Repellat harum rerum eos consequatur accusamus voluptates eos. Harum voluptatem quis ut architecto quia maiores omnis.', 'pcs', 8, 579.00, 'Aut non quae ad tempora et ab est. Eos itaque quo facilis sed explicabo. At blanditiis officia et ut omnis voluptate ipsum distinctio.', 91.00, 186.00, '2025-03-01 15:01:21', '2025-04-02 13:21:50'),
(83, 'Bathroom Fixtures Set #922', 'MAT423337', 'Labore blanditiis id quos vitae qui mollitia voluptatem. Possimus quia quasi modi ut sed sit. Facilis voluptas laborum dolorum aut deserunt consequuntur velit ullam.', 'sets', 8, 3678.00, 'Neque corrupti deleniti magni et ut velit. Voluptates eum perferendis non sequi est. Alias assumenda et illo est est. Ab reiciendis neque assumenda fugiat modi similique.', 87.00, 67.00, '2025-01-24 17:54:53', '2025-05-18 17:46:56'),
(84, 'Sand #785', 'MAT553805', 'Sit nemo quaerat distinctio est. Eum voluptatem vitae sit reprehenderit. Eaque veritatis est voluptatibus tenetur magnam dicta rerum magnam.', 'cu.m', 10, 1736.00, 'Ut enim quos inventore reiciendis repellendus commodi ratione. Est voluptas eaque at repellat mollitia quo. Ratione rerum officiis eius eos alias. Dolor dolores et consequatur qui in corrupti nesciunt ut.', 35.00, 92.00, '2025-03-14 06:36:45', '2025-03-24 21:07:05'),
(85, 'Deformed Steel Bars 16mm #562', 'MAT861597', 'Voluptate quis assumenda ex fuga qui. Nemo nesciunt ea architecto consectetur voluptate. Incidunt eos nihil quos explicabo beatae qui asperiores.', 'pcs', 14, 608.00, 'Repellat eligendi quibusdam corrupti. Ducimus quis deserunt molestiae.', 55.00, 128.00, '2025-03-12 01:53:33', '2025-04-27 23:23:31'),
(86, 'Gravel 3/4\" #194', 'MAT857179', 'Corrupti iste odit a itaque ut rerum eaque vel. Maxime dolor alias esse natus. Non itaque ab aut dicta cupiditate. Aliquid ea laboriosam cum ipsa fugit vel quos.', 'cu.m', 9, 1811.00, NULL, 92.00, 95.00, '2024-07-18 13:36:29', '2025-01-22 05:02:15'),
(87, 'Ceramic Floor Tiles 60x60 #821', 'MAT303567', 'Dolorum maxime voluptatum distinctio molestiae quidem. Libero dolor tempora maxime laboriosam. Voluptatum itaque ipsam harum nobis qui quae cupiditate. Ea assumenda cum aut consequatur ab.', 'boxes', 13, 1783.00, 'Pariatur voluptate adipisci ea qui blanditiis. Amet saepe repellendus eveniet provident corrupti sequi. Aliquam quo repellendus neque. Quis et dolor officia sit.', 28.00, 169.00, '2024-07-08 02:41:32', '2024-12-03 03:03:11'),
(88, 'GI Sheet Roofing #317', 'MAT137356', 'Voluptatem ad est consequatur illum a. Vel cumque commodi non dolore. Cum beatae voluptates veritatis omnis. Dolor aut est aut qui illum.', 'pcs', 4, 533.00, NULL, 31.00, 134.00, '2024-09-20 02:47:36', '2024-06-19 14:43:29'),
(89, 'Gravel 3/4\" #868', 'MAT855654', 'Mollitia unde in molestiae illum quis ex ipsa. Animi quisquam nam occaecati saepe. Ad corrupti quos beatae sunt voluptatem modi. Nulla earum qui qui.', 'cu.m', 10, 1983.00, NULL, 78.00, 101.00, '2024-09-01 14:25:52', '2024-12-09 01:34:58'),
(90, 'Sand #585', 'MAT908601', 'Debitis autem dolor at. Impedit at dolorem dolorem facere. Fugiat id architecto totam impedit vitae.', 'cu.m', 4, 1536.00, NULL, 37.00, 114.00, '2024-08-11 03:02:47', '2024-11-23 17:52:19'),
(91, 'Portland Cement #318', 'MAT219345', 'Quod eligendi sunt nam vitae ratione. Magnam inventore velit et quos qui dolores. Ducimus magnam quo praesentium cumque et vel id. Deleniti et fugiat assumenda quam. Eligendi tempore dolorum sed eos.', 'bag', 6, 283.00, NULL, 66.00, 168.00, '2025-05-06 08:36:17', '2024-12-16 01:39:05'),
(92, 'Concrete Hollow Blocks 6\" #3', 'MAT956132', 'Illum doloribus quis rerum necessitatibus totam iusto magni sit. Et provident quasi id debitis consequatur maiores excepturi. Ut natus occaecati placeat est. Ut aut et ipsam perferendis aliquam consequatur.', 'pcs', 13, 30.00, NULL, 31.00, 68.00, '2025-01-14 20:12:26', '2025-05-07 03:55:20'),
(93, 'Ceramic Floor Tiles 60x60 #205', 'MAT650369', 'Veritatis quis impedit ut ea. Ratione ea nihil sunt occaecati voluptatem illum ea.', 'boxes', 7, 1232.00, NULL, 41.00, 198.00, '2024-10-09 16:13:06', '2025-04-21 04:20:41'),
(94, 'Window Glass 6mm #682', 'MAT399719', 'Et reprehenderit consectetur enim iste fugiat necessitatibus dolorum. Adipisci quis inventore totam omnis in. Odit debitis quo corrupti. Alias incidunt repellat magnam cupiditate est esse.', 'sq.m', 15, 614.00, NULL, 19.00, 161.00, '2025-01-30 18:25:34', '2024-11-25 17:33:43'),
(95, 'PVC Pipes 4\" #823', 'MAT408868', 'Dignissimos tempora sed velit officia. Non repudiandae dolorum voluptatem nobis impedit libero. Doloremque at sit ut sit. Facere explicabo qui nesciunt.', 'pcs', 11, 463.00, 'Laborum ratione et neque et repudiandae dolores officiis. Tempore quasi harum sequi dignissimos placeat id maiores. Aut quis est consequuntur fuga cupiditate.', 76.00, 73.00, '2024-09-29 04:20:00', '2024-12-29 16:56:21'),
(96, 'GI Sheet Roofing #846', 'MAT760648', 'Dolor autem quas molestias eum. Dignissimos aut ducimus earum magni repellendus quia dolore enim. Et pariatur qui at laudantium minus libero veritatis. Blanditiis nisi adipisci consequatur nesciunt dolor impedit.', 'pcs', 13, 555.00, NULL, 50.00, 180.00, '2024-07-03 02:10:06', '2025-02-05 16:34:12'),
(97, 'Concrete Hollow Blocks 6\" #694', 'MAT822669', 'Eius ipsa neque voluptate doloremque dolores sed nemo. Ipsam enim temporibus voluptas minus perferendis similique ipsum illum. Repellendus et quasi modi qui velit quae aut sed. Fugit consequatur autem iure aliquam molestiae.', 'pcs', 11, 35.00, NULL, 77.00, 181.00, '2025-04-26 07:40:13', '2025-05-17 22:21:29'),
(98, 'Kitchen Sink Stainless #135', 'MAT920060', 'Modi nobis et porro et nisi. Earum tenetur error tempora magni reiciendis ea eum minus. Aut eos reprehenderit modi in earum cumque ab.', 'pcs', 5, 3384.00, 'Ut in modi velit non temporibus molestiae. Expedita sapiente ratione consequatur adipisci dolores quaerat. Minus id soluta aut qui numquam occaecati. Eius maiores reiciendis aut error esse.', 16.00, 75.00, '2025-01-27 21:42:29', '2024-07-12 15:24:50'),
(99, 'GI Sheet Roofing #829', 'MAT855711', 'Veritatis voluptate architecto odio recusandae laborum saepe. Distinctio rerum odio excepturi neque culpa. Error voluptatum unde voluptatibus dolorem omnis aperiam. Error unde tenetur harum fuga porro qui. Minus omnis modi nihil eveniet tempora nihil repudiandae perferendis.', 'pcs', 11, 500.00, NULL, 11.00, 165.00, '2024-12-15 06:22:20', '2024-07-28 14:01:40'),
(100, 'Marine Plywood 1/2\" #656', 'MAT136945', 'Et nihil labore quis praesentium dolor modi. Inventore quo officia molestias qui labore. Facilis necessitatibus quasi rerum architecto est nihil. At et sed illo consequatur.', 'pcs', 14, 1157.00, NULL, 89.00, 95.00, '2024-10-03 18:45:52', '2025-01-15 13:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `material_images`
--

CREATE TABLE `material_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_quotation`
--

CREATE TABLE `material_quotation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_supplier`
--

CREATE TABLE `material_supplier` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `lead_time` varchar(255) DEFAULT NULL,
  `is_preferred` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_supplier`
--

INSERT INTO `material_supplier` (`id`, `material_id`, `supplier_id`, `price`, `lead_time`, `is_preferred`, `created_at`, `updated_at`) VALUES
(1, 91, 75, 283.00, '0', 0, '2025-06-05 07:33:41', '2025-06-05 07:33:41');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_03_14_000000_create_users_table', 1),
(2, '2024_03_14_000001_create_categories_table', 1),
(3, '2024_03_14_000001_create_projects_table', 1),
(4, '2024_03_14_000002_create_materials_table', 1),
(5, '2024_03_14_000003_create_suppliers_table', 1),
(6, '2024_03_14_000004_create_attachments_table', 1),
(7, '2024_03_14_000005_create_parties_table', 1),
(8, '2024_03_14_000005_create_properties_table', 1),
(9, '2024_03_14_000005_create_quotations_table', 1),
(10, '2024_03_14_000006_create_activities_table', 1),
(11, '2024_03_14_000008_create_supplier_invitations_table', 1),
(12, '2024_03_14_000011_create_material_supplier_table', 1),
(13, '2024_03_15_000001_add_response_fields_to_supplier_invitations', 1),
(14, '2024_03_15_000002_create_quotation_attachments_table', 1),
(15, '2024_03_19_000001_create_contracts_table', 1),
(16, '2024_03_19_000001_create_purchase_requests_table', 1),
(17, '2024_03_19_000002_create_purchase_orders_table', 1),
(18, '2024_03_19_000005_create_purchase_order_items_table', 1),
(19, '2024_03_21_000007_add_payment_details_to_contracts_table', 1),
(20, '2024_03_21_000008_add_check_details_to_contracts_table', 1),
(21, '2024_03_22_000002_recreate_contract_items_table', 1),
(22, '2024_03_22_000003_add_last_login_at_to_users_table', 1),
(23, '2024_03_22_000004_add_material_details_to_contract_items', 1),
(24, '2024_03_23_000001_create_transactions_table', 1),
(25, '2024_03_24_000001_add_is_preferred_to_material_supplier_table', 1),
(26, '2024_03_25_000001_fix_budget_allocation_in_contracts', 1),
(27, '2024_03_25_000002_add_email_verified_at_to_users', 1),
(28, '2024_03_25_000010_create_inquiries_table', 1),
(29, '2024_03_26_000001_add_force_password_change_to_users', 1),
(30, '2024_06_04_000001_add_specifications_to_purchase_request_items_table', 1),
(31, '2025_05_16_190334_create_cache_table', 1),
(32, '2025_05_16_190335_create_companies_table', 1),
(33, '2025_05_16_190335_create_jobs_table', 1),
(34, '2025_05_16_190336_create_sessions_table', 1),
(35, '2025_05_17_195954_create_employees_table', 1),
(36, '2025_05_17_200018_create_company_docs_table', 1),
(37, '2025_05_17_200827_update_companies_table', 1),
(38, '2025_05_17_213321_fix_user_table_structure', 1),
(39, '2025_05_18_165948_add_email_to_employees_table', 1),
(40, '2025_05_18_175410_update_vat_registered_and_use_sureprice_columns_in_companies_table', 1),
(41, '2025_05_23_143103_add_mime_type_and_size_to_company_docs_table_fix', 1),
(42, '2025_05_24_000001_add_disk_column_to_company_docs_table', 1),
(43, '2025_05_24_000002_add_bank_and_products_to_companies_table', 1),
(44, '2025_05_24_000004_update_supplier_invitations_table', 1),
(45, '2025_05_24_182401_create_bank_details_table', 1),
(46, '2025_06_04_072111_make_contract_optional_in_purchase_requests_and_orders', 1),
(47, '2025_06_04_072404_update_quotations_table_for_purchase_requests', 1),
(48, '2025_06_04_072417_update_quotations_table_for_purchase_requests', 1),
(49, '2025_06_04_101643_add_awarded_fields_to_quotations_table', 1),
(50, '2025_06_04_121935_add_foreign_keys_to_contracts_and_purchase_orders', 1),
(51, '2025_06_04_172849_add_purchase_order_id_to_contracts_table', 1),
(52, '2025_06_05_000001_fix_contract_purchase_order_relationship', 1),
(53, 'add_awarded_fields_to_quotations_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postal` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parties`
--

INSERT INTO `parties` (`id`, `type`, `entity_type`, `name`, `company_name`, `street`, `unit`, `barangay`, `city`, `state`, `postal`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(13, 'contractor', 'person', 'Philippine Construction Supply Trading 996', NULL, 'Doonsatabi', NULL, 'Mapagmahal', 'Quezon City', 'Metro Manila', '8869', 'mckenzie35@ankunding.com', '+63919524074', '2025-06-05 08:17:13', '2025-06-05 08:17:13'),
(14, 'client', 'company', 'Client', 'Company CO.', 'P.Paredes', NULL, '143', 'Manila', 'Metro Manila', '2131', 'sd@gmail.com', '091231231877', '2025-06-05 08:17:13', '2025-06-05 08:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `budget` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `client_name`, `start_date`, `end_date`, `status`, `budget`, `created_at`, `updated_at`) VALUES
(1, 'Hotel and Casino Project 5238', 'Labore fugiat et qui soluta rerum. Facilis minima et harum quis. Aliquid at quaerat est sit quaerat. Iste quia libero consequatur aut.', 'Ayala Land Inc.', '2024-07-09', '2026-12-30', 'on-hold', 58801936.05, '2024-06-11 12:40:42', '2024-10-05 22:18:40'),
(2, 'Shopping Mall Project 3535', 'Nulla quia consequatur fuga unde nihil et. Maiores rerum maiores tenetur. Placeat dolores in hic libero ipsa.', 'Century Properties', '2025-03-06', '2025-04-19', 'active', 88009399.57, '2025-03-30 02:39:26', '2025-02-13 22:29:23'),
(3, 'Residential Subdivision Project 7723', 'Nesciunt deleniti reiciendis et voluptate ea rerum esse expedita. Deserunt ipsum ab minima et. Quibusdam suscipit ipsam est aliquam.', 'Robinsons Land Corp.', '2025-05-15', NULL, 'cancelled', NULL, '2024-10-24 00:14:16', '2025-03-02 21:57:39'),
(4, 'High-rise Condominium Project 2677', 'Incidunt repellendus temporibus et assumenda sit consectetur. Cupiditate magni qui et qui. Qui aut ipsum ipsum et qui quibusdam.', 'Robinsons Land Corp.', '2025-09-13', '2025-10-29', 'active', NULL, '2025-05-17 06:17:00', '2024-11-14 06:04:58'),
(5, 'Transit-oriented Development Project 2673', 'Rerum non doloremque in consequatur tenetur enim eum. Fugiat consequatur nostrum ab inventore quaerat. Labore est quam odio eaque dicta ex voluptatum. Commodi recusandae ut non totam error. Natus cumque qui eos non blanditiis adipisci cumque.', 'Robinsons Land Corp.', '2025-02-04', '2026-10-29', 'completed', 18647878.90, '2024-08-09 20:02:59', '2024-08-05 20:51:34'),
(6, 'Hotel and Casino Project 4751', 'Laborum ullam rerum et qui dolorem sunt. Natus quia consequatur perferendis.', 'Century Properties', '2024-12-12', '2025-12-27', 'active', NULL, '2024-12-13 15:34:29', '2024-12-27 08:41:27'),
(7, 'Industrial Park Project 6900', 'Autem consectetur qui harum quaerat laborum quia nam. Nam ipsa commodi non ipsum eum. Ipsam sit possimus culpa id quis qui.', 'Vista Land', '2025-12-03', '2026-07-11', 'active', 5809120.55, '2024-06-29 17:29:59', '2025-02-07 09:33:28'),
(8, 'Educational Institution Project 3426', 'Distinctio distinctio et nihil odio commodi et. Soluta eius labore accusamus quos. Explicabo odit quae in sint et inventore sequi. Cumque esse saepe enim voluptate.', 'Ayala Land Inc.', '2025-02-08', '2025-04-13', 'on-hold', 21305424.39, '2025-02-04 12:40:53', '2025-05-05 12:18:28'),
(9, 'Transit-oriented Development Project 3102', 'Voluptas quod dolor nihil. Aliquam ipsa non dolore saepe qui asperiores. Quis quae id tempora in ad aut dolores. Occaecati ea quia dolores itaque architecto.', 'Megaworld Corp.', '2025-04-28', '2026-07-23', 'cancelled', NULL, '2024-07-07 14:44:27', '2025-05-24 21:55:41'),
(10, 'Hotel and Casino Project 2042', 'Quisquam modi sint iusto non provident perspiciatis rerum molestiae. Dolor et qui debitis soluta ducimus consequatur cupiditate. Aliquid nobis quia hic aut qui consequatur est.', 'Century Properties', '2025-02-21', NULL, 'on-hold', 2349050.47, '2024-12-17 07:31:15', '2024-08-02 03:09:04'),
(11, 'High-rise Condominium Project 4261', 'Dicta qui quo ducimus perspiciatis est enim esse. Rem mollitia libero a alias cumque neque ipsa. Impedit quia sit provident.', 'Robinsons Land Corp.', '2024-10-12', '2027-03-01', 'completed', NULL, '2024-12-06 05:13:25', '2024-08-14 15:01:10'),
(12, 'Residential Subdivision Project 7377', 'Sed iure fuga non quo ut. Et velit aspernatur quidem itaque non voluptas. Illum alias harum aut nostrum aut. Et eum laboriosam nobis cum autem ducimus.', 'Federal Land', '2025-07-20', '2026-10-15', 'active', 90242668.67, '2025-04-23 22:01:17', '2025-01-19 04:26:47'),
(13, 'Shopping Mall Project 4788', 'Ut sit quibusdam natus sed illum. Dolores deleniti enim voluptatem natus voluptatem accusantium. Corrupti repellat ut qui quis tempora.', 'SM Development Corp.', '2025-01-28', '2026-09-21', 'on-hold', 87548855.39, '2024-11-16 19:44:51', '2024-09-28 23:47:48'),
(14, 'Hotel and Casino Project 2464', 'Laboriosam ut libero animi necessitatibus provident soluta. Temporibus et dicta et vero. Enim accusantium dignissimos architecto quo. Non asperiores laudantium quod minima ut ut.', 'Federal Land', '2025-03-04', '2026-03-04', 'on-hold', 41701251.76, '2025-06-01 00:10:21', '2024-07-06 05:19:19'),
(15, 'High-rise Condominium Project 1163', 'Iusto modi molestias itaque. Dolorem assumenda magnam dolor sed consequatur illo. Commodi enim vero non eaque rerum reiciendis cumque perferendis. Quo qui enim consequatur eum ducimus quisquam voluptatem.', 'Double Dragon Properties', '2025-07-17', NULL, 'on-hold', NULL, '2024-10-30 12:30:38', '2024-12-03 02:51:28'),
(16, 'Healthcare Facility Project 3549', 'Nulla nobis laudantium illo omnis asperiores. Vel iusto a ratione ipsum accusantium. Porro quam et ut temporibus officia.', 'Megaworld Corp.', '2025-04-16', '2025-08-12', 'on-hold', 77069444.00, '2025-02-27 14:51:51', '2025-06-04 05:47:09'),
(17, 'Healthcare Facility Project 3804', 'Voluptas numquam iure dolorem velit nemo sint ut. Aliquam sunt voluptatem ullam omnis magnam dignissimos. Corrupti omnis qui qui blanditiis maxime quis sint rem.', 'Megaworld Corp.', '2025-02-12', '2026-06-14', 'cancelled', 44230344.10, '2024-11-01 22:21:50', '2024-12-11 01:34:29'),
(18, 'Shopping Mall Project 4427', 'Dolorum accusamus quia doloribus nulla inventore quibusdam. Dolor mollitia tempore et accusantium voluptatem.', 'SM Development Corp.', '2025-09-20', NULL, 'completed', 86477110.20, '2025-01-28 11:35:16', '2024-07-09 01:01:22'),
(19, 'Transit-oriented Development Project 4965', 'Omnis et consectetur corrupti ipsum alias consequatur repellendus. Sed officiis dolores fuga eligendi. Perspiciatis suscipit iste est quos.', 'Robinsons Land Corp.', '2024-11-03', '2027-03-27', 'active', NULL, '2024-09-15 07:25:04', '2024-07-10 21:05:48'),
(20, 'Mixed-use Development Project 1250', 'Accusantium a corporis vitae cupiditate. Quo similique eveniet dolorem atque necessitatibus totam rerum. Fugit porro nulla quia impedit.', 'Filinvest Land', '2024-11-28', '2026-08-29', 'on-hold', 92287867.78, '2025-01-17 16:36:00', '2024-07-07 14:34:21'),
(21, 'Shopping Mall Project 6547', 'Corporis dignissimos tempora qui pariatur. A eveniet quae assumenda. Ipsa qui totam inventore ex consequatur laborum. Quia similique fugiat possimus molestiae fuga quas quaerat ullam.', 'Federal Land', '2024-11-15', '2025-03-02', 'completed', 97192021.08, '2024-06-12 15:05:29', '2024-09-09 15:24:29'),
(22, 'Transit-oriented Development Project 6040', 'Praesentium laborum laboriosam qui repellat tempore explicabo. Assumenda doloremque omnis suscipit et iusto quaerat. Veritatis est nostrum facilis rerum quam laboriosam eius sed.', 'Federal Land', '2025-02-26', '2025-07-13', 'active', 39517956.67, '2025-02-02 10:40:23', '2024-10-23 01:57:10'),
(23, 'Industrial Park Project 3127', 'Dolorum dolorem ducimus in quis omnis accusantium earum dicta. Ad voluptatem temporibus ut ab voluptates.', 'Double Dragon Properties', '2024-06-28', '2027-04-13', 'completed', 8634505.26, '2024-06-07 06:28:06', '2024-08-02 12:28:21'),
(24, 'Shopping Mall Project 3488', 'Sequi quia ipsam est. Sequi non et pariatur. Aliquid eum molestias distinctio. Similique dignissimos architecto rerum occaecati ex voluptatem.', 'Megaworld Corp.', '2024-11-06', '2026-03-03', 'completed', NULL, '2025-05-05 19:26:18', '2024-08-08 09:40:09'),
(25, 'Mixed-use Development Project 4144', 'Ipsam cupiditate natus saepe maxime ipsam. Quis quae aut et delectus voluptas sit qui. Cumque fuga consequuntur ex iure modi. Et voluptatem autem temporibus cupiditate.', 'Double Dragon Properties', '2025-02-18', '2026-10-16', 'cancelled', NULL, '2024-11-26 18:15:01', '2024-07-24 09:19:07'),
(26, 'Industrial Park Project 2893', 'Magnam nihil ut magni qui. Voluptates corrupti natus placeat ex sit pariatur doloremque aut.', 'Filinvest Land', '2025-11-08', '2027-05-13', 'completed', 61490223.77, '2025-03-15 00:50:47', '2025-03-24 19:48:03'),
(27, 'Industrial Park Project 1885', 'Voluptatem aut recusandae dolores. Ut sit et quam unde. Accusantium voluptas magni incidunt perspiciatis eaque nam qui impedit. Dolorum ea reiciendis nulla repudiandae ratione delectus aut et.', 'Filinvest Land', '2025-09-08', '2026-11-25', 'completed', NULL, '2024-07-23 18:17:12', '2025-02-18 19:41:41'),
(28, 'BPO Office Tower Project 1874', 'Ea porro illo sint natus. Autem earum vel ea dolores. Quaerat quaerat quas nisi. Vero amet maiores deleniti ipsa deleniti voluptatem voluptas.', 'Vista Land', '2025-07-09', '2026-05-08', 'active', NULL, '2025-05-28 22:15:11', '2025-03-13 22:45:00'),
(29, 'Mixed-use Development Project 9228', 'Ducimus laborum animi est aut hic. Quasi doloribus hic aut quis et. Quo consequatur quidem repellat tenetur impedit.', 'Double Dragon Properties', '2025-09-18', '2026-02-12', 'cancelled', 43762627.47, '2025-04-11 12:55:04', '2024-10-22 08:29:31'),
(30, 'Shopping Mall Project 1171', 'Et velit at temporibus occaecati et nihil minus sapiente. Sequi quo repellendus quas eum est. Non adipisci eligendi qui.', 'Federal Land', '2024-06-05', '2026-10-31', 'completed', 7908412.89, '2025-01-05 13:32:41', '2025-02-14 21:58:46'),
(31, 'Industrial Park Project 6712', 'Voluptas perferendis ipsam est consequatur veritatis qui. Ipsum dolor incidunt nulla iste ut a dolor. Eum illo nemo sint eos aliquam dignissimos itaque.', 'DMCI Homes', '2024-09-11', '2026-09-07', 'cancelled', 44906123.20, '2024-10-18 09:31:57', '2025-04-01 13:24:21'),
(32, 'Healthcare Facility Project 4467', 'Tempore voluptas deleniti ut. Est iusto laudantium voluptatem dignissimos fugiat magnam aut. Ut vel numquam sequi quaerat maiores eligendi.', 'Robinsons Land Corp.', '2025-08-13', '2026-10-23', 'completed', 54768227.48, '2025-02-03 13:04:39', '2024-10-29 16:27:26'),
(33, 'Mixed-use Development Project 3874', 'Quos quam dolor saepe impedit repellat quo natus aliquam. Quibusdam tempore nihil id autem cum autem quam. Quam tenetur sit ut hic velit. Ut sunt nihil repellat.', 'Megaworld Corp.', '2025-06-27', NULL, 'active', 95818875.88, '2024-06-10 22:02:55', '2025-02-22 02:39:37'),
(34, 'High-rise Condominium Project 8609', 'Dicta aspernatur magnam doloribus alias. Esse debitis voluptatibus rerum dolore earum in. Dolor voluptatem dolorem fuga magnam. Pariatur eum unde harum pariatur.', 'Century Properties', '2025-02-27', NULL, 'cancelled', NULL, '2025-03-04 05:26:25', '2024-11-21 20:26:26'),
(35, 'Transit-oriented Development Project 5743', 'Minus est est eaque blanditiis. Qui ut sapiente assumenda sunt harum nobis. Voluptas et consequatur ipsam ex perferendis libero. Quas sed esse saepe et.', 'SM Development Corp.', '2024-07-12', NULL, 'completed', 80319601.04, '2024-11-12 04:16:35', '2024-12-27 16:12:21'),
(36, 'Hotel and Casino Project 3377', 'Eius consequatur earum laborum dolorem non expedita. Rerum beatae et beatae ipsa et. Illo et doloribus facilis id dolor deleniti vitae delectus.', 'Ayala Land Inc.', '2024-08-08', '2026-06-19', 'active', NULL, '2024-07-05 03:42:40', '2025-03-11 09:26:08'),
(37, 'Industrial Park Project 1474', 'Eaque nostrum id quos ratione ullam animi perspiciatis. Doloremque est consequuntur rerum dolor. Et ut nam aliquid ducimus velit qui minus. Quam praesentium qui deleniti.', 'Century Properties', '2024-10-16', '2025-04-24', 'cancelled', 55587108.61, '2024-11-12 04:02:08', '2025-01-19 05:58:26'),
(38, 'Hotel and Casino Project 7990', 'Omnis aspernatur in officia maxime dolores. Nihil reiciendis perspiciatis ut architecto porro quo. Molestias at sunt omnis aspernatur aut et. Voluptas ipsum nulla rerum veritatis ut.', 'Ayala Land Inc.', '2024-06-16', '2026-03-04', 'completed', 63944201.58, '2024-12-29 15:45:17', '2024-12-04 04:13:02'),
(39, 'Mixed-use Development Project 2022', 'Commodi dignissimos ratione veritatis. Deleniti similique earum minus nobis. Ea reprehenderit voluptatem facilis consequatur tenetur maiores rerum. Est iste sit velit.', 'DMCI Homes', '2025-09-06', '2026-12-10', 'active', 78720554.93, '2025-01-02 15:12:08', '2024-08-31 13:09:54'),
(40, 'Mixed-use Development Project 3034', 'Est id error aperiam ea quasi neque rerum. Iusto nihil rem iste nesciunt assumenda. Sint quaerat nostrum et itaque natus doloribus facere. Magnam voluptatum laudantium et dicta tempore eius iure.', 'Century Properties', '2025-06-07', '2026-10-15', 'on-hold', NULL, '2024-08-22 08:07:38', '2025-04-11 21:03:45'),
(41, 'Educational Institution Project 3758', 'Dolor odit fuga dolores ipsa impedit quia. Et dolorem possimus aliquid aut expedita. Omnis consequatur ipsa quod nam.', 'SM Development Corp.', '2025-04-11', '2027-01-13', 'active', NULL, '2025-03-08 14:07:56', '2025-05-05 06:30:03'),
(42, 'Residential Subdivision Project 5616', 'Rerum qui a reprehenderit tempora esse cum. Expedita ut est consequuntur. Autem explicabo architecto vel fugit cupiditate accusamus accusamus voluptatem.', 'DMCI Homes', '2025-04-29', '2027-05-01', 'completed', NULL, '2025-05-02 22:33:41', '2024-12-22 03:00:31'),
(43, 'BPO Office Tower Project 2453', 'Velit officia explicabo incidunt. Ut architecto unde qui iste accusantium. Veritatis facere sunt voluptatem odio soluta id assumenda accusantium. Voluptatibus quam dolores consectetur necessitatibus beatae.', 'Ayala Land Inc.', '2025-01-04', '2026-06-21', 'cancelled', NULL, '2025-04-06 10:04:51', '2025-01-08 02:53:38'),
(44, 'Hotel and Casino Project 9417', 'Delectus praesentium dolores facere cum provident quia. Voluptatem voluptas neque mollitia voluptas illum neque necessitatibus tempora. Voluptatibus non enim quia magnam.', 'SM Development Corp.', '2025-09-08', '2026-02-27', 'cancelled', NULL, '2024-10-07 14:21:23', '2025-03-04 16:15:53'),
(45, 'Healthcare Facility Project 1075', 'Ut voluptates non laudantium pariatur iure vel voluptatem. Quae veritatis maxime fuga repellendus magnam et. Quas exercitationem cum earum et voluptatem nulla sapiente iste. Sed qui et enim harum deleniti nostrum. Eos consequuntur sequi debitis asperiores alias tempore.', 'SM Development Corp.', '2024-11-05', NULL, 'active', 27364395.46, '2024-10-31 13:30:19', '2025-05-25 01:10:23'),
(46, 'Residential Subdivision Project 6091', 'Doloribus quia illum laudantium quia. Aut beatae ut aut officiis aspernatur aut et voluptatum. Sit velit adipisci sed et non repellat impedit. Ipsum quod repellat accusamus delectus necessitatibus voluptatem nobis qui.', 'Federal Land', '2024-09-07', '2025-08-11', 'completed', 4616754.32, '2024-06-20 17:45:51', '2024-10-29 14:11:33'),
(47, 'Residential Subdivision Project 5346', 'Quas perferendis modi deleniti voluptatum voluptas deleniti asperiores. Laudantium harum cupiditate facilis impedit. Optio quia eius id officia eum alias laudantium itaque.', 'DMCI Homes', '2025-03-31', '2025-09-30', 'active', NULL, '2024-12-04 21:30:16', '2024-10-21 17:23:39'),
(48, 'Hotel and Casino Project 4500', 'Dolores sint impedit dolores quibusdam est incidunt quo. Earum aut deleniti et esse velit. Beatae assumenda quidem quis.', 'Megaworld Corp.', '2024-08-06', '2024-09-12', 'cancelled', NULL, '2025-05-09 23:07:38', '2024-06-17 02:12:04'),
(49, 'High-rise Condominium Project 5208', 'Dicta consequatur nam neque est tenetur omnis quia. Repudiandae maiores dolore excepturi a tempore unde laudantium.', 'Robinsons Land Corp.', '2024-12-01', '2025-07-02', 'completed', 55783307.03, '2024-10-31 20:17:29', '2024-07-23 06:24:55'),
(50, 'Shopping Mall Project 7286', 'Eos facilis non doloremque sunt quasi ab vel ut. Iste non quidem perspiciatis doloremque qui dolores unde. Officia ipsam aperiam alias quo. Facere omnis voluptates minus deserunt ut.', 'SM Development Corp.', '2025-10-21', '2026-03-29', 'completed', NULL, '2024-11-11 10:09:27', '2024-08-17 04:08:07');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `street` varchar(255) NOT NULL,
  `unit_number` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postal` varchar(255) NOT NULL,
  `property_type` varchar(255) DEFAULT NULL,
  `property_size` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `street`, `unit_number`, `barangay`, `city`, `state`, `postal`, `property_type`, `property_size`, `created_at`, `updated_at`) VALUES
(7, 'P.Paredes', 'dhsajkhdashdjka', '143', 'Manila', 'Metro Manila', '2131', 'residential', 90909.00, '2025-06-05 08:17:13', '2025-06-05 08:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `delivery_date` date NOT NULL,
  `payment_terms` varchar(255) NOT NULL,
  `shipping_terms` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `purchase_request_id`, `contract_id`, `supplier_id`, `total_amount`, `status`, `delivery_date`, `payment_terms`, `shipping_terms`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'PO20250001', 1, NULL, 1, 250.00, 'approved', '2025-06-12', 'ash', 'asdas', 'hasda', '2025-06-05 07:19:29', '2025-06-05 07:19:35'),
(2, 'PO20250002', 2, NULL, 1, 2340.00, 'approved', '2025-06-13', 'S', 'dsdadasdasds', 'dsadasdasdsadas', '2025-06-05 08:15:34', '2025-06-05 08:15:45');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `specifications` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `material_id`, `quantity`, `unit_price`, `total_price`, `specifications`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 16, 10.00, 25.00, 250.00, 'Odit quia et natus suscipit laborum. Ut maxime qui libero esse quis voluptatibus qui. Exercitationem quia et deleniti. Quidem quo ea iste necessitatibus qui dolorum aperiam porro. Quidem ut dolores non.', NULL, '2025-06-05 07:19:29', '2025-06-05 07:19:29'),
(2, 2, 27, 90.00, 26.00, 2340.00, NULL, NULL, '2025-06-05 08:15:34', '2025-06-05 08:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pr_number` varchar(255) NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(255) NOT NULL,
  `required_date` date NOT NULL,
  `purpose` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `contract_id`, `pr_number`, `requester_id`, `department`, `required_date`, `purpose`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'pr-0001', 1, 'sda', '2025-06-25', 'asda', 'approved', NULL, '2025-06-05 07:19:02', '2025-06-05 07:19:07'),
(2, NULL, 'pr-0002', 1, 'sda', '2025-06-14', 'adasdasdas', 'approved', 'dsdadasda', '2025-06-05 08:15:08', '2025-06-05 08:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_attachments`
--

CREATE TABLE `purchase_request_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `estimated_unit_price` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `specifications` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `material_id`, `supplier_id`, `description`, `quantity`, `unit`, `estimated_unit_price`, `total_amount`, `specifications`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 16, NULL, 'Sunt natus autem veritatis suscipit perferendis delectus iste molestiae. Modi consequuntur dolorem vero et. Vel facere consequatur error laudantium vero temporibus ut.', 10.00, 'pcs', 25.00, 250.00, 'Odit quia et natus suscipit laborum. Ut maxime qui libero esse quis voluptatibus qui. Exercitationem quia et deleniti. Quidem quo ea iste necessitatibus qui dolorum aperiam porro. Quidem ut dolores non.', NULL, '2025-06-05 07:19:02', '2025-06-05 07:19:02'),
(2, 2, 27, NULL, 'Eaque reiciendis est et aut sunt. Recusandae officia voluptate tempora ut mollitia molestiae et. Nisi qui vel quasi ut quo.', 90.00, 'pcs', 26.00, 2340.00, NULL, NULL, '2025-06-05 08:15:08', '2025-06-05 08:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `rfq_number` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `total_amount` decimal(15,2) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `delivery_terms` varchar(255) DEFAULT NULL,
  `validity_period` varchar(255) DEFAULT NULL,
  `awarded_supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `awarded_amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_attachments`
--

CREATE TABLE `quotation_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_responses`
--

CREATE TABLE `quotation_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `delivery_terms` varchar(255) DEFAULT NULL,
  `validity_period` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_response_attachments`
--

CREATE TABLE `quotation_response_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_response_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_response_items`
--

CREATE TABLE `quotation_response_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_response_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `specifications` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_supplier`
--

CREATE TABLE `quotation_supplier` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0zQZT3UEuMWFB3sADWNcbJ8fmWApbMFnON5Psn5Y', NULL, '103.212.146.80', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/22.7.0 Yowser/2.5 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREVuNG9sR1RGT1pmWTlxb2JSTll5b2VpVHVRT2xQTzByMFdIUkFRZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749153893),
('457i0yZF60tPnIXxWJe0umZr0lo1dqJRnl8MmFL2', NULL, '171.42.25.251', 'Mozilla/5.0 (Windows NT 6.5; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3884.88 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicms3OVRkcWpXWWp0NHppVkNZdUYzSlFlbEdtOTl3dkdKN1lJTzIxdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LnN1cmVwcmljZS1nZGMuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749154936),
('5HlklMiPZaoGAThZNXmGWQ4zBFYCyzehGfexmM3p', NULL, '35.243.232.63', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkJYWmN2WjdPSktzT3FPR3YzamlvOVdaSmVOUVJOdnFyR0VCb3JVYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749164119),
('6XtfKiB4WAOxlDKwdz6Xg50XjpLUpzSwNpwIU6n6', NULL, '124.236.100.56', 'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVHhjT0xWZ1lpWlE3VGE2WlhIRXB6dzlhWkw2b3hITFRIczRpTVVXNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LnN1cmVwcmljZS1nZGMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749174747),
('ESUgjb7dODqGgXEKjP2zL5SNz7w0lRRNRXyt4NKc', NULL, '124.236.100.56', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2NmdVkxUGQyZnFjTXhwTFJ2anZpSkxpNU1IbE9TN1VhQWh4ZDAwdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LnN1cmVwcmljZS1nZGMuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749174749),
('GeFoO46IN3xhqMs5Z4074ohr8dQaTD6FwmKa3dwC', NULL, '2a02:4780:6:c0de::10', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiYU8zbG96UXIzRll4VXVGYm5wR2o4Sm5BdmVvbHVLZ2F0eGxCTklFQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749174481),
('GFd2hY72voBzSf3ca9ZOxkHJFdeIvkubtUzOnDsf', NULL, '171.42.25.251', 'Mozilla/5.0 (Windows NT 6.5; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3884.88 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEVyRXZiTGxQVm81blBXdWVqRm1Lblh5WFZjWWZEWmxsZ3doUDVHMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LnN1cmVwcmljZS1nZGMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749154936),
('ie0iwLnoe4UO4sVph6Racdp7lfmxGahxImazEOnP', NULL, '94.176.51.174', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/123.0.6312.52 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidzRYWTd2TXgyUlVmVkxJVktlZUdsNDFPN3pZUmh6eXRWWlZuZTBQWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749150308),
('nbpQS6gujrtzcP0hMHtcGwL3CUATWI9DjH8hGyWi', NULL, '185.177.72.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMksyc1Vha2VvdGpqT1RiMFZJUENiRDNNZkV5UHN0MWhXaFNvNkJUMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749169919),
('NSpPY2ELIhpt4jAHOVW084722VKz6Zi0sz52AxrH', NULL, '18.236.133.21', 'Mozilla/5.0 (X11; Linux i686 on x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN1NiSnFiaDVKbFlwYzFSck5mODVwMnRtQk9oTTlRaU1wU2ptSjM1UyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749168632),
('qSGyoqMBrLIS8dzSd5iLah1uHPGUnlLZoDzeRjcH', NULL, '2a02:4780:6:c0de::10', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidUplYnZMQjhJRHk5RlV4WHNCc201TURjV3g5U0dBN2dmWWlqSjNDaSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749174481),
('RLqdmWItZGsJCedNBG4mBshVEmtie30w1pnHAqoX', NULL, '180.149.0.227', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/123.0.6312.52 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVlplcWdlQzZ3MmQzOVNHMUdCUlpFbmxHbVp6ZjY0RVdpWm5Nc2lGWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749148854),
('RsUXMeVmOQLuLc3rknw1eQ31uXnrcLpsugzRw6dI', NULL, '129.211.215.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2hYeFVKN3Y3ZVZ3RVl4SVZWT2FlNlhIeFhlcHYycG9rTktCeFZ6QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LnN1cmVwcmljZS1nZGMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749157584),
('Tf8JtgMe9uSonJth4iSmPWCAOAhOQiFKQZkJTTA8', NULL, '52.178.182.29', 'Go-http-client/2.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRGl0M0FiZjdLMXZNNEZEZHpOZElvdlQ4MG9nd1R5NzZMV1NtTDltQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749168180),
('uWT5cu9qwkiUU4lXdHoTN00keBrq6qgcjTyjBBuR', NULL, '45.90.61.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1JNaEkzekN1cDRUNGZvOVVzVW5kRllBWkl0cGhyMmd3b2VWSjBvdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749157039),
('yyvli8tZsbN6zm5wf9FgvrmoRRXEAna7SboZNeTf', NULL, '185.177.72.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUNqRlRQSlgyZGJNdDlJYkRnZG9ERzR2MXcyNXJhNEZ1blFacFF1RyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749169920),
('ZJ9CeQUKEXmFNK3DKAIqLPHb2SAy0qpDw375wKta', NULL, '52.178.182.29', 'Go-http-client/2.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXpiOWVucGtydWxLOExzdW1FNjdnNEk0WHc4eTdIOXlVRjd3MzR6aSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vc3VyZXByaWNlLWdkYy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749168180);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `registration_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Philippine Construction Supply Trading 996', 'Flossie Goyette', 'mckenzie35@ankunding.com', '+63919524074', '9195 Ritchie Port\nConnburgh, UT 70433-2540', '678-683-409-255', 'CS30665331', 'active', '2025-04-04 10:06:27', '2024-08-26 23:59:56'),
(2, 'Pioneer Hardware Corporation 648', 'Spencer Swaniawski', 'nienow.saul@hansen.com', '+63363830440', '314 Littel Place Apt. 899\nJordifort, NM 03762-8254', '781-855-481-483', 'CS03961352', 'inactive', '2024-06-14 17:45:37', '2024-12-20 08:59:25'),
(3, 'Asian Building Materials Inc. 136', 'Rosetta Yundt', 'alexandrine.hyatt@stiedemann.com', '+63332783447', '1705 Muller Wells Suite 422\nNew Damaristown, MA 80130', '032-491-064-633', 'CS20273954', 'pending', '2023-06-24 05:26:29', '2024-11-05 18:21:24'),
(4, 'Metro Industrial Supply Company 459', 'Daphne Fay', 'labadie.marcia@schaefer.com', '+63416199433', '6071 Marianne Tunnel\nNew Stanton, ME 12545', '731-818-373-790', 'CS56181424', 'pending', '2024-05-19 22:45:01', '2024-11-03 03:56:26'),
(5, 'Asian Industrial Supply Trading 054', 'Miss Alessia Herman V', 'mcclure.everardo@volkman.com', '+63349200540', '1042 McKenzie Corners Suite 130\nEast Genevieve, FL 71988-8048', '002-009-994-339', 'CS42143870', 'pending', '2025-06-01 06:42:37', '2024-07-14 18:40:28'),
(6, 'Philippine Building Materials Trading 586', 'Curtis Leffler', 'myrtice88@quitzon.info', '+63543318819', '9143 Lynch Stravenue\nRenebury, GA 86352', '847-528-996-202', 'CS52138267', 'pending', '2025-02-19 11:49:18', '2024-09-27 07:58:50'),
(7, 'Philippine Hardware Company 839', 'Verdie Schultz', 'jconnelly@mueller.com', '+63731612541', '325 Jared Avenue Apt. 838\nKeelingborough, NE 90597-7412', '598-970-699-645', 'CS48559097', 'pending', '2023-12-27 03:47:38', '2025-01-30 00:53:53'),
(8, 'Asian Industrial Supply Enterprises 401', 'Brennan Wiza', 'kschuppe@bailey.org', '+63358321115', '8713 Hettinger Mount Suite 989\nKlingburgh, ME 70294-0296', '845-729-315-136', 'CS97878478', 'inactive', '2023-07-24 05:23:09', '2024-07-10 14:26:58'),
(9, 'Pioneer Construction Solutions Trading 272', 'Asia Jacobson', 'roger.boyer@konopelski.biz', '+63223206331', '210 Ludie Ferry Apt. 773\nNorth Bud, SC 81689', '700-855-953-579', 'CS72661967', 'active', '2023-06-13 03:41:34', '2024-07-30 02:53:16'),
(10, 'Metro Construction Solutions Inc. 650', 'Mrs. Clarabelle Cruickshank', 'clind@denesik.net', '+63284338597', '8279 Leonardo Ville Suite 548\nPadbergberg, RI 28330-6793', '892-458-087-392', 'CS84983675', 'active', '2024-05-08 02:37:10', '2024-12-05 07:22:19'),
(11, 'Asian Construction Supply Inc. 917', 'Prof. Terence Smith', 'mary23@haley.net', '+63246270655', '488 Bednar Wells\nSouth Libbietown, ID 98156-8023', '652-609-527-801', 'CS94546518', 'inactive', '2023-07-15 02:06:53', '2025-02-19 00:45:14'),
(12, 'Manila Building Materials Corporation 974', 'Keith Reinger', 'ritchie.dayne@dare.com', '+63288352084', '6066 Fritsch Turnpike\nCarleyside, CA 58858-7472', '499-931-093-793', 'CS82043341', 'pending', '2023-09-18 12:47:30', '2024-10-12 21:18:30'),
(13, 'Manila Construction Solutions Enterprises 470', 'Vida Ratke', 'elwyn24@schuster.com', '+63639603005', '2597 Destin Manor Apt. 296\nWest Hailie, DE 79470-2080', '760-891-972-962', 'CS04714373', 'inactive', '2023-08-08 09:47:57', '2024-10-19 01:49:59'),
(14, 'Manila Construction Solutions Trading 856', 'Emil Hoppe Jr.', 'micheal44@ziemann.com', '+63545594374', '68150 Doyle Expressway\nPort Veronicaburgh, ND 35210-4860', '300-184-202-339', 'CS55127523', 'active', '2024-03-11 13:07:33', '2024-06-27 16:19:06'),
(15, 'Pacific Construction Supply Inc. 160', 'Miss Luisa Bashirian', 'bernhard.beer@bauch.com', '+63318555677', '8495 Sedrick Lake\nEliaston, ID 67055-5423', '118-479-735-853', 'CS36996068', 'pending', '2024-08-19 19:21:38', '2025-05-06 19:06:49'),
(16, 'Royal Hardware Enterprises 939', 'Agustina Mayer', 'klang@lind.com', '+63546268616', '70521 Metz Fall Apt. 268\nNew Juanafurt, KY 92911', '813-629-015-030', 'CS13394445', 'inactive', '2024-11-14 23:02:42', '2024-07-02 02:48:05'),
(17, 'National Hardware Corporation 204', 'Enoch Weissnat', 'mhuel@steuber.com', '+63238643362', '803 Jarred Street Suite 142\nWest Zariaton, MS 51497', '266-885-596-014', 'CS12845876', 'active', '2025-01-04 13:36:33', '2025-01-09 09:43:46'),
(18, 'Pioneer Building Materials Enterprises 169', 'Robbie Barrows I', 'hhayes@doyle.biz', '+63281259300', '204 Wyman Orchard Apt. 320\nRunolfsdottirview, SC 24151-5697', '217-733-255-841', 'CS76084012', 'active', '2025-03-04 14:27:51', '2024-07-21 13:00:24'),
(19, 'Philippine Construction Supply Enterprises 834', 'Judge Hilpert', 'jamison.labadie@bernier.org', '+63649460091', '4120 Christiansen Vista Apt. 740\nRobertsville, MN 95944-1776', '825-663-815-601', 'CS00904926', 'pending', '2024-01-22 09:50:53', '2024-07-14 13:11:55'),
(20, 'Makati Construction Supply Inc. 759', 'Herminia Jaskolski', 'xgutkowski@batz.biz', '+63655328229', '8696 Jo Passage Apt. 821\nNorth Daveburgh, RI 27442-2732', '185-330-962-187', 'CS30106958', 'active', '2025-03-06 11:18:14', '2025-04-18 07:15:24'),
(21, 'Manila Industrial Supply Enterprises 895', 'Annabel Skiles', 'grayson.jones@klein.info', '+63722349451', '211 Carlie Run Suite 215\nElmerborough, MD 49020-5913', '761-659-694-858', 'CS91534258', 'active', '2025-04-12 13:29:06', '2025-03-08 10:17:48'),
(22, 'Pioneer Hardware Corporation 867', 'Erin Cummings', 'yrodriguez@walsh.com', '+63610988180', '775 Gerlach Knolls Suite 208\nHillardhaven, NV 88894-6954', '627-905-280-650', 'CS95772143', 'active', '2024-05-01 20:02:11', '2024-07-12 04:04:56'),
(23, 'Metro Hardware Enterprises 085', 'Jadyn Schamberger', 'emmy97@reichert.info', '+63709388125', '5545 Rice Gateway Apt. 400\nNehaburgh, NY 35284', '603-938-466-916', 'CS59784169', 'pending', '2025-05-01 23:18:30', '2024-12-12 23:29:20'),
(24, 'Makati Industrial Supply Corporation 171', 'Prof. Krista Bogisich', 'zackary.stamm@windler.com', '+63893359235', '867 Hammes Mews\nFredrickbury, UT 03082-8898', '376-264-428-669', 'CS90235591', 'pending', '2023-12-03 13:55:23', '2024-11-17 05:33:07'),
(25, 'Pioneer Construction Supply Inc. 322', 'Javier Dickens', 'german58@osinski.com', '+63899109412', '72509 Randi Port\nLake Keyshawnberg, MI 06693', '980-901-609-046', 'CS85019548', 'pending', '2023-08-12 09:51:01', '2025-04-04 04:10:21'),
(26, 'Royal Construction Supply Company 046', 'Orpha Koepp Sr.', 'ghickle@boehm.com', '+63810670150', '85765 Rutherford Plains Apt. 010\nNorth Leanne, IN 46373-9589', '971-657-033-016', 'CS89228553', 'pending', '2023-07-07 07:45:46', '2024-10-29 09:18:31'),
(27, 'Royal Construction Supply Inc. 665', 'Mathew Kohler', 'vrau@balistreri.com', '+63446102244', '287 Goldner Centers Apt. 454\nNorth Eulahfort, NC 84655-5315', '455-845-903-442', 'CS28501367', 'pending', '2024-12-05 20:49:17', '2024-07-25 14:34:17'),
(28, 'Manila Construction Solutions Corporation 977', 'Jena Kerluke', 'wtorphy@harvey.com', '+63910045425', '926 Jett Wall\nBrakusside, GA 12687', '896-279-751-467', 'CS86403676', 'inactive', '2025-03-14 02:11:28', '2025-03-07 20:23:10'),
(29, 'Mega Industrial Supply Trading 684', 'Mrs. Gabrielle Spencer', 'aledner@hudson.biz', '+63894368971', '36881 Cecilia Pine\nSchoenview, NC 01119-9347', '624-223-747-739', 'CS46954026', 'pending', '2024-01-14 14:04:24', '2024-12-24 02:03:50'),
(30, 'Metro Hardware Inc. 710', 'Madalyn Prosacco', 'marcelle04@witting.info', '+63665887083', '765 Wyman Prairie\nHaaghaven, AR 28393-0115', '413-087-923-986', 'CS16924379', 'active', '2025-01-27 09:24:41', '2025-02-10 21:15:20'),
(31, 'Royal Hardware Trading 407', 'Mr. Kurt Berge', 'hintz.adolphus@beahan.com', '+63245783672', '539 Ortiz Mountains\nAbbottton, MN 91303', '842-321-619-909', 'CS93890975', 'pending', '2023-11-22 20:07:35', '2024-09-02 06:22:23'),
(32, 'Manila Hardware Trading 598', 'Catharine Gorczany', 'loma.tillman@hagenes.com', '+63752707448', '3580 Quitzon Stravenue Apt. 632\nWest Danielaburgh, WI 85930-7606', '360-325-473-196', 'CS55443683', 'inactive', '2023-06-09 14:38:58', '2024-07-14 18:06:21'),
(33, 'Philippine Hardware Company 094', 'Dr. Marcel Schmitt', 'dkohler@lakin.info', '+63390813268', '297 Hintz Hollow\nEast Aniyahhaven, MO 51267', '583-884-152-558', 'CS35089273', 'pending', '2025-04-04 19:13:47', '2025-04-19 19:07:42'),
(34, 'Metro Construction Supply Enterprises 327', 'Carmela Carroll', 'reinger.rafaela@schmeler.com', '+63421824078', '58388 Gwen Summit\nO\'Connerhaven, NM 27319-5452', '724-191-743-452', 'CS35893090', 'pending', '2023-07-28 03:44:33', '2024-12-29 11:09:37'),
(35, 'Royal Industrial Supply Trading 388', 'Mrs. Fae Beahan', 'jgutmann@wehner.org', '+63905055574', '635 Hackett Mountain Apt. 078\nWest Magali, SC 82871', '135-754-421-906', 'CS25160147', 'inactive', '2024-01-20 20:51:29', '2024-06-23 18:21:16'),
(36, 'Philippine Building Materials Company 217', 'Waldo Quigley', 'kulas.clarissa@hintz.info', '+63680117514', '587 Maegan Square\nSouth Doyle, MA 77811', '099-264-734-143', 'CS51948788', 'pending', '2025-04-08 11:23:49', '2024-08-25 08:15:39'),
(37, 'Manila Construction Solutions Corporation 585', 'Federico Ledner', 'schuster.odie@green.com', '+63798459567', '4142 Hermann Station Suite 083\nWittingview, KS 37038', '594-342-752-235', 'CS19435589', 'inactive', '2024-06-15 20:15:19', '2025-03-20 16:16:38'),
(38, 'Royal Construction Supply Trading 077', 'Dangelo Bergstrom', 'xhintz@abshire.org', '+63311218655', '63911 Magnus Hill Apt. 200\nJordynberg, NV 73386', '927-920-737-642', 'CS45392372', 'pending', '2025-04-27 19:22:05', '2025-04-12 06:52:34'),
(39, 'Asian Hardware Corporation 130', 'Prof. Consuelo Mills IV', 'moshe.hintz@wilkinson.com', '+63689136356', '7494 Verna Fort Apt. 382\nFlossieview, DC 95767-3540', '739-713-882-285', 'CS33888919', 'inactive', '2024-05-14 05:32:37', '2024-09-13 05:39:17'),
(40, 'Asian Construction Solutions Enterprises 748', 'Esteban Grant IV', 'nitzsche.hester@labadie.info', '+63848773492', '697 Johan Trail Apt. 977\nBednarborough, AR 35416-1483', '623-606-368-292', 'CS30352464', 'pending', '2023-08-12 12:44:58', '2024-08-25 12:57:04'),
(41, 'Pioneer Hardware Company 290', 'Mr. Milford Dietrich MD', 'vaughn14@dibbert.biz', '+63462945384', '8043 Haylee Flats\nNorth Savion, NH 87697-7167', '884-178-824-160', 'CS19019537', 'active', '2024-09-07 10:25:12', '2025-01-20 10:31:48'),
(42, 'Philippine Building Materials Trading 142', 'Nova O\'Reilly DVM', 'smitham.pearlie@altenwerth.biz', '+63740741759', '411 Addie Ranch Suite 619\nNorth Danny, SC 35611-3009', '700-075-883-023', 'CS61710910', 'active', '2025-01-20 07:29:43', '2024-06-21 06:42:45'),
(43, 'Mega Industrial Supply Enterprises 289', 'Otho Fritsch', 'vheaney@nienow.com', '+63987028921', '6438 Carson Canyon\nTrevionstad, NY 64057', '353-770-129-958', 'CS51459118', 'pending', '2023-09-22 17:39:23', '2025-03-18 08:52:48'),
(44, 'Royal Building Materials Inc. 865', 'Prof. Esther Huels', 'robel.veda@dare.com', '+63678363584', '5144 Flo Junction\nRatkeborough, NE 23673-5649', '901-030-653-367', 'CS20433315', 'inactive', '2023-11-19 12:25:41', '2024-08-07 20:28:02'),
(45, 'Pioneer Construction Supply Company 257', 'Rita Torphy', 'timmothy.sawayn@hansen.com', '+63388234904', '9157 Jasen Via\nHarveyburgh, NV 44547-5595', '010-572-310-429', 'CS25538726', 'pending', '2024-09-18 18:28:18', '2024-12-18 14:58:38'),
(46, 'Asian Construction Supply Inc. 841', 'Uriah DuBuque I', 'dpaucek@mckenzie.org', '+63455512573', '279 Kerluke Plaza\nAmanihaven, NV 85551', '577-319-976-885', 'CS45186670', 'pending', '2025-05-04 19:34:16', '2024-07-12 04:18:30'),
(47, 'Mega Hardware Inc. 523', 'Prof. Keith Green II', 'qdibbert@kuvalis.com', '+63484881822', '99900 Daren Green Apt. 766\nSouth Marielaville, AZ 26406', '132-891-685-868', 'CS15003617', 'pending', '2024-05-17 06:24:28', '2025-02-12 21:53:05'),
(48, 'Manila Building Materials Inc. 286', 'Samara Wyman', 'barrows.caroline@weber.net', '+63708120309', '69264 Sanford Plaza\nHandstad, OK 57675', '163-794-899-192', 'CS80372375', 'active', '2023-11-04 19:24:14', '2025-03-19 21:30:49'),
(49, 'Manila Industrial Supply Trading 074', 'Dr. Gennaro Zboncak', 'lynch.fern@jacobi.info', '+63810050228', '9238 Marie Lock\nWest Elmore, AR 48353-5611', '891-460-601-506', 'CS13065375', 'pending', '2024-10-18 18:40:40', '2024-10-15 07:15:57'),
(50, 'Pioneer Hardware Company 047', 'Darius Daniel', 'okshlerin@waters.com', '+63957349400', '9642 Quitzon Unions\nNew Peggiebury, NY 56486-5359', '561-882-951-375', 'CS35262542', 'pending', '2024-06-06 22:15:37', '2024-12-31 19:46:22'),
(51, 'Metro Construction Solutions Inc. 212', 'Tierra Green MD', 'tyree74@schmeler.com', '+63292338997', '402 Turner Corner\nLamarfurt, MI 53837', '836-873-091-667', 'CS39616256', 'active', '2025-04-22 09:40:20', '2024-10-07 07:12:19'),
(52, 'Metro Building Materials Inc. 245', 'Rickey Cartwright', 'jdurgan@schuppe.biz', '+63815153976', '42218 Jazmyne Cliffs\nMervinchester, IN 55332-4470', '513-209-317-169', 'CS01893185', 'active', '2024-01-06 01:53:25', '2024-07-13 07:55:29'),
(53, 'Mega Construction Solutions Inc. 464', 'Mose Harber', 'spencer.augustine@roberts.info', '+63281095387', '18897 Ray Forks\nHarveyton, HI 41222', '263-397-488-111', 'CS24099295', 'inactive', '2025-05-28 09:27:16', '2024-06-27 00:52:58'),
(54, 'Royal Construction Supply Company 311', 'Jakob Zemlak', 'agoyette@romaguera.com', '+63542021824', '101 Cassandra Motorway\nEast Jordynmouth, OR 77732-3781', '502-129-575-380', 'CS03082915', 'pending', '2024-05-15 20:24:03', '2024-07-31 13:27:08'),
(55, 'Philippine Hardware Inc. 926', 'Noble Mante', 'rbarton@witting.org', '+63367403955', '6980 Aric Radial\nXanderburgh, MT 78074-9634', '015-124-291-632', 'CS61140546', 'pending', '2024-08-05 22:44:39', '2025-03-02 18:50:56'),
(56, 'Pacific Hardware Enterprises 162', 'Rhianna Rempel', 'heller.yvette@walter.com', '+63565806759', '513 Eldridge Rapid\nNew Valerieburgh, VA 65390-2003', '238-702-721-736', 'CS66863751', 'active', '2023-11-28 09:28:11', '2025-01-10 19:57:07'),
(57, 'Pioneer Construction Supply Trading 303', 'Katarina Kreiger', 'jalyn58@klocko.com', '+63858107382', '79170 Seth Lane Suite 092\nWeimannstad, NY 77802', '945-544-774-449', 'CS82173385', 'pending', '2024-07-03 13:56:03', '2024-11-21 22:34:37'),
(58, 'Pacific Construction Solutions Company 518', 'Kenneth Heidenreich Sr.', 'tlangosh@miller.info', '+63672118688', '61559 Ignacio Avenue Apt. 727\nTrantowfort, AL 45445', '691-414-745-399', 'CS23332740', 'inactive', '2024-05-06 14:33:45', '2024-07-30 08:41:18'),
(59, 'Metro Construction Solutions Inc. 826', 'Ashlynn Fay II', 'marshall65@leannon.com', '+63657597849', '22714 Trisha Overpass\nBretstad, LA 06128-4197', '244-839-588-419', 'CS76412115', 'pending', '2023-09-08 21:44:56', '2024-09-01 18:01:44'),
(60, 'Royal Construction Solutions Trading 821', 'Alycia Sanford PhD', 'ufritsch@kuhn.org', '+63996692457', '5083 Schoen Vista Suite 640\nSouth Maybellmouth, MO 97032', '706-230-715-355', 'CS93582616', 'pending', '2023-07-22 12:37:34', '2025-03-01 01:03:54'),
(61, 'Royal Construction Supply Corporation 214', 'Flavie Hodkiewicz', 'durward.skiles@jakubowski.com', '+63896612770', '9077 Edward Well Suite 120\nNew Linaport, IL 37654', '360-572-622-657', 'CS23738805', 'inactive', '2024-12-31 22:22:41', '2024-07-11 02:54:23'),
(62, 'Manila Hardware Company 894', 'Dr. Joan Purdy', 'alivia.mclaughlin@schultz.com', '+63372685325', '378 Orval Forges Apt. 817\nNew Liliane, MN 91269-6839', '556-206-004-253', 'CS51837481', 'pending', '2025-04-30 01:31:38', '2025-03-19 20:52:08'),
(63, 'Pioneer Construction Supply Trading 385', 'Mrs. Nicole Zemlak', 'hdeckow@little.com', '+63546607371', '661 Kadin Green\nHarveystad, MD 03132-4230', '047-697-794-943', 'CS27084371', 'inactive', '2024-01-05 14:37:03', '2024-08-25 17:08:15'),
(64, 'Mega Building Materials Inc. 233', 'Eliza Skiles DVM', 'eernser@bahringer.info', '+63288687461', '236 Olaf Burgs Apt. 888\nNorth Jermain, MI 77406-7057', '971-566-952-303', 'CS51723228', 'pending', '2023-07-22 12:25:51', '2025-04-14 11:32:42'),
(65, 'Royal Industrial Supply Trading 731', 'Selmer Jacobi', 'oromaguera@abbott.info', '+63239264085', '6940 Eichmann Track\nEast Mathildehaven, FL 18414-9043', '363-200-195-579', 'CS20274665', 'pending', '2023-08-03 10:10:28', '2025-01-15 07:01:16'),
(66, 'National Building Materials Corporation 995', 'Deshaun Jaskolski', 'ehand@turner.org', '+63866682583', '90791 Beer Mountain Suite 268\nNorth Edaborough, NM 91284', '417-028-673-632', 'CS15426679', 'inactive', '2024-12-11 21:04:52', '2025-05-18 12:51:11'),
(67, 'National Construction Solutions Enterprises 687', 'Parker Romaguera', 'swolff@pacocha.net', '+63961603684', '481 Torrey Ridge\nKaciburgh, SD 36170-3079', '744-195-727-994', 'CS37608467', 'active', '2024-06-09 06:02:02', '2025-05-16 15:01:58'),
(68, 'Royal Building Materials Inc. 176', 'Mr. Eusebio Daniel', 'louvenia54@bartoletti.net', '+63308195614', '861 Isaias Knolls Apt. 499\nFritschchester, OK 86103-4289', '808-096-436-160', 'CS46630656', 'active', '2024-09-01 14:37:04', '2024-09-03 05:40:49'),
(69, 'Pioneer Hardware Enterprises 299', 'Peggie Hoeger', 'wiegand.reyes@mraz.com', '+63395886163', '41872 Rolando Meadow\nSouth Kiarrastad, VA 23087', '212-122-619-215', 'CS77778929', 'inactive', '2023-10-12 00:55:02', '2025-03-10 01:50:11'),
(70, 'Mega Building Materials Company 946', 'Hettie Jenkins', 'mosciski.suzanne@kihn.com', '+63805153697', '784 Rodrigo Junction Apt. 445\nHandside, IL 22542-1448', '714-061-308-746', 'CS12932626', 'pending', '2024-12-04 06:52:58', '2025-01-23 18:57:31'),
(71, 'Philippine Industrial Supply Company 432', 'Johnny Wuckert', 'eveline.bernier@roberts.biz', '+63347575137', '97049 Schuster Terrace Apt. 960\nLake Ana, MI 26039', '513-662-320-428', 'CS09314569', 'inactive', '2024-02-09 09:40:04', '2024-07-12 19:28:07'),
(72, 'Mega Construction Supply Inc. 905', 'Ms. Leatha Davis', 'salvatore51@grant.com', '+63984899009', '83971 Becker Tunnel\nLake Harry, MN 72145-2684', '365-458-068-079', 'CS03608398', 'pending', '2024-02-05 10:03:42', '2024-09-06 21:54:39'),
(73, 'Royal Construction Solutions Inc. 092', 'Ms. Minnie Wyman', 'botsford.ole@langworth.com', '+63971548188', '97179 Darrel Island\nMelyssaland, LA 90323-3499', '490-327-718-645', 'CS95142886', 'pending', '2023-08-10 16:08:18', '2024-08-02 12:47:02'),
(74, 'Metro Construction Supply Inc. 583', 'Maryse Kozey', 'morton94@rice.biz', '+63478309540', '79982 Schuster Camp\nRobertsside, TN 56021', '565-733-201-028', 'CS21193982', 'active', '2025-04-25 00:01:28', '2024-07-31 01:42:29'),
(75, 'Asian Building Materials Corporation 419', 'Prof. Shaun Williamson II', 'tmarquardt@rempel.com', '+63737433741', '8236 Stanford Parks Apt. 749\r\nWest Phoebe, NH 82510-4569', '302-621-456-376', 'CS89700677', 'inactive', '2024-10-16 09:35:29', '2025-06-05 07:33:41'),
(76, 'Metro Hardware Corporation 267', 'Joaquin Donnelly', 'gmcglynn@rohan.org', '+63769837890', '978 Jewell Isle\nEast Camronton, MA 88030-7043', '324-480-482-254', 'CS98444380', 'inactive', '2024-12-28 00:39:41', '2024-06-24 01:56:55'),
(77, 'Philippine Building Materials Corporation 896', 'Alysa Kunze Jr.', 'rosenbaum.kianna@brakus.com', '+63273210539', '73095 Smitham Island\nSchummview, ME 42210', '510-514-541-475', 'CS72528479', 'inactive', '2024-05-23 10:26:32', '2024-08-23 08:31:47'),
(78, 'Pacific Industrial Supply Inc. 063', 'Jan Cummings', 'hilpert.breanne@nikolaus.com', '+63337084297', '48427 Montana Estates Suite 865\nPort Yvette, ND 11435', '538-650-895-202', 'CS06553139', 'pending', '2024-02-22 02:39:31', '2025-03-22 14:47:14'),
(79, 'Manila Hardware Inc. 936', 'Prof. Monroe O\'Kon I', 'cleora.kling@robel.com', '+63822081610', '82045 Cortney Street\nNorth Emmie, NM 13152', '249-622-024-323', 'CS29938465', 'inactive', '2025-01-16 10:33:36', '2025-02-21 21:54:17'),
(80, 'National Construction Supply Enterprises 871', 'Mr. Adolphus McClure II', 'hfriesen@kutch.com', '+63786475496', '8147 Reynolds Stravenue Suite 686\nPort Valentin, CT 49412', '748-474-762-924', 'CS23699501', 'inactive', '2024-06-14 14:32:00', '2024-07-18 13:00:17'),
(81, 'Pacific Industrial Supply Corporation 561', 'Noel Ortiz', 'murphy.aubree@schneider.com', '+63441322696', '504 Schinner Center\nMoenmouth, CA 49544', '745-466-515-963', 'CS13701807', 'inactive', '2024-03-31 01:57:41', '2025-04-17 05:43:41'),
(82, 'Makati Construction Supply Corporation 292', 'Lennie Prohaska', 'emie87@howell.com', '+63934703138', '232 Erica Extension Suite 683\nEleonorehaven, OR 74851-1676', '359-184-796-125', 'CS30878074', 'active', '2025-04-19 22:07:02', '2024-08-23 20:36:26'),
(83, 'Philippine Construction Solutions Trading 876', 'Rosendo Fisher', 'lakin.francisca@conroy.com', '+63780738193', '714 Rita Glens\nJenningsfurt, NJ 84367', '888-790-322-644', 'CS83284848', 'pending', '2025-01-07 21:24:55', '2025-04-04 18:47:48'),
(84, 'National Hardware Company 234', 'Dr. Jeffrey Hettinger', 'ekassulke@tremblay.biz', '+63335846690', '3978 Dietrich Manor\nPollyborough, TX 39970', '242-974-620-669', 'CS15204916', 'pending', '2023-06-06 12:37:19', '2025-04-05 15:29:37'),
(85, 'Royal Construction Supply Trading 109', 'Aurelie Kris V', 'matilde.ruecker@damore.com', '+63920119900', '7758 Holly Loaf\nEast King, TN 04742', '266-464-590-048', 'CS07139872', 'active', '2024-10-24 09:18:51', '2025-05-10 23:05:28'),
(86, 'Asian Industrial Supply Trading 782', 'Johnny Mann', 'bdeckow@rowe.com', '+63859911677', '67940 Rohan Villages Suite 565\nDionbury, DE 29483', '366-336-213-597', 'CS67408202', 'inactive', '2024-10-06 11:04:57', '2024-12-15 20:18:26'),
(87, 'Mega Construction Solutions Company 434', 'Jasper Schimmel', 'jbraun@beahan.com', '+63580952386', '8788 Ozella Lights Apt. 702\nAmieshire, AK 97739-1162', '071-773-484-528', 'CS24605970', 'active', '2024-01-24 14:55:32', '2024-11-20 17:30:58'),
(88, 'Pacific Building Materials Corporation 790', 'Larue Ratke', 'micah97@conn.biz', '+63238059235', '5242 Cade Trail Apt. 540\nWest Ola, NC 87860-3504', '592-150-286-964', 'CS29685234', 'pending', '2025-02-09 07:58:21', '2024-09-06 19:09:42'),
(89, 'Metro Industrial Supply Corporation 653', 'Fabian Lesch', 'konopelski.fanny@champlin.com', '+63448623492', '433 Reichert Meadows\nNelliebury, MN 58391', '999-767-847-486', 'CS19291679', 'pending', '2023-07-02 19:48:01', '2024-09-26 10:26:38'),
(90, 'Asian Industrial Supply Corporation 138', 'Ms. Amelia Little', 'ophelia.towne@ebert.com', '+63890782366', '140 Amanda Gardens\nCorwinberg, ND 37314', '837-264-546-151', 'CS05771378', 'pending', '2023-12-03 09:12:42', '2025-04-12 00:54:09'),
(91, 'Pacific Construction Solutions Trading 911', 'Litzy Koch', 'price@emard.net', '+63672402663', '73258 Keenan Square\nSouth Madonnastad, GA 61172', '494-001-410-700', 'CS51827864', 'pending', '2023-12-17 05:09:05', '2025-02-24 21:49:41'),
(92, 'Pioneer Hardware Company 766', 'Casandra Blick', 'kuphal.jackson@abshire.com', '+63917801628', '9155 Kassulke Trail\nAbbottfort, AK 59366', '900-332-113-221', 'CS48626690', 'inactive', '2024-08-08 00:15:33', '2025-03-28 04:07:18'),
(93, 'Manila Construction Supply Corporation 248', 'Kris Kuhn', 'ybrakus@west.com', '+63403332009', '8929 Grimes Courts Suite 733\nPort Mohamed, VT 53407', '382-348-410-635', 'CS18636427', 'active', '2023-12-11 19:42:09', '2024-06-20 00:05:40'),
(94, 'Manila Hardware Enterprises 223', 'Mr. Jefferey Tromp', 'willy21@gulgowski.com', '+63848348503', '779 Ian Wells\nTressieville, MT 19722-3023', '132-147-125-493', 'CS43314182', 'active', '2025-03-07 07:29:18', '2025-01-15 00:32:29'),
(95, 'Manila Industrial Supply Trading 006', 'Carley Dicki', 'gpollich@collier.com', '+63648278730', '268 Feest Plain\nEast London, NH 43874', '203-588-987-355', 'CS07369032', 'inactive', '2024-02-05 02:24:53', '2024-08-04 13:19:36'),
(96, 'National Construction Supply Enterprises 436', 'Albertha Brakus', 'gabe37@gleason.com', '+63798578586', '517 Renner Unions Suite 294\nNew Sigridburgh, CA 86676', '776-803-618-410', 'CS13612559', 'inactive', '2024-03-08 22:45:55', '2024-07-21 04:10:40'),
(97, 'Metro Hardware Company 670', 'Pablo Schneider I', 'bgraham@hintz.com', '+63394515108', '88028 Blanda Wells Suite 268\nWest Lewis, WV 26490-6869', '769-528-720-581', 'CS20474136', 'pending', '2025-04-03 20:56:52', '2025-02-09 05:43:02'),
(98, 'Mega Construction Supply Company 623', 'Benedict Denesik', 'kessler.gus@mosciski.net', '+63359584142', '657 Upton Land Apt. 500\nYasminfort, VT 49384-7690', '243-708-214-935', 'CS32582437', 'active', '2024-04-03 07:34:56', '2025-04-13 13:58:43'),
(99, 'Philippine Building Materials Company 276', 'Gabriella Moen', 'wiley.kunde@mills.org', '+63358628701', '5072 Mina Pines Apt. 715\nNew Loriville, AK 28741', '548-441-624-188', 'CS34580488', 'pending', '2023-06-09 06:50:07', '2025-03-11 21:07:28'),
(100, 'Makati Construction Supply Trading 498', 'Don Stiedemann Sr.', 'federico.altenwerth@shanahan.info', '+63890603600', '29554 Hermann Springs Suite 424\nMaddisonfort, SC 47935', '888-933-876-317', 'CS86268484', 'inactive', '2025-02-09 06:09:52', '2024-12-15 15:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invitations`
--

CREATE TABLE `supplier_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `invitation_code` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `due_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `response_notes` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invitation_materials`
--

CREATE TABLE `supplier_invitation_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_invitation_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'expense',
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `contract_id`, `date`, `description`, `amount`, `type`, `status`, `payment_method`, `reference_number`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-06-07', 'NAGBAYAD NA SILA!', 2340.00, 'income', 'completed', 'S', '090909', 'asdasdasd', '2025-06-05 08:19:19', '2025-06-05 08:19:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','employee','company') NOT NULL DEFAULT 'company',
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `username`, `password`, `user_type`, `force_password_change`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'Main Admin', 'admin1@example.com', '2025-06-04 19:30:10', 'mainadmin', '$2y$12$35DHCm8LOc7vdvDx7f.XOeBW3YaS.sZosU/tk8nB1c2EPTivfABmK', 'admin', 0, 'admin', 1, NULL, '2025-06-04 19:30:10', '2025-06-05 08:31:36', '2025-06-05 08:31:36'),
(2, 'Backup Admin', 'admin2@example.com', '2025-06-04 19:30:11', 'backupadmin', '$2y$12$ycC//LmSRzhr9qF8hxjxwubvQ79TSj8B6n4crvM7Ap.i2IU/.CWQ.', 'admin', 0, 'admin', 1, NULL, '2025-06-04 19:30:11', '2025-06-04 19:30:11', NULL),
(3, 'Catalina Thiel', 'roberts.vilma@example.org', '2025-06-04 19:30:13', 'qkub', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'TVK0R0euIc', '2024-07-29 11:42:15', '2024-12-05 04:42:45', '2025-02-13 04:14:42'),
(4, 'Marilou Koss', 'tjacobson@example.net', '2025-06-04 19:30:14', 'adele.grimes', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Ty1AA2zMp4', '2025-02-28 09:54:47', '2025-05-03 16:20:21', '2025-04-16 07:51:24'),
(5, 'Obie Hermiston', 'clementine.thiel@example.org', '2025-06-04 19:30:14', 'kzboncak', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'KZsZ9cl5nH', '2024-12-27 10:02:05', '2025-04-25 17:27:00', '2025-03-13 20:14:45'),
(6, 'Dr. Johnnie Smitham', 'mwisozk@example.net', '2025-06-04 19:30:14', 'jacinthe08', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'SqklHJBVx0', '2024-12-09 03:30:51', '2024-12-24 10:58:26', '2025-01-12 21:32:53'),
(7, 'Maybell Bergstrom', 'pat.toy@example.net', '2025-06-04 19:30:14', 'uullrich', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '6yCVAFj6dv', '2024-10-06 23:04:27', '2024-12-22 14:26:54', '2025-02-05 17:22:06'),
(8, 'Dr. Stephanie Rau I', 'hilda44@example.net', '2025-06-04 19:30:14', 'vwisoky', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'RFpUiT4bcF', '2024-08-21 14:02:54', '2024-10-13 00:13:10', '2024-11-08 08:43:12'),
(9, 'Ruby Prosacco', 'lia19@example.org', '2025-06-04 19:30:14', 'qvonrueden', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1dWNzpH8IT', '2024-09-07 11:43:23', '2025-04-02 17:10:51', '2024-10-12 19:07:26'),
(10, 'Mr. Garret Hansen', 'kgraham@example.com', '2025-06-04 19:30:14', 'cayla.keeling', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'fKYatK2oQU', '2024-09-22 17:56:53', '2024-10-08 23:17:21', '2025-03-01 17:40:12'),
(11, 'Naomi Sauer Jr.', 'knitzsche@example.org', '2025-06-04 19:30:14', 'veronica.auer', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '3MgasFx94g', '2025-04-24 04:16:27', '2025-05-30 21:51:34', NULL),
(12, 'Mrs. Jessyca Dicki IV', 'eichmann.melyna@example.org', '2025-06-04 19:30:14', 'destini97', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'J73krqvum1', '2024-10-22 07:22:16', '2025-02-14 01:52:07', '2024-11-14 14:22:52'),
(13, 'Angelica Spinka', 'alanis46@example.org', '2025-06-04 19:30:14', 'hadley61', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ckQ1M1TPLb', '2025-03-27 13:06:10', '2025-05-21 00:14:03', '2025-05-22 12:04:35'),
(14, 'Marcellus Schmidt', 'sblock@example.com', '2025-06-04 19:30:14', 'gsauer', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'R91pqn1nHp', '2024-07-17 05:08:43', '2024-09-05 19:00:12', '2024-11-08 12:34:30'),
(15, 'Ozella Boyle', 'aurelie46@example.net', '2025-06-04 19:30:14', 'carolina.stiedemann', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'keOkXtuuv9', '2025-05-13 14:23:44', '2025-05-29 19:21:34', '2025-05-31 02:44:35'),
(16, 'Gerda Breitenberg', 'weimann.bria@example.net', '2025-06-04 19:30:14', 'juana03', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Dxa9D5a8ec', '2024-12-29 01:09:22', '2025-01-29 02:04:02', '2025-04-06 23:16:32'),
(17, 'Demario Littel', 'hturcotte@example.org', '2025-06-04 19:30:14', 'sharon96', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1arva2eC6J', '2024-10-06 12:03:20', '2025-04-13 23:36:46', '2024-10-17 17:48:05'),
(18, 'Anya O\'Kon', 'elinor23@example.org', '2025-06-04 19:30:14', 'clare75', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '32qREQ0xNH', '2025-03-24 20:58:39', '2025-05-23 09:17:08', NULL),
(19, 'Monserrate Rosenbaum', 'tina41@example.com', '2025-06-04 19:30:14', 'olehner', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'NbEGc6sotV', '2024-07-13 20:17:38', '2024-08-16 17:09:46', '2024-11-16 04:36:01'),
(20, 'Prof. Rowena Schuster Jr.', 'astrid21@example.net', '2025-06-04 19:30:14', 'guiseppe24', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'to9n2Z101b', '2025-05-01 05:41:25', '2025-05-24 04:57:55', '2025-05-14 02:56:32'),
(21, 'Robyn Pfeffer', 'dovie.roberts@example.com', '2025-06-04 19:30:14', 'evie68', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'xQztFWD5uA', '2025-01-09 06:59:04', '2025-04-04 16:12:09', '2025-04-05 00:53:24'),
(22, 'Michael Hodkiewicz', 'brendon.hand@example.com', '2025-06-04 19:30:14', 'dorothy99', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'QOOBGylUzN', '2025-03-10 02:37:03', '2025-04-11 17:34:50', '2025-05-31 08:14:23'),
(23, 'Trever Moore', 'abernathy.jammie@example.net', '2025-06-04 19:30:14', 'joesph.mante', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ET4z8e0mZc', '2024-09-20 09:54:07', '2025-02-02 09:12:16', '2025-02-24 13:52:52'),
(24, 'Sean Willms V', 'marcellus.marquardt@example.net', '2025-06-04 19:30:14', 'stephan.herman', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'UlN21Jk5iV', '2025-04-04 09:57:48', '2025-05-04 13:50:37', '2025-04-19 18:33:40'),
(25, 'Domenic Zieme', 'xdoyle@example.com', '2025-06-04 19:30:14', 'cheaney', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'GF1i5GgstT', '2025-01-05 15:44:59', '2025-02-11 19:36:23', '2025-05-27 22:40:26'),
(26, 'Armand Emmerich', 'mmarks@example.com', '2025-06-04 19:30:14', 'tschaden', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '0ZakmRcMsa', '2024-12-01 04:29:10', '2025-05-11 17:38:31', '2025-03-29 12:01:46'),
(27, 'Myriam Renner', 'colton25@example.org', '2025-06-04 19:30:14', 'nico24', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'PqykhkpQRJ', '2024-09-20 19:16:57', '2024-10-30 18:53:21', '2024-10-10 19:17:44'),
(28, 'Gisselle Hettinger', 'ryan.carroll@example.net', '2025-06-04 19:30:14', 'sharon.jones', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'QDNUxnnDdm', '2025-06-03 20:08:21', '2025-06-03 23:24:47', '2025-06-04 12:51:00'),
(29, 'Garry Kreiger', 'jakubowski.ansel@example.net', '2025-06-04 19:30:14', 'roob.itzel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'cWfsnEiZ77', '2025-01-12 18:30:55', '2025-04-08 14:03:43', '2025-05-12 07:28:48'),
(30, 'Elijah Kshlerin', 'lkozey@example.com', '2025-06-04 19:30:14', 'rice.bridgette', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'hzNmqEGsiW', '2025-02-26 12:08:05', '2025-06-03 19:43:23', '2025-04-19 05:44:08'),
(31, 'Stefan O\'Connell', 'abernathy.leatha@example.com', '2025-06-04 19:30:14', 'llewellyn73', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'wMFFBZl6Xt', '2025-06-04 18:58:18', '2025-06-04 19:06:52', '2025-06-04 19:12:52'),
(32, 'Prof. Antonetta Jerde V', 'jrau@example.com', '2025-06-04 19:30:14', 'otha99', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'VAyae7oC9v', '2024-10-22 07:59:02', '2025-02-24 08:36:05', '2025-06-02 12:38:48'),
(33, 'Miss Jayda Balistreri', 'rfahey@example.com', '2025-06-04 19:30:14', 'leon.hills', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'SM4VaMVm5Y', '2024-07-08 10:22:00', '2025-06-04 15:23:13', NULL),
(34, 'Franco Dibbert', 'moore.meda@example.com', '2025-06-04 19:30:14', 'wthiel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'JzfwSWMiBn', '2025-02-09 20:18:29', '2025-03-11 10:20:18', NULL),
(35, 'Prof. Destiny Wyman', 'marley58@example.org', '2025-06-04 19:30:14', 'eblick', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'uZvKwGRmod', '2025-05-09 20:59:34', '2025-06-03 10:56:59', '2025-05-17 15:28:14'),
(36, 'Cassie Torp', 'cremin.harrison@example.com', '2025-06-04 19:30:14', 'steuber.solon', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '7rkn42er78', '2025-03-26 11:45:14', '2025-04-03 22:23:08', '2025-05-31 20:08:46'),
(37, 'Celia Ondricka', 'brandon.zulauf@example.net', '2025-06-04 19:30:14', 'pamela52', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'n17716onk7', '2024-09-09 21:08:38', '2025-06-01 22:44:12', '2024-10-03 06:06:50'),
(38, 'Dana Frami', 'terry.zoey@example.net', '2025-06-04 19:30:14', 'kschowalter', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ZfXX5Mti1m', '2025-05-22 09:25:05', '2025-06-02 05:07:52', '2025-05-22 12:31:36'),
(39, 'Prof. Cassie Shields DVM', 'uhickle@example.com', '2025-06-04 19:30:14', 'kkoepp', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Phf18SPiVX', '2024-07-03 20:13:56', '2024-09-15 17:01:45', '2024-09-10 11:18:05'),
(40, 'Luisa Cole', 'stanton.kayli@example.com', '2025-06-04 19:30:14', 'spacocha', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'FdP8CZm9Rh', '2025-02-18 13:21:16', '2025-04-01 18:10:25', '2025-05-25 03:36:31'),
(41, 'Savanna Franecki', 'isatterfield@example.com', '2025-06-04 19:30:14', 'theresa.lockman', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'EAsmOu03qg', '2024-12-21 11:16:26', '2025-04-16 07:06:10', '2024-12-27 09:03:47'),
(42, 'Ms. Delta Walsh', 'xcassin@example.net', '2025-06-04 19:30:14', 'tevin58', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Oy07GnObSp', '2025-01-23 04:52:27', '2025-04-13 23:17:20', '2025-06-04 08:30:52'),
(43, 'Irving Robel', 'maida27@example.org', '2025-06-04 19:30:14', 'hjacobson', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'x8PmDegqeS', '2025-05-10 02:09:04', '2025-05-12 23:08:19', '2025-05-21 22:02:21'),
(44, 'Prof. David Stamm Jr.', 'bernadette22@example.com', '2025-06-04 19:30:14', 'stokes.rodger', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'om63EhnnpG', '2024-12-10 08:26:46', '2025-01-10 06:33:19', '2025-03-25 13:07:20'),
(45, 'Halle O\'Connell', 'jeffrey.lockman@example.com', '2025-06-04 19:30:14', 'beatty.nayeli', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'gyFSQPNRve', '2025-03-20 23:55:37', '2025-05-09 22:02:36', '2025-05-16 09:05:58'),
(46, 'Conrad Reinger', 'simonis.katheryn@example.com', '2025-06-04 19:30:14', 'cordelia17', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'SbjFu8PT55', '2024-07-31 10:44:53', '2025-02-24 22:58:49', '2025-01-01 17:51:55'),
(47, 'Daphney Spinka', 'eileen.strosin@example.com', '2025-06-04 19:30:14', 'allan.russel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'POzVr4G6Oi', '2024-07-05 14:17:23', '2024-11-06 01:12:41', '2025-01-11 05:03:12'),
(48, 'Edyth Morar', 'fadel.kevin@example.com', '2025-06-04 19:30:14', 'mauricio76', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ucbQyw2kkQ', '2025-03-19 03:36:29', '2025-05-26 16:10:50', NULL),
(49, 'Otto Gutkowski II', 'ariel.bednar@example.com', '2025-06-04 19:30:14', 'beryl.damore', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'wd0eabz8sW', '2024-09-06 16:23:30', '2025-03-15 21:25:46', '2024-09-12 06:03:28'),
(50, 'Wilhelm Heidenreich', 'paula30@example.com', '2025-06-04 19:30:14', 'dallas.rolfson', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'u9Q5kBHJon', '2025-01-13 19:46:38', '2025-02-15 12:48:39', '2025-03-06 02:15:44'),
(51, 'Prof. Sheridan Runte III', 'gorczany.ivory@example.org', '2025-06-04 19:30:14', 'lesley60', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'jPHvj2NB7v', '2025-03-14 19:49:39', '2025-04-29 03:04:51', '2025-04-09 08:57:27'),
(52, 'Griffin Schultz', 'hhartmann@example.com', '2025-06-04 19:30:14', 'qwalsh', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'FLfPMwNca8', '2024-09-16 08:49:29', '2025-01-29 10:04:31', '2025-03-04 20:09:24'),
(53, 'Prof. Frieda Schumm DDS', 'dfadel@example.com', '2025-06-04 19:30:14', 'gconroy', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'QAuEjeB94X', '2025-01-22 10:24:38', '2025-02-26 11:35:39', NULL),
(54, 'Prof. Ellis Hansen PhD', 'ora.smitham@example.net', '2025-06-04 19:30:14', 'lowe.lelia', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'gwU34GX3ds', '2024-09-24 15:44:19', '2024-09-28 20:55:06', '2025-02-28 19:06:51'),
(55, 'Dr. Simeon Reichert V', 'runolfsson.mekhi@example.com', '2025-06-04 19:30:14', 'theodore74', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'uc62VljNH0', '2024-12-12 11:14:12', '2024-12-17 01:23:02', '2025-05-14 16:38:07'),
(56, 'Prof. Madelynn Kiehn PhD', 'yernser@example.org', '2025-06-04 19:30:14', 'lockman.edgardo', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'xtFL8UwaTD', '2025-04-14 04:09:56', '2025-05-18 04:06:20', '2025-05-18 22:11:14'),
(57, 'Prof. Tania Mitchell Sr.', 'glover.whitney@example.com', '2025-06-04 19:30:14', 'cassin.rylan', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'vROIQglt5e', '2024-07-08 06:09:36', '2025-06-01 00:21:48', '2025-04-21 14:22:37'),
(58, 'Jedidiah Bartell MD', 'bswift@example.com', '2025-06-04 19:30:14', 'alycia.predovic', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 's44aXAC1K9', '2025-02-20 00:05:28', '2025-03-03 15:15:30', '2025-04-10 19:35:22'),
(59, 'Dayna Cruickshank Sr.', 'shanny28@example.net', '2025-06-04 19:30:14', 'botsford.daniela', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'BbGY0NlylS', '2024-10-14 12:43:21', '2025-03-21 14:10:35', '2025-05-28 03:02:44'),
(60, 'Herminio Wilkinson', 'aniyah31@example.net', '2025-06-04 19:30:14', 'linda.baumbach', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1xrShHLaHL', '2024-09-21 06:54:37', '2025-05-30 04:32:24', '2025-02-24 03:56:24'),
(61, 'Kari Tromp', 'aiden.torphy@example.net', '2025-06-04 19:30:14', 'pascale.ankunding', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'GbtFXbfz0R', '2025-05-21 12:30:59', '2025-05-23 03:12:26', NULL),
(62, 'Prof. Ona Effertz IV', 'augustus.pouros@example.com', '2025-06-04 19:30:14', 'kuhn.shakira', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'AR7OzEy2Yu', '2024-06-13 17:40:38', '2025-02-02 06:50:14', '2025-06-01 05:48:14'),
(63, 'Bethany Kreiger V', 'esteban.nienow@example.com', '2025-06-04 19:30:14', 'beier.lyla', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '6xC2Vjm5ms', '2024-08-18 21:15:55', '2024-12-25 00:38:52', '2025-05-05 12:17:08'),
(64, 'Gregory Harris', 'hilda26@example.net', '2025-06-04 19:30:14', 'rickie10', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'S1MgqXx0Ul', '2025-03-16 16:25:47', '2025-06-01 18:19:47', '2025-05-30 13:56:51'),
(65, 'Mrs. Callie Franecki V', 'stehr.chaya@example.com', '2025-06-04 19:30:14', 'alexander10', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'wTIDXA7Hcs', '2025-04-01 15:23:49', '2025-04-24 12:11:18', '2025-05-19 11:47:47'),
(66, 'Jaren Morissette', 'marietta.grant@example.com', '2025-06-04 19:30:14', 'bernadette.gislason', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'q4jVeZVxpI', '2025-04-15 23:23:52', '2025-05-07 08:11:41', NULL),
(67, 'Mrs. Amiya Metz DDS', 'jarred.hand@example.com', '2025-06-04 19:30:14', 'theresia.spencer', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ko99ACkSxK', '2025-03-11 07:49:25', '2025-05-29 10:20:24', '2025-05-01 07:54:13'),
(68, 'Johanna Mills', 'jacinto48@example.org', '2025-06-04 19:30:14', 'kbosco', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '4fiFVIIhxZ', '2024-12-22 21:02:12', '2025-02-01 21:19:03', '2024-12-30 22:39:04'),
(69, 'Cayla Dickens DVM', 'kherman@example.org', '2025-06-04 19:30:14', 'fwalsh', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Wvw6Sbal1m', '2024-12-11 20:21:50', '2025-02-16 02:38:48', '2025-02-12 11:14:39'),
(70, 'Miss Maybell Fahey II', 'gussie39@example.com', '2025-06-04 19:30:14', 'blanda.cassidy', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '3e9y5RxAbD', '2024-09-17 20:48:24', '2025-02-11 08:41:36', NULL),
(71, 'Rasheed Spinka', 'jglover@example.org', '2025-06-04 19:30:14', 'maia82', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '9PIgEC63gG', '2024-09-26 18:18:58', '2025-04-06 17:03:58', '2025-03-03 18:24:31'),
(72, 'Bryce Gusikowski', 'hamill.kyla@example.net', '2025-06-04 19:30:14', 'marian60', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'kJ47Mosf5D', '2025-02-12 03:51:20', '2025-05-14 09:22:13', '2025-02-13 14:29:05'),
(73, 'Elenor Hintz', 'zachariah82@example.com', '2025-06-04 19:30:14', 'kory25', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'VpLDy1JxLC', '2024-10-09 07:21:59', '2024-12-24 00:57:41', '2025-02-18 15:01:42'),
(74, 'Julio Hyatt', 'vincent.williamson@example.com', '2025-06-04 19:30:14', 'nkessler', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '2bIub3cVn4', '2024-11-11 01:53:47', '2025-05-03 02:47:25', '2025-01-27 23:03:35'),
(75, 'Rasheed Mante', 'athena29@example.net', '2025-06-04 19:30:14', 'josie.runolfsson', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '6wCuHSXFik', '2025-05-16 12:28:32', '2025-05-17 19:55:46', '2025-05-17 19:37:31'),
(76, 'Miss Eryn Muller Jr.', 'kpredovic@example.org', '2025-06-04 19:30:14', 'wilderman.emmitt', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'rB7MuNR8E5', '2025-04-24 02:08:35', '2025-05-30 04:38:02', '2025-05-18 21:36:23'),
(77, 'Hyman Wehner III', 'yfeest@example.org', '2025-06-04 19:30:14', 'uhoppe', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'aOhNW5aa98', '2024-10-31 22:55:23', '2024-11-28 00:34:24', '2025-03-12 02:32:17'),
(78, 'Nola Bednar', 'trantow.william@example.net', '2025-06-04 19:30:14', 'ophelia.heidenreich', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'XyRMjqy6Gp', '2024-12-10 19:36:20', '2025-05-10 09:40:43', '2025-02-02 23:21:29'),
(79, 'Nathanael Donnelly', 'evalyn07@example.net', '2025-06-04 19:30:14', 'lolita.littel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'd6KcOFKuDO', '2024-07-23 02:40:26', '2024-11-01 01:17:24', '2025-02-18 23:36:50'),
(80, 'Ross Boehm', 'schuster.lisa@example.com', '2025-06-04 19:30:14', 'dayton38', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'iIhvV5LjYu', '2024-08-10 15:48:44', '2025-04-14 05:59:21', NULL),
(81, 'Ms. Hope Pollich', 'zbalistreri@example.org', '2025-06-04 19:30:14', 'hirthe.cheyenne', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'CwAwagoazB', '2024-07-11 01:13:23', '2024-12-29 00:51:28', '2025-06-04 15:29:19'),
(82, 'Dr. Korey Huel', 'annetta.breitenberg@example.org', '2025-06-04 19:30:14', 'kennith41', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ddN0jL35bl', '2024-11-23 00:06:18', '2025-01-11 22:57:06', '2025-01-16 08:11:45'),
(83, 'Andrew Hermiston', 'tatyana13@example.com', '2025-06-04 19:30:14', 'hartmann.felix', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'ZmqARwSscj', '2025-04-19 10:13:10', '2025-04-28 20:32:58', '2025-05-07 21:10:31'),
(84, 'Maria Kuhic', 'murray.clinton@example.org', '2025-06-04 19:30:14', 'princess.schmeler', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'oW0Vf2IJHW', '2025-01-11 06:25:52', '2025-02-01 03:04:12', '2025-04-13 21:07:03'),
(85, 'Prof. Millie Jones', 'alindgren@example.net', '2025-06-04 19:30:14', 'marcia.corkery', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1PCqx5smeJ', '2024-10-02 20:38:07', '2024-11-02 12:28:06', '2024-10-05 11:40:16'),
(86, 'Corene Hamill', 'hayes.maybell@example.net', '2025-06-04 19:30:14', 'ernser.chanelle', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'nfj1SLs5na', '2024-06-17 05:42:18', '2025-04-11 07:19:44', NULL),
(87, 'Mr. Jerald Huel', 'douglas.murazik@example.net', '2025-06-04 19:30:14', 'ava.johnston', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '88Nq4dyOMj', '2025-05-16 15:05:18', '2025-05-26 11:41:54', '2025-05-20 19:20:01'),
(88, 'Mrs. Amiya Bode II', 'cayla79@example.com', '2025-06-04 19:30:14', 'gunnar15', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'KWxdmRW8j6', '2024-08-07 12:23:12', '2024-11-20 10:59:56', '2024-12-08 08:17:29'),
(89, 'Alice Rohan', 'collier.lina@example.net', '2025-06-04 19:30:14', 'ona.altenwerth', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'tY5ejMNQll', '2025-03-03 00:14:54', '2025-05-14 00:22:41', '2025-04-15 01:43:57'),
(90, 'Dr. Hettie Adams', 'durward.spinka@example.com', '2025-06-04 19:30:14', 'odessa34', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '4KyH24I3eT', '2024-12-14 16:53:08', '2025-05-26 16:38:52', '2025-03-12 04:16:48'),
(91, 'Esmeralda Kutch DVM', 'ikris@example.org', '2025-06-04 19:30:14', 'jaclyn.bechtelar', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'D3DRIUSq65', '2025-02-11 07:23:31', '2025-02-23 10:16:18', '2025-04-29 20:01:51'),
(92, 'Modesto Huel', 'haven.casper@example.org', '2025-06-04 19:30:14', 'sadye.heaney', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'EsFgDMiaFb', '2024-11-29 09:44:46', '2025-04-19 14:59:25', '2025-02-22 18:18:24'),
(93, 'Mr. Jalon Maggio', 'vbotsford@example.org', '2025-06-04 19:30:14', 'schiller.eden', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '4RfpO6x2eN', '2025-05-17 06:09:22', '2025-05-31 05:48:36', '2025-06-03 21:59:11'),
(94, 'Jaren Homenick DVM', 'kareem85@example.com', '2025-06-04 19:30:14', 'thurman.lindgren', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'wQKMSB8g4q', '2024-07-13 16:01:58', '2024-11-04 20:57:05', '2024-07-29 05:56:16'),
(95, 'Evert Gutkowski', 'pmarks@example.com', '2025-06-04 19:30:14', 'kulas.judson', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'anWMCiE507', '2025-04-08 13:57:18', '2025-05-20 16:37:10', '2025-04-08 19:58:36'),
(96, 'Ms. Maryam Wolf', 'thea.crooks@example.net', '2025-06-04 19:30:14', 'lockman.giovanna', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'NSqTZ2xLON', '2025-03-22 09:18:45', '2025-03-27 21:57:24', NULL),
(97, 'Dr. Victor Ratke III', 'ztrantow@example.net', '2025-06-04 19:30:14', 'kihn.tyrel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'pt1mbDLi0T', '2024-12-08 19:22:18', '2025-04-05 17:44:07', '2024-12-25 12:42:44'),
(98, 'Destinee Medhurst', 'anthony17@example.net', '2025-06-04 19:30:14', 'cole.minerva', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'r6jzrzkLUR', '2025-03-29 06:19:17', '2025-05-24 01:08:46', '2025-05-23 04:53:59'),
(99, 'Shannon Moore', 'fritsch.melvin@example.org', '2025-06-04 19:30:14', 'zelda03', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Ut9UD5OTWg', '2024-07-14 07:36:38', '2025-02-21 03:12:49', '2025-01-24 00:01:41'),
(100, 'Maximillian Leannon', 'lbogan@example.com', '2025-06-04 19:30:14', 'karina.connelly', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Hycu22VI87', '2024-09-06 20:45:56', '2025-02-27 19:29:26', '2024-10-27 23:04:05'),
(101, 'Lurline Cole', 'garry25@example.org', '2025-06-04 19:30:14', 'salvatore.lockman', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'HJG0NZJAu8', '2025-04-06 03:56:46', '2025-05-19 06:40:52', '2025-05-25 02:19:46'),
(102, 'Mr. John Kuvalis MD', 'mclaughlin.hayden@example.net', '2025-06-04 19:30:14', 'howe.rick', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1LxQe1kQq1', '2024-11-01 21:48:35', '2025-04-21 22:17:58', NULL),
(103, 'Miss Alicia Connelly V', 'herminia48@example.com', '2025-06-04 19:30:17', 'oschaden', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'cEjrXPJm7u', '2025-04-21 23:54:11', '2025-06-03 14:19:30', '2025-05-30 22:30:11'),
(104, 'Dr. Isaias Muller III', 'suzanne.bernier@example.org', '2025-06-04 19:30:17', 'sedrick43', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '4ozfmeW9A1', '2024-11-11 09:26:22', '2025-02-24 10:37:07', '2025-05-17 19:52:19'),
(105, 'Carter Fisher Sr.', 'olson.dorothea@example.com', '2025-06-04 19:30:17', 'blair.turner', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'jRzj5sL6Sq', '2024-07-11 19:40:11', '2024-07-21 13:21:32', NULL),
(106, 'Dennis Jacobi', 'samantha60@example.com', '2025-06-04 19:30:17', 'phills', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'SiILyK7KqZ', '2025-02-20 09:01:25', '2025-05-03 16:41:52', '2025-05-01 08:47:52'),
(107, 'Jaren Keebler', 'grimes.frida@example.net', '2025-06-04 19:30:17', 'jwalter', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '1fwrET6qDC', '2024-10-17 18:32:48', '2024-12-26 11:02:55', '2025-02-07 17:01:19'),
(108, 'Ike Bins', 'preynolds@example.org', '2025-06-04 19:30:17', 'ryley23', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'V9pcknBQVl', '2025-03-09 00:26:32', '2025-03-24 21:27:18', '2025-04-04 21:41:36'),
(109, 'Prudence Ondricka', 'ervin.renner@example.net', '2025-06-04 19:30:17', 'jordane07', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'K7bywjGum8', '2024-09-12 03:40:31', '2025-04-06 15:51:10', '2024-10-06 20:49:37'),
(110, 'Samara O\'Conner', 'amira.pfeffer@example.org', '2025-06-04 19:30:17', 'beth34', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'aldsvYmm0u', '2024-09-11 08:36:30', '2024-09-13 08:48:21', '2024-11-05 14:18:14'),
(111, 'Keon Kuhn', 'zblock@example.org', '2025-06-04 19:30:17', 'klocko.arno', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'miwsCqbvS8', '2025-04-23 16:01:14', '2025-05-21 11:51:49', '2025-05-22 17:34:04'),
(112, 'Foster Baumbach', 'athena56@example.org', '2025-06-04 19:30:17', 'crona.grover', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'JZUrFW9QDD', '2025-03-19 00:19:56', '2025-05-21 12:21:48', '2025-04-20 21:08:11'),
(113, 'Mrs. Lillian Bins', 'tlittle@example.net', '2025-06-04 19:30:17', 'aaufderhar', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'HYTSeimfu8', '2024-07-08 05:16:34', '2025-05-06 12:28:15', '2024-09-06 03:51:30'),
(114, 'Meda Heaney', 'simonis.kadin@example.com', '2025-06-04 19:30:17', 'fritsch.desiree', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '9ZMOObt19y', '2025-05-02 07:07:07', '2025-05-05 05:49:47', '2025-05-08 15:25:55'),
(115, 'Prof. Korey Gottlieb', 'ahowell@example.net', '2025-06-04 19:30:17', 'ruben14', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'opfvvYy0hv', '2024-08-08 04:55:47', '2025-04-10 09:04:48', '2025-02-14 22:05:29'),
(116, 'Autumn Moen V', 'vivian.bartoletti@example.com', '2025-06-04 19:30:17', 'maritza.grady', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '7elCSkb3ot', '2024-12-19 21:07:10', '2025-01-21 02:24:53', '2024-12-28 00:45:42'),
(117, 'Mertie Hane', 'olson.kylee@example.net', '2025-06-04 19:30:17', 'mohr.mitchel', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '2mZnbKPTVe', '2025-04-14 18:29:55', '2025-05-13 09:35:57', '2025-05-04 00:19:04'),
(118, 'Mckenna Stoltenberg', 'fhaley@example.net', '2025-06-04 19:30:17', 'izabella88', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'dDpk1pcV0V', '2025-05-29 05:40:13', '2025-05-29 06:24:24', '2025-06-01 00:34:17'),
(119, 'Vena Luettgen MD', 'uhudson@example.net', '2025-06-04 19:30:17', 'nicolas.ally', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'GwMRXBfkyM', '2025-01-21 14:12:49', '2025-02-23 10:11:12', '2025-04-26 05:37:07'),
(120, 'Jaiden Feest', 'swilkinson@example.com', '2025-06-04 19:30:17', 'johns.ericka', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'qQVGKcS3ok', '2024-12-01 16:33:23', '2025-04-24 18:26:23', NULL),
(121, 'Dr. Scarlett Fisher', 'strosin.maudie@example.net', '2025-06-04 19:30:17', 'jfeeney', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'FVugU2ZWpf', '2025-05-25 16:24:54', '2025-06-03 01:47:00', NULL),
(122, 'Mr. Nash Kautzer', 'marina45@example.net', '2025-06-04 19:30:18', 'giles.kulas', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'NuHrIVPqRn', '2025-02-23 23:31:02', '2025-04-03 13:00:17', NULL),
(123, 'Jeffery Cronin I', 'emely.hegmann@example.org', '2025-06-04 19:30:18', 'alek95', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'BsLogqCVjM', '2024-09-29 00:06:46', '2024-11-04 04:52:36', '2024-11-18 06:16:43'),
(124, 'Mr. Emery Dooley III', 'emile.windler@example.org', '2025-06-04 19:30:18', 'angeline20', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'Zan9z47vxw', '2024-10-07 03:25:03', '2024-11-06 16:32:15', '2024-12-14 01:19:22'),
(125, 'Ramon Macejkovic', 'zward@example.org', '2025-06-04 19:30:18', 'tommie.kulas', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'dFIZmX6q6b', '2024-12-20 08:54:03', '2024-12-20 13:05:57', '2025-05-23 08:46:37'),
(126, 'Lamar Hagenes', 'mason.bruen@example.org', '2025-06-04 19:30:18', 'gzemlak', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'mC4gjmz68a', '2025-04-22 15:28:42', '2025-05-05 03:54:09', '2025-05-21 12:23:46'),
(127, 'Skyla Carter', 'lora.franecki@example.net', '2025-06-04 19:30:18', 'herminio12', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'tEPIy3IxlK', '2024-12-15 10:50:08', '2025-03-24 09:24:48', '2025-02-12 09:18:26'),
(128, 'Estevan Rowe', 'thora50@example.net', '2025-06-04 19:30:18', 'hrice', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'YVdi9UMxDx', '2024-12-10 06:07:28', '2025-02-15 19:08:24', '2025-02-11 05:38:37'),
(129, 'Frida Toy', 'fkilback@example.com', '2025-06-04 19:30:18', 'qhane', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'JN3GuMjrzx', '2024-12-26 00:15:44', '2025-01-09 16:05:50', NULL),
(130, 'Alden Funk', 'srenner@example.net', '2025-06-04 19:30:18', 'hcummings', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'hsdF91rrav', '2025-01-09 02:55:13', '2025-04-09 12:58:19', '2025-02-25 04:26:45'),
(131, 'Evert Lindgren', 'schoen.earline@example.com', '2025-06-04 19:30:18', 'gprohaska', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, '468MMCv3rQ', '2025-01-27 17:52:07', '2025-01-28 08:12:04', NULL),
(132, 'Arjun Hill', 'harvey.felicity@example.org', '2025-06-04 19:30:18', 'schultz.noble', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'company', 0, 'user', 1, 'enF3CdqSfd', '2024-07-12 05:21:27', '2024-10-24 14:04:33', '2025-01-28 19:17:35'),
(133, 'Tito Morar', 'kelly88@example.org', '2025-06-04 19:30:19', 'stan.lebsack', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'vekEEh7ZhF', '2024-11-10 18:04:42', '2025-04-25 09:00:15', NULL),
(134, 'Alayna Dickinson', 'rbins@example.net', '2025-06-04 19:30:19', 'koch.pamela', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'fvpPaHKrH2', '2024-10-15 06:01:33', '2025-06-04 06:25:53', '2025-03-19 10:24:04'),
(135, 'Harmon Padberg', 'bo.larkin@example.com', '2025-06-04 19:30:19', 'rempel.marisol', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'aIsSkyp4ct', '2024-12-03 04:34:28', '2025-02-12 17:20:31', '2025-03-23 16:43:27'),
(136, 'Melvin Ledner', 'emard.sylvan@example.org', '2025-06-04 19:30:19', 'bosco.blaise', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, '3hP7IIxjrv', '2025-04-15 11:32:19', '2025-05-08 16:18:12', NULL),
(137, 'Dr. Taurean Lynch', 'fay.cydney@example.org', '2025-06-04 19:30:19', 'flavie88', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'qFVlJXveZr', '2024-07-08 02:28:36', '2025-02-16 10:53:15', '2024-12-19 05:41:53'),
(138, 'Greta Bode', 'jodie47@example.com', '2025-06-04 19:30:19', 'wpadberg', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'TjJ423CDBt', '2025-05-29 09:59:10', '2025-06-04 17:21:45', '2025-06-01 10:11:09'),
(139, 'Stephany Cormier', 'tiffany.funk@example.org', '2025-06-04 19:30:19', 'joanny.haag', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'pGD6z1HEkG', '2025-02-06 12:58:52', '2025-03-18 01:47:29', '2025-03-17 23:58:39'),
(140, 'Mrs. Gloria Ernser', 'skyla.klocko@example.com', '2025-06-04 19:30:19', 'uwalter', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'sQlBnFvlI4', '2025-03-28 22:59:08', '2025-06-03 20:09:40', '2025-04-30 18:17:32'),
(141, 'Miss Daniella Gerlach Jr.', 'nichole.deckow@example.com', '2025-06-04 19:30:19', 'nschoen', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'YoGv7UAfrR', '2024-10-20 06:00:30', '2025-05-31 11:03:34', '2025-01-29 02:54:35'),
(142, 'Carolyn Yost Jr.', 'lenore19@example.com', '2025-06-04 19:30:19', 'chester58', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'ELwP0bmNw9', '2025-02-27 11:39:03', '2025-05-23 08:34:32', '2025-05-17 13:33:48'),
(143, 'Ms. Ona Pagac', 'kreichert@example.net', '2025-06-04 19:30:19', 'hubert.schaefer', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'UaqeV9IR5d', '2025-05-20 02:59:17', '2025-06-04 05:08:57', '2025-06-02 04:02:17'),
(144, 'Daryl Harber I', 'vito.champlin@example.com', '2025-06-04 19:30:19', 'davis.celestino', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, '7SVC8ldz6D', '2025-03-30 11:23:29', '2025-04-10 18:01:45', NULL),
(145, 'Andre Kerluke II', 'geovanny.miller@example.net', '2025-06-04 19:30:19', 'breana38', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'Gwx36gxNGT', '2025-03-14 01:56:40', '2025-05-01 08:41:26', NULL),
(146, 'Laury Powlowski Sr.', 'dedrick.fahey@example.net', '2025-06-04 19:30:19', 'boehm.mattie', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'tDtJ9hjwJI', '2024-07-18 02:58:38', '2024-08-20 04:57:46', '2024-11-23 20:14:03'),
(147, 'Neoma Doyle', 'emayer@example.org', '2025-06-04 19:30:19', 'dana88', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'L2wACeMtOe', '2024-10-18 21:25:17', '2024-10-29 16:05:50', '2025-02-25 14:43:54'),
(148, 'Isabel Thompson IV', 'jewel.wisozk@example.org', '2025-06-04 19:30:19', 'lloyd96', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'W2N2dsnIeV', '2025-05-11 20:53:25', '2025-06-02 19:10:29', '2025-05-16 13:18:05'),
(149, 'Estella Runolfsdottir IV', 'uklocko@example.com', '2025-06-04 19:30:19', 'juliana67', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'iA13YbHVxl', '2024-10-30 03:39:48', '2025-04-27 04:13:21', '2025-01-12 23:50:50'),
(150, 'Mariane Corkery', 'mhowe@example.org', '2025-06-04 19:30:19', 'lenora19', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'g3aQn1qAjM', '2024-10-21 08:12:19', '2025-05-15 19:28:43', '2024-11-02 18:45:03'),
(151, 'Dr. Alena Glover Jr.', 'feest.gerda@example.org', '2025-06-04 19:30:19', 'earnest.daugherty', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'FgVDR5GTur', '2024-07-12 23:09:50', '2025-03-09 10:58:07', '2025-04-01 13:40:00'),
(152, 'Dr. Hailee Bahringer I', 'nitzsche.jaylon@example.net', '2025-06-04 19:30:19', 'ekreiger', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'RPGYfL3JWH', '2024-07-02 07:38:00', '2024-07-06 23:50:19', NULL),
(153, 'Guido Gleichner', 'veronica.huel@example.org', '2025-06-04 19:30:19', 'okuneva.yesenia', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'UdtFg1rjKh', '2025-02-03 06:03:37', '2025-04-05 17:28:20', '2025-05-30 01:37:32'),
(154, 'Clara Rau', 'gisselle.kub@example.net', '2025-06-04 19:30:19', 'orion.rau', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'fvBfApqBlw', '2024-06-29 02:04:21', '2024-08-16 03:04:26', '2024-08-27 20:24:15'),
(155, 'Dr. Fermin Grimes', 'marques04@example.org', '2025-06-04 19:30:19', 'hand.lyric', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'xMu2kMwlZS', '2025-05-25 07:31:30', '2025-05-25 11:03:43', '2025-06-02 00:35:38'),
(156, 'Darion Hansen', 'mckayla53@example.org', '2025-06-04 19:30:19', 'mparker', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'fJQ6RkLail', '2025-01-03 04:01:27', '2025-05-25 22:08:18', '2025-02-08 03:43:03'),
(157, 'Isabelle Marquardt', 'kunze.fausto@example.org', '2025-06-04 19:30:19', 'donato80', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, '3ikZhV3Bxg', '2024-12-27 13:47:23', '2025-04-27 18:11:32', '2025-03-26 21:39:01'),
(158, 'Roy Bashirian III', 'xbalistreri@example.org', '2025-06-04 19:30:19', 'larson.alba', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, '0siuqPNfEc', '2025-05-13 06:03:04', '2025-05-19 06:41:50', '2025-06-03 01:26:15'),
(159, 'Prof. Karl Bruen II', 'brendan79@example.net', '2025-06-04 19:30:20', 'mante.sigurd', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'DXA9yJ2Ain', '2024-12-22 09:15:35', '2025-05-07 06:03:12', '2025-02-27 02:41:30'),
(160, 'Emile Walter', 'elsa.reichel@example.net', '2025-06-04 19:30:20', 'kihn.taryn', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'XiTKpA54fo', '2024-10-06 02:15:45', '2024-12-31 14:29:03', '2025-02-03 06:43:22'),
(161, 'Prof. Ottis Wiza', 'marlon.welch@example.org', '2025-06-04 19:30:20', 'langosh.nat', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'XnkEYHHun7', '2024-12-09 13:54:04', '2025-01-11 17:21:21', '2025-02-15 12:39:37'),
(162, 'Rafael Schmeler Jr.', 'adelia.weissnat@example.com', '2025-06-04 19:30:20', 'schmidt.tamara', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'rMncPkIoN3', '2024-06-10 06:51:54', '2025-02-20 22:48:09', NULL),
(163, 'Nadia Zieme', 'selena16@example.org', '2025-06-04 19:30:20', 'vhaag', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'VujKXx6Qa0', '2024-11-08 06:05:50', '2025-01-04 12:49:04', '2024-12-04 13:21:22'),
(164, 'Dr. Jack Bashirian', 'wbailey@example.net', '2025-06-04 19:30:20', 'andreanne03', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'F1QZe6mR3v', '2025-05-09 12:19:10', '2025-05-16 08:00:28', '2025-05-31 21:01:37'),
(165, 'Mrs. Ellen Heaney', 'weber.carolyne@example.net', '2025-06-04 19:30:20', 'herzog.isaiah', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'VMyHXyPdhR', '2024-07-16 14:08:44', '2025-05-01 05:29:48', '2024-08-17 15:42:06'),
(166, 'Glen Waters', 'quinn.ernser@example.net', '2025-06-04 19:30:20', 'mckenna.cassin', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'LwmCIy1DdJ', '2025-05-11 22:01:55', '2025-05-30 15:52:46', '2025-05-17 08:41:50'),
(167, 'Miss Birdie Yundt', 'abbott.reymundo@example.org', '2025-06-04 19:30:20', 'christine16', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'fc7hcLR6Ur', '2024-08-20 01:48:53', '2025-03-23 11:30:35', '2024-08-25 19:17:01'),
(168, 'Dr. Cornell Jacobi MD', 'pgerlach@example.org', '2025-06-04 19:30:20', 'fadel.domenico', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'qkb5boRMDQ', '2024-10-21 05:49:15', '2024-12-16 17:40:39', NULL),
(169, 'Shawn Steuber', 'hulda77@example.org', '2025-06-04 19:30:20', 'chaim.miller', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'Llb2Ltb0PM', '2024-07-04 11:26:17', '2025-05-31 19:38:01', '2025-03-05 15:29:33'),
(170, 'Pansy Weissnat IV', 'ona70@example.com', '2025-06-04 19:30:20', 'rjerde', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'z7gzP1CfmA', '2024-11-30 04:31:10', '2025-01-11 09:01:54', '2025-02-03 15:17:02'),
(171, 'Nikolas Hudson', 'abahringer@example.org', '2025-06-04 19:30:20', 'macejkovic.elissa', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'IZTxISviwr', '2024-07-21 12:29:54', '2025-01-20 12:43:59', '2024-11-10 21:11:00'),
(172, 'Elena Schuppe', 'ulices59@example.net', '2025-06-04 19:30:20', 'salvador.schumm', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'dm2EMwuFRb', '2024-07-28 13:13:27', '2025-03-11 02:34:44', NULL),
(173, 'Mr. Foster Lubowitz PhD', 'franecki.vida@example.org', '2025-06-04 19:30:20', 'owuckert', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'S2QYiuAGeq', '2024-06-12 09:04:00', '2024-12-19 03:15:18', '2025-04-09 22:54:49'),
(174, 'Daniella Brekke', 'martin54@example.org', '2025-06-04 19:30:20', 'acarter', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'bqiedX6kFv', '2024-07-19 16:08:41', '2025-05-06 00:19:56', NULL),
(175, 'Chaim Welch', 'kade37@example.org', '2025-06-04 19:30:20', 'bailee54', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'zjWxxDG2xp', '2025-03-14 22:24:02', '2025-03-24 01:02:13', '2025-04-26 16:12:09'),
(176, 'Eunice Reichel', 'dereck31@example.org', '2025-06-04 19:30:20', 'zula.labadie', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, '8eQXUPm4VU', '2025-01-03 03:19:14', '2025-03-08 19:11:02', '2025-04-30 10:29:34'),
(177, 'Kasey O\'Keefe II', 'watsica.davion@example.net', '2025-06-04 19:30:20', 'ferry.laurence', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'O6yy3oyVsL', '2024-12-10 21:26:44', '2025-02-16 16:32:39', '2025-01-26 08:19:14'),
(178, 'Ms. Viva Harvey', 'gaylord.tod@example.net', '2025-06-04 19:30:20', 'king.amalia', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'KVHfrq6rOg', '2024-06-09 13:53:08', '2025-01-20 16:59:57', '2025-03-28 17:12:40'),
(179, 'Dr. Tia Miller Jr.', 'brohan@example.com', '2025-06-04 19:30:20', 'xschaden', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'y35TAjHcj6', '2025-01-25 15:04:04', '2025-02-18 11:55:15', '2025-01-28 05:19:56'),
(180, 'Lorenzo Shanahan', 'michelle34@example.net', '2025-06-04 19:30:20', 'abby.baumbach', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'yblZEOLnlF', '2025-02-16 05:48:44', '2025-05-17 12:05:16', '2025-03-31 10:32:27'),
(181, 'Gussie McCullough', 'rebeca.stehr@example.com', '2025-06-04 19:30:20', 'pinkie.gerhold', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'O4HX26T8Qa', '2025-04-18 08:21:46', '2025-05-19 08:33:25', '2025-05-18 14:06:18'),
(182, 'Ms. Aida Dickens', 'shaina80@example.org', '2025-06-04 19:30:20', 'arlo65', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 1, 'user', 1, 'fari0PlzXx', '2025-03-09 08:22:42', '2025-05-01 11:51:22', '2025-05-21 07:50:53'),
(183, 'Mark Feeney', 'dulce.schulist@example.com', '2025-06-04 19:30:30', 'jonas38', '$2y$12$5WvnrKGZCiLhycqRs1LXJuGTmSNGAzrEmShfsQo4badV0foUybaXu', 'employee', 0, 'user', 1, 'FoC5EGDeU0', '2025-01-19 13:30:52', '2025-01-25 02:25:11', '2025-05-08 09:28:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_foreign` (`user_id`),
  ADD KEY `activities_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_details_company_id_foreign` (`company_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_username_unique` (`username`),
  ADD KEY `companies_user_id_foreign` (`user_id`);

--
-- Indexes for table `company_docs`
--
ALTER TABLE `company_docs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_docs_company_id_index` (`company_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contracts_contract_id_unique` (`contract_id`),
  ADD KEY `contracts_contractor_id_foreign` (`contractor_id`),
  ADD KEY `contracts_client_id_foreign` (`client_id`),
  ADD KEY `contracts_property_id_foreign` (`property_id`);

--
-- Indexes for table `contract_items`
--
ALTER TABLE `contract_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_items_contract_id_foreign` (`contract_id`),
  ADD KEY `contract_items_material_id_foreign` (`material_id`),
  ADD KEY `contract_items_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_username_unique` (`username`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiries_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `inquiry_material`
--
ALTER TABLE `inquiry_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiry_material_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `inquiry_material_material_id_foreign` (`material_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materials_code_unique` (`code`),
  ADD KEY `materials_category_id_foreign` (`category_id`);

--
-- Indexes for table `material_images`
--
ALTER TABLE `material_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_images_material_id_foreign` (`material_id`);

--
-- Indexes for table `material_quotation`
--
ALTER TABLE `material_quotation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_quotation_quotation_id_foreign` (`quotation_id`),
  ADD KEY `material_quotation_material_id_foreign` (`material_id`);

--
-- Indexes for table `material_supplier`
--
ALTER TABLE `material_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_supplier_material_id_foreign` (`material_id`),
  ADD KEY `material_supplier_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_purchase_request_id_foreign` (`purchase_request_id`),
  ADD KEY `purchase_orders_contract_id_foreign` (`contract_id`),
  ADD KEY `purchase_orders_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_material_id_foreign` (`material_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_requests_pr_number_unique` (`pr_number`),
  ADD KEY `purchase_requests_requester_id_foreign` (`requester_id`),
  ADD KEY `purchase_requests_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `purchase_request_attachments`
--
ALTER TABLE `purchase_request_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_attachments_purchase_request_id_foreign` (`purchase_request_id`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_items_purchase_request_id_foreign` (`purchase_request_id`),
  ADD KEY `purchase_request_items_material_id_foreign` (`material_id`),
  ADD KEY `purchase_request_items_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_rfq_number_unique` (`rfq_number`),
  ADD KEY `quotations_purchase_request_id_foreign` (`purchase_request_id`),
  ADD KEY `quotations_awarded_supplier_id_foreign` (`awarded_supplier_id`);

--
-- Indexes for table `quotation_attachments`
--
ALTER TABLE `quotation_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_attachments_quotation_id_foreign` (`quotation_id`);

--
-- Indexes for table `quotation_responses`
--
ALTER TABLE `quotation_responses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_responses_quotation_id_supplier_id_unique` (`quotation_id`,`supplier_id`),
  ADD KEY `quotation_responses_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `quotation_response_attachments`
--
ALTER TABLE `quotation_response_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_response_attachments_quotation_response_id_foreign` (`quotation_response_id`);

--
-- Indexes for table `quotation_response_items`
--
ALTER TABLE `quotation_response_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_response_items_quotation_response_id_foreign` (`quotation_response_id`),
  ADD KEY `quotation_response_items_material_id_foreign` (`material_id`);

--
-- Indexes for table `quotation_supplier`
--
ALTER TABLE `quotation_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_supplier_quotation_id_foreign` (`quotation_id`),
  ADD KEY `quotation_supplier_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_email_unique` (`email`);

--
-- Indexes for table `supplier_invitations`
--
ALTER TABLE `supplier_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_invitations_email_unique` (`email`),
  ADD UNIQUE KEY `supplier_invitations_invitation_code_unique` (`invitation_code`),
  ADD KEY `supplier_invitations_project_id_foreign` (`project_id`);

--
-- Indexes for table `supplier_invitation_materials`
--
ALTER TABLE `supplier_invitation_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_invitation_materials_supplier_invitation_id_foreign` (`supplier_invitation_id`),
  ADD KEY `supplier_invitation_materials_material_id_foreign` (`material_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `company_docs`
--
ALTER TABLE `company_docs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contract_items`
--
ALTER TABLE `contract_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_material`
--
ALTER TABLE `inquiry_material`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `material_images`
--
ALTER TABLE `material_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_quotation`
--
ALTER TABLE `material_quotation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_supplier`
--
ALTER TABLE `material_supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_request_attachments`
--
ALTER TABLE `purchase_request_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_attachments`
--
ALTER TABLE `quotation_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_responses`
--
ALTER TABLE `quotation_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_response_attachments`
--
ALTER TABLE `quotation_response_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_response_items`
--
ALTER TABLE `quotation_response_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_supplier`
--
ALTER TABLE `quotation_supplier`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `supplier_invitations`
--
ALTER TABLE `supplier_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_invitation_materials`
--
ALTER TABLE `supplier_invitation_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD CONSTRAINT `bank_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_docs`
--
ALTER TABLE `company_docs`
  ADD CONSTRAINT `company_docs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `parties` (`id`),
  ADD CONSTRAINT `contracts_contractor_id_foreign` FOREIGN KEY (`contractor_id`) REFERENCES `parties` (`id`),
  ADD CONSTRAINT `contracts_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `contract_items`
--
ALTER TABLE `contract_items`
  ADD CONSTRAINT `contract_items_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `contract_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiry_material`
--
ALTER TABLE `inquiry_material`
  ADD CONSTRAINT `inquiry_material_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inquiry_material_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_images`
--
ALTER TABLE `material_images`
  ADD CONSTRAINT `material_images_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_quotation`
--
ALTER TABLE `material_quotation`
  ADD CONSTRAINT `material_quotation_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `material_quotation_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_supplier`
--
ALTER TABLE `material_supplier`
  ADD CONSTRAINT `material_supplier_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `material_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`),
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `purchase_requests_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_request_attachments`
--
ALTER TABLE `purchase_request_attachments`
  ADD CONSTRAINT `purchase_request_attachments_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_request_items_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_request_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_awarded_supplier_id_foreign` FOREIGN KEY (`awarded_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotations_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_attachments`
--
ALTER TABLE `quotation_attachments`
  ADD CONSTRAINT `quotation_attachments_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_responses`
--
ALTER TABLE `quotation_responses`
  ADD CONSTRAINT `quotation_responses_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotation_responses_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_response_attachments`
--
ALTER TABLE `quotation_response_attachments`
  ADD CONSTRAINT `quotation_response_attachments_quotation_response_id_foreign` FOREIGN KEY (`quotation_response_id`) REFERENCES `quotation_responses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_response_items`
--
ALTER TABLE `quotation_response_items`
  ADD CONSTRAINT `quotation_response_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotation_response_items_quotation_response_id_foreign` FOREIGN KEY (`quotation_response_id`) REFERENCES `quotation_responses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_supplier`
--
ALTER TABLE `quotation_supplier`
  ADD CONSTRAINT `quotation_supplier_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotation_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_invitations`
--
ALTER TABLE `supplier_invitations`
  ADD CONSTRAINT `supplier_invitations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_invitation_materials`
--
ALTER TABLE `supplier_invitation_materials`
  ADD CONSTRAINT `supplier_invitation_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_invitation_materials_supplier_invitation_id_foreign` FOREIGN KEY (`supplier_invitation_id`) REFERENCES `supplier_invitations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
