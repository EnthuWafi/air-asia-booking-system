-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2023 at 05:33 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `air-asia-booking-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL,
  `admin_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `admin_code`) VALUES
(9, '2023701089'),
(1, 'MAIN-ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `age_category_prices`
--

CREATE TABLE `age_category_prices` (
  `age_category_price_code` varchar(11) NOT NULL,
  `age_category_name` varchar(255) NOT NULL,
  `age_minimum` int(11) NOT NULL,
  `age_maximum` int(11) NOT NULL,
  `cost_multiplier` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `age_category_prices`
--

INSERT INTO `age_category_prices` (`age_category_price_code`, `age_category_name`, `age_minimum`, `age_maximum`, `cost_multiplier`) VALUES
('ADT', 'Adult', 12, 50, '1.00'),
('CHD', 'Child', 3, 11, '0.75'),
('INF', 'Infant', 0, 2, '0.50'),
('SNR', 'Senior', 50, 99, '0.90');

-- --------------------------------------------------------

--
-- Table structure for table `aircrafts`
--

CREATE TABLE `aircrafts` (
  `aircraft_id` int(11) NOT NULL,
  `aircraft_name` varchar(255) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `economy_capacity` int(11) NOT NULL,
  `premium_economy_capacity` int(11) NOT NULL,
  `business_capacity` int(11) NOT NULL,
  `first_class_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aircrafts`
--

INSERT INTO `aircrafts` (`aircraft_id`, `aircraft_name`, `registration_date`, `economy_capacity`, `premium_economy_capacity`, `business_capacity`, `first_class_capacity`) VALUES
(1, 'Airbus A320', '2023-06-27 14:49:46', 80, 40, 40, 30),
(2, 'Boeing 737', '2023-06-27 14:49:46', 80, 40, 20, 20),
(3, 'Airbus A330', '2023-06-27 14:49:46', 100, 50, 30, 30),
(4, 'Boeing 777', '2023-06-27 14:49:46', 80, 70, 50, 10),
(5, 'Airbus A350', '2023-06-27 14:49:46', 60, 40, 30, 30);

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `airline_id` int(11) NOT NULL,
  `airline_name` varchar(255) NOT NULL,
  `airline_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`airline_id`, `airline_name`, `airline_image`) VALUES
(1, 'Malaysia Airlines', '/assets/img/malaysia-airlines.png'),
(2, 'AirAsia', '/assets/img/airasia.png'),
(3, 'Malindo Air', '/assets/img/malindo-air.png'),
(4, 'Firefly', '/assets/img/firefly.png'),
(5, 'Air Mauritius', '/assets/img/air-mauritius.png');

-- --------------------------------------------------------

--
-- Table structure for table `airports`
--

CREATE TABLE `airports` (
  `airport_code` varchar(5) NOT NULL,
  `airport_name` varchar(255) NOT NULL,
  `airport_state` varchar(255) NOT NULL,
  `airport_country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airports`
--

INSERT INTO `airports` (`airport_code`, `airport_name`, `airport_state`, `airport_country`) VALUES
('AOR', 'Sultan Abdul Halim Airport', 'Kedah', 'Malaysia'),
('BKK', 'Suvarnabhumi Airport', 'Bangkok', 'Thailand'),
('BWN', 'Brunei International Airport', 'Bandar Seri Begawan', 'Brunei'),
('CCU', 'Netaji Subhas Chandra Bose International Airport', 'Kolkata', 'India'),
('CMB', 'Bandaranaike International Airport', 'Negombo', 'Sri Lanka'),
('CNX', 'Chiang Mai International Airport', 'Chiang Mai', 'Thailand'),
('CRK', 'Clark International Airport', 'Pampanga', 'Philippines'),
('DAD', 'Da Nang International Airport', 'Da Nang', 'Vietnam'),
('DPS', 'Ngurah Rai International Airport', 'Bali', 'Indonesia'),
('HAN', 'Noi Bai International Airport', 'Hanoi', 'Vietnam'),
('HKT', 'Phuket International Airport', 'Phuket', 'Thailand'),
('ICN', 'Incheon International Airport', 'Incheon', 'South Korea'),
('IPH', 'Sultan Azlan Shah Airport', 'Perak', 'Malaysia'),
('JHB', 'Senai International Airport', 'Johor', 'Malaysia'),
('KBR', 'Sultan Ismail Petra Airport', 'Kelantan', 'Malaysia'),
('KCH', 'Kuching International Airport', 'Sarawak', 'Malaysia'),
('KUA', 'Kuantan Airport', 'Pahang', 'Malaysia'),
('KUL', 'Kuala Lumpur International Airport', 'Selangor', 'Malaysia'),
('LBU', 'Labuan Airport', 'Labuan', 'Malaysia'),
('LGK', 'Langkawi International Airport', 'Kedah', 'Malaysia'),
('MKZ', 'Malacca International Airport', 'Malacca', 'Malaysia'),
('MNL', 'Ninoy Aquino International Airport', 'Metro Manila', 'Philippines'),
('PEN', 'Penang International Airport', 'Penang', 'Malaysia'),
('PNH', 'Phnom Penh International Airport', 'Phnom Penh', 'Cambodia'),
('RGN', 'Yangon International Airport', 'Yangon', 'Myanmar'),
('SDK', 'Sandakan Airport', 'Sabah', 'Malaysia'),
('SGN', 'Tan Son Nhat International Airport', 'Ho Chi Minh City', 'Vietnam'),
('SIN', 'Changi Airport', 'Singapore', 'Singapore'),
('TGG', 'Sultan Mahmud Airport', 'Terengganu', 'Malaysia'),
('TWU', 'Tawau Airport', 'Sabah', 'Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `baggage_prices`
--

CREATE TABLE `baggage_prices` (
  `baggage_price_code` varchar(11) NOT NULL,
  `baggage_name` varchar(255) NOT NULL,
  `baggage_weight` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baggage_prices`
--

INSERT INTO `baggage_prices` (`baggage_price_code`, `baggage_name`, `baggage_weight`, `cost`) VALUES
('LRG', '30 Kg Baggage: Large', '30.00', '70.00'),
('SML', '10 Kg Baggage: Small', '10.00', '20.00'),
('STD', '20 Kg Baggage: Standard', '20.00', '45.00'),
('XLG', '40 Kg Baggage: Extra Large', '40.00', '100.00'),
('XSM', '5 Kg Baggage: Extra Small', '5.00', '10.00');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_reference` varchar(255) DEFAULT NULL,
  `booking_payment_location` varchar(255) DEFAULT NULL,
  `booking_status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `trip_type` varchar(255) NOT NULL,
  `booking_phone` varchar(255) DEFAULT NULL,
  `booking_email` varchar(255) DEFAULT NULL,
  `booking_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `booking_cost` decimal(10,2) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `booking_reference`, `booking_payment_location`, `booking_status`, `trip_type`, `booking_phone`, `booking_email`, `booking_discount`, `booking_cost`, `date_created`) VALUES
(21, 5, '21PRER-AORKUL', 'D:/xampp/htdocs/air-asia-booking-system/public_html/payments/#21PRER-AORKUL.pdf', 'COMPLETED', 'RETURN', '0108857639', 'wafithird@gmail.com', '28.00', '264.00', '2023-07-05 16:44:06'),
(22, 8, '22ECOO-AORKUL', 'D:/xampp/htdocs/air-asia-booking-system/public_html/payments/#22ECOO-AORKUL.pdf', 'COMPLETED', 'ONE-WAY', '0108857639', 'wafithird@gmail.com', '29.00', '116.00', '2023-07-08 23:32:27'),
(23, 8, '23BUSO-AORBKK', 'D:/xampp/htdocs/air-asia-booking-system/public_html/payments/#23BUSO-AORBKK.pdf', 'COMPLETED', 'ONE-WAY', '0108857639', 'wafithird@gmail.com', '0.00', '821.00', '2023-07-09 22:26:53'),
(24, 5, '24BUSO-BKKAOR', 'D:/xampp/htdocs/air-asia-booking-system/public_html/payments/#24BUSO-BKKAOR.pdf', 'REJECTED', 'ONE-WAY', '0108857639', 'wafithird@gmail.com', '93.00', '372.00', '2023-07-10 11:39:09'),
(25, 8, '25BUSO-BKKAOR', 'D:/xampp/htdocs/air-asia-booking-system/public_html/payments/#25BUSO-BKKAOR.jpeg', 'PENDING', 'ONE-WAY', '0108857639', 'wafithird@gmail.com', '84.00', '336.00', '2023-07-11 21:44:13');

-- --------------------------------------------------------

--
-- Table structure for table `booking_statuses`
--

CREATE TABLE `booking_statuses` (
  `booking_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_statuses`
--

INSERT INTO `booking_statuses` (`booking_status`) VALUES
('COMPLETED'),
('PENDING'),
('REFUNDED'),
('REJECTED');

-- --------------------------------------------------------

--
-- Table structure for table `booking_trip_types`
--

CREATE TABLE `booking_trip_types` (
  `trip_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_trip_types`
--

INSERT INTO `booking_trip_types` (`trip_type`) VALUES
('ONE-WAY'),
('RETURN');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `user_id` int(11) NOT NULL,
  `customer_dob` date DEFAULT NULL,
  `customer_phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`user_id`, `customer_dob`, `customer_phone`) VALUES
(5, '2001-01-01', '0108857639'),
(8, '2000-06-05', '0108857639');

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `flight_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `origin_airport_code` varchar(11) NOT NULL,
  `destination_airport_code` varchar(11) NOT NULL,
  `departure_time` datetime NOT NULL,
  `duration` time NOT NULL,
  `flight_base_price` decimal(10,2) NOT NULL,
  `flight_discount` decimal(10,2) DEFAULT 0.00,
  `aircraft_id` int(11) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`flight_id`, `user_id`, `origin_airport_code`, `destination_airport_code`, `departure_time`, `duration`, `flight_base_price`, `flight_discount`, `aircraft_id`, `airline_id`, `date_created`) VALUES
(4, 1, 'SIN', 'BKK', '2023-06-09 10:45:00', '03:00:00', '200.00', '0.00', 1, 1, '2023-06-27 20:10:32'),
(6, 1, 'BWN', 'BKK', '2023-06-29 17:05:01', '03:05:01', '200.00', '0.00', 3, 5, '2023-06-27 20:10:32'),
(9, 1, 'BKK', 'BWN', '2023-07-03 19:10:00', '03:12:00', '222.00', '0.40', 2, 2, '2023-06-28 19:10:26'),
(10, 1, 'CCU', 'CMB', '2023-07-06 15:00:00', '03:12:00', '100.00', '0.10', 4, 4, '2023-06-30 23:00:59'),
(11, 1, 'BKK', 'BWN', '2023-07-04 02:23:00', '03:12:00', '210.00', '0.10', 2, 3, '2023-07-01 02:23:46'),
(12, 1, 'AOR', 'KUL', '2023-07-16 17:14:00', '01:00:00', '100.00', '0.20', 4, 2, '2023-07-03 17:15:03'),
(13, 1, 'KUL', 'AOR', '2023-07-17 17:16:00', '00:59:00', '110.00', '0.00', 1, 2, '2023-07-03 17:16:41'),
(14, 1, 'DAD', 'DPS', '2023-07-20 17:16:00', '04:00:00', '222.00', '0.50', 1, 5, '2023-07-03 17:17:08'),
(17, 1, 'AOR', 'BKK', '2023-07-11 22:14:00', '03:12:00', '121.00', '0.00', 1, 1, '2023-07-09 22:15:08'),
(18, 1, 'BKK', 'AOR', '2023-07-15 22:15:00', '03:12:00', '100.00', '0.20', 3, 2, '2023-07-09 22:15:54'),
(19, 9, 'PEN', 'AOR', '2023-07-20 01:04:00', '02:00:00', '210.00', '0.00', 2, 3, '2023-07-12 01:04:19');

-- --------------------------------------------------------

--
-- Table structure for table `flight_addons`
--

CREATE TABLE `flight_addons` (
  `flight_addon_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `age_category_price_code` varchar(11) NOT NULL,
  `travel_class_price_code` varchar(11) NOT NULL,
  `seat_number` varchar(255) NOT NULL,
  `baggage_price_code` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight_addons`
--

INSERT INTO `flight_addons` (`flight_addon_id`, `flight_id`, `passenger_id`, `age_category_price_code`, `travel_class_price_code`, `seat_number`, `baggage_price_code`) VALUES
(40, 12, 27, 'ADT', 'PRE', '1B', 'SML'),
(41, 13, 27, 'ADT', 'PRE', '1A', 'SML'),
(42, 12, 28, 'ADT', 'ECO', '1A', 'STD'),
(43, 17, 29, 'ADT', 'BUS', '1C', 'SML'),
(44, 17, 30, 'CHD', 'BUS', '1B', 'STD'),
(45, 17, 31, 'CHD', 'BUS', '2A', 'SML'),
(46, 17, 32, 'INF', 'BUS', '1A', 'XSM'),
(47, 18, 33, 'ADT', 'BUS', '1A', 'SML'),
(48, 18, 34, 'ADT', 'BUS', '1B', 'STD'),
(49, 18, 35, 'ADT', 'BUS', '1D', 'XSM'),
(50, 18, 36, 'ADT', 'BUS', '1C', 'XSM');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `message_type` varchar(255) NOT NULL,
  `message_content` text NOT NULL,
  `message_email` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `message_type`, `message_content`, `message_email`, `date_created`) VALUES
(2, 'problem', 'Stop implementing new features last minute bro', 'wafithird@gmail.com', '2023-07-11 19:07:30'),
(6, 'problem', 'http://localhost/contact-us.php\r\nhttp://localhost/contact-us.php\r\nhttp://localhost/contact-us.php\r\nhttp://localhost/contact-us.php\r\nhttp://localhost/contact-us.php', 'wafithird@gmail.com', '2023-07-11 19:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `message_types`
--

CREATE TABLE `message_types` (
  `message_types` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_types`
--

INSERT INTO `message_types` (`message_types`) VALUES
('feedback'),
('others'),
('problem');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `passenger_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `passenger_fname` varchar(255) NOT NULL,
  `passenger_lname` varchar(255) NOT NULL,
  `passenger_dob` date NOT NULL,
  `passenger_gender` varchar(255) DEFAULT NULL,
  `special_assistance` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`passenger_id`, `booking_id`, `passenger_fname`, `passenger_lname`, `passenger_dob`, `passenger_gender`, `special_assistance`) VALUES
(27, 21, 'ABDUL', 'AB.RAHIM', '2023-07-06', 'Male', 0),
(28, 22, 'ABDUL', 'AB.RAHIM', '2013-01-07', 'Male', 1),
(29, 23, 'abd', 'wafi', '2003-07-12', 'Male', 0),
(30, 23, 'jes', 'seee', '2013-11-11', 'Female', 1),
(31, 23, 'see', 'jes', '2015-06-09', 'Male', 1),
(32, 23, 'hahah', 'ahah', '2022-07-04', 'Male', 0),
(33, 24, 'ABDUL', 'AB.RAHIM', '2003-10-01', 'Male', 0),
(34, 24, 'ABDUL EMIN', 'AB.RAHIM', '2001-01-01', 'Male', 1),
(35, 25, 'adw', 'awdwad', '2003-07-03', 'Male', 0),
(36, 25, 'aw', 'awdwad', '2000-01-09', 'Female', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `password_reset_id` int(11) NOT NULL,
  `password_reset_email` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) NOT NULL,
  `password_reset_expires` datetime NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `check_used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`password_reset_id`, `password_reset_email`, `password_reset_token`, `password_reset_expires`, `date_created`, `check_used`) VALUES
(1, 'trombonefader1234@gmail.com', 'bbd76d2f23c99b5a80b1258bb2d11a47bd144f4591d81477e0215e7464b8a99c', '2023-07-13 22:05:48', '2023-07-11 21:45:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_message` varchar(255) NOT NULL,
  `request_check` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traffic`
--

CREATE TABLE `traffic` (
  `traffic_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `traffic`
--

INSERT INTO `traffic` (`traffic_id`, `timestamp`) VALUES
(1, '2023-06-27 19:46:29'),
(2, '2023-06-27 20:41:51'),
(3, '2023-06-28 19:08:17'),
(4, '2023-06-28 19:09:28'),
(5, '2023-06-28 19:10:38'),
(6, '2023-06-28 19:55:44'),
(7, '2023-06-28 22:36:12'),
(8, '2023-07-03 14:09:43'),
(9, '2023-07-04 00:20:15'),
(10, '2023-07-06 19:34:25'),
(11, '2023-07-06 21:52:41'),
(12, '2023-07-08 15:04:19'),
(13, '2023-07-10 14:15:47'),
(14, '2023-07-10 15:15:49'),
(15, '2023-07-10 21:08:25'),
(16, '2023-07-10 21:30:42'),
(17, '2023-07-11 18:20:01'),
(18, '2023-07-11 20:32:52'),
(19, '2023-07-12 09:43:01'),
(20, '2023-07-12 10:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `travel_class_prices`
--

CREATE TABLE `travel_class_prices` (
  `travel_class_price_code` varchar(11) NOT NULL,
  `travel_class_name` varchar(255) NOT NULL,
  `cost_multiplier` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_class_prices`
--

INSERT INTO `travel_class_prices` (`travel_class_price_code`, `travel_class_name`, `cost_multiplier`) VALUES
('BUS', 'Business', '2.00'),
('ECO', 'Economy', '1.00'),
('FST', 'First Class', '3.00'),
('PRE', 'Premium Economy', '1.20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_fname` varchar(255) DEFAULT NULL,
  `user_lname` varchar(255) DEFAULT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `user_fname`, `user_lname`, `registration_date`, `user_type`) VALUES
(1, 'EnthuWafi', '$2y$10$ewZdV/7ra9fObiLCZQ9GgO0Q/VlQ8Mi0mZTn4A0elCkt5.XfvtLiO', 'wafithird@gmail.com', 'ABDUL WAFI', 'CHE AB.RAHIM', '2023-05-30 01:08:54', 'admin'),
(5, 'wafi', '$2y$10$dPV.LD6XQD.Jtn/E7psq2.sVAyqurrWFE1OnKpX98tS2T3GKqKoRi', 'wafi@gmail.com', 'ABDUL', 'AB.RAHIM', '2023-06-07 20:53:47', 'customer'),
(8, 'Key', '$2y$10$9BT5cDSk1Lwm7sCDsfr/AuDkT8i06TFT.Bxt6dL5LGcSV1zfHxLxq', 'trombonefader1234@gmail.com', 'ABDUL', 'KKK', '2023-07-01 02:32:35', 'customer'),
(9, 'itsjustworks', '$2y$10$V4cIdcMIzEjr5x65L0cCReRppVo4TcURQBWwrsv6Os.jgil5naGSe', 'toddhoward@gmail.com', 'OK', 'WORKS NOW', '2023-07-01 02:44:08', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `user_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`user_type`) VALUES
('admin'),
('customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `admin_code` (`admin_code`);

--
-- Indexes for table `age_category_prices`
--
ALTER TABLE `age_category_prices`
  ADD PRIMARY KEY (`age_category_price_code`),
  ADD UNIQUE KEY `age_category_price_code` (`age_category_price_code`);

--
-- Indexes for table `aircrafts`
--
ALTER TABLE `aircrafts`
  ADD PRIMARY KEY (`aircraft_id`);

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`airline_id`);

--
-- Indexes for table `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`airport_code`),
  ADD UNIQUE KEY `airport_code` (`airport_code`);

--
-- Indexes for table `baggage_prices`
--
ALTER TABLE `baggage_prices`
  ADD PRIMARY KEY (`baggage_price_code`),
  ADD UNIQUE KEY `baggage_price_code` (`baggage_price_code`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `FK_USER_ID` (`user_id`),
  ADD KEY `FK_BOOKING_STATUS` (`booking_status`),
  ADD KEY `FK_TRIP_TYPE` (`trip_type`);

--
-- Indexes for table `booking_statuses`
--
ALTER TABLE `booking_statuses`
  ADD PRIMARY KEY (`booking_status`);

--
-- Indexes for table `booking_trip_types`
--
ALTER TABLE `booking_trip_types`
  ADD PRIMARY KEY (`trip_type`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`flight_id`),
  ADD KEY `FK_ORIGIN_AIRPORT` (`origin_airport_code`),
  ADD KEY `FK_DESTINATION_AIRPORT` (`destination_airport_code`),
  ADD KEY `FK_AIRLINE` (`airline_id`),
  ADD KEY `FK_AIRCRAFT` (`aircraft_id`),
  ADD KEY `FK_USER` (`user_id`);

--
-- Indexes for table `flight_addons`
--
ALTER TABLE `flight_addons`
  ADD PRIMARY KEY (`flight_addon_id`),
  ADD UNIQUE KEY `seat_number` (`seat_number`,`flight_id`,`travel_class_price_code`) USING BTREE,
  ADD UNIQUE KEY `passenger_id` (`passenger_id`,`flight_id`) USING BTREE,
  ADD KEY `FK_AGE_CATEGORY` (`age_category_price_code`),
  ADD KEY `FK_TRAVEL_CLASS` (`travel_class_price_code`),
  ADD KEY `FK_BAGGAGE_PRICE` (`baggage_price_code`),
  ADD KEY `FK_FLIGHT` (`flight_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `FK_TYPE` (`message_type`);

--
-- Indexes for table `message_types`
--
ALTER TABLE `message_types`
  ADD PRIMARY KEY (`message_types`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`passenger_id`),
  ADD KEY `FK_BOOKING_ID` (`booking_id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `FK_EMAIL` (`password_reset_email`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `traffic`
--
ALTER TABLE `traffic`
  ADD PRIMARY KEY (`traffic_id`);

--
-- Indexes for table `travel_class_prices`
--
ALTER TABLE `travel_class_prices`
  ADD PRIMARY KEY (`travel_class_price_code`),
  ADD UNIQUE KEY `travel_class_price_code` (`travel_class_price_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_email` (`username`,`email`),
  ADD KEY `FK_USER_TYPE` (`user_type`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`user_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aircrafts`
--
ALTER TABLE `aircrafts`
  MODIFY `aircraft_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `airline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `flight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `flight_addons`
--
ALTER TABLE `flight_addons`
  MODIFY `flight_addon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `passenger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `traffic`
--
ALTER TABLE `traffic`
  MODIFY `traffic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `FK_USER_ADMIN` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `FK_BOOKING_STATUS` FOREIGN KEY (`booking_status`) REFERENCES `booking_statuses` (`booking_status`),
  ADD CONSTRAINT `FK_TRIP_TYPE` FOREIGN KEY (`trip_type`) REFERENCES `booking_trip_types` (`trip_type`),
  ADD CONSTRAINT `FK_USER_ID` FOREIGN KEY (`user_id`) REFERENCES `customers` (`user_id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `FK_USER_CUSTOMER` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `FK_AIRCRAFT` FOREIGN KEY (`aircraft_id`) REFERENCES `aircrafts` (`aircraft_id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `FK_AIRLINE` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`airline_id`),
  ADD CONSTRAINT `FK_DESTINATION_AIRPORT` FOREIGN KEY (`destination_airport_code`) REFERENCES `airports` (`airport_code`),
  ADD CONSTRAINT `FK_ORIGIN_AIRPORT` FOREIGN KEY (`origin_airport_code`) REFERENCES `airports` (`airport_code`),
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`user_id`) REFERENCES `admins` (`user_id`) ON DELETE NO ACTION;

--
-- Constraints for table `flight_addons`
--
ALTER TABLE `flight_addons`
  ADD CONSTRAINT `FK_AGE_CATEGORY` FOREIGN KEY (`age_category_price_code`) REFERENCES `age_category_prices` (`age_category_price_code`),
  ADD CONSTRAINT `FK_BAGGAGE_PRICE` FOREIGN KEY (`baggage_price_code`) REFERENCES `baggage_prices` (`baggage_price_code`),
  ADD CONSTRAINT `FK_FLIGHT` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`flight_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_PASSENGER` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`passenger_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TRAVEL_CLASS` FOREIGN KEY (`travel_class_price_code`) REFERENCES `travel_class_prices` (`travel_class_price_code`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `FK_TYPE` FOREIGN KEY (`message_type`) REFERENCES `message_types` (`message_types`);

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `FK_BOOKING_ID` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `FK_EMAIL` FOREIGN KEY (`password_reset_email`) REFERENCES `users` (`email`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_USER_TYPE` FOREIGN KEY (`user_type`) REFERENCES `user_types` (`user_type`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
