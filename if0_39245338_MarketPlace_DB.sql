-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql313.byetcluster.com
-- Generation Time: Oct 16, 2025 at 09:37 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39245338_MarketPlace_DB`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `appointment_status` varchar(100) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `education_id` int(11) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `field_of_study` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`education_id`, `school`, `degree`, `field_of_study`, `start_date`, `end_date`) VALUES
(1, 'EDUVOS', 'BSC Software engineering', 'Information technology', '2025-06-02', '2025-06-16'),
(2, 'University of Johannesburg', 'BA Marketing', 'Marketing', '2022-01-01', '2023-12-30');

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `experience_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience`
--

INSERT INTO `experience` (`experience_id`, `title`, `company`, `start_date`, `end_date`) VALUES
(1, 'Shop assistant', 'PNA,Mallm of Africa', '2024-11-28', '2025-06-01'),
(5, 'Social media strategist', 'Candy Girl ltd', '2023-06-01', '2024-04-30'),
(6, 'Marketing strategist', 'Candy pop Ltd', '2024-02-01', '2024-09-30');

-- --------------------------------------------------------

--
-- Table structure for table `hire_requests`
--

CREATE TABLE `hire_requests` (
  `hire_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text NOT NULL,
  `preferred_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hire_requests`
--

INSERT INTO `hire_requests` (`hire_id`, `buyer_id`, `seller_id`, `task_title`, `task_description`, `preferred_date`, `created_at`) VALUES
(1, 1, 2, 'Printing', 'i need a paper printed', '2025-07-02', '2025-06-30 01:15:02'),
(2, 9, 1, 'Logo Design', 'Create logo for business', '2025-07-08', '2025-06-30 08:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` float DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` text DEFAULT NULL,
  `product_type` enum('good','service') NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `user_id`, `name`, `price`, `description`, `category`, `product_type`, `image_path`) VALUES
