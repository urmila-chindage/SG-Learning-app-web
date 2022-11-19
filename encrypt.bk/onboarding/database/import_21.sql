SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `menu_manager` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `mm_parent_id` int(200) NOT NULL DEFAULT '0',
  `mm_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mm_item_type` varchar(15) NOT NULL,
  `mm_item_connected_id` int(100) NOT NULL,
  `mm_item_connected_slug` varchar(100) DEFAULT NULL,
  `mm_external_url` varchar(200) DEFAULT NULL,
  `mm_sort_order` int(100) NOT NULL DEFAULT '0',
  `mm_connected_as_external` int(5) DEFAULT NULL,
  `mm_new_window` int(5) DEFAULT NULL,
  `mm_show_in` int(5) NOT NULL DEFAULT '1',
  `mm_status` enum('','1','0') NOT NULL DEFAULT '1',
  `mm_account_id` int(11) NOT NULL,
  `mm_created_date` date NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mobile_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mb_title` text NOT NULL,
  `mb_type` int(11) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) NOT NULL DEFAULT '0' ,
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `conversion_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lecture_id` int(11) NOT NULL DEFAULT '0',
  `s3_upload` tinyint(1) NOT NULL DEFAULT '0',
  `from_cisco` tinyint(1) NOT NULL DEFAULT '0',
  `file_path` text NOT NULL,
  `output_path` text,
  `conversion_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 => Upload complete, 2 => Conversion  Started, 3 => Conversion Completed, 4 => S3 Upload Completed, 5 => Conversion Error',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `doc_unique_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dut_date` date NOT NULL,
  `dut_name` text NOT NULL,
  `dut_account_id` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

