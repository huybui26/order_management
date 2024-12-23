-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 23, 2024 at 04:10 PM
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
-- Database: `order_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'Nguyễn Văn A', 'vana@gmail.com', '0912345678', '123 Đường Lý Thường Kiệt, Hà Nội', '2024-12-07 04:50:08'),
(2, 'Trần Thị B', 'thib@gmail.com', '0923456789', '456 Đường Nguyễn Huệ, TP. Hồ Chí Minh', '2024-12-07 04:50:08'),
(3, 'Lê Văn C', 'vanc@gmail.com', '0934567890', '789 Đường Hùng Vương, Đà Nẵng', '2024-12-07 04:50:08'),
(4, 'Phạm Thị D', 'thid@gmail.com', '0945678901', '101 Đường Trần Hưng Đạo, Cần Thơ', '2024-12-07 04:50:08'),
(5, 'Hoàng Văn E', 'vane@gmail.com', '0956789012', '202 Đường Lê Lợi, Hải Phòng', '2024-12-07 04:50:08'),
(6, 'Võ Thị F', 'thif@gmail.com', '0967890123', '303 Đường Hai Bà Trưng, Huế', '2024-12-07 04:50:08'),
(7, 'Đặng Văn G', 'vang@gmail.com', '0978901234', '404 Đường Phan Chu Trinh, Nha Trang', '2024-12-07 04:50:08'),
(8, 'Bùi Thị H', 'thih@gmail.com', '0989012345', '505 Đường Nguyễn Trãi, Quảng Ninh', '2024-12-07 04:50:08'),
(9, 'Ngô Văn I', 'vani@gmail.com', '0990123456', '606 Đường Bà Triệu, Bình Dương', '2024-12-07 04:50:08'),
(10, 'Hà Thị K', 'thik@gmail.com', '0901234567', '707 Đường Pasteur, Đồng Nai', '2024-12-07 04:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `recipient_name`, `shipping_address`, `shipping_method`, `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nguyễn Văn A', '123 Đường Lý Thường Kiệt, Hà Nội', 'Nhận tại cửa hàng', 'Thanh toán khi nhận hàng', 23.00, 'Pending', '2024-12-07 04:50:38', '2024-12-12 21:43:16'),
(2, 2, 'Trần Thị B', '456 Đường Nguyễn Huệ, TP. Hồ Chí Minh', 'Giao hàng nhanh', 'Thẻ tín dụng', 18.50, 'Completed', '2024-12-07 04:50:38', NULL),
(3, 3, 'Lê Văn C', '789 Đường Hùng Vương, Đà Nẵng', 'Giao hàng tiêu chuẩn', 'Ví điện tử', 12.00, 'Cancelled', '2024-12-07 04:50:38', NULL),
(4, 4, 'Phạm Thị Dr', '101 Đường Trần Hưng Đạo, Cần Thơ', 'Giao tận nơi', 'Thanh toán khi nhận hàng', 30.00, 'Pending', '2024-12-07 04:50:38', '2024-12-07 05:41:58'),
(5, 5, 'Hoàng Văn E', '202 Đường Lê Lợi, Hải Phòng', 'Giao hàng tiêu chuẩn', 'Thanh toán khi nhận hàng', 25.00, 'Completed', '2024-12-07 04:50:38', NULL),
(6, 1, 'Nguyễn Văn A', '123 Đường Lý Thường Kiệt, Hà Nội', 'Giao tận nơi', 'Thanh toán khi nhận hàng', 5.00, 'Completed', '2024-12-07 04:55:13', '2024-12-07 12:16:41'),
(9, 3, 'Lê Văn C', '789 Đường Hùng Vương, Đà Nẵng', 'Giao tận nơi', 'Thanh toán khi nhận hàng', 12.00, 'Pending', '2024-12-07 05:24:24', NULL),
(10, 4, 'Phạm Thị D', '101 Đường Trần Hưng Đạo, Cần Thơ', 'Giao tận nơi', 'Thanh toán khi nhận hàng', 25.00, 'Pending', '2024-12-07 05:39:52', NULL),
(12, 4, 'Phạm Thị D', '101 Đường Trần Hưng Đạo, Cần Thơ', 'Nhận tại cửa hàng', 'Thanh toán khi nhận hàng', 44.00, 'Pending', '2024-12-07 05:41:01', NULL),
(13, 3, 'Lê Văn C', '789 Đường Hùng Vương, Đà Nẵng', 'Nhận tại cửa hàng', 'Thanh toán khi nhận hàng', 7.00, 'Pending', '2024-12-07 05:41:12', NULL),
(14, 3, 'Lê Văn C', '789 Đường Hùng Vương, Đà Nẵng', 'Giao tận nơi', 'Thanh toán khi nhận hàng', 8.00, 'Pending', '2024-12-07 05:41:30', NULL),
(15, 1, 'Nguyễn Văn A', '123 Đường Lý Thường Kiệt, Hà Nội', 'Nhận tại cửa hàng', 'Thanh toán khi nhận hàng', 11.00, 'Pending', '2024-12-07 05:42:43', NULL),
(16, 1, 'Nguyễn Văn A', '123 Đường Lý Thường Kiệt, Hà Nội', 'Nhận tại cửa hàng', 'Thanh toán khi nhận hàng', 2.50, 'Pending', '2024-12-07 05:48:26', NULL),
(17, 2, 'Trần Thị B', '456 Đường Nguyễn Huệ, TP. Hồ Chí Minh', 'Giao tận nơi', 'Chuyển khoản ngân hàng', 8.00, 'Completed', '2024-12-12 02:31:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 5.50),
(2, 1, 2, 2, 6.00),
(3, 2, 3, 1, 3.00),
(4, 2, 4, 2, 7.00),
(5, 3, 5, 3, 2.50),
(6, 4, 6, 2, 5.00),
(7, 4, 7, 4, 1.50),
(8, 5, 8, 1, 4.00),
(9, 5, 9, 3, 3.50),
(10, 5, 10, 2, 6.50);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `code`, `description`, `image_url`, `price`, `stock`) VALUES
(1, 'Bánh Mousse Chocolate', 'MOUSSE01', 'Bánh mousse chocolate ngọt ngào', 'images/mousse_chocolate.jpg', 5.50, 20),
(2, 'Bánh Tiramisu', 'TIRAMISU01', 'Bánh Tiramisu hương vị Ý', 'images/tiramisu.jpg', 6.00, 15),
(3, 'Bánh Cupcake Vani', 'CUPVAN01', 'Cupcake hương vani thơm ngon', 'images/cupcake_vani.jpg', 3.00, 30),
(4, 'Bánh Red Velvet', 'REDVEL01', 'Bánh Red Velvet kem phô mai', 'images/red_velvet.jpg', 7.00, 10),
(5, 'Bánh Mì Bơ Tỏi', 'BROT01', 'Bánh mì bơ tỏi giòn tan', 'images/banh_mi_bo_toi.jpg', 2.50, 25),
(6, 'Bánh Cheesecake', 'CHEESE01', 'Cheesecake hương dâu tây', 'images/cheesecake.jpg', 5.00, 18),
(7, 'Bánh Su Kem', 'SUKEM01', 'Bánh su kem nhân trứng sữa', 'images/su_kem.jpg', 1.50, 50),
(8, 'Bánh Mì Hoa Cúc', 'HOACUC01', 'Bánh mì hoa cúc mềm mịn', 'images/hoa_cuc.jpg', 4.00, 20),
(9, 'Bánh Sừng Bò', 'SUNGBO01', 'Bánh sừng bò bơ thơm ngon', 'images/sung_bo.jpg', 3.50, 25),
(10, 'Bánh Chiffon Cam', 'CHIFFON01', 'Bánh chiffon vị cam mịn màng', 'images/chiffon_cam.jpg', 6.50, 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
