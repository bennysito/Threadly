-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2025 at 06:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `threadly`
--

-- --------------------------------------------------------

--
-- Table structure for table `bidding_session`
--

CREATE TABLE `bidding_session` (
  `session_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('upcoming','ongoing','ended') DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(1, 'New In', 'Images/panti.png'),
(2, 'Dresses', 'Images/baggy_pants.png'),
(3, 'Tops', 'Images/underwear_women.png'),
(4, 'Bottoms', 'Images/jacket_hoodie.png'),
(5, 'Outerwear', 'Images/panti.png'),
(6, 'Sweaters & Cardigans', 'Images/baggy_pants.png'),
(7, 'Hoodies & Sweatshirts', 'Images/underwear_women.png'),
(8, 'Two-Piece Sets', 'Images/jacket_hoodie.png'),
(9, 'Jumpsuits & Rompers', 'Images/panti.png'),
(10, 'Activewear', 'Images/baggy_pants.png'),
(11, 'Swimwear', 'Images/underwear_women.png'),
(12, 'Lingerie & Sleepwear', 'Images/jacket_hoodie.png'),
(13, 'Plus Size / Curve', 'Images/panti.png'),
(14, 'Denim', 'Images/baggy_pants.png'),
(15, 'Basics', 'Images/underwear_women.png');

-- --------------------------------------------------------

--
-- Table structure for table `highest_bid`
--

CREATE TABLE `highest_bid` (
  `product_id` int(11) NOT NULL,
  `highest_user_id` int(11) NOT NULL,
  `highest_amount` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_purchase` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `product_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `availability` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `seller_id`, `quantity`, `product_name`, `description`, `image_url`, `availability`, `created_at`, `price`, `category_id`) VALUES
(1, NULL, 0, 'jacket', 'very cotton', 'Jacket.png', 'available', '2025-11-29 15:25:49', 142.00, NULL),
(3, NULL, 0, 'Zip-Up Hoodie Jacket', 'Cozy cotton hoodie with zipper, perfect for cold days', 'jacket_hoodie.png', 'available', '2025-11-29 06:23:45', 149.99, 5),
(4, NULL, 0, 'Baggy Cargo Pants Black', 'Relaxed-fit cargo pants with 6 pockets', 'Images/baggy_pants.png', 'available', '2025-11-29 06:25:12', 89.90, 4),
(5, NULL, 0, 'Seamless Cotton Panties (5-Pack)', 'Super soft and invisible under clothes', 'Images/panti.png', 'available', '2025-11-29 06:26:33', 32.50, 1),
(6, NULL, 0, 'Lace Underwear Set for Women', 'Elegant lace bra + panty combo', 'Images/underwear_women.png', 'available', '2025-11-29 06:27:58', 59.00, 3),
(7, NULL, 0, 'Oversized Pullover Hoodie', 'Heavyweight hoodie, unisex fit', 'Images/jacket_hoodie.png', 'available', '2025-11-29 06:29:15', 169.00, 5),
(8, NULL, 0, 'Streetwear Baggy Jeans', 'Extra baggy denim pants, light wash', 'Images/baggy_pants.png', 'available', '2025-11-29 06:30:40', 99.99, 4),
(9, NULL, 0, 'High-Waist Shaping Panties', 'Tummy control seamless underwear', 'Images/panti.png', 'available', '2025-11-29 06:32:05', 44.90, 1),
(10, NULL, 0, 'Sporty Briefs 3-Pack', 'Breathable microfiber women\'s briefs', 'Images/underwear_women.png', 'available', '2025-11-29 06:33:22', 39.99, 3),
(11, NULL, 0, 'Fleece-Lined Winter Hoodie', 'Warmest hoodie in the collection', 'Images/jacket_hoodie.png', 'available', '2025-11-29 06:34:50', 189.00, 5),
(12, NULL, 0, 'Relaxed Fit Khaki Cargo Pants', 'Comfortable baggy khaki cargos', 'Images/baggy_pants.png', 'available', '2025-11-29 06:36:18', 85.00, 4),
(13, NULL, 0, 'Oversized Zip Hoodie', 'Premium cotton fleece hoodie', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:30:36', 159.90, 7),
(14, NULL, 0, 'Relaxed Cargo Pants', 'Baggy fit with side pockets', 'Images/baggy_pants.png', 'available', '2025-11-29 19:30:36', 94.00, 4),
(15, NULL, 0, 'Seamless High-Waist Panties', 'Pack of 5 – no visible lines', 'Images/panti.png', 'available', '2025-11-29 19:30:36', 34.99, 1),
(16, NULL, 0, 'Lace Bralette Set', 'Wireless bralette + matching panty', 'Images/underwear_women.png', 'available', '2025-11-29 19:30:36', 62.50, 8),
(17, NULL, 0, 'Denim Trucker Jacket', 'Classic outerwear piece', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:30:36', 179.00, 5),
(18, NULL, 0, 'Wide-Leg Palazzo Pants', 'Flowy and comfortable bottoms', 'Images/baggy_pants.png', 'available', '2025-11-29 19:30:36', 89.90, 4),
(19, NULL, 0, 'Crochet Summer Cardigan', 'Lightweight open-front cardigan', 'Images/panti.png', 'available', '2025-11-29 19:30:36', 72.00, 6),
(20, NULL, 0, 'Velour Tracksuit Set', 'Soft two-piece lounge set', 'Images/underwear_women.png', 'available', '2025-11-29 19:30:36', 119.00, 8),
(21, NULL, 0, 'Fleece Lined Hoodie', 'Warmest hoodie for winter', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:30:36', 189.99, 7),
(22, NULL, 0, 'Satin Cami Jumpsuit', 'Elegant one-piece for evening', 'Images/panti.png', 'available', '2025-11-29 19:30:36', 129.00, 9),
(23, NULL, 0, 'Black Oversized Hoodie', 'Premium 400gsm cotton, unisex', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:32:27', 169.90, 7),
(24, NULL, 0, 'Cream Baggy Parachute Pants', 'Lightweight nylon cargo style', 'Images/baggy_pants.png', 'available', '2025-11-29 19:32:27', 92.00, 4),
(25, NULL, 0, 'Nude Seamless Panties 6-Pack', 'Invisible under any outfit', 'Images/panti.png', 'available', '2025-11-29 19:32:27', 39.90, 1),
(26, NULL, 0, 'Floral Lace Lingerie Set', 'Bralette + high-waist panty', 'Images/underwear_women.png', 'available', '2025-11-29 19:32:27', 69.90, 8),
(27, NULL, 0, 'Leather-Look Bomber Jacket', 'Trendy outerwear piece', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:32:27', 219.00, 5),
(28, NULL, 0, 'Grey Wide-Leg Sweatpants', 'Super comfy lounge bottoms', 'Images/baggy_pants.png', 'available', '2025-11-29 19:32:27', 79.90, 4),
(29, NULL, 0, 'Chunky Knit Cardigan', 'Oversized and cozy', 'Images/panti.png', 'available', '2025-11-29 19:32:27', 95.00, 6),
(30, NULL, 0, 'Matching Crop Hoodie & Shorts Set', 'Perfect summer two-piece', 'Images/underwear_women.png', 'available', '2025-11-29 19:32:27', 109.90, 8),
(31, NULL, 0, 'Vintage Wash Hoodie', 'Soft faded look, streetwear favorite', 'Images/jacket_hoodie.png', 'available', '2025-11-29 19:32:27', 179.00, 7),
(32, NULL, 0, 'Backless Satin Romper', 'Elegant jumpsuit for evening', 'Images/panti.png', 'available', '2025-11-29 19:32:27', 149.00, 9),
(34, NULL, 0, 'awda', 'awd32', '1764504392_Screenshot_2025-11-30_160251.png', 'unavailable', '2025-11-30 12:06:32', 232.00, NULL),
(35, NULL, 0, 'awda', '32423', '1764504402_Screenshot_2025-11-30_115924.png', '', '2025-11-30 12:06:42', 324.00, 2),
(36, NULL, 0, 'awda', '32423', '1764504453_Screenshot_2025-11-30_115924.png', '', '2025-11-30 12:07:33', 324.00, 2),
(37, NULL, 0, 'awdawd', 'awdaw', '1764643482_Screenshot_2025-11-30_160256.png', '', '2025-12-02 02:44:43', 1231.00, 2),
(38, NULL, 0, 'awdawd', 'awdaw', '1764643564_Screenshot_2025-11-30_160256.png', '', '2025-12-02 02:46:04', 1231.00, 2),
(39, NULL, 0, 'awdaw', 'qqdaqw', '1764643579_download__34_.png', '', '2025-12-02 02:46:19', 12232.00, 1),
(40, NULL, 0, 'awdwa', 'awdwa', '1764643877_Screenshot_2025-02-07_134607.png', '', '2025-12-02 02:51:17', 234.00, 2),
(41, NULL, 0, 'awdwa', 'awdawd', '1764644223_Screenshot_2025-11-24_110514.png', '', '2025-12-02 02:57:03', 42342.00, 2),
(42, NULL, 0, 'awdwa', 'awdawd', '1764644403_Screenshot_2025-11-30_115924.png', '', '2025-12-02 03:00:03', 213.00, 2),
(43, NULL, 0, 'awd', 'awdwa', '1764644668_line_grap.png', '', '2025-12-02 03:04:28', 2131.00, 2),
(44, NULL, 0, 'AWDWA', '213', '1764644815_Screenshot_2025-11-25_203101.png', 'unavailable', '2025-12-02 03:06:55', 21312.00, 2),
(45, NULL, 0, 'AWDAW', 'AWDAW', '1764644893_line-graph.png', '', '2025-12-02 03:08:13', 2132.00, 3),
(50, NULL, 0, 'awd', 'awda', '1764646713_475657501_3563246783973707_219491349713990838_n.jpg', 'unavailable', '2025-12-02 03:38:33', 232.00, 10),
(51, NULL, 0, 'awd', 'awda', '1764646718_475657501_3563246783973707_219491349713990838_n.jpg', 'unavailable', '2025-12-02 03:38:38', 232.00, 10),
(52, NULL, 0, 'awd', 'awda', '1764646897_475657501_3563246783973707_219491349713990838_n.jpg', 'unavailable', '2025-12-02 03:41:37', 232.00, 10),
(53, NULL, 0, 'awdwa', 'a23', '1764646907_Screenshot_2025-10-30_104306.png', '', '2025-12-02 03:41:47', 1.00, 2),
(54, NULL, 0, 'awdwa', 'a23', '1764647071_Screenshot_2025-10-30_104306.png', '', '2025-12-02 03:44:31', 1.00, 2),
(55, NULL, 0, 'awdwa', 'a23', '1764647200_Screenshot_2025-10-30_104306.png', '', '2025-12-02 03:46:40', 1.00, 2),
(56, NULL, 0, 'awdaw', 'awdaw', '1764647215_e9db8719-6e74-448d-ac9e-000a0955f242.jpg', '', '2025-12-02 03:46:55', 23.00, 2),
(57, NULL, 0, 'awdaw', 'awdaw', '1764647319_e9db8719-6e74-448d-ac9e-000a0955f242.jpg', '', '2025-12-02 03:48:39', 23.00, 2),
(58, NULL, 0, 'TEsting product', 'awdawdwa', '1764647335_notion-dark-8x.png', '', '2025-12-02 03:48:55', 1231.00, 3),
(59, 6, 0, 'TEsting product', 'awdawdwa', '1764647443_notion-dark-8x.png', '', '2025-12-02 03:50:43', 1231.00, 3),
(60, 6, 0, 'awdaw', 'awdaw', '1764647458_Screenshot_2025-09-30_121703.png', '', '2025-12-02 03:50:58', 213.00, 2),
(61, 6, 0, 'Sam bayot', 'SAM BAYOT', '1764647484_line_grap.png', '', '2025-12-02 03:51:24', 2134.00, 2),
(62, 6, 0, 'Shoes', 'Kent', '1764647736_Screenshot_2025-12-02_111702.png', 'available', '2025-12-02 03:55:36', 123.00, 1),
(63, 6, 0, 'Sam Mariscal', 'Sam Bayot Sam Bayot Sam Bayot Sam Bayot Sam Bayot Sam Bayot Sam Bayot', '1764647952_2X2Pic_Montesa.jpg', 'available', '2025-12-02 03:59:12', 2134.00, 3),
(64, 6, 23, 'awdwa', 'awdaw', '1764648366_craiyon_194723_image.png', 'available', '2025-12-02 04:06:06', 23423.00, 10),
(65, 6, 12, 'DAVEN PALAUTOG', 'AWDWA', '1764648394_Screenshot_2025-11-30_115508.png', 'available', '2025-12-02 04:06:34', 234.00, 1),
(66, 6, 12, 'DAVEN PALAUTOG', 'AWDWA', '1764648477_Screenshot_2025-11-30_115508.png', 'available', '2025-12-02 04:07:57', 234.00, 1),
(67, 6, 213, 'awd', 'awdaw', '1764648496_Screenshot_2025-09-23_154144.png', 'available', '2025-12-02 04:08:16', 12.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

CREATE TABLE `product_variant` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variant`
--

INSERT INTO `product_variant` (`variant_id`, `product_id`, `size_id`, `stock_quantity`) VALUES
(1, 1, 2, 10),
(2, 1, 3, 8),
(3, 1, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `size_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_name`) VALUES
(1, 'XS'),
(2, 'S'),
(3, 'M'),
(4, 'L'),
(5, 'XL'),
(6, 'XXL');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `user_password`, `email`, `contact_number`, `role`) VALUES
(1, 'as', 'as', 'kennylovesoten', 'benediklovesoten', 'asasa', '123', 'customer'),
(2, '', '', '', '$2y$10$WV3Jk4oSroGWaXlJ1W9VguFdBatteCNcJE1WXT4Dzhtusn.A922cy', '', '', 'seller'),
(3, 'Sam', 'Mariscal', 'Samert43', '$2y$10$76k/PyAeeoXCq8LW9xTYTuiA/8O2qclkzrqEiCyMH2uk4EF7bVOwy', 'sammiermariscal@gmail.com', '09207614681', 'customer'),
(4, 'kenny', 'kenny', 'kenny', '$2y$10$nS5JZr6VrNjrWpm1UFl.O.44rhnBpC/SvtU0q9MwKS8Je89akK/4.', 'kenny32@gm.com', '324234', 'seller'),
(5, 'kenny', 'kenny', 'kenny', '$2y$10$ANMrAVX.2oosYVP1kpEyv.TEGxBuPZGIxcRC80/e8T9KcmQD2FCd.', 'adawda@awsdaw.com', '32423894283', 'customer'),
(6, '', '', '', '$2y$10$KZLJtxub.RH8L55vllphkuHlReAMdEWxKzJTMfTclkAbEwgeHnm0O', 'kennysayson1232@gmail.com123', '', 'seller');

