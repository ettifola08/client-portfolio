--
-- Database: `imperial_asset_db`
--
CREATE DATABASE IF NOT EXISTS `imperial_asset_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `imperial_asset_db`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', 'admin123', 'admin'),
('staff', 'staff123', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--
CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `joined_date` date NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clients`
--
INSERT INTO `clients` (`client_id`, `name`, `contact`, `email`, `joined_date`) VALUES
(1, 'Alice Johnson', '123-456-7890', 'alice.j@example.com', '2022-01-15'),
(2, 'Bob Smith', '987-654-3210', 'bob.s@example.com', '2022-03-22'),
(3, 'Charlie Brown', '555-123-4567', 'charlie.b@example.com', '2022-05-10'),
(4, 'Diana Prince', '444-999-8888', 'diana.p@example.com', '2022-08-01'),
(5, 'Eve Adams', '333-777-6666', 'eve.a@example.com', '2022-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--
CREATE TABLE `investments` (
  `investment_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `investment_type` varchar(255) NOT NULL,
  `amount_invested` decimal(10,2) NOT NULL,
  `current_value` decimal(10,2) NOT NULL,
  `date_invested` date NOT NULL,
  PRIMARY KEY (`investment_id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`client_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `investments`
--
INSERT INTO `investments` (`investment_id`, `client_id`, `investment_type`, `amount_invested`, `current_value`, `date_invested`) VALUES
(1, 1, 'Stocks', 10000.00, 12500.00, '2022-02-01'),
(2, 1, 'Bonds', 5000.00, 5200.00, '2022-03-05'),
(3, 2, 'Real Estate', 50000.00, 65000.00, '2022-04-10'),
(4, 2, 'Mutual Funds', 2000.00, 1800.00, '2022-06-12'),
(5, 3, 'Crypto', 1500.00, 2000.00, '2022-07-20'),
(6, 3, 'Stocks', 8000.00, 7500.00, '2022-09-01'),
(7, 4, 'Bonds', 3000.00, 3100.00, '2022-10-05'),
(8, 4, 'Real Estate', 25000.00, 28000.00, '2022-12-11'),
(9, 5, 'Stocks', 7000.00, 7100.00, '2023-01-25'),
(10, 5, 'Mutual Funds', 1000.00, 1200.00, '2023-02-28');
