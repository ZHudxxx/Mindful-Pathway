-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2025 at 07:27 AM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `imgProfile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `username`, `password_hash`, `email`, `bio`, `imgProfile`) VALUES
(0, 'adminjohn', '$2y$10$OC5Rt4hoFQpZt0zDg6MwQWj7l6F5tV4kd8E0tRQUeHKlsYgVv5nvi', 'john@gmail.com', 'Admin for testing', NULL),
(1, 'admin', '$2y$10$GYTpIiOn1.DqCQ.lmKct4e/PRwzEesMJQVqkzPPlR2GuhUq.WYqi.', 'admin@gmail.com', 'saya admin', 'uploads/677f503ac6d13-kucentudung.jpeg'),
(2, 'adminnana', '$2y$10$TnhGK9MRJPcVmr9D3pudGuzq8iMtAjbSTtfKHw5DhwKESLgjzMoEy', 'nana@gmail.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ai_assistance`
--

CREATE TABLE `ai_assistance` (
  `assistanceID` int(11) NOT NULL,
  `interactionDetails` text NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `adminID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `articleID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `coverIMG` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `timePosted` datetime NOT NULL DEFAULT current_timestamp(),
  `authorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`articleID`, `title`, `status`, `coverIMG`, `content`, `timePosted`, `authorID`) VALUES
(0, 'Fear of Sleep: Understanding Sleep Anxiety', 'Approved', NULL, 'Sleep anxiety involves stress and feelings of worry about being able to fall or stay asleep (Staner, 2003). The problem with this is that anxiety over how well or how much a person sleeps can interfere with the quality and quantity of sleep, creating a vicious cycle of sleep issues. While it is not an official diagnosis, sleep anxiety is related to general anxiety and has many common symptoms with other anxiety disorders (Staner, 2003). \r\n\r\nSleep anxiety can involve factors such as insomnia, nightmares, panic attacks, and maladaptive stress responses. Diagnosing underlying conditions involved with sleep anxiety, or ruling them out, is a first step in overcoming a fear of sleep and addressing the frustrating cycle. For that purpose, a polysomnogram is a sleep study that can track data from brain waves, eye movements, breathing rhythm, heart rate, and blood pressure. This test can diagnose sleep apnea or restless legs syndrome, which are treatable conditions related to sleep anxiety (Staner, 2003).', '2024-12-18 00:00:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `commentID` int(11) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `timePosted` time NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `articleID` int(11) DEFAULT NULL,
  `parentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `adminID` int(11) DEFAULT NULL,
  `status` enum('pending','replied','ignored') DEFAULT NULL,
  `response_content` text DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `reviewed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackID`, `userID`, `content`, `adminID`, `status`, `response_content`, `review_date`, `reviewed`) VALUES
(21, 2, 'i luv your website', NULL, 'replied', 'thank you,i hope u have a good day', '2025-01-12 08:55:14', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `imgProfile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `password_hash`, `email`, `bio`, `imgProfile`) VALUES
(2, 'Hannah', '$2y$10$w2AxWAnFtuQOzFkHWYL4n.DwWF.C7GDyi5fMzpo6W/wU//n5wHI/C', 'hannahizzati@gmail.com', 'blub saya', 'uploads/profile_677f51e36d0694.29214963.jpeg'),
(4, 'hani', '$2y$10$mApGZKEouMpWte8Rf0NnkemVvoYArZSL8MPeCdYhPM.Fn5qLbpcU.', 'hani@gmail.com', 'ha nak apa', 'uploads/67712a953d987-pingu.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`articleID`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `articleID` (`articleID`),
  ADD KEY `fk_parent_comment` (`parentID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE SET NULL,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_comment` FOREIGN KEY (`parentID`) REFERENCES `comment` (`commentID`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
