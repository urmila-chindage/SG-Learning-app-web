SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `ofabee_slabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_name` varchar(255) NOT NULL,
  `os_short_description` varchar(255) NOT NULL,
  `os_description` text NOT NULL,
  `os_price` int(11) NOT NULL,
  `os_status` enum('1','0') NOT NULL DEFAULT '0',
  `os_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ph_order_id` varchar(255) NOT NULL,
  `ph_user_id` int(11) NOT NULL,
  `ph_user_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ph_item_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 => Course, 2 => Catalog',
  `ph_item_id` int(11) NOT NULL,
  `ph_item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_item_code` varchar(255) DEFAULT NULL,
  `ph_item_base_price` float NOT NULL DEFAULT '0',
  `ph_item_discount_price` float NOT NULL DEFAULT '0',
  `ph_tax_type` enum('0','1') NOT NULL DEFAULT '0',
  `ph_tax_objects` text,
  `ph_promocode` longtext NOT NULL,
  `ph_item_amount_received` float NOT NULL DEFAULT '0',
  `ph_item_other_details` text,
  `ph_payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ph_payment_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 => online, 2 = free course(standard), 3 => offline',
  `ph_transaction_id` varchar(555) NOT NULL DEFAULT '0',
  `ph_transaction_details` text NOT NULL,
  `ph_account_id` int(11) NOT NULL DEFAULT '0',
  `ph_payment_gateway_used` varchar(55) NOT NULL,
  `ph_status` enum('0','1','2') NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `payment_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ps_payment_id` int(11) NOT NULL,
  `ps_teacher_id` int(11) NOT NULL,
  `ps_amount` float NOT NULL,
  `ps_payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ps_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_route_id` int(11) NOT NULL DEFAULT '0',
  `p_slug` varchar(255) DEFAULT NULL,
  `p_name` varchar(255) DEFAULT NULL,
  `p_slogan` varchar(255) DEFAULT NULL,
  `p_plan_type` enum('0','1') DEFAULT '0' COMMENT '0=>Paid,1=>Free',
  `p_price` int(11) NOT NULL DEFAULT '0',
  `p_validity_type` int(11) NOT NULL DEFAULT '0',
  `p_validity` int(11) NOT NULL DEFAULT '0',
  `p_short_description` varchar(255) DEFAULT NULL,
  `p_plan_features` text,
  `p_advantages` text,
  `p_status` enum('0','1') NOT NULL DEFAULT '0',
  `p_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `p_position` int(11) NOT NULL,
  `p_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL DEFAULT '1',
  `action_by` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `promo_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_type` enum('0','1') NOT NULL COMMENT '0 - Created promo code, 1 - Generated promo code',
  `pc_promo_code_name` varchar(191) NOT NULL,
  `pc_description` text NOT NULL,
  `pc_user_permission` enum('0','1') NOT NULL COMMENT '0 - Open to all, 1 - Open to n users',
  `pc_user_limit` int(11) NOT NULL,
  `pc_discount_type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1 - Flat discount, 0 - Percentage Discount',
  `pc_discount_rate` float NOT NULL,
  `pc_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0 - Inactive; 1 - Active',
  `pc_user_count` int(11) NOT NULL,
  `pc_user_detail` longtext NOT NULL,
  `pc_account_id` int(11) NOT NULL,
  `pc_expiry_date` datetime NOT NULL,
  `pc_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pc_updated_date` datetime NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchase_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ph_order_id` varchar(100) NOT NULL,
  `ph_user_id` int(11) NOT NULL,
  `ph_item_id` int(11) NOT NULL,
  `ph_flag` enum('1','2') NOT NULL COMMENT '1 - course, 2 - catalog',
  `ph_amount` varchar(50) NOT NULL,
  `ph_start_date` date NOT NULL,
  `ph_end_date` date NOT NULL,
  `ph_mode` varchar(50) NOT NULL,
  `ph_type` enum('1','2') NOT NULL COMMENT '1 - Feepal, 2 - standard',
  `ph_status` enum('0','1') NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '2016-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gp_name` varchar(255) NOT NULL,
  `gp_course_id` int(11) NOT NULL,
  `gp_institute_code` varchar(50) NOT NULL,
  `gp_course_code` varchar(50) DEFAULT NULL,
  `gp_year` varchar(5) NOT NULL,
  `gp_institute_id` int(11) DEFAULT NULL,
  `gp_account_id` int(11) NOT NULL,
  `gp_created_by` int(11) NOT NULL DEFAULT '0',
  `gp_status` enum('1','0') NOT NULL DEFAULT '1',
  `gp_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '2017-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `invited_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iu_email_id` varchar(255) NOT NULL,
  `iu_account_id` int(11) NOT NULL,
  `iu_invited_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `item_sort_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso_item_type` varchar(100) DEFAULT 'course, bundle',
  `iso_item_id` int(11) DEFAULT NULL,
  `iso_item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso_item_sort_order` int(11) DEFAULT NULL,
  `iso_item_price` float DEFAULT NULL,
  `iso_item_discount_price` float DEFAULT NULL,
  `iso_item_status` enum('0','1') NOT NULL DEFAULT '0',
  `iso_account_id` int(11) NOT NULL DEFAULT '0',
  `iso_item_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `iso_item_popular` int(11) DEFAULT '0',
  `iso_item_featured` int(11) NOT NULL DEFAULT '0',
  `iso_item_rating` float DEFAULT NULL,
  `iso_item_is_free` int(11) DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `lecture_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ll_user_id` int(11) NOT NULL,
  `ll_course_id` int(11) NOT NULL,
  `ll_lecture_id` int(11) NOT NULL,
  `ll_percentage` int(11) NOT NULL DEFAULT '0',
  `ll_attempt` int(11) NOT NULL DEFAULT '1',
  `ll_marks` int(11) NOT NULL DEFAULT '0' COMMENT 'in percentage',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

