-- Luxit Global Escapes Database Schema
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Drop existing tables for a clean setup
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `quotations`;
DROP TABLE IF EXISTS `activity_feed`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `tours`;
DROP TABLE IF EXISTS `destinations`;
DROP TABLE IF EXISTS `admins`;

-- --------------------------------------------------------

-- Table structure for table `admins`
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data for `admins` (Password is 'password')
INSERT INTO `admins` (`username`, `email`, `password`) VALUES
('admin', 'admin@luxitglobalescapes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

-- Table structure for table `destinations`
CREATE TABLE `destinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `region` varchar(50) DEFAULT NULL,
  `tours_count` int(11) DEFAULT 0,
  `visits` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `destinations` (`id`, `parent_id`, `name`, `region`, `tours_count`, `visits`) VALUES
(1, NULL, 'Kenya', 'Africa', 12, 1250),
(2, NULL, 'Indonesia', 'Asia', 8, 3400),
(3, NULL, 'Switzerland', 'Europe', 5, 2100),
(4, NULL, 'Japan', 'Asia', 6, 4500),
(5, NULL, 'UAE', 'Middle East', 15, 6000),
(6, 2, 'Bali', 'Asia', 4, 1800),
(7, 2, 'Lombok', 'Asia', 2, 800),
(8, 4, 'Tokyo', 'Asia', 3, 2500),
(9, 4, 'Kyoto', 'Asia', 2, 1200),
(10, 1, 'Mombasa', 'Africa', 3, 900);

-- --------------------------------------------------------

-- Table structure for table `tours`
CREATE TABLE `tours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 4.5,
  `status` enum('Active', 'Inactive') DEFAULT 'Active',
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tours` (`title`, `location`, `price`, `duration`, `rating`, `status`, `category`, `image`, `description`) VALUES
('Bali Luxury Escape', 'Bali, Indonesia', 472.00, '8 Days / 3 Nights', 4.8, 'Active', 'Luxury', 'assets/images/tour/style1/pic1.jpg', 'Nusa Penida is a stunning island located just southeast of Bali.'),
('South Korea Mountain Trek', 'South Korea', 300.00, '4 Days / 2 Nights', 4.8, 'Active', 'Adventure', 'assets/images/tour/style1/pic2.jpg', 'Deogyusan mountain peak at 1,614 m above sea level.'),
('Tokyo City Highlights', 'Tokyo, Japan', 594.00, '6 Days / 3 Nights', 4.8, 'Active', 'Culture', 'assets/images/tour/style1/pic3.jpg', 'The bridge offers panoramic views of Tokyo Tower.'),
('Plateau in Slovenia', 'Slovenia', 1192.00, '8 Days / 3 Nights', 4.8, 'Active', 'Adventure', 'assets/images/tour/style1/pic4.jpg', 'Discover the emerald lakes and scenic plateaus.'),
('Switzerland Tour Package', 'Switzerland', 516.00, '4 Days / 2 Nights', 4.8, 'Active', 'Luxury', 'assets/images/tour/style1/pic5.jpg', 'Experience the magic of the Swiss Alps.'),
('Tokyo City (Standard)', 'Tokyo, Japan', 474.00, '6 Days / 3 Nights', 4.8, 'Active', 'Culture', 'assets/images/tour/style1/pic6.jpg', 'Explore the bustling streets and rich heritage of Tokyo.'),
('Majestic Iceland', 'Reykjavik, Iceland', 1800.00, '5 Days', 4.8, 'Active', 'Adventure', 'assets/images/tour/style1/pic1.jpg', 'The Land of Fire and Ice awaits you.'),
('Maldives Paradise', 'Male, Maldives', 2500.00, '7 Days', 5.0, 'Active', 'Beach', 'assets/images/tour/style1/pic4.jpg', 'Book for 2027 and lock in today prices.');

-- --------------------------------------------------------

-- Table structure for table `bookings`
CREATE TABLE `bookings` (
  `id` varchar(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tour_name` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Confirmed', 'Pending', 'Cancelled') DEFAULT 'Pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `bookings` (`id`, `user_name`, `email`, `tour_name`, `booking_date`, `amount`, `status`) VALUES
('BK-1001', 'John Doe', 'john@example.com', 'Safari in Kenya', '2026-05-15', 1200.00, 'Confirmed'),
('BK-1002', 'Alice Smith', 'alice@example.com', 'Bali Luxury Escape', '2026-06-10', 1700.00, 'Pending'),
('BK-1003', 'Bob Wilson', 'bob@example.com', 'Swiss Alps Adventure', '2026-05-20', 1500.00, 'Confirmed'),
('BK-1004', 'Emma Davis', 'emma@example.com', 'Dubai Desert Safari', '2026-04-25', 450.00, 'Cancelled'),
('BK-1005', 'Mike Brown', 'mike@example.com', 'Safari in Kenya', '2026-07-01', 2400.00, 'Pending'),
('BK-1006', 'Sarah Jenkins', 'sarah.j@outlook.com', 'Maldives Paradise', '2026-08-12', 2500.00, 'Confirmed');

-- --------------------------------------------------------

-- Table structure for table `customers`
CREATE TABLE `customers` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `bookings_count` int(11) DEFAULT 0,
  `joined_date` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `customers` (`id`, `name`, `email`, `country`, `bookings_count`, `joined_date`) VALUES
('CU-001', 'John Doe', 'john@example.com', 'USA', 3, '2025-10-01'),
('CU-002', 'Alice Smith', 'alice@example.com', 'UK', 1, '2026-01-15'),
('CU-003', 'Bob Wilson', 'bob@example.com', 'Canada', 5, '2025-05-20'),
('CU-004', 'Emma Davis', 'emma@example.com', 'Australia', 2, '2026-02-10'),
('CU-005', 'Sarah Johnson', 'sarah@example.com', 'Germany', 4, '2025-11-30');

-- --------------------------------------------------------

-- Table structure for table `events`
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `events` (`title`, `event_date`, `type`) VALUES
('Safari Group Departure', '2026-04-15', 'tour'),
('Staff Meeting', '2026-04-18', 'meeting'),
('Bali Package Launch', '2026-04-20', 'promo'),
('Customer Appreciation Day', '2026-04-25', 'event'),
('Iceland Expedition Starts', '2026-05-01', 'tour');

-- --------------------------------------------------------

-- Table structure for table `quotations`
CREATE TABLE `quotations` (
  `id` varchar(20) NOT NULL,
  `customer_id` varchar(20) NOT NULL,
  `tour_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Draft', 'Sent', 'Accepted', 'Expired') DEFAULT 'Draft',
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `quotations` (`id`, `customer_id`, `tour_name`, `amount`, `status`, `valid_until`) VALUES
('QT-001', 'CU-001', 'Private Serengeti Safari', 4500.00, 'Sent', '2026-06-01'),
('QT-002', 'CU-002', 'Alpine Honeymoon Special', 2800.00, 'Accepted', '2026-05-15');

-- --------------------------------------------------------

-- Table structure for table `invoices`
CREATE TABLE `invoices` (
  `id` varchar(20) NOT NULL,
  `customer_id` varchar(20) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Unpaid', 'Paid', 'Overdue', 'Partial') DEFAULT 'Unpaid',
  `due_date` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `invoices` (`id`, `customer_id`, `booking_id`, `amount`, `status`, `due_date`) VALUES
('INV-001', 'CU-001', 'BK-1001', 1250.00, 'Paid', '2025-10-15'),
('INV-002', 'CU-003', 'BK-1003', 2100.00, 'Unpaid', '2026-05-20');

-- --------------------------------------------------------

-- Table structure for table `expenses`
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `expenses` (`category`, `description`, `amount`, `expense_date`) VALUES
('Marketing', 'Facebook Ad Campaign - Summer Tours', 500.00, '2026-05-01'),
('Operations', 'Office Rent - Nairobi Branch', 1200.00, '2026-05-05'),
('Flight', 'Customer Shuttle Service Maintenance', 350.00, '2026-05-06');

-- --------------------------------------------------------

-- Table structure for table `activity_feed`
CREATE TABLE `activity_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `activity_time` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `activity_feed` (`user`, `action`, `target`, `activity_time`) VALUES
('Admin', 'Added new tour', 'Iceland Northern Lights', '2 hours ago'),
('System', 'New Booking', 'BK-1011', '4 hours ago'),
('John Doe', 'Left a review', 'Safari in Kenya', '1 day ago');
