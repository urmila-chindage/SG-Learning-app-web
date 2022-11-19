SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `q_type` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '1 => single choice, 2 => multiple choice, 3 => subjective, 4 => fill in the blanks',
  `q_difficulty` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1 => Easy, 2 => Medium, 3 => Hard',
  `q_positive_mark` float NOT NULL DEFAULT '0',
  `q_negative_mark` float NOT NULL DEFAULT '0',
  `q_directions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `q_question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `q_explanation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `q_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `q_answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma seperated answer id from the table assessment options',
  `q_course_id` int(11) NOT NULL DEFAULT '0',
  `q_que_bank` int(11) NOT NULL DEFAULT '0',
  `q_category` int(11) NOT NULL DEFAULT '1',
  `q_subject` int(11) NOT NULL DEFAULT '0',
  `q_topic` int(11) NOT NULL DEFAULT '0',
  `q_tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `q_tags_label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `q_status` enum('1','0') NOT NULL DEFAULT '0',
  `q_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `q_code` varchar(30) NOT NULL,
  `q_pending_status` int(5) NOT NULL DEFAULT '0' COMMENT '1 => ''not completed'', 2 => ''partially completed'', 3 => ''completed''',
  `q_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `questions_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_category_name` varchar(255) NOT NULL,
  `qc_status` tinyint(2) NOT NULL,
  `qc_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `qc_parent_id` int(11) NOT NULL DEFAULT '0',
  `qc_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `questions_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qo_options` text NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `questions_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qs_category_id` int(11) NOT NULL DEFAULT '0',
  `qs_subject_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qs_status` tinyint(2) NOT NULL DEFAULT '1',
  `qs_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `qs_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL DEFAULT '1',
  `action_by` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


