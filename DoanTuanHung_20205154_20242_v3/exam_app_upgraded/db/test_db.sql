-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2024 at 12:42 PM
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
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(10) NOT NULL,
  `class_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lecturer_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `class_name`, `lecturer_name`) VALUES
(1, '1A', 'X'),
(2, '1B', 'X');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `eid` int(11) NOT NULL,
  `qid` int(11) NOT NULL,
  `question_title` varchar(500) DEFAULT NULL,
  `number_of_choices` int(10) DEFAULT NULL,
  `score` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`eid`, `qid`, `question_title`, `number_of_choices`, `score`) VALUES
(1, 1, 'hello', 4, 5),
(1, 2, 'goodbye', 4, 5),
(1, 3, 'what is Php', 4, 8),
(1, 4, 'this is end of exam', 4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `user_name` varchar(50) NOT NULL,
  `eid` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `correct` int(10) DEFAULT NULL,
  `wrong` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`user_name`, `eid`, `score`, `level`, `correct`, `wrong`, `date`) VALUES
('hello', 2, 26, 10, 9, 1, '2024-10-31 17:11:55'),
('hello', 2, 30, 10, 10, 0, '2024-10-31 17:23:36'),
('john', 1, 22, 10, 8, 2, '2024-10-31 17:27:21');

-- --------------------------------------------------------

--
-- Table structure for table `history_details`
--

CREATE TABLE `history_details` (
  `qid` int(10) NOT NULL,
  `answer_chosen` varchar(50) NOT NULL,
  `correct_answer` varchar(50) NOT NULL,
  `score` int(10) NOT NULL,
  `isCorrect` tinyint(1) DEFAULT NULL,
  `answer_id` int(10) NOT NULL,
  `user_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `lecturer_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `lecturer_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`lecturer_id`, `email`, `password`, `lecturer_name`) VALUES
(1, 'a@gmail.com', 'hello', 'X');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `qid` int(50) NOT NULL,
  `option` varchar(5000) NOT NULL,
  `optionid` int(11) NOT NULL,
  `isCorrect` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`qid`, `option`, `optionid`, `isCorrect`) VALUES
(1, 'how r u', 1, 1),
(1, 'beta', 2, 0),
(1, 'testing', 3, 0),
(1, 'exam', 4, 0),
(2, 'see u', 5, 1),
(2, 'again', 6, 0),
(2, 'soon', 7, 0),
(2, 'wtf', 8, 0),
(3, '1', 9, 0),
(3, '2', 10, 1),
(3, '3', 11, 0),
(3, '4', 12, 0),
(4, 'a', 13, 0),
(4, 'b', 14, 0),
(4, 'c', 15, 1),
(4, 'd', 16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

CREATE TABLE `question_bank` (
  `qid` int(10) NOT NULL,
  `question_title` varchar(100) NOT NULL,
  `number_of_choices` int(10) NOT NULL,
  `score` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_bank`
--

INSERT INTO `question_bank` (`qid`, `question_title`, `number_of_choices`, `score`) VALUES
(1, 'hello', 4, 5),
(2, 'goodbye', 4, 5),
(3, 'what is Php', 4, 8),
(4, 'this is end of exam', 4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `eid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `class_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`eid`, `title`, `total`, `date`, `class_id`) VALUES
(1, 'Php & Mysqli', 10, '2024-11-03 17:14:53', 1),
(2, 'Ip Networking', 10, '2024-11-03 17:14:55', 2);

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE `rank` (
  `user_name` varchar(100) NOT NULL,
  `score` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eid` int(11) NOT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`user_name`, `score`, `time`, `eid`, `class_name`, `title`) VALUES
('hello', 30, '2024-11-05 22:49:31', 2, '1A', 'Ip Networking'),
('john', 22, '2024-11-05 22:49:19', 1, '1B', 'Php & Mysqli');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `password`, `name`) VALUES
(2, 'john', 'abc', 'John'),
(3, 'hello', '827ccb0eea8a706c4c34a16891f84e7b', 'aaa');

-- --------------------------------------------------------

--
-- Table structure for table `users_class`
--

CREATE TABLE `users_class` (
  `user_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_class`
--

INSERT INTO `users_class` (`user_id`, `class_id`) VALUES
(2, 1),
(2, 2),
(3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`,`class_name`),
  ADD KEY `class_name` (`class_name`),
  ADD KEY `lecturer_name` (`lecturer_name`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`eid`,`qid`),
  ADD KEY `qid` (`qid`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`user_name`,`eid`,`date`),
  ADD KEY `eid` (`eid`);

--
-- Indexes for table `history_details`
--
ALTER TABLE `history_details`
  ADD PRIMARY KEY (`qid`,`answer_id`,`user_name`),
  ADD KEY `qid` (`qid`),
  ADD KEY `answer_id` (`answer_id`),
  ADD KEY `user_name` (`user_name`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`lecturer_id`,`lecturer_name`),
  ADD KEY `lecturer_name` (`lecturer_name`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`optionid`),
  ADD KEY `qid` (`qid`);

--
-- Indexes for table `question_bank`
--
ALTER TABLE `question_bank`
  ADD PRIMARY KEY (`qid`),
  ADD KEY `qid` (`qid`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`eid`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `rank`
--
ALTER TABLE `rank`
  ADD PRIMARY KEY (`user_name`,`eid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `class_name` (`class_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `users_class`
--
ALTER TABLE `users_class`
  ADD PRIMARY KEY (`user_id`,`class_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`lecturer_name`) REFERENCES `lecturer` (`lecturer_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `quiz` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`),
  ADD CONSTRAINT `exam_questions_ibfk_2` FOREIGN KEY (`qid`) REFERENCES `options` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history_details`
--
ALTER TABLE `history_details`
  ADD CONSTRAINT `history_details_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `exam_questions` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_details_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `options` (`optionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_details_ibfk_3` FOREIGN KEY (`user_name`) REFERENCES `history` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_bank`
--
ALTER TABLE `question_bank`
  ADD CONSTRAINT `question_bank_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `exam_questions` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rank`
--
ALTER TABLE `rank`
  ADD CONSTRAINT `rank_ibfk_1` FOREIGN KEY (`user_name`) REFERENCES `history` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rank_ibfk_2` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rank_ibfk_3` FOREIGN KEY (`class_name`) REFERENCES `class` (`class_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_name`) REFERENCES `history` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id`) REFERENCES `users_class` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_class`
--
ALTER TABLE `users_class`
  ADD CONSTRAINT `users_class_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
