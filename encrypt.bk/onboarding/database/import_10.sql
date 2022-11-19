SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `course_lectures_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clt_name` varchar(255) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `course_perfomance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cp_course_id` int(11) NOT NULL DEFAULT '0',
  `cp_institute_id` int(11) NOT NULL DEFAULT '0',
  `cp_course_institute_map` varchar(11) DEFAULT NULL,
  `cp_course_likes` int(11) NOT NULL DEFAULT '0',
  `cp_course_dislikes` int(11) NOT NULL DEFAULT '0',
  `cp_forum_likes` int(11) NOT NULL DEFAULT '0',
  `cp_forum_dislikes` int(11) NOT NULL DEFAULT '0',
  `cp_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_preview_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpt_user_id` int(11) NOT NULL,
  `cpt_course_id` int(11) NOT NULL,
  `cpt_course_time` int(11) DEFAULT NULL,
  `cpt_status` enum('0','1') NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_course_id` int(11) NOT NULL,
  `cc_user_id` int(11) NOT NULL,
  `cc_institute_id` int(11) NOT NULL DEFAULT '0',
  `cc_user_name` varchar(100) NOT NULL,
  `cc_user_image` varchar(100) NOT NULL,
  `cc_status` enum('','0','1','2') NOT NULL DEFAULT '2',
  `cc_reviews` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_admin_reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cc_admin_rating_id` varchar(50) NOT NULL DEFAULT '0',
  `cc_rating` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `cc_account_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


