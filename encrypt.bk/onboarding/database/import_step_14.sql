SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `conversion_queue` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL DEFAULT '0',
  `s3_upload` tinyint(1) NOT NULL DEFAULT '0',
  `from_cisco` tinyint(1) NOT NULL DEFAULT '0',
  `file_path` text NOT NULL,
  `output_path` text,
  `conversion_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 => Upload complete, 2 => Conversion  Started, 3 => Conversion Completed, 4 => S3 Upload Completed, 5 => Conversion Error',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `doc_unique_templates` (
  `id` int(11) NOT NULL,
  `dut_date` date NOT NULL,
  `dut_name` text NOT NULL,
  `dut_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `email_token` (
  `id` int(11) NOT NULL,
  `et_user_id` int(11) DEFAULT '0',
  `et_user_email` char(100) DEFAULT NULL,
  `et_account_id` int(11) NOT NULL DEFAULT '0',
  `et_token` text NOT NULL,
  `et_status` enum('0','1') NOT NULL COMMENT '1=>Active,0=>Used',
  `et_change_status` enum('0','1') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `faculty_expertise` (
  `id` int(11) NOT NULL,
  `fe_title` varchar(255) DEFAULT NULL,
  `fe_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;