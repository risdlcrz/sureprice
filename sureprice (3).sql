-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 07:24 PM
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
-- Database: `sureprice`
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
-- Table structure for table `additional_works`
--

CREATE TABLE `additional_works` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `additional_work_materials`
--

CREATE TABLE `additional_work_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `additional_work_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
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

--
-- Dumping data for table `bank_details`
--

INSERT INTO `bank_details` (`id`, `company_id`, `bank_name`, `account_name`, `account_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'bdo', 'patricia', '995858585445', '2025-06-20 07:50:29', '2025-06-20 07:50:29'),
(2, 2, 'BPI', 'Luis', '7584758454', '2025-06-20 07:52:39', '2025-06-20 07:52:39');

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
(1, 'Construction', 'construction', 'Construction materials', NULL, NULL),
(2, 'Electrical', 'electrical', 'Electrical materials', NULL, NULL),
(3, 'Plumbing', 'plumbing', 'Plumbing materials', NULL, NULL),
(4, 'Finishing', 'finishing', 'Finishing materials', NULL, NULL),
(5, 'Tools', 'tools', 'Tools and equipment', NULL, NULL),
(6, 'Other', 'other', 'Other materials', NULL, NULL),
(7, 'General', 'general', 'General construction materials', '2025-06-19 07:22:48', '2025-06-19 07:22:48');

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
  `unit` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal` varchar(10) DEFAULT NULL,
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
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `pending_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pending_changes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `created_at`, `updated_at`, `user_id`, `username`, `email`, `company_name`, `supplier_type`, `other_supplier_type`, `business_reg_no`, `contact_person`, `designation`, `mobile_number`, `telephone_number`, `street`, `unit`, `barangay`, `city`, `state`, `postal`, `years_operation`, `business_size`, `service_areas`, `primary_products_services`, `bank_name`, `bank_account_name`, `bank_account_number`, `vat_registered`, `use_sureprice`, `payment_terms`, `status`, `pending_changes`) VALUES
(1, '2025-06-20 07:50:29', '2025-06-20 08:45:57', 14, 'patchochai', 'patchochai@gmail.com', 'PIYUTEK', 'Individual', NULL, '12334454', 'Patricia Dela cruz', 'client', '09875454443', '34343333', 'Doonlang', NULL, 'Mapagmahal', 'Quezon City', 'Manila', '1001', 15, 'Solo', 'Quezon City, Taguig', NULL, NULL, NULL, NULL, 1, 1, '15 days', 'approved', NULL),
(2, '2025-06-20 07:52:39', '2025-06-20 08:46:01', 15, 'lowishopi', 'lowishofi@gmail.com', 'LBJ', 'Material Supplier', NULL, '323234334324', 'Luis Hofi', 'supplier', '09483478333', '32434234', 'Chickenjoy', NULL, 'Jabili', 'Taguig City', 'Metro Manila', '3232', 4, 'Small Enterprise', 'Manila', NULL, 'BPI', 'Luis', '7584758454', 1, 1, '30 days', 'approved', NULL);

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

--
-- Dumping data for table `company_docs`
--

INSERT INTO `company_docs` (`id`, `company_id`, `disk`, `type`, `path`, `original_name`, `created_at`, `updated_at`, `mime_type`, `size`) VALUES
(1, 1, 'public', 'DTI_SEC_REGISTRATION', 'company_docs/1/zHKaDdP8hQniN0JOkoBQfC7e4toUD10V8mMF5YLI.png', 'Add a heading (4).png', '2025-06-20 07:50:30', '2025-06-20 07:50:30', 'image/png', 621982),
(2, 1, 'public', 'BUSINESS_PERMIT_MAYOR_PERMIT', 'company_docs/1/KWn3dyiQ45WpFEpfCgzMZh5NkNYPUfNW6uSm9ywB.png', 'Add a heading (4).png', '2025-06-20 07:50:31', '2025-06-20 07:50:31', 'image/png', 621982),
(3, 1, 'public', 'VALID_ID_OWNER_REP', 'company_docs/1/koVrCmoSex2mlwiZgWcu7T39aYjrh14bFCAOfkUx.png', 'Add a heading (4).png', '2025-06-20 07:50:31', '2025-06-20 07:50:31', 'image/png', 621982),
(4, 2, 'public', 'DTI_SEC_REGISTRATION', 'company_docs/2/K1wZygNAF5gENHVwaL6Uqwrfso46AoVgTP9QMvvZ.pdf', 'Module 5 PPT Lesson - IT0005.pdf', '2025-06-20 07:52:39', '2025-06-20 07:52:39', 'application/pdf', 2477856),
(5, 2, 'public', 'BUSINESS_PERMIT_MAYOR_PERMIT', 'company_docs/2/BqCnXWGcrP2HbhCXdlSbkIjBlPfNuwTiqOVjvQ6W.png', 'Add a heading (4).png', '2025-06-20 07:52:39', '2025-06-20 07:52:39', 'image/png', 621982),
(6, 2, 'public', 'VALID_ID_OWNER_REP', 'company_docs/2/8HGGa17w8MhZH0TZ7nLxNDXpL71crU5f9w3srjwD.png', 'Add a heading (4).png', '2025-06-20 07:52:39', '2025-06-20 07:52:39', 'image/png', 621982);

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contractor_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `contract_number` varchar(255) NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `property_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `labor_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `materials_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) NOT NULL,
  `payment_terms` text NOT NULL,
  `advance_payment_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `retention_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `payment_due_days` int(11) NOT NULL DEFAULT 0,
  `warranty_terms` text DEFAULT NULL,
  `cancellation_terms` text DEFAULT NULL,
  `additional_terms` text DEFAULT NULL,
  `payment_schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_schedule`)),
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `check_number` varchar(255) DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `check_image` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `estimated_days` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `contractor_signature` longtext DEFAULT NULL,
  `client_signature` longtext DEFAULT NULL,
  `contract_terms` text DEFAULT NULL,
  `contract_payment_terms` text DEFAULT NULL,
  `jurisdiction` varchar(255) DEFAULT NULL,
  `scope_of_work` text DEFAULT NULL,
  `scope_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_items`
