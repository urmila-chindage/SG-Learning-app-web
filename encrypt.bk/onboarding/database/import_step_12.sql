SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `rl_name` varchar(255) NOT NULL,
  `rl_status` enum('1','0') NOT NULL DEFAULT '1',
  `rl_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1 => admin, 2 => user',
  `rl_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `rl_account` int(11) NOT NULL COMMENT '0 => common for all accounts, rest for corresponding account',
  `rl_default_role` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1- default, 0 - not default',
  `rl_full_course` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1- full course access, 0- not full course access',
  `rl_content_types` text,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `notification_log` (
  `id` int(11) NOT NULL,
  `nl_action_code` varchar(255) DEFAULT NULL,
  `nl_object` varchar(48) NOT NULL DEFAULT '0',
  `nl_triggered_by` text,
  `nl_notify_to` int(11) NOT NULL DEFAULT '0',
  `nl_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nl_invertable` enum('0','1') NOT NULL DEFAULT '0',
  `nl_assets` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nl_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nl_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL,
  `p_parent_id` int(11) NOT NULL DEFAULT '0',
  `p_title` varchar(128) NOT NULL,
  `p_short_description` varchar(250) NOT NULL,
  `p_position` int(11) NOT NULL,
  `p_category` int(11) NOT NULL,
  `p_slug` varchar(255) NOT NULL,
  `p_mandatory` int(11) NOT NULL DEFAULT '0',
  `p_route_id` int(128) NOT NULL,
  `p_content` longtext NOT NULL,
  `p_seo_title` text NOT NULL,
  `p_meta` text NOT NULL,
  `p_external_url` text,
  `p_goto_external_url` enum('1','0') NOT NULL DEFAULT '0',
  `p_new_window` enum('0','1') DEFAULT '0',
  `p_status` tinyint(1) NOT NULL DEFAULT '0',
  `p_show_page_in` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - header, 4 - category',
  `p_connected_menu` int(100) DEFAULT NULL,
  `p_quick_link` int(11) NOT NULL DEFAULT '0',
  `p_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `p_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(1) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `rl_name`, `rl_status`, `rl_type`, `rl_deleted`, `rl_account`, `rl_default_role`, `rl_full_course`, `rl_content_types`, `action_id`, `action_by`, `created_date`, `updated_date`) VALUES
(1, 'Subadmin', '1', '1', '0', 0, '1', '1', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2016-05-06 07:51:51', '2016-05-06 15:22:30'),
(2, 'Student', '1', '2', '0', 0, '1', '1', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2016-05-06 07:51:51', '2016-05-06 15:22:36'),
(3, 'Tutor', '1', '1', '0', 0, '1', '0', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2016-05-06 07:54:54', '2018-09-02 00:00:00'),
(4, 'Content Editor', '1', '1', '0', 1, '1', '0', '{"0":"video","1":"document","2":"quiz","3":"youtube","4":"text","5":"live","6":"descriptive_test","7":"scorm","8":"cisco_recorded_videos","9":"audio","10":"survey","11":"certificate"}', 0, 0, '2016-05-06 07:54:54', '2018-09-02 00:00:00'),
(5, 'Parent', '0', '2', '1', 1, '1', '1', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2016-05-06 07:54:54', '2018-09-02 00:00:00'),
(6, 'Mentor', '0', '2', '1', 1, '1', '1', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2017-03-01 09:54:54', '2017-03-01 00:00:00'),
(7, 'Finance Manager', '0', '1', '0', 1, '1', '0', '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}', 0, 0, '2016-05-06 07:54:54', '2017-03-24 00:00:00'),
(8, 'Institute Manager', '1', '1', '0', -1, '1', '1', '{"0":"video","1":"document","2":"quiz","3":"youtube","4":"text","5":"live","6":"descriptive_test","7":"scorm","8":"cisco_recorded_videos","9":"audio","10":"survey","11":"certificate"}', 0, 0, '2016-05-06 07:51:51', '2016-05-06 15:22:30'),
(64, 'Content Manager', '1', '1', '0', 0, '0', '1', '{"0":"video","1":"document","2":"quiz","3":"youtube","4":"survey"}', 0, 0, '2019-10-21 09:08:45', NULL);