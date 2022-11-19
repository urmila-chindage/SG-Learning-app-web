SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `routes` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `route` varchar(32) DEFAULT NULL,
  `r_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `ct_name` varchar(255) NOT NULL,
  `ct_status` enum('1','0') NOT NULL DEFAULT '0',
  `ct_parent_id` int(11) NOT NULL DEFAULT '0',
  `ct_slug` varchar(255) NOT NULL,
  `ct_route_id` int(11) NOT NULL,
  `ct_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `ct_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `ct_order` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `institute_basics` (
  `id` int(11) NOT NULL,
  `ib_name` varchar(255) DEFAULT NULL,
  `ib_institute_code` varchar(50) DEFAULT NULL,
  `ib_institute_id` int(11) NOT NULL DEFAULT '0',
  `ib_address` text,
  `ib_head_name` varchar(255) DEFAULT NULL,
  `ib_head_email` varchar(255) DEFAULT NULL,
  `ib_head_phone` varchar(255) DEFAULT NULL,
  `ib_officer_name` varchar(255) DEFAULT NULL,
  `ib_officer_email` varchar(255) DEFAULT NULL,
  `ib_officer_phone` varchar(255) DEFAULT NULL,
  `ib_class_code` varchar(20) DEFAULT NULL,
  `ib_class_strength` bigint(11) DEFAULT NULL,
  `ib_image` varchar(255) DEFAULT 'default.jpg',
  `ib_about` longtext,
  `ib_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `ib_status` enum('0','1','2') NOT NULL DEFAULT '1' COMMENT '0=>Inactive,1=>Active,2=>Waiting for aproval',
  `ib_native` varchar(255) DEFAULT NULL,
  `ib_phone` varchar(20) DEFAULT NULL,
  `ib_location` varchar(255) DEFAULT NULL,
  `ib_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `us_name` varchar(255) NOT NULL,
  `us_email` varchar(255) NOT NULL,
  `us_register_number` varchar(50) DEFAULT NULL,
  `us_password` varchar(255) NOT NULL,
  `us_image` varchar(255) DEFAULT 'default.jpg',
  `us_about` text NOT NULL,
  `us_country_code` varchar(255) DEFAULT NULL,
  `us_phone` tinytext NOT NULL,
  `us_phone_verfified` enum('0','1') NOT NULL DEFAULT '1',
  `us_email_verified` enum('0','1') NOT NULL DEFAULT '0',
  `us_role_id` int(11) NOT NULL DEFAULT '1',
  `us_category_id` text,
  `us_account_id` int(11) NOT NULL,
  `us_institute_id` int(11) NOT NULL DEFAULT '0',
  `us_branch` int(11) NOT NULL DEFAULT '0',
  `us_branch_code` varchar(10) DEFAULT NULL,
  `us_institute_code` varchar(6) DEFAULT NULL,
  `us_register_no` varchar(10) DEFAULT NULL,
  `us_invited` enum('1','0') NOT NULL DEFAULT '0',
  `us_groups` text NOT NULL,
  `us_reset_password` enum('1','0') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `us_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0=>Inactive,1=>Active,2=>Waiting for aproval',
  `us_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `us_degree` varchar(255) DEFAULT NULL,
  `us_experiance` int(11) NOT NULL DEFAULT '0' COMMENT 'in months',
  `us_native` varchar(255) DEFAULT NULL,
  `us_language_speaks` varchar(255) DEFAULT NULL,
  `us_expertise` text,
  `us_youtube_url` varchar(200) NOT NULL,
  `us_badge` int(11) NOT NULL,
  `us_course_first_view` enum('1','0') NOT NULL DEFAULT '1',
  `us_messages` longtext,
  `us_profile_fields` text,
  `us_profile_completed` enum('0','1') NOT NULL DEFAULT '0',
  `us_email_exist` int(11) NOT NULL DEFAULT '0',
  `us_token` longtext NOT NULL,
  `us_session_id` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;