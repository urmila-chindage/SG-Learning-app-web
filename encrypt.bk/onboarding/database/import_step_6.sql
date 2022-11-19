SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `assessments` (
  `id` int(11) NOT NULL,
  `a_course_id` int(11) NOT NULL DEFAULT '0',
  `a_category` int(11) NOT NULL DEFAULT '0',
  `a_lecture_id` int(11) NOT NULL,
  `a_instructions` text,
  `a_questions` int(11) NOT NULL DEFAULT '0',
  `a_mark` float NOT NULL DEFAULT '0',
  `a_que_report` enum('0','1') NOT NULL DEFAULT '0',
  `a_submit_immediate` enum('0','1') NOT NULL DEFAULT '1',
  `a_test_report` enum('0','1') NOT NULL DEFAULT '1',
  `a_qgrouping` enum('0','1') NOT NULL DEFAULT '0',
  `a_qshuffling` enum('0','1') NOT NULL DEFAULT '0',
  `a_show_mark` enum('0','1') NOT NULL DEFAULT '0',
  `a_limit_navigation` enum('0','1') NOT NULL DEFAULT '0',
  `a_show_smessage` enum('0','1') NOT NULL DEFAULT '0',
  `a_smessage` text,
  `a_has_pass_fail` enum('0','1') NOT NULL DEFAULT '0',
  `a_fail_pass_message` enum('0','1') NOT NULL DEFAULT '0',
  `a_pass_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `a_fail_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `a_published` enum('0','1') NOT NULL DEFAULT '0',
  `a_attend_all` enum('0','1') NOT NULL DEFAULT '0',
  `a_show_categories` enum('0','1') NOT NULL DEFAULT '1',
  `a_plans` text,
  `a_pass_percentage` int(11) NOT NULL DEFAULT '0',
  `a_institutions` varchar(155) DEFAULT NULL,
  `a_groups` varchar(555) DEFAULT NULL,
  `a_to` date DEFAULT NULL,
  `a_duration` int(11) NOT NULL DEFAULT '10' COMMENT 'in minutes',
  `a_total_attempt` int(11) NOT NULL DEFAULT '0',
  `a_from` date DEFAULT NULL,
  `a_from_time` varchar(55) NOT NULL,
  `a_to_time` varchar(55) NOT NULL,
  `a_from_availability` int(2) NOT NULL,
  `a_to_availability` int(2) NOT NULL,
  `rule_availability` int(2) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assessment_attempts` (
  `id` int(11) NOT NULL,
  `aa_assessment_id` int(11) NOT NULL,
  `aa_user_id` int(11) NOT NULL,
  `aa_course_id` int(11) NOT NULL,
  `aa_lecture_id` int(11) NOT NULL,
  `aa_attempted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aa_evaluated_date` datetime DEFAULT NULL,
  `aa_from_date` datetime DEFAULT NULL,
  `aa_to_date` datetime DEFAULT NULL,
  `aa_duration` int(11) NOT NULL DEFAULT '0' COMMENT 'in seconds',
  `aa_valuated` enum('0','1') NOT NULL DEFAULT '1',
  `aa_valuated_by` int(11) NOT NULL DEFAULT '0',
  `aa_mark_scored` decimal(13,2) DEFAULT NULL,
  `aa_percentage` int(11) NOT NULL DEFAULT '0',
  `aa_grade` char(3) NOT NULL DEFAULT '-',
  `aa_grade_higher_value` int(11) NOT NULL DEFAULT '0',
  `aa_total_mark` int(11) NOT NULL DEFAULT '0',
  `aa_total_questions` int(11) NOT NULL DEFAULT '0',
  `aa_total_duration` int(11) NOT NULL DEFAULT '0',
  `aa_marked_preview` text,
  `aa_completed` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `aa_latest` enum('0','1') NOT NULL DEFAULT '1',
  `aa_assessment_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `assessment_questions` (
  `id` int(11) NOT NULL,
  `aq_assesment_id` int(11) NOT NULL DEFAULT '0',
  `aq_question_id` int(11) NOT NULL DEFAULT '0',
  `aq_positive_mark` float NOT NULL DEFAULT '0',
  `aq_negative_mark` float NOT NULL DEFAULT '0',
  `aq_status` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assessment_report` (
  `id` int(11) NOT NULL,
  `ar_user_id` int(11) NOT NULL,
  `ar_course_id` int(11) NOT NULL,
  `ar_lecture_id` int(11) NOT NULL,
  `ar_attempt_id` int(11) NOT NULL,
  `ar_question_id` int(11) NOT NULL,
  `ar_answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ar_duration` int(11) NOT NULL DEFAULT '0',
  `ar_mark` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assessment_rules` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `selected_lecture` varchar(55) NOT NULL,
  `activity_option` varchar(5) NOT NULL,
  `percentage` varchar(55) NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;