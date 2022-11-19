SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `section` (
  `id` int(11) NOT NULL,
  `s_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_image` varchar(100) NOT NULL DEFAULT 'default.jpg',
  `s_course_id` int(11) NOT NULL,
  `s_order_no` int(11) NOT NULL DEFAULT '0',
  `s_status` enum('1','0') NOT NULL DEFAULT '0',
  `s_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `s_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL DEFAULT '0',
  `action_by` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` varchar(255) NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `site_admins` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `studio` (
  `id` bigint(20) NOT NULL,
  `st_name` varchar(100) NOT NULL,
  `st_dial_in_number` varchar(4) NOT NULL,
  `st_url` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `study_plans` (
  `id` int(11) NOT NULL,
  `sp_user_id` int(11) NOT NULL,
  `sp_week_format` varchar(40) NOT NULL,
  `sp_lectures` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `subject_report` (
  `id` int(11) NOT NULL,
  `sr_course_id` int(11) NOT NULL DEFAULT '0',
  `sr_user_id` int(11) NOT NULL DEFAULT '0',
  `sr_subject_id` int(11) NOT NULL DEFAULT '0',
  `sr_percentage` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `subscription_archive` (
  `id` int(11) NOT NULL,
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
  `sa_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `super_admins` (
  `id` int(11) NOT NULL,
  `sa_account_id` int(11) NOT NULL DEFAULT '0',
  `sa_user_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `support_chat` (
  `id` int(11) NOT NULL,
  `support_chat_script` text NOT NULL,
  `support_chat_status` enum('1','0') NOT NULL,
  `support_chat_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(11) NOT NULL,
  `s_name` varchar(255) NOT NULL,
  `s_description` text NOT NULL,
  `s_course_id` int(11) NOT NULL,
  `s_lecture_id` int(11) NOT NULL,
  `s_section_id` int(11) NOT NULL,
  `s_tutor_id` int(11) DEFAULT NULL,
  `s_response_received` enum('0','1') NOT NULL DEFAULT '0',
  `s_tutor_name` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `s_start_date` varchar(50) NOT NULL,
  `s_end_date` varchar(50) NOT NULL,
  `s_title` varchar(255) DEFAULT NULL,
  `s_html` text,
  `s_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey_questions` (
  `id` int(11) NOT NULL,
  `sq_order` int(11) NOT NULL DEFAULT '0',
  `sq_survey_id` int(11) NOT NULL,
  `sq_course_id` int(11) DEFAULT NULL,
  `sq_lecture_id` int(11) NOT NULL,
  `sq_question` text NOT NULL,
  `sq_required` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1- required, 0 - not required',
  `sq_type` enum('1','2','3','4','5') NOT NULL COMMENT '1=> Single Choice 2=> MultipleChoice 3=>Dropdown 4=>Text 5=>Range',
  `sq_options` text,
  `sq_low_limit` int(11) DEFAULT NULL,
  `sq_high_limit` int(11) DEFAULT NULL,
  `sq_low_limit_label` varchar(50) DEFAULT NULL,
  `sq_high_limit_label` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey_user_response` (
  `id` int(11) NOT NULL,
  `sur_survey_id` int(11) NOT NULL,
  `sur_lecture_id` int(11) NOT NULL,
  `sur_tutor_id` int(11) DEFAULT NULL,
  `sur_user_id` int(11) NOT NULL,
  `sur_user_name` varchar(100) DEFAULT NULL,
  `sur_course_id` int(11) NOT NULL,
  `sur_question_id` int(11) NOT NULL,
  `sur_question` text,
  `sur_answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;