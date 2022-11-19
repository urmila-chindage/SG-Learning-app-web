SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `cisco_recordings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cr_source_ip` varchar(255) DEFAULT NULL,
  `cr_destination_ip` varchar(255) DEFAULT NULL,
  `cr_filename` varchar(255) DEFAULT NULL,
  `cr_size` int(11) NOT NULL DEFAULT '0',
  `cr_modified` varchar(255) DEFAULT NULL,
  `cr_date` date DEFAULT NULL,
  `cr_created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_course_id` int(11) NOT NULL DEFAULT '0',
  `dt_name` varchar(500) NOT NULL,
  `dt_file` varchar(1000) NOT NULL,
  `dt_grace_days` int(11) NOT NULL,
  `dt_instruction` text NOT NULL,
  `dt_grade` int(11) NOT NULL,
  `dt_words_limit` varchar(55) NOT NULL,
  `dt_sub_method` int(11) NOT NULL,
  `dt_content` text NOT NULL,
  `dt_uploded_files` text NOT NULL,
  `dt_status` tinyint(2) NOT NULL DEFAULT '0',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_by` int(4) NOT NULL,
  `action_id` int(4) NOT NULL,
  `dt_description` text NOT NULL,
  `dt_last_date` date NOT NULL,
  `dt_lecture_id` int(11) NOT NULL,
  `dt_total_mark` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_test_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `da_attempt_id` int(11) NOT NULL,
  `da_lecture_id` int(11) NOT NULL DEFAULT '0',
  `da_user_id` int(11) NOT NULL,
  `status` smallint(4) NOT NULL,
  `da_user_type` int(11) NOT NULL DEFAULT '0',
  `action_by` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_test_user_answered` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dtua_lecture_id` int(11) NOT NULL,
  `dtua_course_id` int(11) NOT NULL DEFAULT '0',
  `dtua_user_id` int(11) NOT NULL,
  `mark` int(11) NOT NULL,
  `dtua_grade` varchar(255) DEFAULT NULL,
  `dtua_grade_higher_value` int(11) NOT NULL DEFAULT '0',
  `dtua_assigned_to` int(11) NOT NULL DEFAULT '0',
  `dtua_evaluated` int(3) NOT NULL DEFAULT '0',
  `status` smallint(2) NOT NULL,
  `dtua_comments` text NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2017-06-29 10:41:14',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ev_name` varchar(150) NOT NULL,
  `ev_short_description` text NOT NULL,
  `ev_description` text NOT NULL,
  `ev_type` int(11) NOT NULL DEFAULT '0',
  `ev_studio_id` int(11) NOT NULL,
  `ev_account` int(11) NOT NULL DEFAULT '0',
  `ev_date` date NOT NULL,
  `ev_time` time DEFAULT NULL,
  `ev_status` enum('0','1') NOT NULL DEFAULT '1',
  `ev_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `ev_join_type` enum('1','2','3') NOT NULL DEFAULT '1',
  `ev_course_id` text,
  `ev_institute_id` text,
  `ev_batch_id` text,
  `action` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '2018-11-10 23:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `event_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ep_user_id` int(11) NOT NULL,
  `ep_event_id` int(11) NOT NULL,
  `ep_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ep_added_by` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


