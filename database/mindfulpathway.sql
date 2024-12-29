-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 04:38 AM
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
  `timePosted` date NOT NULL DEFAULT current_timestamp(),
  `authorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`articleID`, `title`, `status`, `coverIMG`, `content`, `timePosted`, `authorID`) VALUES
(1, 'Fear of Sleep: Understanding Sleep Anxiety', 'Approved', NULL, 'Sleep anxiety involves stress and feelings of worry about being able to fall or stay asleep (Staner, 2003). The problem with this is that anxiety over how well or how much a person sleeps can interfere with the quality and quantity of sleep, creating a vicious cycle of sleep issues. While it is not an official diagnosis, sleep anxiety is related to general anxiety and has many common symptoms with other anxiety disorders (Staner, 2003). \r\n\r\nSleep anxiety can involve factors such as insomnia, nightmares, panic attacks, and maladaptive stress responses. Diagnosing underlying conditions involved with sleep anxiety, or ruling them out, is a first step in overcoming a fear of sleep and addressing the frustrating cycle. For that purpose, a polysomnogram is a sleep study that can track data from brain waves, eye movements, breathing rhythm, heart rate, and blood pressure. This test can diagnose sleep apnea or restless legs syndrome, which are treatable conditions related to sleep anxiety (Staner, 2003).', '2024-12-18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `commentID` int(11) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `articleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `content` text NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `adminID` int(11) DEFAULT NULL
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
(1, 'Melissa Madeson', 'melissa', 'melissa@gmail.com', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `ai_assistance`
--
ALTER TABLE `ai_assistance`
  ADD PRIMARY KEY (`assistanceID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`articleID`),
  ADD KEY `authorID` (`authorID`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `articleID` (`articleID`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_assistance`
--
ALTER TABLE `ai_assistance`
  MODIFY `assistanceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `articleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_assistance`
--
ALTER TABLE `ai_assistance`
  ADD CONSTRAINT `ai_assistance_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `ai_assistance_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`);

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`authorID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
