SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `studio` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `st_name` varchar(100) NOT NULL,
  `st_dial_in_number` varchar(4) NOT NULL,
  `st_url` varchar(100) NOT NULL ,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `study_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sp_user_id` int(11) NOT NULL,
  `sp_week_format` varchar(40) NOT NULL,
  `sp_lectures` longtext NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `subject_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sr_course_id` int(11) NOT NULL DEFAULT '0',
  `sr_user_id` int(11) NOT NULL DEFAULT '0',
  `sr_subject_id` int(11) NOT NULL DEFAULT '0',
  `sr_percentage` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `subscription_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sa_user_id` int(11) NOT NULL,
  `sa_user_name` varchar(555) DEFAULT NULL,
  `sa_user_email` varchar(555) DEFAULT NULL,
  `sa_user_register_number` varchar(255) DEFAULT NULL,
  `sa_user_phone` varchar(55) DEFAULT NULL,
  `sa_user_institute_id` int(11) NOT NULL,
  `sa_user_groups` varchar(555) DEFAULT NULL,
  `sa_course_id` int(11) NOT NULL,
  `sa_course_title` varchar(555) DEFAULT NULL,
  `sa_course_code` varchar(555) DEFAULT NULL,
  `sa_user_details` longtext NOT NULL,
  `sa_course_details` longtext NOT NULL,
  `sa_subscription_details` longtext NOT NULL,
  `sa_cs_startdate` date DEFAULT NULL,
  `sa_cs_enddate` date DEFAULT NULL,
  `sa_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


