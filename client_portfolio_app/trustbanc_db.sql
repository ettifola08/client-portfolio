-- The name of the database is 'portfolio_db'
-- This script contains updated tables for the new, fully-functional application.

CREATE DATABASE IF NOT EXISTS `trustbanc_db`;

USE `trustbanc_db`;

--
-- Table structure for table `users`
--
-- This table now includes a `risk_profile` field.
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `risk_profile` varchar(50) DEFAULT 'Conservative', -- Default risk profile
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `portfolios`
--
-- This table stores individual asset holdings for each user.
--
CREATE TABLE `portfolios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `quantity` decimal(15, 6) NOT NULL,
  `purchase_price` decimal(15, 2) NOT NULL, -- Price per unit at purchase
  `current_price` decimal(15, 2) NOT NULL,  -- Current market price (can be updated later)
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `financial_goals`
--
-- Stores the user's financial goals.
--
CREATE TABLE `financial_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `goal_name` varchar(255) NOT NULL,
  `target_amount` decimal(15, 2) NOT NULL,
  `saved_amount` decimal(15, 2) DEFAULT '0.00',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `transactions`
--
-- A new table to log all 'buy' and 'sell' actions for a portfolio.
--
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `transaction_type` ENUM('buy', 'sell') NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `quantity` decimal(15, 6) NOT NULL,
  `price_per_unit` decimal(15, 2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
