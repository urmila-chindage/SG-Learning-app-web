SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `live_lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ll_lecture_id` int(11) NOT NULL,
  `ll_course_id` int(11) NOT NULL,
  `ll_studio_id` int(11) NOT NULL,
  `ll_date` date NOT NULL,
  `ll_time` time DEFAULT '00:00:00',
  `ll_duration` int(11) NOT NULL DEFAULT '0',
  `ll_is_online` enum('1','0','2') NOT NULL DEFAULT '0',
  `ll_mode` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1 => LMS, 2 => VC(CISCO)',
  `ll_files` longtext,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_lecture_recordings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `llr_live_id` int(11) NOT NULL,
  `llr_course_id` int(11) NOT NULL,
  `llr_title` varchar(255) NOT NULL,
  `llr_clip_id` varchar(255) NOT NULL,
  `llr_type` int(11) NOT NULL DEFAULT '2',
  `llr_status` enum('0','1') NOT NULL DEFAULT '0',
  `llr_lecture_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_lecture_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `llu_live_id` int(11) NOT NULL,
  `llu_user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_presentation_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lpd_user_name` varchar(100) NOT NULL,
  `lpd_file_name` varchar(255) NOT NULL,
  `lpd_swf_url` varchar(255) NOT NULL,
  `lpd_live_id` int(11) NOT NULL,
  `lpd_course_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mentor_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mr_user_id` int(11) NOT NULL,
  `mr_mentor_id` int(11) NOT NULL,
  `mr_rating` enum('1','2','3','4','5') NOT NULL,
  `mr_review` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


