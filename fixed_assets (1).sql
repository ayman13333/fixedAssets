-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2023 at 04:31 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixed_assets`
--

-- --------------------------------------------------------

--
-- Table structure for table `cate_assets`
--

CREATE TABLE `cate_assets` (
  `id` int(255) NOT NULL,
  `num` varchar(255) DEFAULT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) NOT NULL,
  `depreciation_percentage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cate_assets`
--

INSERT INTO `cate_assets` (`id`, `num`, `name_ar`, `name_en`, `depreciation_percentage`) VALUES
(2, '6', 'طاولات', 'طاولات', '5'),
(6, '1', 'كراسي', 'كراسي', '10'),
(8, '3', 'اجهزة كهربائية', 'اجهزة كهربائية', '15'),
(9, '4', 'مكاتب ', 'مكاتب ', '1'),
(10, '5', 'خزائن', 'خزائن', '2');

-- --------------------------------------------------------

--
-- Table structure for table `class_workshop_other`
--

CREATE TABLE `class_workshop_other` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `num` varchar(255) DEFAULT NULL,
  `qr_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `class_workshop_other`
--

INSERT INTO `class_workshop_other` (`id`, `name_ar`, `name_en`, `num`, `qr_image`) VALUES
(1, 'ورشة 1', 'ورشة 1', '1', 'photos/11676752229.png'),
(2, 'ورشة 2', 'ورشة 2', '2', 'photos/21676752229.png'),
(3, 'ورشة 3', 'ورشة 3', '3', 'photos/31676752229.png'),
(4, 'ورشة 4', 'ورشة 4', '4', 'photos/41676752229.png'),
(5, 'ورشة 5', 'ورشة 5', '5', 'photos/51676752229.png'),
(8, 'ورشة 6', 'ورشة 6', '6', 'photos/1677011324.png'),
(9, 'ورشة 7', 'ورشة 7', '7', 'photos/1677099839.png');

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `date_purchase` varchar(255) DEFAULT NULL,
  `price_purchase` varchar(255) DEFAULT NULL,
  `cancel_date` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `qr_text` varchar(255) DEFAULT NULL,
  `qr_image` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `depreciation_value` varchar(255) DEFAULT NULL,
  `categories_id` int(11) NOT NULL,
  `places_id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `workshop_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fixed_assets`
--

INSERT INTO `fixed_assets` (`id`, `name_ar`, `name_en`, `date_purchase`, `price_purchase`, `cancel_date`, `photo`, `qr_text`, `qr_image`, `notes`, `num`, `quantity`, `depreciation_value`, `categories_id`, `places_id`, `floor_id`, `workshop_id`) VALUES
(53, 'test2', 'test2', '2023-02-09', '1000', '2023-02-01', 'photos/1677240165burger-1.jpg', 'editFixedAsset.php?id=', 'photos/1677240165.png', '                                            ', 2, 3, '100', 6, 7, 13, 1),
(54, 'test3', 'test3', '2023-03-25', '500', '2023-02-01', 'photos/1677243960bg_3.jpg', 'editFixedAsset.php?id=', 'photos/1677243960.png', '                                            ', 1, 200, '25', 2, 7, 12, 2),
(55, 'test4', 'test4', '2023-02-01', '100', '2023-04-01', 'photos/1677244038about.jpg', 'editFixedAsset.php?id=', 'photos/1677244038.png', '                                            <br />\r\n', 5, 200, '15', 8, 7, 12, 3),
(56, 'test1', 'test1', '2023-02-28', '5000', '2023-03-28', 'photos/1677244202burger-1.jpg', 'editFixedAsset.php?id=', 'photos/1677244202.png', '                                            ', 100, 20, '50', 9, 7, 12, 4),
(57, 'test11', 'test11', '2023-02-01', '9000', '2023-04-27', 'photos/1677244263dessert-2.jpg', 'editFixedAsset.php?id=', 'photos/1677244263.png', '                                            ', 5, 20, '90', 9, 6, 12, 8);

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `num` varchar(255) NOT NULL,
  `qr_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `name_ar`, `name_en`, `num`, `qr_image`) VALUES
(12, 'الدور 1', 'الدور 1', '1', 'photos/11677099398.png'),
(13, 'الدور 2', 'الدور 2', '2', 'photos/21677099398.png'),
(14, 'الدور 3', 'الدور 3', '3', 'photos/31677099398.png'),
(15, 'الدور 4', 'الدور 4', '4', 'photos/41677099398.png'),
(16, 'الدور 5', 'الدور 5', '5', 'photos/51677099398.png');

-- --------------------------------------------------------

--
-- Table structure for table `loca_assets`
--

CREATE TABLE `loca_assets` (
  `id` int(255) NOT NULL,
  `num` varchar(255) DEFAULT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `qr_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loca_assets`
--

INSERT INTO `loca_assets` (`id`, `num`, `name_ar`, `name_en`, `qr_image`) VALUES
(6, '1', 'مبنى A', 'مبنى A', 'photos/11677239113.png'),
(7, '2', 'مبنىB', 'مبنىB', 'photos/21677239113.png'),
(8, '3', 'مبنى C', 'مبنى C', 'photos/31677239113.png'),
(9, '4', 'مبنى D', 'مبنى D', 'photos/41677239113.png'),
(10, '5', 'مبنى E', 'مبنى E', 'photos/51677239114.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cate_assets`
--
ALTER TABLE `cate_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_workshop_other`
--
ALTER TABLE `class_workshop_other`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories` (`categories_id`),
  ADD KEY `places_id` (`places_id`),
  ADD KEY `workshop_id` (`workshop_id`),
  ADD KEY `floor_id` (`floor_id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loca_assets`
--
ALTER TABLE `loca_assets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cate_assets`
--
ALTER TABLE `cate_assets`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `class_workshop_other`
--
ALTER TABLE `class_workshop_other`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `loca_assets`
--
ALTER TABLE `loca_assets`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD CONSTRAINT `fixed_assets_ibfk_1` FOREIGN KEY (`categories_id`) REFERENCES `cate_assets` (`id`),
  ADD CONSTRAINT `fixed_assets_ibfk_2` FOREIGN KEY (`places_id`) REFERENCES `loca_assets` (`id`),
  ADD CONSTRAINT `fixed_assets_ibfk_3` FOREIGN KEY (`workshop_id`) REFERENCES `class_workshop_other` (`id`),
  ADD CONSTRAINT `fixed_assets_ibfk_4` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
