-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 01:25 PM
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
  `class_id` int(100) NOT NULL,
  `class_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lecturer_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `class_name`, `lecturer_name`) VALUES
(1, '1A', 'aaa'),
(2, '1B', 'aaa'),
(4, '1C', 'bbb');

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
(1, 4, 'this is end of exam', 4, 10),
(2, 2, 'goodbye', 4, 5),
(2, 4, 'this is end of exam', 4, 10),
(9, 1, 'hello', 4, 5),
(9, 2, 'goodbye', 4, 5),
(9, 3, 'what is Php', 4, 8);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `user_name` varchar(50) NOT NULL,
  `eid` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct` int(10) DEFAULT NULL,
  `wrong` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`user_name`, `eid`, `score`, `total_questions`, `correct`, `wrong`, `date`) VALUES
('hello', 1, 1, 4, 1, 3, '2025-05-06 06:39:03'),
('hello', 9, 1, 3, 1, 2, '2025-06-07 20:22:08'),
('hello2', 9, 1, 3, 1, 2, '2025-04-14 10:18:21'),
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
  `user_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `eid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_details`
--

INSERT INTO `history_details` (`qid`, `answer_chosen`, `correct_answer`, `score`, `isCorrect`, `answer_id`, `user_name`, `time`, `eid`) VALUES
(1, '1', '1', 1, 1, 1, 'hello', '2025-06-07 20:22:08', 9),
(2, '7', '5', 1, 0, 7, 'hello', '2025-06-07 20:22:08', 9),
(3, '9', '10', 1, 0, 9, 'hello', '2025-06-07 20:22:08', 9);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 9, 'You have been assigned to a class.', 0, '2025-06-02 17:13:05'),
(2, 9, 'You have been assigned to a class.', 0, '2025-06-02 17:54:56'),
(3, 9, 'You have been assigned to a class.', 0, '2025-06-02 17:55:11'),
(4, 9, 'You have been assigned to a class.', 0, '2025-06-02 17:57:16'),
(5, 9, 'You have been assigned to a class.', 0, '2025-06-02 17:57:20'),
(6, 9, 'You have been assigned to a class.', 0, '2025-06-02 18:03:59'),
(7, 9, 'You have been assigned to a class.', 0, '2025-06-02 18:11:11'),
(8, 9, 'You have been assigned to a class.', 0, '2025-06-02 18:11:18'),
(9, 9, 'You have been assigned to a class.', 0, '2025-06-02 18:11:42'),
(10, 9, 'You have been removed from a class.', 0, '2025-06-02 18:16:32'),
(11, 8, 'You have been removed from class: 1B', 0, '2025-06-04 12:15:58'),
(12, 8, 'You have been added to class: 1B', 0, '2025-06-04 12:16:11');

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
(4, 'd', 16, 0),
(5, 'True', 104, 0),
(5, 'False', 105, 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

CREATE TABLE `question_bank` (
  `qid` int(11) NOT NULL,
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
(4, 'this is end of exam', 4, 10),
(5, 'New question', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_type`
--

CREATE TABLE `question_type` (
  `qid` int(11) NOT NULL,
  `type_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_type`
--

INSERT INTO `question_type` (`qid`, `type_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(3, 4),
(4, 4),
(5, 1),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `eid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `class_id` int(10) NOT NULL,
  `exam_time` int(11) NOT NULL DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_open` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`eid`, `title`, `total`, `date`, `class_id`, `exam_time`, `start_time`, `end_time`, `is_open`) VALUES
(1, 'Php & Mysqli', 10, '2025-05-20 05:59:59', 1, 60, '2025-05-06 15:32:00', '2025-05-06 15:34:00', 0),
(2, 'Ip Networking', 10, '2025-04-29 18:08:05', 2, 10, '2025-04-30 01:05:00', '2025-04-30 01:08:00', 0),
(9, 'New exam', 5, '2025-03-25 06:01:02', 2, 20, NULL, NULL, 1);

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
('hello', 1, '2025-06-07 20:22:08', 9, '1B', 'New exam');

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`type_id`, `type_name`) VALUES
(1, 'midterm'),
(2, 'final'),
(3, 'math'),
(4, 'biology'),
(5, 'history'),
(6, 'algebra');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `password`, `real_name`, `role`) VALUES
(2, 'john', 'abc', 'John', 'student'),
(8, 'hello', 'e10adc3949ba59abbe56e057f20f883e', 'hh', 'student'),
(9, 'goodbye', '827ccb0eea8a706c4c34a16891f84e7b', 'aaa', 'lecturer'),
(11, 'hello2', '4297f44b13955235245b2497399d7a93', 'hello2', 'student'),
(12, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'headmaster');

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
(8, 2),
(9, 1),
(11, 2);

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
  ADD PRIMARY KEY (`qid`,`answer_id`,`user_name`,`time`,`eid`),
  ADD KEY `qid` (`qid`),
  ADD KEY `answer_id` (`answer_id`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `eid` (`eid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `question_type`
--
ALTER TABLE `question_type`
  ADD PRIMARY KEY (`qid`,`type_id`),
  ADD KEY `type_id` (`type_id`);

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
  ADD PRIMARY KEY (`user_name`,`time`,`eid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `class_name` (`class_name`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`type_id`);

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
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `optionid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `question_bank`
--
ALTER TABLE `question_bank`
  MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_3` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_questions_ibfk_4` FOREIGN KEY (`qid`) REFERENCES `question_bank` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`user_name`) REFERENCES `users` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history_details`
--
ALTER TABLE `history_details`
  ADD CONSTRAINT `history_details_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `exam_questions` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_details_ibfk_3` FOREIGN KEY (`user_name`) REFERENCES `history` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_details_ibfk_4` FOREIGN KEY (`answer_id`) REFERENCES `options` (`optionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `history_details_ibfk_5` FOREIGN KEY (`eid`) REFERENCES `history` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `question_bank` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_type`
--
ALTER TABLE `question_type`
  ADD CONSTRAINT `question_type_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `question_bank` (`qid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `question_type_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `type` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rank`
--
ALTER TABLE `rank`
  ADD CONSTRAINT `rank_ibfk_1` FOREIGN KEY (`user_name`) REFERENCES `history` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rank_ibfk_2` FOREIGN KEY (`eid`) REFERENCES `quiz` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rank_ibfk_3` FOREIGN KEY (`class_name`) REFERENCES `class` (`class_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_class`
--
ALTER TABLE `users_class`
  ADD CONSTRAINT `users_class_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_class_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
