-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2025 at 06:04 AM
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
-- Database: `mindfulpathway`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `commentID` int(11) DEFAULT NULL,
  `articleID` int(11) DEFAULT NULL,
  `messages` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `timePosted` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notificationID`, `userID`, `commentID`, `articleID`, `messages`, `is_read`, `timePosted`) VALUES
(1, 2, 5, NULL, 'You have a new reply to your comment.', 1, '2024-12-29 17:01:12'),
(2, 2, 10, 1, '2 have reply to your comment.', 1, '2024-12-29 17:17:16'),
(3, 2, 13, 1, '2 have reply to your comment.', 1, '2025-01-02 12:15:42'),
(4, 2, 1, 1, '2 have reply to your comment.', 0, '2025-01-02 12:42:53'),
(5, 2, 2, 1, '2 have reply to your comment.', 1, '2025-01-02 12:43:12'),
(6, 2, 15, 1, '2 have reply to your comment.', 1, '2025-01-02 12:43:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `commentID` (`commentID`),
  ADD KEY `notifications_ibfk_3` (`articleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`commentID`) REFERENCES `comment` (`commentID`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
