-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2025 at 05:27 PM
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
  `category` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `coverIMG` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `timePosted` datetime NOT NULL DEFAULT current_timestamp(),
  `authorID` int(11) DEFAULT NULL,
  `summary` text NOT NULL DEFAULT 'No summary provided',
  `tags` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`articleID`, `title`, `category`, `status`, `coverIMG`, `content`, `timePosted`, `authorID`, `summary`, `tags`) VALUES
(1, 'Fear of Sleep: Understanding Sleep Anxiety', '', 'Approved', 'banner1.png', 'Sleep anxiety involves stress and feelings of worry about being able to fall or stay asleep (Staner, 2003). The problem with this is that anxiety over how well or how much a person sleeps can interfere with the quality and quantity of sleep, creating a vicious cycle of sleep issues. While it is not an official diagnosis, sleep anxiety is related to general anxiety and has many common symptoms with other anxiety disorders (Staner, 2003). \r\n\r\nSleep anxiety can involve factors such as insomnia, nightmares, panic attacks, and maladaptive stress responses. Diagnosing underlying conditions involved with sleep anxiety, or ruling them out, is a first step in overcoming a fear of sleep and addressing the frustrating cycle. For that purpose, a polysomnogram is a sleep study that can track data from brain waves, eye movements, breathing rhythm, heart rate, and blood pressure. This test can diagnose sleep apnea or restless legs syndrome, which are treatable conditions related to sleep anxiety (Staner, 2003).', '2024-12-18 00:00:00', 8, 'No summary provided', NULL),
(2, 'Young people’s mental health is finally getting the attention it needs', '', 'Rejected', 'favicon.png', 'Worldwide, at least 13% of people between the ages of 10 and 19 live with a diagnosed mental-health disorder, according to the latest State of the World’s Children report, published this week by the United Nations children’s charity UNICEF. It’s the first time in the organization’s history that this flagship report has tackled the challenges in and opportunities for preventing and treating mental-health problems among young people. It reveals that adolescent mental health is highly complex, understudied — and underfunded. These findings are echoed in a parallel collection of review articles published this week in a number of Springer Nature journals.\r\n\r\nAnxiety and depression constitute more than 40% of mental-health disorders among young people (those aged 10–19). UNICEF also reports that, worldwide, suicide is the fourth most-common cause of death (after road injuries, tuberculosis and interpersonal violence) among adolescents (aged 15–19). In eastern Europe and central Asia, suicide is the leading cause of death for young people in that age group — and it’s the second-highest cause in western Europe and North America.\r\n\r\n\r\nCollection: Promoting youth mental health\r\n\r\nSadly, psychological distress among young people seems to be rising. One study found that rates of depression among a nationally representative sample of US adolescents (aged 12 to 17) increased from 8.5% of young adults to 13.2% between 2005 and 20171. There’s also initial evidence that the coronavirus pandemic is exacerbating this trend in some countries. For example, in a nationwide study2 from Iceland, adolescents (aged 13–18) reported significantly more symptoms of mental ill health during the pandemic than did their peers before it. And girls were more likely to experience these symptoms than were boys.\r\n\r\nAlthough most mental-health disorders arise during adolescence, UNICEF says that only one-third of investment in mental-health research is targeted towards young people. Moreover, the research itself suffers from fragmentation — scientists involved tend to work inside some key disciplines, such as psychiatry, paediatrics, psychology and epidemiology, and the links between research and health-care services are often poor. This means that effective forms of prevention and treatment are limited, and lack a solid understanding of what works, in which context and why.\r\n\r\nThis week’s collection of review articles dives deep into the state of knowledge of interventions — those that work and those that don’t — for preventing and treating anxiety and depression in young people aged 14–24. In some of the projects, young people with lived experience of anxiety and depression were co-investigators, involved in both the design and implementation of the reviews, as well as in interpretation of the findings.', '2024-12-28 00:00:00', 2, 'No summary provided', NULL),
(3, 'What is Mental Illness?', '', 'Approved', NULL, 'Mental Health...involves effective functioning in daily activities resulting in:\r\n\r\nProductive activities (such as in work, school or caregiving).\r\nHealthy relationships.\r\nAbility to adapt to change and cope with adversity.\r\nMental Illness...refers collectively to all diagnosable mental disorders — health conditions involving:\r\n\r\nSignificant changes in thinking, emotion and/or behavior.\r\nDistress and/or problems functioning in social, work or family activities.\r\nMental health is the foundation for emotions, thinking, communication, learning, resilience, hope and self-esteem. Mental health is also key to relationships, personal and emotional well-being and contributing to community or society. Mental health is a component of overall well-being. It can influence and be influenced by physical health.\r\n\r\nMany people who have a mental illness do not want to talk about it. But mental illness is nothing to be ashamed of! It is a medical condition, just like heart disease or diabetes. And mental health conditions are treatable. We are continually expanding our understanding of how the human brain works, and treatments are available to help people successfully manage mental health conditions.\r\n\r\nMental illness does not discriminate; it can affect anyone regardless of your age, gender, geography, income, social status, race, ethnicity, religion/spirituality, sexual orientation, background or other aspect of cultural identity. While mental illness can occur at any age, three-fourths of all mental illness begins by age 24.\r\n\r\nMental illnesses take many forms. Some are mild and only interfere in limited ways with daily life, such as some phobias (abnormal fears). Other mental health conditions are so severe that a person may need care in a hospital. Similar to other medical illnesses, the optimal ways to provide care depend on the illness and the severity of its impact.', '2024-12-28 00:00:00', 2, 'No summary provided', NULL),
(4, 'What is Mental Illness?', '', 'Approved', NULL, 'Mental Health...involves effective functioning in daily activities resulting in:\r\n\r\nProductive activities (such as in work, school or caregiving).\r\nHealthy relationships.\r\nAbility to adapt to change and cope with adversity.\r\nMental Illness...refers collectively to all diagnosable mental disorders — health conditions involving:\r\n\r\nSignificant changes in thinking, emotion and/or behavior.\r\nDistress and/or problems functioning in social, work or family activities.\r\nMental health is the foundation for emotions, thinking, communication, learning, resilience, hope and self-esteem. Mental health is also key to relationships, personal and emotional well-being and contributing to community or society. Mental health is a component of overall well-being. It can influence and be influenced by physical health.\r\n\r\nMany people who have a mental illness do not want to talk about it. But mental illness is nothing to be ashamed of! It is a medical condition, just like heart disease or diabetes. And mental health conditions are treatable. We are continually expanding our understanding of how the human brain works, and treatments are available to help people successfully manage mental health conditions.\r\n\r\nMental illness does not discriminate; it can affect anyone regardless of your age, gender, geography, income, social status, race, ethnicity, religion/spirituality, sexual orientation, background or other aspect of cultural identity. While mental illness can occur at any age, three-fourths of all mental illness begins by age 24.\r\n\r\nMental illnesses take many forms. Some are mild and only interfere in limited ways with daily life, such as some phobias (abnormal fears). Other mental health conditions are so severe that a person may need care in a hospital. Similar to other medical illnesses, the optimal ways to provide care depend on the illness and the severity of its impact.', '2024-12-28 00:00:00', 4, 'No summary provided', NULL),
(5, 'Bertabahlaah Sayang', 'self-care', 'Pending', 'uploads/11 January 2025 (2).png', 'Semua insan sedang gembira di hari ini, sayang\r\nHari yang mulia, marilah bersama berhari raya, hilangkan duka\r\nLupakan saja kisah yang lalu, gantikan cerita baru\r\nAgar hatimu waspada selalu\r\nJangan kau kesalkan, jangan engkau tangiskan\r\nPada mereka yang \'tak mengerti\r\nMenuduh dirimu, di jurang kesalahan saja\r\n\'Ku \'tak sampai hati, biarkan engkau sendiri\r\nTetapi diriku dan juga temanmu\r\nYang tahu kisah derita dan luka di dada\r\nHanya \'ku harapkan kau harus bersabar dan bertenang selalu\r\nPada mereka yang \'tak mengerti\r\nMenuduh dirimu di jurang kesalahan saja\r\n\'Ku \'tak sampai hati biarkan engkau sendiri\r\nTetapi diriku dan juga temanmu\r\nYang tahu kisah derita dan luka di dada\r\nHanya \'ku harapkan kau harus bersabar dan bertenang selalu', '2025-01-12 23:28:38', 8, '', ''),
(6, 'Hot N Cold', 'personal-growth', 'Pending', 'uploads/pxArt (7).png', 'You change your mind\r\nLike a girl changes clothes\r\nYeah, you PMS like a bitch\r\nI would know\r\nAnd you overthink\r\nAlways speak cryptically\r\nI should know\r\nThat you\'re no good for me\r\n\'Cause you\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nYou\'re wrong when it\'s right\r\nIt\'s black and it\'s white\r\nWe fight, we break up\r\nWe kiss, we make up\r\nyou don\'t really want to stay, no\r\n(You) but you don\'t really want to go\r\nYou\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nWe used to be just like twins\r\nSo in sync\r\nThe same energy now\'s a dead battery\r\nUsed to laugh \'bout nothing\r\nNow you\'re plain boring\r\nI should know\r\nThat you\'re not gonna change\r\n\'Cause you\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nYou\'re wrong when it\'s right\r\nIt\'s black and it\'s white\r\nWe fight, we break up\r\nWe kiss, we make up\r\nyou don\'t really want to stay, no\r\n(You) but you don\'t really want to go\r\nYou\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nSomeone call the doctor\r\nGot a case of a love bipolar\r\nStuck on a roller coaster\r\nCan\'t get off this ride\r\nYou change your mind\r\nLike a girl changes clothes\r\n\'Cause you\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nYou\'re wrong when it\'s right\r\nIt\'s black and it\'s white\r\nWe fight, we break up\r\nWe kiss, we make up\r\nYou\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down\r\nYou\'re wrong when it\'s right\r\nIt\'s black and it\'s white\r\nWe fight, we break up\r\nWe kiss, we make up\r\nyou don\'t really want to stay, no\r\n(You) but you don\'t really want to go\r\nYou\'re hot then you\'re cold\r\nYou\'re yes then you\'re no\r\nYou\'re in then you\'re out\r\nYou\'re up then you\'re down', '2025-01-12 23:49:56', 8, '', ''),
(7, 'New Home', 'mental-health-and-wellness', 'Pending', 'uploads/16-removebg-preview.png', 'Sometimes we swim in our sadness so long\r\nThat the pain starts to feel like home\r\nGet out of the waters and dry up those tears\r\n\'Cause out here, the joy makes us whole\r\nWe\'ll find new places and make it ours\r\nWe\'ll find new spaces to breathe\r\nMaybe a change is what we deserve right now\r\n\'Cause now you feel like home\r\nI\'d like to walk through the unknown with you\r\nI thought I\'ve loved enough, cried enough\r\nLonely was all I could be\r\nBut you proved me wrong\r\n\'Cause you\'re my-\r\nYou\'re my new home\r\nI\'ve been asleep in the darkness too long\r\nI wouldn\'t know good if it hit me\r\nOh, but the lies, thought it was cruel\r\nBut its kind, said it may be time to move on\r\n\'Cause we\'ve found new places and made it ours\r\nWe\'ve found new spaces to breathe\r\nI know better days are what we deserve right now\r\n\'Cause now you feel like home\r\nI\'d like to walk through the unknown with you\r\nI thought I\'ve loved enough, cried enough\r\nLonely was all I could be\r\nBut you proved me wrong\r\n\'Cause you\'re my-\r\nYou\'re my new home\r\nAnd maybe this is too soon to say\r\nBut now that you\'re here\r\nI won\'t go back\r\n\'Cause now you feel like home\r\nI\'d like to walk through the unknown with you\r\nI thought I\'ve loved enough, cried enough\r\nLonely was all I could be\r\nBut you proved me wrong\r\n\'Cause you\'re my-\r\nYou\'re my new home\r\nYou\'re my-\r\nYou\'re my new home', '2025-01-12 23:55:19', 8, '', '');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notificationID`, `userID`, `commentID`, `articleID`, `messages`, `is_read`, `timePosted`) VALUES
(0, 8, 1, 0, 'Fadzillah have reply to your comment.', 1, '2025-01-12 21:24:29'),
(0, 8, 2, 0, 'Fadzillah have reply to your comment.', 1, '2025-01-12 21:24:44'),
(0, 8, 4, 0, 'Fadzillah have reply to your comment.', 1, '2025-01-12 21:25:06'),
(0, 8, 3, 0, 'iyah have reply to your comment.', 1, '2025-01-12 21:26:21'),
(0, 8, 8, 2, 'Fadzillah have reply to your comment.', 0, '2025-01-12 22:07:46');

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
(4, 'hani', '$2y$10$mApGZKEouMpWte8Rf0NnkemVvoYArZSL8MPeCdYhPM.Fn5qLbpcU.', 'hani@gmail.com', 'ha nak apa', 'uploads/67712a953d987-pingu.jpeg'),
(8, 'Fadzillah', '$2y$10$lJn85HEVfuyFvjKgrMSeh./W6ESgn0..WB8COMO9fYxaFsdtSYq3.', 'fadzillahroslan5903@gmail.com', NULL, NULL),
(9, 'iyah', '$2y$10$oqha9WAlRyP/.zRXUzPxs.5CLVle0Gd7HUeGlHhbm7Yo3qP0u1Ve2', 'fadzrose5903@gmail.com', NULL, NULL);

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
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `articleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
