-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2015 at 03:42 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `alphasms`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `msisdn_from` varchar(20) NOT NULL,
  `msisdn_to` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(10) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `record_time` (`record_time`,`msisdn_from`,`msisdn_to`,`type`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `reply_messages`
--

CREATE TABLE IF NOT EXISTS `reply_messages` (
  `error_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_code` varchar(3) NOT NULL,
  `messages` varchar(1024) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`error_id`),
  UNIQUE KEY `error_id` (`error_id`),
  KEY `error_code` (`message_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `reply_messages`
--

INSERT INTO `reply_messages` (`error_id`, `message_code`, `messages`, `created`) VALUES
(1, '100', 'Terima kasih atas laporan anda. Data akan kami gunakan demi kemakmuran rakyat Indonesia.', '2015-08-21 22:18:35'),
(2, '900', 'Perintah tidak ditemukan. Cek kembali format perintah yang anda isi. Jika butuh bantuan balas HELP', '2015-08-21 23:51:29'),
(3, '200', '{MESSAGE}', '2015-08-21 23:35:15'),
(4, '901', 'Mohon maaf, data anda tidak berhasil diinput.\\r\\n Silakan coba kembali beberapa saat lagi.', '2015-08-21 23:48:25'),
(5, '902', 'Mohon maaf, data yang anda cari tidak ditemukan.\\r\\nSilakan mencoba untuk data yang lain.', '2015-08-22 12:12:34'),
(6, '300', 'Point anda :\\r{POINT}\\rBalas HELP jika mengalami kesulitan.', '2015-08-22 15:13:24'),
(7, '903', 'Mohon maaf, saat ini anda belum memiliki point.', '2015-08-22 12:12:34'),
(8, '400', 'Utk melapor data ketik\\rLAPOR<spasi>JENIS SENTRA#NAMA SENTRA#KODE POS#NAMA KOMODITI#JENIS KOMODITI#KUANTITAS#SATUAN#HARGA', '2015-08-22 14:10:21'),
(9, '909', 'Mohon maaf pesan yang anda kirimkan tidak lengkap. Silakan cek kembali.', '2015-08-22 11:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `sms_command`
--

CREATE TABLE IF NOT EXISTS `sms_command` (
  `command_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `command_name` varchar(20) NOT NULL,
  `command_code` varchar(5) NOT NULL,
  `last_update` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`command_id`),
  UNIQUE KEY `command_id` (`command_id`),
  KEY `command_name` (`command_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sms_command`
--

INSERT INTO `sms_command` (`command_id`, `command_name`, `command_code`, `last_update`) VALUES
(1, 'LAPOR', '001', '2015-08-21 23:30:00'),
(2, 'CARI', '002', '2015-08-21 22:27:28'),
(3, 'POINT', '003', '2015-08-22 14:11:19'),
(4, 'HELP', '004', '2015-08-22 15:24:14');

-- --------------------------------------------------------

--
-- Table structure for table `sms_log`
--

CREATE TABLE IF NOT EXISTS `sms_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trx_id` varchar(16) NOT NULL,
  `receive_datetime` datetime NOT NULL,
  `sender` varchar(15) NOT NULL,
  `receiver` varchar(15) NOT NULL,
  `message` varchar(255) NOT NULL,
  `reply_message` varchar(255) NOT NULL,
  `reply_datetime` datetime NOT NULL,
  `command_code` varchar(5) NOT NULL,
  `reply_code` varchar(5) NOT NULL,
  `status` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `trx_id_2` (`trx_id`),
  KEY `trx_id` (`trx_id`,`receive_datetime`,`sender`,`receiver`,`reply_datetime`,`command_code`,`reply_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
