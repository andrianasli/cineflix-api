-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 11:11 PM
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
-- Database: `db_cinetix`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `seat_count` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `schedule_id`, `seat_count`, `total_price`, `status`) VALUES
(1, 1, 1, 2, 80000.00, 'Pending'),
(2, 2, 3, 1, 35000.00, 'Pending'),
(3, 3, 5, 3, 165000.00, 'Pending'),
(4, 4, 7, 2, 100000.00, 'Pending'),
(5, 5, 9, 1, 45000.00, 'Pending'),
(6, 6, 2, 2, 90000.00, 'Pending'),
(7, 7, 4, 1, 45000.00, 'Pending'),
(8, 8, 6, 4, 220000.00, 'Pending'),
(9, 9, 8, 1, 40000.00, 'Confirmed'),
(11, 3, 5, 2, 90000.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `films`
--

CREATE TABLE `films` (
  `film_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration_min` int(11) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `films`
--

INSERT INTO `films` (`film_id`, `title`, `genre`, `duration_min`, `rating`) VALUES
(1, 'The Last Samurai', 'Action', 154, 'R'),
(2, 'Spirited Away', 'Animation', 125, 'PG'),
(3, 'Your Name', 'Romance', 112, 'PG'),
(4, 'Parasite', 'Thriller', 132, 'R'),
(5, 'Avengers: Endgame', 'Action', 181, 'PG-13'),
(6, 'Joker', 'Crime', 122, 'R'),
(7, 'Interstellar', 'Sci-Fi', 169, 'PG-13'),
(8, 'The Matrix', 'Sci-Fi', 136, 'R'),
(9, 'Inception', 'Sci-Fi', 148, 'PG-13'),
(10, 'Coco', 'Animation', 105, 'PG');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_method`, `payment_status`) VALUES
(1, 1, 'QRIS', 'Pending'),
(2, 2, 'Cash', 'Pending'),
(3, 3, 'Transfer', 'Paid'),
(4, 4, 'QRIS', 'Pending'),
(5, 5, 'Cash', 'Pending'),
(6, 6, 'Transfer', 'Pending'),
(7, 7, 'QRIS', 'Paid'),
(8, 8, 'Cash', 'Pending'),
(9, 9, 'Transfer', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `studio` varchar(20) DEFAULT NULL,
  `show_time` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `film_id`, `studio`, `show_time`, `price`) VALUES
(1, 1, 'Studio 1', '2026-05-15 13:00:00', 40000.00),
(2, 1, 'Studio 2', '2026-05-15 16:30:00', 45000.00),
(3, 2, 'Studio 1', '2026-05-15 10:00:00', 35000.00),
(4, 3, 'Studio 3', '2026-05-15 19:00:00', 45000.00),
(5, 4, 'Studio 2', '2026-05-15 12:30:00', 50000.00),
(6, 5, 'Studio 1', '2026-05-15 20:00:00', 55000.00),
(7, 6, 'Studio 3', '2026-05-15 15:00:00', 45000.00),
(8, 7, 'Studio 2', '2026-05-15 18:00:00', 50000.00),
(9, 8, 'Studio 1', '2026-05-15 14:00:00', 40000.00),
(10, 9, 'Studio 3', '2026-05-15 17:00:00', 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `member_tier` enum('Silver','Gold','Platinum') DEFAULT 'Silver'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `member_tier`) VALUES
(1, 'naruto', 'naruto@konoha.jp', 'Gold'),
(2, 'sakura', 'sakura@konoha.jp', 'Silver'),
(3, 'sasuke', 'sasuke@konoha.jp', 'Platinum'),
(4, 'kakashi', 'kakashi@konoha.jp', 'Gold'),
(5, 'hinata', 'hinata@konoha.jp', 'Silver'),
(6, 'shikamaru', 'shika@konoha.jp', 'Silver'),
(7, 'rocklee', 'lee@konoha.jp', 'Gold'),
(8, 'gaara', 'gaara@suna.jp', 'Platinum'),
(9, 'itachi', 'itachi@akatsuki.jp', 'Platinum'),
(10, 'jiraiya', 'jiraiya@sannin.jp', 'Gold'),
(13, 'faker', 'faker@t1.com', 'Gold'),
(14, 'string', 'string', 'Platinum');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`film_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `film_id` (`film_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `films`
--
ALTER TABLE `films`
  MODIFY `film_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`film_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
