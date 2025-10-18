--
-- Step 1: Create all table structures first
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`)
);

CREATE TABLE `courses` (
  `courseId` int(11) NOT NULL AUTO_INCREMENT,
  `courseName` varchar(100) NOT NULL,
  `courseCode` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructorName` varchar(100) DEFAULT NULL,
  `totalHours` int(11) DEFAULT 0,
  PRIMARY KEY (`courseId`)
);

CREATE TABLE `enrollment` (
  `enrollmentId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress_percentage` int(11) DEFAULT 0,
  `status` enum('Enrolled','Completed','Dropped') NOT NULL DEFAULT 'Enrolled',
  PRIMARY KEY (`enrollmentId`),
  KEY `userId` (`userId`),
  KEY `courseId` (`courseId`)
);

CREATE TABLE `sessions` (
  `sessionId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `sessionTitle` varchar(100) NOT NULL,
  `sessionDate` date NOT NULL,
  `sessionTime` time NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `sessionType` enum('Lecture','Lab','Practical') NOT NULL DEFAULT 'Lecture',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`sessionId`),
  KEY `courseId` (`courseId`)
);

CREATE TABLE `attendance` (
  `attendanceId` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `status` enum('Present','Absent','Late','Excused') NOT NULL DEFAULT 'Absent',
  `checkInTime` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`attendanceId`),
  KEY `sessionId` (`sessionId`),
  KEY `userId` (`userId`)
);

--
-- Step 2: Insert all the sample data
--

INSERT INTO `users` (`userId`, `firstName`, `lastName`, `email`, `password_hash`, `date_registered`, `profile_picture`) VALUES
(1, 'Test', 'User', 'student@ashesi.edu.gh', '$2y$10$.wMl3cBbDjlh/vmxr4v18uI3gPA8g.nZmCO9V.1VKpc9Vi4fQjmYe', '2025-10-18 21:40:36', NULL);

INSERT INTO `courses` (`courseId`, `courseName`, `courseCode`, `description`, `instructorName`, `totalHours`) VALUES
(101, 'Web Technologies', 'CS331', 'An introduction to front-end and back-end web development.', 'Dr. Ayorkor Korsah', 45),
(102, 'Data Structures & Algorithms', 'CS211', 'A fundamental course on common data structures and algorithms.', 'Dr. David Ebo', 50),
(103, 'Introduction to Artificial Intelligence', 'CS451', 'Exploring the foundational concepts of AI.', 'Prof. Wiafe', 40);

INSERT INTO `enrollment` (`userId`, `courseId`, `progress_percentage`, `status`) VALUES
(1, 101, 75, 'Enrolled'),
(1, 102, 50, 'Enrolled'),
(1, 103, 25, 'Enrolled'),
(2, 101, 90, 'Enrolled'),
(3, 102, 60, 'Enrolled');

INSERT INTO `sessions` (`sessionId`, `courseId`, `sessionTitle`, `sessionDate`, `sessionTime`, `sessionType`, `notes`) VALUES
(1001, 101, 'PHP & Database Connectivity', '2025-10-13', '10:00:00', 'Lecture', NULL),
(1002, 101, 'PHP Forms Lab', '2025-10-15', '14:00:00', 'Lab', 'Please bring your laptop with XAMPP pre-installed.'),
(1003, 102, 'Arrays and Linked Lists', '2025-10-14', '08:30:00', 'Lecture', NULL),
(1004, 103, 'Intro to Search Algorithms', '2025-10-16', '11:00:00', 'Lecture', 'Review chapter 3 before class.'),
(2001, 101, 'Introduction to APIs', '2025-10-20', '10:00:00', 'Lecture', NULL),
(2002, 101, 'API Practical Session', '2025-10-22', '14:00:00', 'Practical', 'We will be using Postman.'),
(2003, 102, 'Trees and Graphs', '2025-10-21', '08:30:00', 'Lecture', 'This is a foundational topic.'),
(2004, 103, 'Machine Learning Basics', '2025-10-23', '11:00:00', 'Lecture', NULL),
(2005, 102, 'Sorting Algorithms Lab', '2025-10-28', '13:00:00', 'Lab', NULL);

INSERT INTO `attendance` (`sessionId`, `userId`, `status`, `checkInTime`) VALUES
(1001, 1, 'Present', '2025-10-13 10:02:00'),
(1002, 1, 'Late', '2025-10-15 14:10:00'),
(1003, 1, 'Absent', NULL),
(1004, 1, 'Present', '2025-10-16 10:58:00');

--
-- Step 3: Add all constraints after the data is inserted
--

ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `courses` (`courseId`) ON DELETE CASCADE;

ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `courses` (`courseId`) ON DELETE CASCADE;

ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`sessionId`) REFERENCES `sessions` (`sessionId`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE;