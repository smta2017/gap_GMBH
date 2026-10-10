-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2021 at 11:27 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wfs_new`
--

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `code`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, 'Southern Asia', '034', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(2, 'Northern Europe', '154', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(3, 'Southern Europe', '039', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(4, 'Northern Africa', '015', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(5, 'Polynesia', '061', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(6, 'Sub-Saharan Africa', '202', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(7, 'Latin America and the Caribbean', '419', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(8, 'Western Asia', '145', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(9, 'Australia and New Zealand', '053', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(10, 'Western Europe', '155', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(11, 'Eastern Europe', '151', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(12, 'Northern America', '021', 1, '2021-10-06 07:14:07', '2021-10-06 07:14:07', NULL, NULL, NULL, NULL),
(13, 'South-eastern Asia', '035', 1, '2021-10-06 07:14:08', '2021-10-06 07:14:08', NULL, NULL, NULL, NULL),
(14, 'Eastern Asia', '030', 1, '2021-10-06 07:14:08', '2021-10-06 07:14:08', NULL, NULL, NULL, NULL),
(15, 'Melanesia', '054', 1, '2021-10-06 07:14:08', '2021-10-06 07:14:08', NULL, NULL, NULL, NULL),
(16, 'Micronesia', '057', 1, '2021-10-06 07:14:08', '2021-10-06 07:14:08', NULL, NULL, NULL, NULL),
(17, 'Central Asia', '143', 1, '2021-10-06 07:14:08', '2021-10-06 07:14:08', NULL, NULL, NULL, NULL);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
