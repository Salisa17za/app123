-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2025 at 02:12 PM
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
-- Database: `db_asset`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `asset_id` int(11) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `category_id` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_code`, `asset_name`, `category_id`, `purchase_date`, `price`, `image`, `created_at`) VALUES
(1, 'EQ001', 'คอมพิวเตอร์ All-in-One Dell', 'อุปกรณ์คอมพิวเตอร์', '2024-01-15', 25000.00, 'asset_1763816305_6921b371a77ff.jpg', '2025-11-21 07:48:36'),
(3, 'EQ003', 'โน้ตบุ๊ค', 'อุปกรณ์คอมพิวเตอร์', '2024-03-10', 32000.00, 'asset_1763816339_6921b393a0b59.jpg', '2025-11-21 07:48:36'),
(4, 'EQ004', 'เครื่องปรับอากาศ', 'เครื่องใช้ไฟฟ้า', '2024-01-05', 20000.00, 'asset_1763816631_6921b4b731012.jpg', '2025-11-21 07:48:36'),
(5, 'EQ005', 'โต๊ะทำงาน', 'เฟอร์นิเจอร์', '2024-02-15', 3500.00, 'asset_1763816693_6921b4f59bd9c.png', '2025-11-21 07:48:36'),
(6, 'EQ006', 'เก้าอี้สำนักงาน', 'เฟอร์นิเจอร์', '2024-02-15', 4200.00, 'asset_1763816721_6921b511f052f.jpg', '2025-11-21 07:48:36'),
(7, 'EQ007', 'เครื่องสแกนเนอร์ Canon', 'อุปกรณ์สำนักงาน', '2024-03-01', 6500.00, 'asset_1763816499_6921b433e7619.jpg', '2025-11-21 07:48:36'),
(8, 'EQ008', 'จอคอมพิวเตอร์', 'อุปกรณ์คอมพิวเตอร์', '2024-01-20', 7800.00, 'asset_1763816399_6921b3cfa36c9.jpg', '2025-11-21 07:48:36'),
(9, 'EQ009', 'เครื่องถ่ายเอกสาร', 'อุปกรณ์สำนักงาน', '2024-02-10', 45000.00, 'asset_1763816531_6921b4533defa.jpg', '2025-11-21 07:48:36'),
(10, 'EQ010', 'ตู้เก็บเอกสาร 4 ลิ้นชัก', 'เฟอร์นิเจอร์', '2024-01-25', 5500.00, 'asset_1763815882_6921b1ca067e3.jpg', '2025-11-21 07:48:36'),
(14, 'EQ014', 'โทรศัพท์สำนักงาน', 'อุปกรณ์สำนักงาน', '2024-03-05', 2500.00, 'asset_1763816559_6921b46f6802d.jpg', '2025-11-21 07:48:36'),
(15, 'EQ015', 'เครื่องทำลายเอกสาร', 'อุปกรณ์สำนักงาน', '2024-02-25', 3200.00, 'asset_1763816582_6921b4865d1fe.jpg', '2025-11-21 07:48:36'),
(16, 'EQ016', 'UPS สำรองไฟ 1000VA', 'อุปกรณ์คอมพิวเตอร์', '2024-03-12', 4500.00, 'asset_1763816449_6921b401ab8be.jpg', '2025-11-21 07:48:36'),
(18, 'EQ018', 'ตู้แช่น้ำ', 'เครื่องใช้ไฟฟ้า', '2024-02-05', 12500.00, 'asset_1763816663_6921b4d733ca5.png', '2025-11-21 07:48:36'),
(19, 'EQ019', 'โต๊ะประชุม', 'เฟอร์นิเจอร์', '2024-01-30', 18000.00, 'asset_1763816750_6921b52e42d8a.jpg', '2025-11-21 07:48:36'),
(20, 'EQ020', 'กล้อง CCTV ชุด 4 ตัว', 'อื่นๆ', '2024-03-08', 9500.00, 'asset_1763816771_6921b5433b776.jpg', '2025-11-21 07:48:36'),
(24, 'EQ31', 'ถังขยะแยกประเภท', 'อื่นๆ', '2025-11-01', 950.00, 'asset_1763816995_6921b62360f41.jpg', '2025-11-22 13:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `asset_categories`
--

CREATE TABLE `asset_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `firstName`, `lastName`, `phone`, `username`, `password`) VALUES
(0, 'one', 'onew', '1', 'one', '1'),
(0, 'Josh', 'Jee', '0812345678', 'js', '1234'),
(0, 'John', 'Doe', '0812345678', 'sp', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `asset_categories`
--
ALTER TABLE `asset_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `asset_categories`
--
ALTER TABLE `asset_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