(5, 3, 'Website Development', 1200, 'Custom responsive website built with modern technologies.', 'Development', 'service', '../images/pexels-anntarazevich-5775937.jpg'),
(6, 5, 'Social Media Management', 800, 'Strategic social media posting and content creation for your business.', 'Marketing', 'service', '../images/pexels-cottonbro-4114731.jpg'),
(7, 1, 'Business Card Design', 300, 'Sleek, professional business card tailored to your brand.', 'Design', 'service', 'images/pexels-fauxels-3183197.jpg'),
(8, 1, 'Product Mockup Design', 500, 'High-quality product mockups for branding and presentation.', 'Design', 'service', 'images/pexels-fauxels-3183202.jpg'),
(9, 1, 'TUTORING', 200, 'MATH TUTORING', 'service', 'service', NULL),
(10, 1, 'TUTORING', 200, 'MATH TUTORING', 'service', 'service', '../images/kimble.jpg'),
(11, 9, 'Tutoring', 250, 'Science tutoring', 'Education', 'service', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seller_details`
--

CREATE TABLE `seller_details` (
  `seller_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `skills_id` int(11) DEFAULT NULL,
  `user_experience_id` int(11) NOT NULL,
  `user_education_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_details`
--

INSERT INTO `seller_details` (`seller_id`, `user_id`, `address_id`, `skills_id`, `user_experience_id`, `user_education_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 0, 0, '2025-06-27 00:13:56', '2025-06-27 00:13:56'),
(2, 5, NULL, NULL, 0, 0, '2025-06-29 16:03:14', '2025-06-29 16:03:14'),
(3, 8, NULL, NULL, 0, 0, '2025-06-30 07:21:36', '2025-06-30 07:21:36'),
(4, 9, NULL, NULL, 0, 0, '2025-06-30 08:34:37', '2025-06-30 08:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `seller_skills`
--

CREATE TABLE `seller_skills` (
  `seller_skills_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `skills_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_skills`
--

INSERT INTO `seller_skills` (`seller_skills_id`, `seller_id`, `skills_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 2, 4),
(5, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

CREATE TABLE `shipping_address` (
  `address_id` int(11) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `isDefault` tinyint(1) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skills_id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `proficiency` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skills_id`, `title`, `description`, `proficiency`) VALUES
(1, 'PHP', NULL, NULL),
(2, 'Javascript', NULL, NULL),
(3, 'Web devlopemnt', NULL, NULL),
(4, 'leader', NULL, NULL),
(5, 'canva', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`testimonial_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 'This platform helped me find amazing freelance projects and grow my career!', '2025-06-27 03:10:49'),
(2, 2, 'I love how easy it is to connect with talented freelancers here. Highly recommend!', '2025-06-27 03:10:49'),
(3, 3, 'A trustworthy marketplace where service providers and clients meet successfully.', '2025-06-27 03:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fname` varchar(250) DEFAULT NULL,
  `lname` varchar(250) DEFAULT NULL,
  `about_me` text NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `username` varchar(250) NOT NULL,
  `phone_num` varchar(15) DEFAULT NULL,
  `user_password` varchar(250) NOT NULL,
  `role` enum('buyer','seller','admin') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `lname`, `about_me`, `email`, `username`, `phone_num`, `user_password`, `role`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'Tanatswa', 'Mthembu', 'third year software engineering student at the eduvos,midrand campus', 'tanatswamthembu@gmail.com', 'Tanatswa', '0839808124', '$2y$10$QihBdMHhiK1MsM6LKTFCPebdclK0w5RHsStBfz3m8Kd4LnFdHSdf.', 'seller', '	\r\n../images/pexels-fauxels-3183202.jpg', '2025-06-27 00:13:56', '2025-06-30 07:34:49'),
(2, 'Tanatswa', 'Mthembu', '', 'tanatswamthembu2002@gmail.com', 'Tanatswa12', '0839808124', '$2y$10$o0lhncQCdS257IaEy5yiEuGRKDN4fOZCVIpHMvQ5fto.t0uwce/XC', 'buyer', NULL, '2025-06-27 00:14:46', '2025-06-27 00:14:46'),
(3, 'Tanyaradzwa', 'Gweme', '', 'tanyaradzwamiranda5@gmail.com', 'Tanyaradzwa Gweme', '0780695000', '$2y$10$MI2OqHjb7xfnRaY6j5xwMeSV17C1AhnzTruUc.Qvpq67kdU9CCBBe', 'seller', NULL, '2025-06-27 01:12:53', '2025-06-27 01:12:53'),
(4, 'kimble', 'Mthembu', '', 'kimblemthembu2002@gmail.com', 'Kimble', '0780695000', '$2y$10$5Wt8pLiCtAkY0Ccub0LMNew9odXAsQckJTBQe82tsillKtj6Tb0rS', 'seller', NULL, '2025-06-29 15:58:06', '2025-06-29 15:58:06'),
(5, 'kimble', 'Mthembu', 'I am a passionate social media strategist with a love for helping brands grow their online presence. With a mix of creativity and data-driven insight, I craft compelling content that connects, engages, and converts.\'\r\n', 'kimmthembu@gmail.com', 'kim', '0780695000', '$2y$10$8onmc4PCt4T3./PlXVpjuuc6amG3vmkb5Z77lPTbrO6g4NRSDAjbW', 'seller', './images/kimble.jpg', '2025-06-29 16:03:14', '2025-06-30 07:45:05'),
(6, 'Tanatswa', 'Mthembu', '', 'Tannnatswa@gmail.com', 'Tana', '0780695000', '$2y$10$28bxLCQyMXrZuekx8vdKuuq/gHqGS2vntAQnBuC1Z7Yvt3ApvzYaq', 'buyer', 'uploads/profile_images/profile_6861a35ec437d0.50919540.jpg', '2025-06-29 20:34:38', '2025-06-29 20:34:38'),
(7, 'Admin', 'User', 'System administrator for managing the platform.', 'admin@example.com', 'admin', '0123456789', '$2y$10$YrUjs03pVu0I1Db.wbvdeukpfZEOWR8npSa1pDtSeCiXNtJwWXE/m', 'admin', '../images/pexels-cottonbro-4114731.jpg', '2025-06-29 21:52:22', '2025-06-29 21:52:22'),
(8, 'Tendani', 'Makhera', '', 'TendaniMakhera@gmail.com', 'Tendani', '0846771234', '$2y$10$kCf2qzYnlJiMLFx9xZSvl.J.DF8bcSdTN0S/k4SIw9oLQdwfrS3/G', 'seller', NULL, '2025-06-30 07:21:36', '2025-06-30 07:21:36'),
(9, 'Alice', 'Mutero', '', 'AliceMutero@gmail.com', 'Alice', '0781479670', '$2y$10$VFa9S/9DZXfzVRDlm0cmTOrVhJz4jCikfogYGj9Bn9R2uSkk91.1W', 'seller', NULL, '2025-06-30 08:34:37', '2025-06-30 08:34:37'),
(10, 'Tanya', 'Gweme', '', 'Gweme@gmail.com', 'Gweme', '0845674545', '$2y$10$FXhKTLSksz/bx2xJuaUMyOjkO43VxZBnLfOZSNqx66fnAHjjgsiZ.', 'buyer', NULL, '2025-06-30 08:38:48', '2025-06-30 08:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_education`
--

CREATE TABLE `user_education` (
  `user_education_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `education_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_education`
--

INSERT INTO `user_education` (`user_education_id`, `user_id`, `education_id`) VALUES
(1, 1, 1),
(2, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_experience`
--

CREATE TABLE `user_experience` (
  `user_experience_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `experience_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_experience`
--

INSERT INTO `user_experience` (`user_experience_id`, `user_id`, `experience_id`) VALUES
(1, 1, 1),
(2, 5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `verification_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `verification_status` text DEFAULT NULL,
  `submit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_date` timestamp NULL DEFAULT NULL,
  `supporting_docs` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`education_id`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`experience_id`);

--
-- Indexes for table `hire_requests`
--
ALTER TABLE `hire_requests`
  ADD PRIMARY KEY (`hire_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seller_details`
--
ALTER TABLE `seller_details`
  ADD PRIMARY KEY (`seller_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seller_skills`
--
ALTER TABLE `seller_skills`
  ADD PRIMARY KEY (`seller_skills_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `skills_id` (`skills_id`);

--
-- Indexes for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skills_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_education`
--
ALTER TABLE `user_education`
  ADD PRIMARY KEY (`user_education_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `education_id` (`education_id`);

--
-- Indexes for table `user_experience`
--
ALTER TABLE `user_experience`
  ADD PRIMARY KEY (`user_experience_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `experience_id` (`experience_id`);

--
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hire_requests`
--
ALTER TABLE `hire_requests`
  MODIFY `hire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seller_details`
--
ALTER TABLE `seller_details`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seller_skills`
--
ALTER TABLE `seller_skills`
  MODIFY `seller_skills_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipping_address`
--
ALTER TABLE `shipping_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skills_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_education`
--
ALTER TABLE `user_education`
  MODIFY `user_education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_experience`
--
ALTER TABLE `user_experience`
  MODIFY `user_experience_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `seller_details` (`seller_id`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller_details`
--
ALTER TABLE `seller_details`
  ADD CONSTRAINT `seller_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller_skills`
--
ALTER TABLE `seller_skills`
  ADD CONSTRAINT `seller_skills_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `seller_details` (`seller_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_skills_ibfk_2` FOREIGN KEY (`skills_id`) REFERENCES `skills` (`skills_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD CONSTRAINT `shipping_address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_education`
--
ALTER TABLE `user_education`
  ADD CONSTRAINT `user_education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_education_ibfk_2` FOREIGN KEY (`education_id`) REFERENCES `education` (`education_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_experience`
--
ALTER TABLE `user_experience`
  ADD CONSTRAINT `user_experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_experience_ibfk_2` FOREIGN KEY (`experience_id`) REFERENCES `experience` (`experience_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification`
--
ALTER TABLE `verification`
  ADD CONSTRAINT `verification_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `seller_details` (`seller_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