-- --------------------------------------------------------

--
-- Table structure for table `verify_seller`
--

CREATE TABLE `verify_seller` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `birthdate` date NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `id_front` varchar(255) NOT NULL,
  `id_back` varchar(255) NOT NULL,
  `agree_terms` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `verify_seller`
--

INSERT INTO `verify_seller` (`id`, `user_id`, `birthdate`, `contact_number`, `address`, `id_front`, `id_back`, `agree_terms`, `status`, `submitted_at`) VALUES
(1, 2, '2025-11-28', '09647325561', '702-D Sagingan Street, Dalaguete, Cebu', 'uploads/Threadly Seller Flowchart.drawio.png', 'uploads/Threadly UI Flowchart.png', 1, 'rejected', '2025-11-28 15:07:42'),
(2, 2, '2025-11-28', '09647325561', '702-D Sagingan Street, Dalaguete, Cebu', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Threadly Seller Flowchart.drawio.png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Threadly UI Flowchart.png', 1, 'rejected', '2025-11-28 15:18:27'),
(3, 2, '2025-11-28', '09647325561', '607-C Sagingan Street, SIBONGA, Cebu', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Threadly Guest Flowchart.png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Threadly User Flowchart.drawio.png', 1, 'rejected', '2025-11-28 15:18:59'),
(4, 2, '0000-00-00', '09647322341', 'Ambot Sagingan Street, Basak Pardo', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Threadly Guest Flowchart.drawio.png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Code_Generated_Image.png', 1, 'approved', '2025-11-28 16:25:33'),
(5, 4, '2000-02-23', '98329848293', '32aewsdwa', 'C:/xampp/htdocs/Threadly/Front_End/uploads/download (34).png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-30 111305.png', 1, 'approved', '2025-11-30 12:03:48'),
(6, 4, '2000-02-23', '98329848293', '32aewsdwa', 'C:/xampp/htdocs/Threadly/Front_End/uploads/download (34).png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-30 111305.png', 1, 'approved', '2025-11-30 12:03:56'),
(7, 4, '2000-02-23', '98329848293', '32aewsdwa', 'C:/xampp/htdocs/Threadly/Front_End/uploads/download (34).png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-30 111305.png', 1, 'approved', '2025-11-30 12:04:04'),
(8, 6, '2005-12-06', '09563569099', 'awdaw', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-29 205130.png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-30 115924.png', 1, 'approved', '2025-12-02 03:27:44'),
(9, 6, '2005-12-06', '09563569099', 'awdaw', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-29 205130.png', 'C:/xampp/htdocs/Threadly/Front_End/uploads/Screenshot 2025-11-30 115924.png', 1, 'approved', '2025-12-02 03:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `created_at`) VALUES
(1, 1, '2025-11-30 00:17:35'),
(2, 2, '2025-11-30 00:17:35'),
(3, 6, '2025-12-02 12:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_item`
--

CREATE TABLE `wishlist_item` (
  `wishlist_item_id` int(11) NOT NULL,
  `wishlist_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist_item`
--

INSERT INTO `wishlist_item` (`wishlist_item_id`, `wishlist_id`, `product_id`, `variant_id`, `added_at`) VALUES
(23, 3, 10, NULL, '2025-12-02 12:19:41'),
(48, 3, 67, NULL, '2025-12-02 12:30:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bidding_session`
--
ALTER TABLE `bidding_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bid_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `highest_bid`
--
ALTER TABLE `highest_bid`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verify_seller`
--
ALTER TABLE `verify_seller`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD KEY `wishlist_id` (`wishlist_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bidding_session`
--
ALTER TABLE `bidding_session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `bid_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `highest_bid`
--
ALTER TABLE `highest_bid`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `verify_seller`
--
ALTER TABLE `verify_seller`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  MODIFY `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bidding_session`
--
ALTER TABLE `bidding_session`
  ADD CONSTRAINT `bidding_session_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `bidding_session` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `highest_bid`
--
ALTER TABLE `highest_bid`
  ADD CONSTRAINT `highest_bid_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variant_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verify_seller`
--
ALTER TABLE `verify_seller`
  ADD CONSTRAINT `verify_seller_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD CONSTRAINT `wishlist_item_ibfk_1` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`wishlist_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_item_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`variant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
