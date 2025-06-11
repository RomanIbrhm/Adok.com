-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 01:41 AM
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
-- Database: `singgak_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `pickup_location` varchar(255) DEFAULT NULL,
  `dropoff_location` varchar(255) DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `booking_status` enum('pending','confirmed','completed','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `car_id`, `start_date`, `end_date`, `pickup_location`, `dropoff_location`, `total_price`, `booking_status`, `created_at`) VALUES
(14, 3, 1, '2025-06-12 00:00:00', '2025-06-15 00:00:00', NULL, NULL, 5430.00, 'confirmed', '2025-06-11 22:24:15'),
(15, 4, 3, '2025-06-12 00:00:00', '2025-06-28 00:00:00', NULL, NULL, 21270.00, '', '2025-06-11 22:45:18'),
(16, 4, 2, '2025-06-12 00:00:00', '2025-06-13 00:00:00', NULL, NULL, 590.00, 'confirmed', '2025-06-11 22:45:43'),
(17, 3, 3, '2025-06-12 00:00:00', '2025-06-20 00:00:00', NULL, NULL, 10710.00, 'rejected', '2025-06-11 22:48:37'),
(18, 3, 1, '2025-06-21 00:00:00', '2025-06-27 00:00:00', NULL, NULL, 10710.00, 'rejected', '2025-06-11 22:56:13'),
(19, 3, 1, '2025-06-12 00:00:00', '2025-06-14 00:00:00', NULL, NULL, 3670.00, '', '2025-06-11 23:01:23'),
(20, 3, 2, '2025-06-12 00:00:00', '2025-06-13 00:00:00', NULL, NULL, 590.00, '', '2025-06-11 23:14:52'),
(21, 3, 3, '2025-06-12 00:00:00', '2025-06-18 00:00:00', NULL, NULL, 8070.00, 'pending', '2025-06-11 23:15:21'),
(22, 3, 3, '2025-07-05 00:00:00', '2025-07-06 00:00:00', NULL, NULL, 1470.00, 'pending', '2025-06-11 23:15:38'),
(23, 3, 1, '2025-06-12 00:00:00', '2025-06-26 00:00:00', NULL, NULL, 24790.00, '', '2025-06-11 23:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `seater` int(11) DEFAULT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `status` enum('available','rented','maintenance') NOT NULL DEFAULT 'available',
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `brand`, `model`, `seater`, `transmission`, `fuel_type`, `price_per_day`, `status`, `image_url`) VALUES
(1, 'Rolls Royce', 'Spectre', 4, 'Auto', 'Electric', 1600.00, 'available', 'assets/img/rr_spectre.png'),
(2, 'Bentley', 'Flying Spur', 5, 'Auto', 'Petrol', 400.00, 'available', 'assets/img/bentley_spur.png'),
(3, 'Ferrari', '488', 2, 'Auto', 'Petrol', 1200.00, 'available', 'assets/img/fer_488.png');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_status` enum('successful','rejected','pending') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `transaction_status`, `payment_date`) VALUES
(14, 14, 5430.00, 'Card', 'successful', '2025-06-11 22:24:19'),
(15, 16, 590.00, 'Cash', 'successful', '2025-06-11 22:45:45'),
(16, 17, 10710.00, 'Cash', '', '2025-06-11 22:48:44'),
(17, 18, 10710.00, 'Cash', '', '2025-06-11 22:56:15'),
(18, 21, 8070.00, 'Cash', 'pending', '2025-06-11 23:15:24'),
(19, 22, 1470.00, 'Card', 'successful', '2025-06-11 23:15:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `created_at`) VALUES
(3, 'mito imit', 'mito@gmail.com', '$2y$10$CEA0e4am9xlVu2ZJ/9s87uXpi6kYFGYNRy.dWBqdj0RAD/0awALAy', '2025-06-10 16:35:11'),
(4, 'kevin', 'kevinmuammargathfan@gmail.com', '$2y$10$.E/Bs.PNyJK5vOamXrXi7uyZbbgkn/qTp3.8LRVNkBN3evF17Kzmy', '2025-06-11 22:42:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
