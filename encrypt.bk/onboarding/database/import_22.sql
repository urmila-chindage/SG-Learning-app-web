SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `email_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `et_user_id` int(11) DEFAULT '0',
  `et_user_email` char(100) DEFAULT NULL,
  `et_account_id` int(11) NOT NULL DEFAULT '0',
  `et_token` text NOT NULL,
  `et_status` enum('0','1') NOT NULL COMMENT '1=>Active,0=>Used',
  `et_change_status` enum('0','1') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `faculty_expertise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fe_title` varchar(255) DEFAULT NULL,
  `fe_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `actions_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `at_token` text,
  `at_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'token status',
  `at_purpose` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=>Profile Approval',
  `at_params` text COMMENT 'Params to perform action.(JSON)',
  `at_expire` datetime DEFAULT NULL COMMENT 'expiration datetime',
  `at_account_id` int(11) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'token generated on.',
  `updated` datetime DEFAULT NULL COMMENT 'token used on.',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(10) DEFAULT NULL,
  `branch_name` varchar(50) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `lecture_override` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lo_course_id` int(11) NOT NULL DEFAULT '0',
  `lo_lecture_id` int(11) NOT NULL,
  `lo_lecture_type` int(11) NOT NULL,
  `lo_start_date` date DEFAULT NULL,
  `lo_end_date` date DEFAULT NULL,
  `lo_start_time` varchar(155) NOT NULL,
  `lo_end_time` varchar(155) NOT NULL,
  `lo_duration` int(55) NOT NULL,
  `lo_attempts` int(55) NOT NULL,
  `lo_period` int(11) NOT NULL,
  `lo_period_type` varchar(5) NOT NULL,
  `lo_override_batches` longtext NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


