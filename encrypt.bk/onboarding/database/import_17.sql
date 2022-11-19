SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `questions_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qt_category_id` int(11) NOT NULL DEFAULT '0',
  `qt_subject_id` int(11) NOT NULL DEFAULT '0',
  `qt_topic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qt_status` tinyint(2) NOT NULL,
  `qt_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `qt_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `recently_view_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rvc_user_id` int(11) NOT NULL,
  `rvc_course_id` int(11) NOT NULL,
  `rvc_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `recently_view_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rvp_user_id` int(11) NOT NULL,
  `rvp_page_id` int(11) NOT NULL,
  `rvp_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `recently_view_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rvu_admin_id` int(11) NOT NULL,
  `rvu_user_id` int(11) NOT NULL,
  `rvu_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_code` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_mobile_number` varchar(255) NOT NULL,
  `user_organization` varchar(255) NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_country` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_status` int(11) NOT NULL DEFAULT '0',
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tg_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tg_account_id` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