--

CREATE TABLE `contract_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'pcs',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_number` varchar(255) NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_date` date NOT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','received','rejected') NOT NULL DEFAULT 'pending',
  `total_units` decimal(15,2) NOT NULL DEFAULT 0.00,
  `defective_units` decimal(15,2) NOT NULL DEFAULT 0.00,
  `wastage_units` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quality_check_notes` text DEFAULT NULL,
  `is_on_time` tinyint(1) NOT NULL DEFAULT 1,
  `actual_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `supplier_evaluation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `defective_quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `wastage_quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `role` enum('procurement','warehousing','contractor') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `username`, `first_name`, `last_name`, `email`, `role`, `company_name`, `street`, `barangay`, `city`, `state`, `postal`, `phone`, `created_at`, `updated_at`) VALUES
(1, 11, 'mimamo', 'Mhiema', 'Saur', 'mimasor@gmail.com', 'procurement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-20 07:44:57', '2025-06-20 07:44:57'),
(2, 12, 'jinbilog', 'Jin', 'Bilog', 'jinbilog@gmail.com', 'warehousing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-20 07:46:21', '2025-06-20 07:46:21'),
(3, 13, 'librojems', 'Libro', 'Jems', 'librojems@gmail.com', 'contractor', 'ENBIEY', 'BahayniHofi', '143', 'Quezon City', 'Manila', '1121', '09849385434', '2025-06-20 07:47:57', '2025-06-20 07:47:57');

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
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive','discontinued') NOT NULL DEFAULT 'active',
  `last_restock_date` timestamp NULL DEFAULT NULL,
  `last_restock_quantity` decimal(10,2) DEFAULT NULL,
  `minimum_threshold` decimal(10,2) NOT NULL DEFAULT 0.00,
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
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `warranty_period` int(11) DEFAULT NULL,
  `srp_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `specifications` text DEFAULT NULL,
  `minimum_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_per_area` tinyint(1) NOT NULL DEFAULT 0,
  `is_wall_material` tinyint(1) NOT NULL DEFAULT 0,
  `coverage_rate` decimal(10,2) DEFAULT NULL COMMENT 'How many square meters one unit covers',
  `waste_factor` decimal(5,2) NOT NULL DEFAULT 1.10 COMMENT 'Default 10% waste factor',
  `minimum_quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `bulk_pricing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array of {min_quantity, price} objects' CHECK (json_valid(`bulk_pricing`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `code`, `description`, `unit`, `base_price`, `warranty_period`, `srp_price`, `specifications`, `minimum_stock`, `current_stock`, `category_id`, `custom_category`, `created_at`, `updated_at`, `is_per_area`, `is_wall_material`, `coverage_rate`, `waste_factor`, `minimum_quantity`, `bulk_pricing`) VALUES
(1, 'Paint (latex/acrylic)', 'MAT250001', NULL, 'liters', 500.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(2, 'Primer', 'MAT250002', NULL, 'liters', 450.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(3, 'Sandpaper', 'MAT250003', NULL, 'sheets', 25.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(4, 'Caulk', 'MAT250004', NULL, 'kg', 300.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(5, 'Painter\'s tape', 'MAT250005', NULL, 'meters', 50.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(6, 'Joint compound', 'MAT250006', NULL, 'kg', 200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(7, 'Drywall tape', 'MAT250007', NULL, 'meters', 30.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(8, 'Gypsum board', 'MAT250008', NULL, 'sqm', 350.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(9, 'Screws', 'MAT250009', NULL, 'pcs', 5.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(10, 'Metal studs/channels', 'MAT250010', NULL, 'meters', 150.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(11, 'Tiles', 'MAT250011', NULL, 'sqm', 800.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(12, 'Thin-set mortar', 'MAT250012', NULL, 'kg', 250.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(13, 'Grout', 'MAT250013', NULL, 'kg', 300.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(14, 'Spacers', 'MAT250014', NULL, 'pcs', 2.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(15, 'Plywood/MDF', 'MAT250015', NULL, 'sqm', 1200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(16, 'Screws/nails', 'MAT250016', NULL, 'pcs', 8.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(17, 'Adhesive', 'MAT250017', NULL, 'kg', 400.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(18, 'Spray-applied fireproofing', 'MAT250018', NULL, 'kg', 600.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(19, 'Wire mesh', 'MAT250019', NULL, 'sqm', 200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(20, 'Conduit', 'MAT250020', NULL, 'meters', 150.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(21, 'Wires', 'MAT250021', NULL, 'meters', 80.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(22, 'Junction boxes', 'MAT250022', NULL, 'pcs', 200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(23, 'PVC pipes', 'MAT250023', NULL, 'meters', 200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(24, 'Fittings', 'MAT250024', NULL, 'pcs', 150.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(25, 'Vinyl planks', 'MAT250025', NULL, 'sqm', 1200.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(26, 'Underlayment', 'MAT250026', NULL, 'sqm', 150.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(27, 'Epoxy coating', 'MAT250027', NULL, 'kg', 800.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(28, 'Sealant', 'MAT250028', NULL, 'kg', 600.00, NULL, 0.00, NULL, 0.00, 0.00, 7, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 0, 0, NULL, 1.10, 1.00, NULL),
(29, 'Nyatdog', 'FIN-9220', 'djsdhjsdsds', 'pcs', 0.00, NULL, 0.00, NULL, 0.00, 0.00, 4, NULL, '2025-06-20 08:42:41', '2025-06-20 08:42:41', 0, 0, NULL, 1.10, 1.00, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `content` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(14, '2024_03_15_000001_create_inventories_table', 1),
(15, '2024_03_15_000002_create_quotation_attachments_table', 1),
(16, '2024_03_19_000000_create_contracts_table', 1),
(17, '2024_03_19_000000_create_rooms_table', 1),
(18, '2024_03_19_000000_create_scope_types_table', 1),
(19, '2024_03_19_000001_create_additional_works_table', 1),
(20, '2024_03_19_000001_create_purchase_requests_table', 1),
(21, '2024_03_19_000002_create_purchase_orders_table', 1),
(22, '2024_03_19_000004_add_foreign_keys', 1),
(23, '2024_03_19_000005_create_purchase_order_items_table', 1),
(24, '2024_03_19_000006_create_supplier_evaluations_table', 1),
(25, '2024_03_20_000005_create_deliveries_table', 1),
(26, '2024_03_20_000006_create_delivery_items_table', 1),
(27, '2024_03_20_000007_create_payments_table', 1),
(28, '2024_03_20_000008_create_project_tasks_table', 1),
(29, '2024_03_20_183900_add_estimated_days_to_contracts', 1),
(30, '2024_03_21_000002_create_supplier_metrics_table', 1),
(31, '2024_03_21_000003_add_evaluation_fields_to_purchase_orders_table', 1),
(32, '2024_03_21_000007_add_payment_details_to_contracts_table', 1),
(33, '2024_03_21_000008_add_check_details_to_contracts_table', 1),
(34, '2024_03_22_000002_recreate_contract_items_table', 1),
(35, '2024_03_22_000003_add_last_login_at_to_users_table', 1),
(36, '2024_03_22_000004_add_material_details_to_contract_items', 1),
(37, '2024_03_22_000005_fix_contract_items_table', 1),
(38, '2024_03_23_000001_create_transactions_table', 1),
(39, '2024_03_24_000001_add_is_preferred_to_material_supplier_table', 1),
(40, '2024_03_25_000001_add_marked_paid_by_to_payments_table', 1),
(41, '2024_03_25_000001_fix_budget_allocation_in_contracts', 1),
(42, '2024_03_25_000010_create_inquiries_table', 1),
(43, '2024_03_26_000001_add_force_password_change_to_users', 1),
(44, '2024_03_26_000001_create_messages_table', 1),
(45, '2024_03_26_000002_add_costs_to_rooms_table', 1),
(46, '2024_03_27_000001_remove_budget_allocation_from_contracts', 1),
(47, '2024_03_27_000001_remove_force_password_change_from_users', 1),
(48, '2024_06_04_000001_add_specifications_to_purchase_request_items_table', 1),
(49, '2025_05_16_190334_create_cache_table', 1),
(50, '2025_05_16_190335_create_companies_table', 1),
(51, '2025_05_16_190335_create_jobs_table', 1),
(52, '2025_05_16_190336_create_sessions_table', 1),
(53, '2025_05_17_195954_create_employees_table', 1),
(54, '2025_05_17_200018_create_company_docs_table', 1),
(55, '2025_05_17_200827_update_companies_table', 1),
(56, '2025_05_18_165948_add_email_to_employees_table', 1),
(57, '2025_05_18_175410_update_vat_registered_and_use_sureprice_columns_in_companies_table', 1),
(58, '2025_05_23_143103_add_mime_type_and_size_to_company_docs_table_fix', 1),
(59, '2025_05_24_000001_add_disk_column_to_company_docs_table', 1),
(60, '2025_05_24_000002_add_bank_and_products_to_companies_table', 1),
(61, '2025_05_24_000004_update_supplier_invitations_table', 1),
(62, '2025_05_24_182401_create_bank_details_table', 1),
(63, '2025_06_04_072111_make_contract_optional_in_purchase_requests_and_orders', 1),
(64, '2025_06_04_072404_update_quotations_table_for_purchase_requests', 1),
(65, '2025_06_04_072417_update_quotations_table_for_purchase_requests', 1),
(66, '2025_06_04_101643_add_awarded_fields_to_quotations_table', 1),
(67, '2025_06_06_033055_add_srp_price_to_materials_table', 1),
(68, '2025_06_07_000001_add_labor_cost_fields_to_contracts', 1),
(69, '2025_06_07_000002_update_contracts_table_remove_labor_fields', 1),
(70, '2025_06_07_000003_update_contracts_table_add_labor_fields', 1),
(71, '2025_06_07_000005_fix_contract_items_unit_column', 1),
(72, '2025_06_07_000006_fix_contract_items_unit_column_again', 1),
(73, '2025_06_07_000007_add_default_to_contract_items_unit', 1),
(74, '2025_06_07_000008_add_signature_and_contract_fields_to_contracts', 1),
(75, '2025_06_10_133048_create_room_scope_type_table', 1),
(76, '2025_06_10_133049_create_scope_type_material_table', 1),
(77, '2025_06_11_000001_update_purchase_requests_add_missing_fields', 1),
(78, '2025_06_11_000002_update_purchase_orders_add_missing_fields', 1),
(79, '2025_06_11_103101_make_state_and_postal_nullable_in_companies_table', 1),
(80, '2025_06_11_103107_make_companies_address_nullable', 1),
(81, '2025_06_11_132704_remove_type_column_from_parties_table', 1),
(82, '2025_06_11_132924_add_payment_schedule_to_contracts_table', 1),
(83, '2025_06_11_183600_add_estimated_days_to_rooms_table', 1),
(84, '2025_06_11_183800_add_timestamps_to_scope_type_material_table', 1),
(85, '2025_06_11_183900_remove_materials_column_from_scope_types', 1),
(86, '2025_06_11_215739_add_payment_schedule_to_contracts_table', 1),
(87, '2025_06_12_000001_add_contractor_role_to_employees_table', 1),
(88, '2025_06_12_030319_add_contract_terms_fields_to_contracts_table', 1),
(89, '2025_06_12_031001_add_request_number_to_purchase_requests_table', 1),
(90, '2025_06_12_034245_fix_purchase_requests_table_schema', 1),
(91, '2025_06_12_043028_fix_purchase_requests_column_names', 1),
(92, '2025_06_12_044000_remove_old_purchase_request_columns', 1),
(93, '2025_06_13_000001_add_contractor_fields_to_employees_table', 1),
(94, '2025_06_13_000001_add_payment_id_to_transactions_table', 1),
(95, '2025_06_13_000002_add_created_by_to_transactions_table', 1),
(96, '2025_06_13_000002_add_role_to_users_table', 1),
(97, '2025_06_13_142507_create_personal_access_tokens_table', 1),
(98, '2025_06_14_000000_update_companies_add_address_fields', 1),
(99, '2025_06_14_140859_create_warranty_requests_table', 1),
(100, '2025_06_15_000000_add_calculation_fields_to_materials_and_scopes', 1),
(101, '2025_06_15_000001_add_payment_fields_to_contracts_table', 1),
(102, '2025_06_15_000002_add_payment_verification_fields_to_payments_table', 1),
(103, '2025_06_15_065309_add_warranty_to_materials_table', 1),
(104, '2025_06_15_073349_add_warranty_period_to_materials_table', 1),
(105, '2025_06_15_114241_add_new_fields_to_scope_types_and_materials', 1),
(106, '2025_06_18_082024_create_password_reset_tokens_table', 1),
(107, '2024_01_01_000001_create_warehouses_table', 2),
(108, '2024_01_01_000002_create_stocks_table', 2),
(109, '2024_01_01_000003_migrate_materials_stock_to_stocks', 2),
(110, '2024_01_01_000004_rename_warehouses_to_main_and_second', 2),
(111, '2024_01_01_000005_add_warehouse_id_to_deliveries', 2),
(112, '2024_01_01_000006_rename_minimum_stock_to_threshold_in_stocks', 2),
(113, '2024_06_15_000001_add_supplier_id_to_conversations_table', 2),
(114, '2024_06_15_000002_add_pending_changes_to_suppliers_table', 2),
(115, '2024_06_15_000003_create_supplier_rankings_table', 2),
(116, '2024_06_15_000004_create_order_evaluations_table', 2),
(117, '2025_06_21_075252_create_stock_movements_table', 2),
(118, '2025_06_22_000001_create_reports_table', 2),
(119, '2025_06_21_000001_add_pending_changes_to_companies_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `order_evaluations`
--

CREATE TABLE `order_evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `quality_rating` double NOT NULL DEFAULT 0,
  `has_complaints` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` bigint(20) UNSIGNED NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(255) NOT NULL,
  `payable_type` varchar(255) NOT NULL,
  `payable_id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL DEFAULT 'regular',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `client_payment_proof` varchar(255) DEFAULT NULL,
  `client_payment_method` varchar(255) DEFAULT NULL,
  `client_reference_number` varchar(255) DEFAULT NULL,
  `client_paid_amount` decimal(15,2) DEFAULT NULL,
  `client_paid_date` date DEFAULT NULL,
  `client_notes` text DEFAULT NULL,
  `admin_payment_proof` varchar(255) DEFAULT NULL,
  `admin_payment_method` varchar(255) DEFAULT NULL,
  `admin_reference_number` varchar(255) DEFAULT NULL,
  `admin_received_amount` decimal(15,2) DEFAULT NULL,
  `admin_received_date` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `marked_paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scope_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `progress` int(11) NOT NULL DEFAULT 0,
  `priority` varchar(255) NOT NULL DEFAULT 'medium',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `delivery_date` date NOT NULL,
  `payment_terms` varchar(255) NOT NULL,
  `shipping_terms` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_delivered` tinyint(1) NOT NULL DEFAULT 0,
  `is_on_time` tinyint(1) NOT NULL DEFAULT 0,
  `total_units` int(11) NOT NULL DEFAULT 0,
  `defective_units` int(11) NOT NULL DEFAULT 0,
  `estimated_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quality_notes` text DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `is_project_related` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `preferred_brand` varchar(255) DEFAULT NULL,
  `preferred_supplier_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `generated_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `length` decimal(10,2) NOT NULL,
  `width` decimal(10,2) NOT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `area` decimal(10,2) NOT NULL,
  `materials_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `labor_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `estimated_days` int(11) NOT NULL DEFAULT 0,
  `floor_area` decimal(10,2) DEFAULT NULL,
  `wall_area` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_scope_type`
--

CREATE TABLE `room_scope_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `scope_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scope_types`
--

