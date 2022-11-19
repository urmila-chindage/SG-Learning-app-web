SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `certificate_manage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cm_filename` varchar(100) NOT NULL,
  `cm_image` varchar(255) DEFAULT NULL,
  `cm_account_id` int(11) NOT NULL,
  `cm_is_active` enum('0','1') NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `challenge_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cz_title` varchar(255) NOT NULL,
  `cz_category` int(11) NOT NULL,
  `cz_start_date` datetime DEFAULT NULL,
  `cz_end_date` datetime DEFAULT NULL,
  `cz_instructions` text,
  `cz_duration` int(11) NOT NULL DEFAULT '1' COMMENT 'in minutes',
  `cz_show_categories` enum('0','1') NOT NULL DEFAULT '1',
  `cz_status` enum('0','1') NOT NULL DEFAULT '0',
  `cz_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `cz_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '2016-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cza_challenge_zone_id` int(11) NOT NULL,
  `cza_user_id` int(11) NOT NULL,
  `cza_attempted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cza_duration` int(11) NOT NULL,
  `cza_valuated` enum('0','1') NOT NULL DEFAULT '1',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `czq_challenge_zone_id` int(11) NOT NULL DEFAULT '0',
  `czq_question_id` int(11) NOT NULL DEFAULT '0',
  `czq_status` enum('1','0') NOT NULL DEFAULT '1',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `czr_attempt_id` int(11) NOT NULL,
  `czr_question_id` int(11) NOT NULL,
  `czr_answer` text NOT NULL,
  `czr_duration` int(11) NOT NULL DEFAULT '0',
  `czr_mark` varchar(10) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

