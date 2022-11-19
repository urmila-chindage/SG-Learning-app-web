SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `profile_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pb_name` varchar(255) NOT NULL,
  `pb_order` int(11) NOT NULL DEFAULT '0',
  `pb_account_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pf_block_id` int(11) NOT NULL,
  `pf_label` varchar(255) NOT NULL,
  `pf_name` varchar(50) NOT NULL,
  `pf_mandatory` enum('1','0') NOT NULL DEFAULT '0',
  `pf_auto_suggestion` enum('0','1') NOT NULL DEFAULT '0',
  `pf_order` int(11) NOT NULL,
  `pf_placeholder` varchar(255) NOT NULL,
  `pf_default_value` longtext NOT NULL,
  `pf_field_input_type` enum('1','2') NOT NULL DEFAULT '1',
  `pf_account_id` int(11) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upf_user_id` int(11) NOT NULL,
  `upf_field_id` int(11) NOT NULL,
  `upf_field_value` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_image` varchar(100) NOT NULL DEFAULT 'default-section.jpg',
  `s_course_id` int(11) NOT NULL,
  `s_order_no` int(11) NOT NULL DEFAULT '0',
  `s_status` enum('1','0') NOT NULL DEFAULT '0',
  `s_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `s_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL DEFAULT '0',
  `action_by` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` varchar(255) NOT NULL DEFAULT '0000-00-00 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `site_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


