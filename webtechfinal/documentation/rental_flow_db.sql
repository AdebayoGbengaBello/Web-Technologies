-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 12:53 AM
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
-- Database: `rental_flow_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `employee_email` varchar(100) NOT NULL,
  `employee_password` varchar(255) DEFAULT NULL,
  `employee_phone` varchar(20) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_name`, `employee_email`, `employee_password`, `employee_phone`, `manager_id`) VALUES
(1, 'John Doe', 'john@demo.com', '$2y$10$MO8lN8nOH5wpWB9JYeTueOK./Z9Wx1BNEtFC6hYtGAmZD4k7nEXMq', '0209876543', 1),
(2, 'Jane Smith', 'jane@demo.com', '$2y$10$MO8lN8nOH5wpWB9JYeTueOK./Z9Wx1BNEtFC6hYtGAmZD4k7nEXMq', '0245558888', 1);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `log_description` text NOT NULL,
  `log_date` datetime DEFAULT current_timestamp(),
  `property_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `log_description`, `log_date`, `property_id`, `employee_id`, `tenant_id`) VALUES
(2, 'Tenant reported a leaking faucet in the kitchen.', '2025-12-01 10:00:00', 1, 1, 1),
(3, 'Faucet repaired successfully.', '2025-12-02 14:30:00', 1, 1, 1),
(4, 'Monthly inspection completed. No issues found.', '2025-12-05 09:15:00', 2, 2, 2),
(5, 'Scheduled painting for the living room.', '2025-12-10 11:00:00', 3, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `manager_id` int(11) NOT NULL,
  `manager_name` varchar(100) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `manager_email` varchar(100) NOT NULL,
  `manager_password` varchar(250) NOT NULL,
  `manager_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`manager_id`, `manager_name`, `business_name`, `manager_email`, `manager_password`, `manager_phone`) VALUES
(1, 'Sarah Admin', 'Prestige Properties', 'sarah@demo.com', '$2y$10$MO8lN8nOH5wpWB9JYeTueOK./Z9Wx1BNEtFC6hYtGAmZD4k7nEXMq', '0501234567');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_description` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `property_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `payment_description`, `payment_amount`, `payment_date`, `property_id`, `employee_id`, `tenant_id`) VALUES
(2, 'December Rent', 1200.00, '2025-12-01 08:00:00', 1, 1, 1),
(3, 'Security Deposit', 1200.00, '2025-11-01 08:00:00', 1, 1, 1),
(4, 'December Rent', 600.00, '2025-12-03 15:45:00', 2, 2, 2),
(5, 'Security Deposit', 600.00, '2025-11-03 10:00:00', 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(150) NOT NULL,
  `property_description` text DEFAULT NULL,
  `property_rent` decimal(10,2) NOT NULL,
  `property_revenue` decimal(15,2) DEFAULT 0.00,
  `property_image` varchar(255) DEFAULT 'default.webp',
  `manager_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_description`, `property_rent`, `property_revenue`, `property_image`, `manager_id`, `employee_id`, `tenant_id`) VALUES
(1, 'Sunset Villa', 'A beautiful 3-bedroom villa with a pool.', 1200.00, 3600.00, 'default.webp', 1, 1, 1),
(2, 'Downtown Apartment', 'Modern 1-bedroom flat near the city center.', 600.00, 1200.00, 'default.webp', 1, 2, 2),
(3, 'Lakeside Cabin', 'Cozy wooden cabin. Currently undergoing renovations.', 450.00, 0.00, 'default.webp', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenant_id` int(11) NOT NULL,
  `tenant_name` varchar(100) NOT NULL,
  `tenant_email` varchar(100) NOT NULL,
  `tenant_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`tenant_id`, `tenant_name`, `tenant_email`, `tenant_phone`) VALUES
(1, 'Alice Johnson', 'alice@gmail.com', '0551112222'),
(2, 'Bob Williams', 'bob@yahoo.com', '0273334444');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_email` (`employee_email`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `manager_email` (`manager_email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `manager_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `managers` (`manager_id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `logs_ibfk_3` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `managers` (`manager_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_3` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