CREATE TABLE `scope_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `is_wall_work` tinyint(1) NOT NULL DEFAULT 0,
  `estimated_days` int(11) NOT NULL,
  `labor_rate` decimal(10,2) NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `labor_type` varchar(255) NOT NULL DEFAULT 'per_area' COMMENT 'per_area, fixed, or per_unit',
  `minimum_labor_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `complexity_factor` decimal(5,2) NOT NULL DEFAULT 1.00,
  `tasks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tasks`)),
  `labor_hours_per_unit` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scope_types`
--

INSERT INTO `scope_types` (`id`, `code`, `name`, `category`, `is_wall_work`, `estimated_days`, `labor_rate`, `items`, `created_at`, `updated_at`, `labor_type`, `minimum_labor_cost`, `complexity_factor`, `tasks`, `labor_hours_per_unit`) VALUES
(1, 'painting_crew', 'Painting Crew', 'Painting', 1, 2, 350.00, NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', 'per_area', 0.00, 1.20, '[{\"name\":\"Surface Prep\",\"labor_hours_per_sqm\":0.2,\"description\":\"Includes cleaning, sanding, priming.\"},{\"name\":\"Paint Application\",\"labor_hours_per_sqm\":0.15,\"description\":\"2 coats (cut-in + rolling).\"}]', NULL),
(2, 'drywall_finishing', 'Drywall Finishing', 'Painting', 1, 3, 400.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.30, '[{\"name\":\"Drywall Finishing\",\"labor_hours_per_sqm\":0.35,\"description\":\"Taping, mudding, sanding.\"}]', NULL),
(3, 'drywall_installation', 'Drywall Installation', 'Fit-outs', 1, 4, 450.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.40, '[{\"name\":\"Framing\",\"labor_hours_per_sqm\":0.4,\"description\":\"Install metal\\/wood studs\"},{\"name\":\"Hanging\",\"labor_hours_per_sqm\":0.3,\"description\":\"Secure gypsum boards to studs\"},{\"name\":\"Cutting\",\"labor_hours_per_sqm\":0.2,\"description\":\"Fit boards around outlets\\/doors\"}]', NULL),
(4, 'tile_installation', 'Tile Installation', 'Fit-outs', 1, 5, 500.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.50, '[{\"name\":\"Tile Installation\",\"labor_hours_per_sqm\":0.5,\"description\":\"Layout, mortar, grout.\"}]', NULL),
(5, 'cabinetry_installation', 'Cabinetry Installation', 'Fit-outs', 1, 6, 550.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.60, '[{\"name\":\"Measurement & Assembly\",\"labor_hours_per_sqm\":0.5,\"description\":\"Verify dimensions, construct cabinets\"},{\"name\":\"Installation\",\"labor_hours_per_sqm\":0.4,\"description\":\"Secure to walls\\/floor\"},{\"name\":\"Finishing\",\"labor_hours_per_sqm\":0.2,\"description\":\"Attach hardware (handles, hinges)\"}]', NULL),
(6, 'fireproofing', 'Fireproofing Spray', 'MEPFS', 1, 3, 400.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.40, '[{\"name\":\"Fireproofing Spray\",\"labor_hours_per_sqm\":0.075,\"description\":\"Vertical surfaces only.\"}]', NULL),
(7, 'electrical_wiring', 'Electrical Wiring', 'MEPFS', 1, 4, 450.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.50, '[{\"name\":\"Electrical Wiring\",\"labor_hours_per_sqm\":0.125,\"description\":\"Rough-in for walls\\/floors.\"}]', NULL),
(8, 'plumbing_rough_in', 'Plumbing Pipes', 'MEPFS', 1, 3, 400.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.30, '[{\"name\":\"Plumbing Pipes\",\"labor_hours_per_sqm\":0.175,\"description\":\"PVC\\/CPVC installation.\"}]', NULL),
(9, 'flooring_installation', 'Vinyl Flooring', 'Infrastructure', 0, 4, 500.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.40, '[{\"name\":\"Vinyl Flooring\",\"labor_hours_per_sqm\":0.25,\"description\":\"Includes underlayment.\"}]', NULL),
(10, 'concrete_coating', 'Concrete Waterproofing', 'Infrastructure', 0, 3, 450.00, NULL, '2025-06-19 07:22:49', '2025-06-19 07:22:49', 'per_area', 0.00, 1.30, '[{\"name\":\"Concrete Waterproofing\",\"labor_hours_per_sqm\":0.125,\"description\":\"Epoxy\\/polyurethane application.\"}]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scope_type_material`
--

CREATE TABLE `scope_type_material` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope_type_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scope_type_material`
--

INSERT INTO `scope_type_material` (`id`, `scope_type_id`, `material_id`, `created_at`, `updated_at`) VALUES
(12, 4, 11, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(15, 4, 14, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(19, 6, 18, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(31, 1, 9, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(32, 1, 18, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(33, 1, 26, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(34, 2, 1, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(35, 2, 9, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(36, 2, 10, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(37, 3, 4, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(38, 3, 12, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(39, 3, 27, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(40, 4, 1, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(41, 5, 8, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(42, 5, 14, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(43, 5, 19, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(44, 6, 5, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(45, 6, 8, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(46, 7, 8, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(47, 7, 11, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(48, 7, 12, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(49, 8, 8, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(50, 8, 10, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(51, 8, 22, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(52, 9, 13, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(53, 9, 14, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(54, 9, 24, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(55, 10, 2, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(56, 10, 5, '2025-06-19 07:22:49', '2025-06-19 07:22:49'),
(57, 10, 26, '2025-06-19 07:22:49', '2025-06-19 07:22:49');

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
('9dPZfbE452bKnPISx4XEtwASAWY4K1Mhu3ZsPyYp', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiOG80VzZHWXdlWmRxbTl6Wk5xcDViOWpvcGRjdHFMTDFYc3FIWVBjMyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750346598),
('MC5A4q27ixxph78FQqo5jYCqiqiyMgKUfDXlGpvN', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaVI1YVVNempHc1J1NmRwYUE4a3NoUXZSd05GRTdVYWh1Wlp4c0ZSeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9sb2NhbGhvc3Qvc3VyZXByaWNlL3B1YmxpYy9wcm9qZWN0LWRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1750438242);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `threshold` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `previous_stock` decimal(12,2) NOT NULL,
  `new_stock` decimal(12,2) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `pending_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pending_changes`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_evaluations`
