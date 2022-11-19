SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";




CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL,
  `tg_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tg_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL,
  `t_name` varchar(100) NOT NULL,
  `t_other_detail` varchar(100) NOT NULL COMMENT 'location,designation, role status',
  `t_image` varchar(255) NOT NULL,
  `t_text` text NOT NULL,
  `t_featured` enum('1','0') NOT NULL DEFAULT '0',
  `t_status` enum('1','0') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `transition_contents` (
  `id` int(11) NOT NULL,
  `tc_content` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tutor_category` (
  `id` int(11) NOT NULL,
  `tc_tutor_id` int(11) NOT NULL DEFAULT '0',
  `tc_categories` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assesment` (
  `id` int(11) NOT NULL,
  `uga_title` varchar(255) NOT NULL,
  `uga_category` int(11) NOT NULL,
  `uga_number_of_questions` int(11) NOT NULL,
  `uga_user_id` int(11) NOT NULL,
  `uga_duration` int(11) NOT NULL DEFAULT '1' COMMENT 'in minutes',
  `uga_show_categories` enum('0','1') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uga_status` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assesment_question` (
  `id` int(11) NOT NULL,
  `uga_assesment_id` int(11) NOT NULL,
  `uga_question_id` int(11) NOT NULL COMMENT 'in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assessment_attempt` (
  `id` int(11) NOT NULL,
  `uga_user_id` int(11) NOT NULL,
  `uga_assessment_id` int(11) NOT NULL,
  `uga_duration` int(11) NOT NULL,
  `uga_attempted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uga_evaluated` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assessment_report` (
  `id` int(11) NOT NULL,
  `ugar_attempted_id` int(11) NOT NULL,
  `ugar_question_id` int(11) NOT NULL,
  `ugar_answer` text NOT NULL,
  `ugar_duration` int(11) NOT NULL DEFAULT '0',
  `ugar_mark` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_messages` (
  `id` int(11) NOT NULL,
  `um_user_id` int(11) NOT NULL,
  `um_message_count` int(11) NOT NULL DEFAULT '0',
  `um_messages` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_plan` (
  `id` int(11) NOT NULL,
  `up_user_id` int(11) NOT NULL DEFAULT '0',
  `up_plan_id` int(11) NOT NULL DEFAULT '0',
  `up_plan_validity_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>Limitted,2=>Unlimitted',
  `up_start_date` datetime DEFAULT NULL,
  `up_end_date` datetime DEFAULT NULL,
  `up_status` enum('0','1') NOT NULL DEFAULT '0',
  `up_active_plan` enum('0','1') NOT NULL DEFAULT '0',
  `up_account_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `web_languages` (
  `id` int(11) NOT NULL,
  `wl_name` varchar(255) DEFAULT NULL,
  `wl_status` enum('0','1') NOT NULL DEFAULT '0',
  `wl_account` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;