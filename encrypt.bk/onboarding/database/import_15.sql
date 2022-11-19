SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `super_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sa_account_id` int(11) NOT NULL DEFAULT '0',
  `sa_user_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `support_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `support_chat_script` text NOT NULL,
  `support_chat_status` enum('1','0') NOT NULL,
  `support_chat_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `s_account_id` int(11) DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `sq_high_limit_label` varchar(50) DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `survey_user_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sur_survey_id` int(11) NOT NULL,
  `sur_lecture_id` int(11) NOT NULL,
  `sur_tutor_id` int(11) DEFAULT NULL,
  `sur_user_id` int(11) NOT NULL,
  `sur_user_name` varchar(100) DEFAULT NULL,
  `sur_course_id` int(11) NOT NULL,
  `sur_question_id` int(11) NOT NULL,
  `sur_question` text,
  `sur_answer` text NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


