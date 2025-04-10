-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 16, 2025 at 08:03 AM
-- Server version: 5.7.34
-- PHP Version: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bhouses_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `boarding_houses`
--

CREATE TABLE `boarding_houses` (
  `id` int(11) NOT NULL,
  `boarding_house_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `mini_image1` varchar(255) DEFAULT NULL,
  `mini_image2` varchar(255) DEFAULT NULL,
  `mini_image3` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `gender_requirements` enum('Both','Male','Female') DEFAULT NULL,
  `distance_from_university` enum('Near','Moderate','Far') DEFAULT NULL,
  `rental_price` enum('Cheap','Moderate','Expensive') DEFAULT NULL,
  `accommodation_type` enum('Room Space','Bed Space') DEFAULT NULL,
  `kitchen_type` enum('Shared','Exclusive') DEFAULT NULL,
  `toilet_type` enum('Shared','Exclusive') DEFAULT NULL,
  `landlord_quarters` enum('Same Building','Nearby','Far from boarding house') DEFAULT NULL,
  `pet_policy` enum('Allowed','Not Allowed') DEFAULT NULL,
  `visitor_policy` enum('Limited','Not Allowed') DEFAULT NULL,
  `noise_policy` enum('Strict','Moderate','Low') DEFAULT NULL,
  `curfew` enum('Yes','No') DEFAULT NULL,
  `fire_extinguisher` enum('Yes','None') DEFAULT NULL,
  `surveillance_camera` enum('Yes','None') DEFAULT NULL,
  `fence_material` enum('Metal','Wood','Concrete','None') DEFAULT NULL,
  `building_material` enum('Wood','Concrete','Mixed') DEFAULT NULL,
  `wifi_availability` enum('Yes','No') DEFAULT NULL,
  `wifi_billing` enum('Free','Included In Rent','Separated Bill') DEFAULT NULL,
  `electricity_included` enum('Yes','No') DEFAULT NULL,
  `electricity_billing` enum('Metered','Fixed') DEFAULT NULL,
  `water_included` enum('Yes','No') DEFAULT NULL,
  `water_billing` enum('Metered','Fixed') DEFAULT NULL,
  `description` text,
  `landlord_name` varchar(255) DEFAULT NULL,
  `landlord_permanent_address` varchar(255) DEFAULT NULL,
  `landlord_contact_number` varchar(20) DEFAULT NULL,
  `room_count` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `boarding_house_id` int(11) NOT NULL,
  `room_type` enum('Room Space','Bed Space') DEFAULT NULL,
  `room_price` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boarding_houses`
--
ALTER TABLE `boarding_houses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boarding_house_id` (`boarding_house_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boarding_houses`
--
ALTER TABLE `boarding_houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
