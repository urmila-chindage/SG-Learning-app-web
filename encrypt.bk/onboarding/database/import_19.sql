SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `user_generated_assessment_attempt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uga_user_id` int(11) NOT NULL,
  `uga_assessment_id` int(11) NOT NULL,
  `uga_duration` int(11) NOT NULL,
  `uga_attempted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uga_evaluated` enum('0','1') NOT NULL DEFAULT '1',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assessment_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ugar_attempted_id` int(11) NOT NULL,
  `ugar_question_id` int(11) NOT NULL,
  `ugar_answer` text NOT NULL,
  `ugar_duration` int(11) NOT NULL DEFAULT '0',
  `ugar_mark` varchar(10) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `um_user_id` int(11) NOT NULL,
  `um_message_count` int(11) NOT NULL DEFAULT '0',
  `um_messages` longtext,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `up_user_id` int(11) NOT NULL DEFAULT '0',
  `up_plan_id` int(11) NOT NULL DEFAULT '0',
  `up_plan_validity_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>Limitted,2=>Unlimitted',
  `up_start_date` datetime DEFAULT NULL,
  `up_end_date` datetime DEFAULT NULL,
  `up_status` enum('0','1') NOT NULL DEFAULT '0',
  `up_active_plan` enum('0','1') NOT NULL DEFAULT '0',
  `up_account_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `web_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wl_name` varchar(255) DEFAULT NULL,
  `wl_status` enum('0','1') NOT NULL DEFAULT '0',
  `wl_account` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_messages` (`um_user_id`,`um_messages`) VALUES ('1','{}');