--

CREATE TABLE `supplier_evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `engagement_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `delivery_speed_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `delivery_ontime_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `performance_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `quality_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `defect_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cost_variance_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `cost_variance_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sustainability_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `final_score` decimal(3,2) NOT NULL DEFAULT 0.00,
  `evaluation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `supplier_metrics`
--

CREATE TABLE `supplier_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_deliveries` int(11) NOT NULL DEFAULT 0,
  `ontime_deliveries` int(11) NOT NULL DEFAULT 0,
  `average_defect_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `average_cost_variance` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_rankings`
--

CREATE TABLE `supplier_rankings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `score` double NOT NULL DEFAULT 0,
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
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'expense',
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'admin',
  `role` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `user_type`, `role`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'Main Admin', 'mainadmin', 'admin', 'admin', 'admin1@example.com', NULL, '$2y$12$mbyq9PPS.fNQXYm3IVXmVOLXY9Cn0wwWCmMrrAMcuFimeQHew2C72', NULL, '2025-06-19 07:22:48', '2025-06-20 08:49:37', '2025-06-20 08:49:37'),
(2, 'Backup Admin', 'backupadmin', 'admin', 'admin', 'admin2@example.com', NULL, '$2y$12$9wpUG8thyALQnj6lprRHeOvX.Q.nEGcG8sEdABkhs6f7BQi8SYuFW', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(3, 'Contractor One', 'contractor1', 'contractor', NULL, 'contractor1@example.com', NULL, '$2y$12$pDUoGz.qIvZ.mYbsMWCO7.3.UrQKldHUas041dW2tI7bAP8W43e0u', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(4, 'Contractor Two', 'contractor2', 'contractor', NULL, 'contractor2@example.com', NULL, '$2y$12$L2P0SbmAWjd6AkVuYA9t5.U3n9jENbogHJ6c.s74hmOjMOseM1Tj2', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(5, 'Client One', 'client1', 'client', NULL, 'client1@example.com', NULL, '$2y$12$aZx2GB3AUvbmdssRFWBgE.tNuDE6MmiiWBPYCZX3Swy8BkjplcDQq', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(6, 'Client Two', 'client2', 'client', NULL, 'client2@example.com', NULL, '$2y$12$4Jb2jXTrr3zs5WPq/VUgg.FEPvejGOHINN53GrHchballDUnPscgm', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(7, 'Test User 1', 'testuser1', 'client', NULL, 'testuser1@example.com', NULL, '$2y$12$pALuL5qttG179ZRMnrPUU.Zi6mFmDRtS.ffe746pH9ypJG/e5MyKK', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(8, 'Test User 2', 'testuser2', 'contractor', NULL, 'testuser2@example.com', NULL, '$2y$12$.ek2e1BUAY0e61SrBKpgZ.xyN1e7oOR1WeS5A.fcwaBtagczvH1z2', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(9, 'Test User 3', 'testuser3', 'client', NULL, 'testuser3@example.com', NULL, '$2y$12$4SsQYcOc8bvbugUTRAKzVOqe9l7LTIPacgNYNVtv.eP8yO2YdiIDK', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(10, 'Test User 4', 'testuser4', 'contractor', NULL, 'testuser4@example.com', NULL, '$2y$12$/cSJdvvWAGWBvWyQkbOHA.kvyuptgNyOw5cLQ1/CHPVRVD30YTLqy', NULL, '2025-06-19 07:22:48', '2025-06-19 07:22:48', NULL),
(11, 'Mhiema Saur', 'mimamo', 'employee', 'procurement', 'mimasor@gmail.com', NULL, '$2y$12$ZGle3H71ssk1yUBBPHuT2e/ltP8x.20oS5FDO8o5xtvTO6m5iCP4a', NULL, '2025-06-20 07:44:57', '2025-06-20 07:44:57', NULL),
(12, 'Jin Bilog', 'jinbilog', 'employee', 'warehousing', 'jinbilog@gmail.com', NULL, '$2y$12$iTWhRsHrruLygNusU859GOC2IpxZ/p3IQUjMu/hRzJWrHs1z2yXFi', NULL, '2025-06-20 07:46:21', '2025-06-20 07:46:21', NULL),
(13, 'Libro Jems', 'librojems', 'employee', 'contractor', 'librojems@gmail.com', NULL, '$2y$12$W1eKDU5yAG8qy0SO0/A9XuvDAkhxIeAzQvNwD3r9rbCPdOlMD/Spq', NULL, '2025-06-20 07:47:57', '2025-06-20 07:47:57', NULL),
(14, 'PIYUTEK', 'patchochai', 'company', 'client', 'patchochai@gmail.com', NULL, '$2y$12$p3HUILcaKBWB8YPRtUmVGuvfg36aTppYhVIvjyabORZ7yLJSYgOdG', NULL, '2025-06-20 07:50:29', '2025-06-20 07:50:29', NULL),
(15, 'LBJ', 'lowishopi', 'company', 'supplier', 'lowishofi@gmail.com', NULL, '$2y$12$0Ib9y58HZ6OSL4tixNH5TeZxyWVFxP/TQR9fAYedVggqk0QeBO7N.', NULL, '2025-06-20 07:52:39', '2025-06-20 07:52:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warranty_requests`
--

CREATE TABLE `warranty_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `model_number` varchar(255) DEFAULT NULL,
  `issue_description` text NOT NULL,
  `proof_of_purchase_path` varchar(255) NOT NULL,
  `issue_photos_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`issue_photos_paths`)),
  `status` enum('pending','in_review','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `additional_works`
--
ALTER TABLE `additional_works`
  ADD PRIMARY KEY (`id`),
  ADD KEY `additional_works_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `additional_work_materials`
--
ALTER TABLE `additional_work_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `additional_work_materials_additional_work_id_foreign` (`additional_work_id`),
  ADD KEY `additional_work_materials_material_id_foreign` (`material_id`);

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
  ADD UNIQUE KEY `contracts_contract_number_unique` (`contract_number`),
  ADD KEY `contracts_contractor_id_foreign` (`contractor_id`),
  ADD KEY `contracts_client_id_foreign` (`client_id`),
  ADD KEY `contracts_project_id_foreign` (`project_id`),
  ADD KEY `contracts_supplier_id_foreign` (`supplier_id`),
  ADD KEY `contracts_property_id_foreign` (`property_id`),
  ADD KEY `contracts_purchase_order_id_foreign` (`purchase_order_id`);

--
-- Indexes for table `contract_items`
--
ALTER TABLE `contract_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_items_contract_id_foreign` (`contract_id`),
  ADD KEY `contract_items_material_id_foreign` (`material_id`),
  ADD KEY `contract_items_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversations_client_id_foreign` (`client_id`),
  ADD KEY `conversations_admin_id_foreign` (`admin_id`),
  ADD KEY `conversations_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deliveries_delivery_number_unique` (`delivery_number`),
  ADD KEY `deliveries_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `deliveries_received_by_foreign` (`received_by`),
  ADD KEY `deliveries_supplier_evaluation_id_foreign` (`supplier_evaluation_id`),
  ADD KEY `deliveries_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_items_delivery_id_foreign` (`delivery_id`),
  ADD KEY `delivery_items_material_id_foreign` (`material_id`);

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
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventories_material_id_foreign` (`material_id`);

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
  ADD UNIQUE KEY `materials_code_unique` (`code`);

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_conversation_id_foreign` (`conversation_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_evaluations`
--
ALTER TABLE `order_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_evaluations_order_id_foreign` (`order_id`);

--
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  ADD KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  ADD KEY `payments_contract_id_foreign` (`contract_id`),
  ADD KEY `payments_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_approved_by_foreign` (`approved_by`),
  ADD KEY `payments_marked_paid_by_foreign` (`marked_paid_by`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_tasks_contract_id_foreign` (`contract_id`),
  ADD KEY `project_tasks_room_id_foreign` (`room_id`),
  ADD KEY `project_tasks_scope_type_id_foreign` (`scope_type_id`),
  ADD KEY `project_tasks_assigned_to_foreign` (`assigned_to`),
  ADD KEY `project_tasks_created_by_foreign` (`created_by`);

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
  ADD UNIQUE KEY `purchase_requests_request_number_unique` (`request_number`),
  ADD KEY `purchase_requests_contract_id_foreign` (`contract_id`),
  ADD KEY `purchase_requests_project_id_foreign` (`project_id`);

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
  ADD KEY `purchase_request_items_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchase_request_items_preferred_supplier_id_foreign` (`preferred_supplier_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_rfq_number_unique` (`rfq_number`),
  ADD KEY `quotations_purchase_request_id_foreign` (`purchase_request_id`);

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
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_generated_by_id_foreign` (`generated_by_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_contract_id_foreign` (`contract_id`);

--
-- Indexes for table `room_scope_type`
--
ALTER TABLE `room_scope_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_scope_type_room_id_foreign` (`room_id`),
  ADD KEY `room_scope_type_scope_type_id_foreign` (`scope_type_id`);

--
-- Indexes for table `scope_types`
--
ALTER TABLE `scope_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scope_types_code_unique` (`code`);

--
-- Indexes for table `scope_type_material`
--
ALTER TABLE `scope_type_material`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scope_type_material_scope_type_id_material_id_unique` (`scope_type_id`,`material_id`),
  ADD KEY `scope_type_material_material_id_foreign` (`material_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stocks_material_id_foreign` (`material_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_material_id_foreign` (`material_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_email_unique` (`email`);

--
-- Indexes for table `supplier_evaluations`
--
ALTER TABLE `supplier_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_evaluations_supplier_id_foreign` (`supplier_id`);

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
-- Indexes for table `supplier_metrics`
--
ALTER TABLE `supplier_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_metrics_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `supplier_rankings`
--
ALTER TABLE `supplier_rankings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_rankings_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_contract_id_foreign` (`contract_id`),
  ADD KEY `transactions_payment_id_foreign` (`payment_id`),
  ADD KEY `transactions_created_by_foreign` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warranty_requests`
--
ALTER TABLE `warranty_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warranty_requests_contract_id_foreign` (`contract_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `additional_works`
--
ALTER TABLE `additional_works`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `additional_work_materials`
--
ALTER TABLE `additional_work_materials`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_docs`
--
ALTER TABLE `company_docs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_items`
--
ALTER TABLE `contract_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `order_evaluations`
--
ALTER TABLE `order_evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request_attachments`
--
ALTER TABLE `purchase_request_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_scope_type`
--
ALTER TABLE `room_scope_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scope_types`
--
ALTER TABLE `scope_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `scope_type_material`
--
ALTER TABLE `scope_type_material`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_evaluations`
--
ALTER TABLE `supplier_evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `supplier_metrics`
--
ALTER TABLE `supplier_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_rankings`
--
ALTER TABLE `supplier_rankings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warranty_requests`
--
ALTER TABLE `warranty_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `additional_works`
--
ALTER TABLE `additional_works`
  ADD CONSTRAINT `additional_works_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `additional_work_materials`
--
ALTER TABLE `additional_work_materials`
  ADD CONSTRAINT `additional_work_materials_additional_work_id_foreign` FOREIGN KEY (`additional_work_id`) REFERENCES `additional_works` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `additional_work_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `contracts_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `contracts_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `contracts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `contract_items`
--
ALTER TABLE `contract_items`
  ADD CONSTRAINT `contract_items_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `contract_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_supplier_evaluation_id_foreign` FOREIGN KEY (`supplier_evaluation_id`) REFERENCES `supplier_evaluations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `deliveries_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_items_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `inventories`
--
ALTER TABLE `inventories`
  ADD CONSTRAINT `inventories_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_evaluations`
--
ALTER TABLE `order_evaluations`
  ADD CONSTRAINT `order_evaluations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_marked_paid_by_foreign` FOREIGN KEY (`marked_paid_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_tasks_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `project_tasks_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_tasks_scope_type_id_foreign` FOREIGN KEY (`scope_type_id`) REFERENCES `scope_types` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `purchase_requests_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `purchase_request_items_preferred_supplier_id_foreign` FOREIGN KEY (`preferred_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_request_items_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_request_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
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
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_generated_by_id_foreign` FOREIGN KEY (`generated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_scope_type`
--
ALTER TABLE `room_scope_type`
  ADD CONSTRAINT `room_scope_type_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_scope_type_scope_type_id_foreign` FOREIGN KEY (`scope_type_id`) REFERENCES `scope_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scope_type_material`
--
ALTER TABLE `scope_type_material`
  ADD CONSTRAINT `scope_type_material_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scope_type_material_scope_type_id_foreign` FOREIGN KEY (`scope_type_id`) REFERENCES `scope_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_evaluations`
--
ALTER TABLE `supplier_evaluations`
  ADD CONSTRAINT `supplier_evaluations_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `supplier_metrics`
--
ALTER TABLE `supplier_metrics`
  ADD CONSTRAINT `supplier_metrics_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_rankings`
--
ALTER TABLE `supplier_rankings`
  ADD CONSTRAINT `supplier_rankings_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warranty_requests`
--
ALTER TABLE `warranty_requests`
  ADD CONSTRAINT `warranty_requests_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
