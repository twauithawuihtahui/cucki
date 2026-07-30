-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2026 at 02:34 PM
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
-- Database: `csdl`
--

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_combo`
--

CREATE TABLE `chi_tiet_combo` (
  `ma_ct_combo` int(11) NOT NULL,
  `ma_combo` int(11) NOT NULL,
  `ma_cookie` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `ma_ct_don_hang` int(11) NOT NULL,
  `ma_cookie` int(11) NOT NULL,
  `ma_combo` int(11) DEFAULT NULL,
  `ma_hop` int(11) NOT NULL,
  `ma_don_hang` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `don_gia` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_gio_hang`
--

CREATE TABLE `chi_tiet_gio_hang` (
  `ma_ct_gio_hang` int(11) NOT NULL,
  `ma_gio_hang` int(11) NOT NULL,
  `ma_cookie` int(11) DEFAULT NULL,
  `ma_combo` int(11) DEFAULT NULL,
  `ma_hop` int(11) DEFAULT NULL,
  `ma_topping` int(11) DEFAULT NULL,
  `so_luong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`ma_ct_gio_hang`, `ma_gio_hang`, `ma_cookie`, `ma_combo`, `ma_hop`, `ma_topping`, `so_luong`) VALUES
(0, 1, 201, NULL, NULL, NULL, 8),
(0, 1, 202, NULL, NULL, NULL, 1),
(0, 14, 201, NULL, NULL, NULL, 2),
(0, 14, 203, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_topping`
--

CREATE TABLE `chi_tiet_topping` (
  `ma_ct_topping` int(11) NOT NULL,
  `ma_ct_don_hang` int(11) NOT NULL,
  `ma_topping` int(11) NOT NULL,
  `gia_them` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `combo_banh`
--

CREATE TABLE `combo_banh` (
  `ma_combo` int(11) NOT NULL,
  `ten_combo` varchar(150) NOT NULL,
  `soluong` int(11) NOT NULL,
  `gia_combo` decimal(10,3) NOT NULL,
  `mo_ta` text NOT NULL,
  `hinh_anh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combo_banh`
--

INSERT INTO `combo_banh` (`ma_combo`, `ten_combo`, `soluong`, `gia_combo`, `mo_ta`, `hinh_anh`) VALUES
(100, 'Combo Best Seller', 50, 73.000, 'Gồm: Classic + Kinder + Oreo + Double Chocolate. Khách mới muốn thử các vị bán chạy.\r\nCombo đã bao gồm hộp.', 'bestseller.png'),
(101, 'Combo Chocolate Lover', 49, 73.000, 'Gồm: Double Chocolate + Kinder + Oreo + Red Velvet. Dành cho tín đồ chocolate.\r\nCombo đã bao gồm hộp.', 'chocolatelover.png'),
(102, 'Combo Premium Nuts', 50, 77.000, 'Gồm: Matcha & Pistachio + Red Velvet & Macadamia + Cashew & Raisin + Pistachio. Khách thích hạt cao cấp.\r\nCombo đã bao gồm hộp.', 'premiumnuts.png'),
(103, 'Combo Healthy Choice', 50, 77.000, 'Gồm: Blueberry Almond Oats + Cashew & Raisin + Lemon + Matcha & Pistachio. Ít ngấy, nhiều hạt và trái cây.\r\nCombo đã bao gồm hộp.', 'healthychoice.png'),
(104, 'Combo Sweet & Fruity', 50, 77.000, 'Gồm: Lemon + Blueberry Almond Oats + Caramel + Cashew & Raisin. Vị ngọt thanh cùng trái cây tươi mát.\r\nCombo đã bao gồm hộp.', 'sweet&fruity.png');

-- --------------------------------------------------------

--
-- Table structure for table `cookie`
--

CREATE TABLE `cookie` (
  `ma_cookie` int(11) NOT NULL,
  `ten_sp` varchar(150) NOT NULL,
  `gia` decimal(10,3) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `mo_ta` text NOT NULL,
  `ma_loai` int(11) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `gia_nhap` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cookie`
--

INSERT INTO `cookie` (`ma_cookie`, `ten_sp`, `gia`, `so_luong`, `mo_ta`, `ma_loai`, `hinh_anh`, `gia_nhap`) VALUES
(201, 'Cookie Hạt Điều & Nho Khô (Cashew Nut & Raisin Cookies)', 20.000, 48, 'Cookie thơm béo kết hợp hạt điều rang giòn và nho khô ngọt dịu. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, hạt điều rang, nho khô, chocolate chip, vani, bột nở, muối.', 1, 'cookiehatdieu.png', 12.000),
(202, 'Cookie Matcha & Hạt Dẻ Cười (Matcha & Pistachio Cookies)', 20.000, 42, 'Cookie trà xanh Matcha thơm nhẹ hòa quyện cùng hạt dẻ cười rang bùi béo. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, bột Matcha, hạt dẻ cười rang, chocolate trắng, vani, bột nở, muối.', 2, 'cookiematcha.png', 12.000),
(203, 'Cookie Red Velvet & Hạt Mắc Ca (Red Velvet & Macadamia Cookies)', 20.000, 49, 'Cookie Red Velvet mềm ẩm kết hợp hạt mắc ca giòn béo và chocolate trắng. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, bột cacao, màu thực phẩm đỏ, hạt mắc ca rang, chocolate trắng, vani, bột nở, muối.', 3, 'cookieredvelvet&macca.png', 12.000),
(204, 'Cookie Việt Quất, Hạnh Nhân & Yến Mạch (Blueberry, Almond & Oats Cookies)', 20.000, 50, 'Cookie giàu dinh dưỡng với việt quất sấy, hạnh nhân rang và yến mạch thơm bùi. Thành phần: Bột mì, yến mạch cán dẹt, bơ lạt, đường nâu, đường trắng, trứng gà, hạnh nhân rang, việt quất sấy, vani, bột nở, muối.', 4, 'cookievietquat.png', 12.000),
(205, 'Cookie Red Velvet', 18.000, 50, 'Cookie Red Velvet mềm ẩm với chocolate trắng tan chảy. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, bột cacao, màu thực phẩm đỏ, chocolate trắng, vani, bột nở, muối.', 5, 'cookieredvelvet.png', 12.000),
(206, 'Cookie Caramel', 28.000, 50, 'Cookie nhân caramel mềm tan, ngọt thơm đậm vị. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, nhân caramel, chocolate chip, vani, bột nở, muối.', 6, 'cookiecaramel.png', 12.000),
(207, 'Cookie Oreo', 19.000, 50, 'Cookie giòn mềm với bánh Oreo nghiền và chocolate trắng. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, bánh Oreo nghiền, chocolate trắng, vani, bột nở, muối.', 7, 'cookieoreo.png', 12.000),
(208, 'Cookie Chanh (Lemon Cookies)', 18.000, 50, 'Cookie vị chanh thanh mát với lớp kem phô mai mềm mịn. Thành phần: Bột mì, bơ lạt, đường, trứng gà, nước cốt chanh, vỏ chanh bào, cream cheese, vani, bột nở, muối.', 8, 'cookiechanh.png', 12.000),
(209, 'Cookie Hạt Dẻ Cười (Pistachio Cookies)', 19.000, 50, 'Cookie thơm bơ kết hợp hạt dẻ cười rang và chocolate trắng. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, hạt dẻ cười rang, chocolate trắng, vani, bột nở, muối.', 9, 'cookiehatdecuoi.png', 12.000),
(210, 'Cookie Kinder', 19.000, 50, 'Cookie chứa những miếng chocolate Kinder béo ngậy trong từng miếng cắn. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, thanh chocolate Kinder, chocolate chip, vani, bột nở, muối.', 10, 'cookiekinder.png', 12.000),
(211, 'Cookie Truyền Thống (Classic Cookies)', 18.000, 50, 'Cookie bơ truyền thống với nhiều chocolate chip. Thành phần: Bột mì, bơ lạt, đường nâu, đường trắng, trứng gà, chocolate chip, vani, bột nở, muối.', 11, 'cookietruyenthong.png', 12.000),
(212, 'Cookie Socola Kép (Double Chocolate Cookies)', 19.000, 50, 'Cookie đậm vị socola với cacao nguyên chất và chocolate đen. Thành phần: Bột mì, bột cacao nguyên chất, bơ lạt, đường nâu, đường trắng, trứng gà, chocolate đen, chocolate chip, vani, bột nở, muối.', 12, 'cookiesocolakep.png', 12.000);

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `ma_don_hang` int(11) NOT NULL,
  `ma_khach_hang` int(11) NOT NULL,
  `ngay_dat` datetime NOT NULL,
  `trangthai` tinyint(4) NOT NULL,
  `tong_tien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`ma_don_hang`, `ma_khach_hang`, `ngay_dat`, `trangthai`, `tong_tien`) VALUES
(1, 2, '2026-07-22 19:59:47', 3, 98.00),
(2, 2, '2026-07-22 20:01:11', 3, 25.00),
(3, 2, '2026-07-22 20:03:35', 3, 20.00),
(4, 2, '2026-07-22 20:08:53', 3, 20.00),
(5, 2, '2026-07-22 20:13:03', 3, 20.00),
(6, 2, '2026-07-22 20:19:24', 3, 20.00),
(7, 2, '2026-07-23 18:16:41', 3, 45.00),
(8, 2, '2026-07-23 18:34:46', 3, 40.00),
(9, 2, '2026-07-23 18:36:33', 3, 20.00),
(10, 2, '2026-07-23 18:57:41', 3, 93.00),
(11, 2, '2026-07-23 19:00:19', 3, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

CREATE TABLE `gio_hang` (
  `ma_gio_hang` int(11) NOT NULL,
  `ma_khach_hang` int(11) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ngay_cap_nhat` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gio_hang`
--

INSERT INTO `gio_hang` (`ma_gio_hang`, `ma_khach_hang`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, '2026-07-19 12:22:50', '0000-00-00 00:00:00'),
(14, 2, '2026-07-28 09:01:21', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hop_banh`
--

CREATE TABLE `hop_banh` (
  `ma_hop` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `suc_chua` int(11) NOT NULL,
  `mau_hop` varchar(50) NOT NULL,
  `mo_ta` text NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `gia_nhap` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hop_banh`
--

INSERT INTO `hop_banh` (`ma_hop`, `soluong`, `suc_chua`, `mau_hop`, `mo_ta`, `hinh_anh`, `gia_nhap`) VALUES
(301, 100, 4, 'Trắng', 'Tối giản, tinh tế và sạch sẽ.', 'boxtrang.png', 500),
(302, 100, 4, 'Hồng', 'Ngọt ngào, dễ thương.', 'boxhong.png', 500),
(303, 100, 4, 'Xanh', 'Thanh lịch, mát mẻ, năng động.', 'boxxanh.png', 500),
(304, 101, 4, 'Đen', 'Sang trọng, cao cấp và ấn tượng.', 'boxden.png', 500),
(305, 100, 4, 'Kraft', 'Mộc mạc, gần gũi và thân thiện với môi trường.', 'boxkraft.png', 500);

-- --------------------------------------------------------

--
-- Table structure for table `loai_cookie`
--

CREATE TABLE `loai_cookie` (
  `ma_loai` int(11) NOT NULL,
  `ten_loai` varchar(100) NOT NULL,
  `mo_ta` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loai_cookie`
--

INSERT INTO `loai_cookie` (`ma_loai`, `ten_loai`, `mo_ta`) VALUES
(1, '', ''),
(2, '', ''),
(3, '', ''),
(4, '', ''),
(5, '', ''),
(6, '', ''),
(7, '', ''),
(8, '', ''),
(9, '', ''),
(10, '', ''),
(11, '', ''),
(12, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `ma_khach_hang` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `mat_khau` varchar(255) DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma_khach_hang`, `ho_ten`, `ten_dang_nhap`, `mat_khau`, `gioi_tinh`, `email`, `so_dien_thoai`, `dia_chi`, `ngay_tao`) VALUES
(1, 'Trần Hữu Lý', 'lee309', '$2y$10$UQc5x0/f5UHIFzeemfkLS.szaTwfJoQ3.KbTPxBnN24KMZr3.ZMNS', 'Nam', 'lyb2405447@student.ctu.edu.vn', '0374767886', 'Cần Thơ', '2026-07-18 11:04:30'),
(2, 'thien123', 'thien123', '$2y$10$lh/kmI6bE0JEjdgX3p2/Cuz8ZFNE5kf0N2hf15HtKNW5lLtbFPeKG', 'Nam', 'thien123@gmail.com', '04565459889', 'thien123thien123thien123', '2026-07-21 12:05:35');

-- --------------------------------------------------------

--
-- Table structure for table `topping_banh_them`
--

CREATE TABLE `topping_banh_them` (
  `ma_topping` int(11) NOT NULL,
  `ten_topping` varchar(150) NOT NULL,
  `soluong` int(11) NOT NULL,
  `gia_them` decimal(10,3) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `mo_ta` text NOT NULL,
  `gia_nhap` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `topping_banh_them`
--

INSERT INTO `topping_banh_them` (`ma_topping`, `ten_topping`, `soluong`, `gia_them`, `hinh_anh`, `mo_ta`, `gia_nhap`) VALUES
(101, 'Cốm', 100, 4.000, 'toppingcom.jpg', 'Vị ngọt nhẹ, giòn vui miệng, nhiều màu sắc.', 2.000),
(102, 'Chocolate chips trắng', 99, 5.000, 'toppingsocolatrang.jpg', 'Ngọt béo, thơm vị sữa, tan mịn trong miệng.', 4.000),
(103, 'Chocolate chips đen', 100, 5.000, 'toppingsocoladen.jpg', 'Đậm vị cacao, ngọt vừa, hậu vị hơi đắng nhẹ.', 4.000),
(104, 'Kẹo M&M', 100, 6.000, 'toppingkeomm.jpg', 'Lớp vỏ giòn nhiều màu sắc, nhân chocolate ngọt béo.', 4.000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chi_tiet_combo`
--
ALTER TABLE `chi_tiet_combo`
  ADD PRIMARY KEY (`ma_ct_combo`),
  ADD KEY `ma_combo` (`ma_combo`,`ma_cookie`),
  ADD KEY `ma_cookie` (`ma_cookie`);

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`ma_ct_don_hang`),
  ADD KEY `maDH` (`ma_cookie`,`ma_don_hang`),
  ADD KEY `ma_hop` (`ma_hop`),
  ADD KEY `ma_don_hang` (`ma_don_hang`),
  ADD KEY `ma_combo` (`ma_combo`);

--
-- Indexes for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD KEY `ma_gio_hang` (`ma_gio_hang`,`ma_cookie`,`ma_combo`,`ma_hop`),
  ADD KEY `ma_cookie` (`ma_cookie`),
  ADD KEY `ma_combo` (`ma_combo`),
  ADD KEY `ma_hop` (`ma_hop`),
  ADD KEY `idx_ma_topping` (`ma_topping`);

--
-- Indexes for table `chi_tiet_topping`
--
ALTER TABLE `chi_tiet_topping`
  ADD PRIMARY KEY (`ma_ct_topping`),
  ADD KEY `ma_ct_don_hang` (`ma_ct_don_hang`,`ma_topping`),
  ADD KEY `ma_topping` (`ma_topping`);

--
-- Indexes for table `combo_banh`
--
ALTER TABLE `combo_banh`
  ADD PRIMARY KEY (`ma_combo`);

--
-- Indexes for table `cookie`
--
ALTER TABLE `cookie`
  ADD PRIMARY KEY (`ma_cookie`),
  ADD KEY `ma_loai` (`ma_loai`),
  ADD KEY `ma_cost` (`gia_nhap`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`ma_don_hang`),
  ADD KEY `maKH` (`ma_khach_hang`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`ma_gio_hang`),
  ADD KEY `ma_khach_hang` (`ma_khach_hang`);

--
-- Indexes for table `hop_banh`
--
ALTER TABLE `hop_banh`
  ADD PRIMARY KEY (`ma_hop`),
  ADD KEY `ma_cost` (`gia_nhap`);

--
-- Indexes for table `loai_cookie`
--
ALTER TABLE `loai_cookie`
  ADD PRIMARY KEY (`ma_loai`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`ma_khach_hang`);

--
-- Indexes for table `topping_banh_them`
--
ALTER TABLE `topping_banh_them`
  ADD PRIMARY KEY (`ma_topping`),
  ADD KEY `ma_cost` (`gia_nhap`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chi_tiet_combo`
--
ALTER TABLE `chi_tiet_combo`
  MODIFY `ma_ct_combo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_topping`
--
ALTER TABLE `chi_tiet_topping`
  MODIFY `ma_ct_topping` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_banh`
--
ALTER TABLE `combo_banh`
  MODIFY `ma_combo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `cookie`
--
ALTER TABLE `cookie`
  MODIFY `ma_cookie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `ma_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `ma_gio_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hop_banh`
--
ALTER TABLE `hop_banh`
  MODIFY `ma_hop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT for table `loai_cookie`
--
ALTER TABLE `loai_cookie`
  MODIFY `ma_loai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `ma_khach_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `topping_banh_them`
--
ALTER TABLE `topping_banh_them`
  MODIFY `ma_topping` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_combo`
--
ALTER TABLE `chi_tiet_combo`
  ADD CONSTRAINT `chi_tiet_combo_ibfk_1` FOREIGN KEY (`ma_combo`) REFERENCES `combo_banh` (`ma_combo`),
  ADD CONSTRAINT `chi_tiet_combo_ibfk_2` FOREIGN KEY (`ma_cookie`) REFERENCES `cookie` (`ma_cookie`);

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`ma_don_hang`) REFERENCES `don_hang` (`ma_don_hang`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`ma_hop`) REFERENCES `hop_banh` (`ma_hop`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_3` FOREIGN KEY (`ma_cookie`) REFERENCES `cookie` (`ma_cookie`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_4` FOREIGN KEY (`ma_combo`) REFERENCES `combo_banh` (`ma_combo`);

--
-- Constraints for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_1` FOREIGN KEY (`ma_gio_hang`) REFERENCES `gio_hang` (`ma_gio_hang`),
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_2` FOREIGN KEY (`ma_cookie`) REFERENCES `cookie` (`ma_cookie`),
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_3` FOREIGN KEY (`ma_combo`) REFERENCES `combo_banh` (`ma_combo`),
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_4` FOREIGN KEY (`ma_hop`) REFERENCES `hop_banh` (`ma_hop`),
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_5` FOREIGN KEY (`ma_topping`) REFERENCES `topping_banh_them` (`ma_topping`);

--
-- Constraints for table `chi_tiet_topping`
--
ALTER TABLE `chi_tiet_topping`
  ADD CONSTRAINT `chi_tiet_topping_ibfk_1` FOREIGN KEY (`ma_topping`) REFERENCES `topping_banh_them` (`ma_topping`);

--
-- Constraints for table `cookie`
--
ALTER TABLE `cookie`
  ADD CONSTRAINT `cookie_ibfk_1` FOREIGN KEY (`ma_loai`) REFERENCES `loai_cookie` (`ma_loai`);

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `nguoi_dung` (`ma_khach_hang`);

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`ma_khach_hang`) REFERENCES `nguoi_dung` (`ma_khach_hang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
