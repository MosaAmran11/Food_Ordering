-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2022 at 05:15 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,  -- زيادة الطول ليتناسب مع التشفير
  UNIQUE (`name`)  -- فرض عدم تكرار اسم المستخدم
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL UNIQUE,  -- فرض عدم تكرار البريد الإلكتروني
  `number` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,  -- زيادة الطول ليتناسب مع التشفير
  `address` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),  -- Add index for foreign key
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE  -- ربط مع جدول المستخدمين
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),  -- Add index for foreign key
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE  -- ربط مع جدول المستخدمين
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),  -- Add index for foreign key
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE  -- ربط مع جدول المستخدمين
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE (`name`);  -- Ensure unique constraint on name

--
-- Indexes for table `cart`
--
-- ALTER TABLE `cart`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `user_id` (`user_id`);  -- Add index for foreign key

--
-- Indexes for table `messages`
--
-- ALTER TABLE `messages`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `user_id` (`user_id`);  -- Add index for foreign key

--
-- Indexes for table `orders`
--
-- ALTER TABLE `orders`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `user_id` (`user_id`);  -- Add index for foreign key

--
-- Indexes for table `products`
--
-- ALTER TABLE `products`
--   ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
-- ALTER TABLE `users`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `email` (`email`);  -- Ensure unique constraint on email

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- Init a superadmin in table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'system', '');  -- استخدم دالة تجزئة مثل password_hash في PHP لتوليد هذا


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;