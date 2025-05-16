-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 06:09 PM
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
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `customername` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `customername`, `comment`, `rating`, `activity_date`) VALUES
(1, 'marjon pogi', 'mabis po dumating salamat', 4, '2025-05-12 23:38:58'),
(2, 'marjon pogi', 'nkon', 2, '2025-05-12 23:49:52'),
(3, 'marjon pogi', 'mabilis dumating at complete lahat', 5, '2025-05-13 18:59:11'),
(4, 'marjon pogi', 'sanaol', 5, '2025-05-13 19:02:13'),
(5, 'marjon pogi', 'lami', 5, '2025-05-13 20:51:01'),
(6, 'marjon pogi', 'wowowowwowow mabilis mabilis', 2, '2025-05-15 01:51:55'),
(7, 'marjon pogi', 'wowowowwowow mabilis mabilis', 5, '2025-05-15 02:20:26'),
(8, 'marjon pogi', 'salamat sa lamian nga rice', 5, '2025-05-15 03:41:50'),
(9, 'marjon pogi', 'wow', 5, '2025-05-15 04:14:34'),
(10, 'marjon pogi', 'salamuch', 5, '2025-05-15 09:51:11'),
(11, 'marjon pogi', 'gggg', 4, '2025-05-15 19:34:29'),
(12, 'marjon pogi', 'mabilis', 5, '2025-05-16 00:09:29'),
(13, 'marjon pogi', 'fvdfgft', 3, '2025-05-16 05:01:50');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `total_pay` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `time_in`, `time_out`, `hours_worked`, `total_pay`) VALUES
(2, 1, '2025-05-07 02:20:00', '2025-05-07 21:30:00', 19.17, 1916.67),
(4, 1, '2025-05-07 13:30:00', '2025-05-07 20:30:00', 7.00, 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customername` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customername`, `email`, `address`, `phone_number`, `password`, `created_at`) VALUES
(1, 'marjon pogi', '20191336@nbsc.edu.ph', 'Balanban City', '09309354237', '$2y$10$Yk81gbiSeQvirDQA7fLPkecaw.nXNPmIUCGXOoTzoEjdLLYz7cW7K', '2025-05-08 15:25:06'),
(2, 'hahaha', 'myles@gmail.com', 'zonw 2', '09309354237', '$2y$10$rZFo1GCWGcjHTVi3n08hLOpM.CppurMPcQGZ23EDlW8oLn6NXpqti', '2025-05-09 11:11:09'),
(3, 'vella', '20191337@nbsc.edu.ph', 'cagayan', '09309354237', '$2y$10$zv3XLjDdAFAyGxB0Td7zmumzvy7CgpHIf/P9K6bMA4LqKpnw2MRQG', '2025-05-16 05:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL,
  `notification` tinyint(1) DEFAULT 0,
  `status` varchar(10) DEFAULT 'unread',
  `customer_reply` text DEFAULT NULL,
  `reply_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `customer_id`, `order_id`, `feedback`, `rating`, `created_at`, `reply`, `notification`, `status`, `customer_reply`, `reply_read`) VALUES
(12, 1, 5, 'wow', 5, '2025-05-15 12:14:34', 'wawawawaw', 0, 'read', 'thank\r\n', 1),
(13, 1, 5, 'salamuch', 5, '2025-05-15 17:51:11', 'yeah', 0, 'read', 'qqqqqqqqq', 0),
(14, 1, 5, 'gggg', 4, '2025-05-16 03:34:29', NULL, 0, 'read', 'gdgd', 0),
(15, 1, 6, 'mabilis', 5, '2025-05-16 08:09:28', 'thanks for oordering', 0, 'read', 'thanks', 0),
(16, 1, 10, 'fvdfgft', 3, '2025-05-16 13:01:50', 'thnkhbgyh', 0, 'read', 'cbdjhcdedberkj', 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_messages`
--

CREATE TABLE `feedback_messages` (
  `message_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `sender` enum('customer','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_messages`
--

INSERT INTO `feedback_messages` (`message_id`, `feedback_id`, `sender`, `message`, `created_at`) VALUES
(1, 14, 'admin', 'wow', '2025-05-16 13:51:02'),
(2, 14, 'customer', 'thankyu', '2025-05-16 13:51:17'),
(3, 14, 'admin', 'gifgui', '2025-05-16 13:52:05'),
(4, 14, 'customer', 'thankyu', '2025-05-16 13:52:10'),
(5, 14, 'customer', 'kihoi', '2025-05-16 13:52:14'),
(6, 14, 'admin', 'gifgui', '2025-05-16 13:52:23'),
(7, 14, 'admin', 'khiol', '2025-05-16 13:52:27'),
(8, 14, 'admin', 'khiol', '2025-05-16 13:52:35'),
(9, 14, 'admin', 'khiol', '2025-05-16 13:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customername` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `delivery_mode` varchar(50) NOT NULL,
  `preferred_time` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `notification_status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `payment_mode`, `delivery_mode`, `preferred_time`, `order_date`, `status`, `notification_status`) VALUES
(6, 1, 19.00, 'GCash', 'Delivery', '2025-05-15T23:45', '2025-05-16 09:45:43', 'Completed', 'unread'),
(7, 1, 350.00, 'GCash', 'Delivery', '2025-05-15T23:48', '2025-05-16 09:48:20', 'Pending', 'unread'),
(10, 1, 16624.00, 'GCash', 'Delivery', '2025-05-16T05:01', '2025-05-16 15:00:39', 'Completed', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(6, 6, 12, 1, 19.00),
(7, 7, 11, 1, 350.00),
(10, 10, 14, 7, 50.00),
(11, 10, 12, 2, 19.00),
(12, 10, 11, 1, 350.00),
(13, 10, 13, 13, 1222.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Uncategorized',
  `unit_option` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `price`, `quantity`, `image`, `description`, `category`, `unit_option`) VALUES
(11, 'rice hasmin', 1350.00, 1000, 'https://tse2.mm.bing.net/th?id=OIP.5YAFe8Ml4xurKVfVt1CbZAHaFj&pid=Api&P=0&h=180', 'humok nga lami\r\n(1 sack)', 'Rice', '1 Sack'),
(12, 'Red Rice', 19.00, 88, 'https://tse2.mm.bing.net/th?id=OIP.fob4mcLtf8s5DkrDCi0kkQHaEF&pid=Api&P=0&h=180', 'balikbalikan nmo ni\r\n(1 kilo)', 'Rice', 'Per Kilo'),
(13, 'RICE HASMIN', 1500.00, 0, 'https://down-ph.img.susercontent.com/file/sg-11134201-22090-q41f068io3hv36', 'lami Kaayu\r\n(1 sack)', 'Rice', '1 Sack'),
(14, 'PRINCESS BEA', 50.00, 93, 'https://tse1.mm.bing.net/th?id=OIP.Za70uUFdfU4m0gymjdmm8AHaHa&pid=Api&P=0&h=180', 'masarap nga lami ambot\r\n(1 kilo)', 'Rice', 'Per Kilo'),
(15, 'PRINCESS BEA', 1222.00, 100, 'https://tse1.mm.bing.net/th?id=OIP.Za70uUFdfU4m0gymjdmm8AHaHa&pid=Api&P=0&h=180', 'masarap nga lami ambot\r\n(HALF SACK)', 'Rice', 'Half Sack'),
(16, 'Coca-Cola', 123.00, 100, 'https://tse2.mm.bing.net/th?id=OIP.FORJ_qOTz3ZIaisyweSChwHaHa&pid=Api&P=0&h=180', 'zero\r\n(BY CASE)', 'Soft Drinks', 'Per Case'),
(19, 'CORNED BEEF', 386.00, 100, 'https://tse3.mm.bing.net/th?id=OIP.Ur8o5NFvaQohFYU3T21PAQHaFE&pid=Api&P=0&h=180', 'Lami nga beef\r\n(1 Case)', 'Canned Goods', 'Per Case'),
(20, 'SARDINES MEGA', 250.00, 100, 'https://tse3.mm.bing.net/th?id=OIP.vNy6tNyv3fuGk7yU4h8fPAAAAA&pid=Api&P=0&h=180', 'Lami nga tinapa', 'Canned Goods', 'Per Case');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `position`, `department`, `password`, `created_at`) VALUES
(1, 'mylescutie', '20221232@nbsc.edu.ph', 'CEO', 'XTECH', '$2y$10$MsSM1FXjwIDJv2u7jTyxQeF3PcHnIJAZjN4oDCETS4mbik.yiQJE.', '2025-05-07 07:12:41'),
(2, 'wwww', '20191336@nbsc.edu.ph', 'cashier', 'regege', '$2y$10$geA9uuzNA0MWnn88v..YEuFdz4AWOSKrC9.KxwOK//rTMXZL3ZFfC', '2025-05-07 09:41:29'),
(3, 'user', 'user@gmail.com', 'saler', 'gresrr', '$2y$10$BUrioRihiYJqg9.VIPwgm.q2hZjhakCYxBrQCfTMIOrvW9je2pZya', '2025-05-09 10:46:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `username` (`customername`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `feedback_id` (`feedback_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD CONSTRAINT `feedback_messages_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`feedback_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
