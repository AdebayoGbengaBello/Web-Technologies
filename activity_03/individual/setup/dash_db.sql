-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2025 at 11:41 PM
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
-- Database: `dash_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendanceId` int(11) NOT NULL,
  `sessionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `status` enum('Present','Absent','Late','Excused') NOT NULL DEFAULT 'Absent',
  `checkInTime` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendanceId`, `sessionId`, `userId`, `status`, `checkInTime`, `notes`) VALUES
(1, 1001, 1, 'Present', '2025-10-13 10:02:00', NULL),
(2, 1002, 1, 'Late', '2025-10-15 14:10:00', NULL),
(3, 1003, 1, 'Absent', NULL, NULL),
(4, 1004, 1, 'Present', '2025-10-16 10:58:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `courseId` int(11) NOT NULL,
  `courseName` varchar(100) NOT NULL,
  `courseCode` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructorName` varchar(100) DEFAULT NULL,
  `totalHours` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseId`, `courseName`, `courseCode`, `description`, `instructorName`, `totalHours`) VALUES
(101, 'Web Technologies', 'CS331', 'An introduction to front-end and back-end web development, including HTML, CSS, JavaScript, and PHP.', 'Dr. Ayorkor Korsah', 45),
(102, 'Data Structures & Algorithms', 'CS211', 'A fundamental course on common data structures and algorithms for efficient problem-solving.', 'Dr. David Ebo', 50),
(103, 'Introduction to Artificial Intelligence', 'CS451', 'Exploring the foundational concepts of AI, including search, knowledge representation, and machine learning.', 'Prof. Wiafe', 40);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enrollmentId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress_percentage` int(11) DEFAULT 0,
  `status` enum('Enrolled','Completed','Dropped') NOT NULL DEFAULT 'Enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`enrollmentId`, `userId`, `courseId`, `enrollment_date`, `progress_percentage`, `status`) VALUES
(1, 1, 101, '2025-10-18 20:47:35', 75, 'Enrolled'),
(2, 1, 102, '2025-10-18 20:47:35', 50, 'Enrolled'),
(3, 1, 103, '2025-10-18 20:47:35', 25, 'Enrolled'),
(4, 2, 101, '2025-10-18 20:47:35', 90, 'Enrolled'),
(5, 3, 102, '2025-10-18 20:47:35', 60, 'Enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `sessionId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `sessionTitle` varchar(100) NOT NULL,
  `sessionDate` date NOT NULL,
  `sessionTime` time NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `sessionType` enum('Lecture','Lab','Practical') NOT NULL DEFAULT 'Lecture',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`sessionId`, `courseId`, `sessionTitle`, `sessionDate`, `sessionTime`, `duration`, `location`, `sessionType`, `notes`) VALUES
(1001, 101, 'PHP & Database Connectivity', '2025-10-13', '10:00:00', NULL, NULL, 'Lecture', NULL),
(1002, 101, 'PHP Forms Lab', '2025-10-15', '14:00:00', NULL, NULL, 'Lab', 'Please bring your laptop with XAMPP pre-installed.'),
(1003, 102, 'Arrays and Linked Lists', '2025-10-14', '08:30:00', NULL, NULL, 'Lecture', NULL),
(1004, 103, 'Intro to Search Algorithms', '2025-10-16', '11:00:00', NULL, NULL, 'Lecture', 'Review chapter 3 before class.'),
(2001, 101, 'Introduction to APIs', '2025-10-20', '10:00:00', NULL, NULL, 'Lecture', NULL),
(2002, 101, 'API Practical Session', '2025-10-22', '14:00:00', NULL, NULL, 'Practical', 'We will be using Postman to test our new API endpoints.'),
(2003, 102, 'Trees and Graphs', '2025-10-21', '08:30:00', NULL, NULL, 'Lecture', 'This is a foundational topic for the final project.'),
(2004, 103, 'Machine Learning Basics', '2025-10-23', '11:00:00', NULL, NULL, 'Lecture', NULL),
(2005, 102, 'Sorting Algorithms Lab', '2025-10-28', '13:00:00', NULL, NULL, 'Lab', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `firstName`, `lastName`, `email`, `password_hash`, `date_registered`, `profile_picture`) VALUES
(1, 'Test', 'User', 'student@ashesi.edu.gh', '$2y$10$.wMl3cBbDjlh/vmxr4v18uI3gPA8g.nZmCO9V.1VKpc9Vi4fQjmYe', '2025-10-18 21:40:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendanceId`),
  ADD KEY `sessionId` (`sessionId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseId`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enrollmentId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `courseId` (`courseId`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sessionId`),
  ADD KEY `courseId` (`courseId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendanceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `courseId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `enrollmentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `sessionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2006;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`sessionId`) REFERENCES `sessions` (`sessionId`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `courses` (`courseId`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `courses` (`courseId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
