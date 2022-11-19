SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `expert_lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `el_title` varchar(128) NOT NULL,
  `el_url` varchar(250) NOT NULL,
  `el_image` varchar(100) NOT NULL,
  `el_thumbnail` varchar(255) NOT NULL,
  `el_status` tinyint(1) NOT NULL DEFAULT '0',
  `el_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `el_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(1) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `an_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `an_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `an_date` datetime NOT NULL,
  `an_course_id` int(11) NOT NULL,
  `an_sent_to` enum('1','2','3') NOT NULL DEFAULT '1',
  `an_batch_ids` text NOT NULL,
  `an_institution_ids` text NOT NULL,
  `an_created_by` varchar(25) NOT NULL,
  `an_created_date` datetime NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `api_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL ,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `news_letter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `mailchimp` text NOT NULL,
  `zoho` text NOT NULL,
  `n_account_id` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_slug` varchar(255) NOT NULL,
  `n_route_id` int(128) NOT NULL,
  `n_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_seo_title` text NOT NULL,
  `n_meta` text NOT NULL,
  `n_new_window` enum('0','1') DEFAULT '0',
  `n_status` tinyint(1) NOT NULL DEFAULT '0',
  `n_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `n_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(1) NOT NULL,
  `action_by` int(11) NOT NULL,
  `n_expiry_date` date DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,
  `n_notification_bar_type` enum('1','2','') NOT NULL DEFAULT '1' COMMENT '1= Pop up, 2= Top Bar',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


