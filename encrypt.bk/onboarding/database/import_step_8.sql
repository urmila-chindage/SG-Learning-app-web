SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `bundle_ratings` (
  `id` int(11) NOT NULL,
  `cc_bundle_id` int(11) NOT NULL,
  `cc_user_id` int(11) NOT NULL,
  `cc_institute_id` int(11) NOT NULL DEFAULT '0',
  `cc_user_name` varchar(100) NOT NULL,
  `cc_user_image` varchar(100) NOT NULL,
  `cc_status` enum('','0','1','2') NOT NULL DEFAULT '2',
  `cc_reviews` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_admin_reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cc_admin_rating_id` varchar(50) NOT NULL DEFAULT '0',
  `cc_rating` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `cc_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `bundle_reviews` (
  `id` int(11) NOT NULL,
  `rv_bundle_id` int(11) NOT NULL,
  `rv_user_id` int(11) NOT NULL,
  `rv_user_name` varchar(200) DEFAULT NULL,
  `rv_admin_rating_id` varchar(50) NOT NULL DEFAULT '0',
  `rv_rating_copy` int(11) NOT NULL DEFAULT '0',
  `rv_reviews` text NOT NULL,
  `rv_blocked` enum('1','0') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL DEFAULT '0',
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `bundle_subscription` (
  `id` int(11) NOT NULL,
  `bs_bundle_id` int(11) NOT NULL,
  `bs_user_id` int(11) NOT NULL,
  `bs_user_name` varchar(255) DEFAULT NULL,
  `bs_user_groups` varchar(25) DEFAULT NULL,
  `bs_start_date` date DEFAULT NULL,
  `bs_end_date` date NOT NULL,
  `bs_course_validity_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `bs_percentage` int(11) NOT NULL DEFAULT '0',
  `bs_user_institute` int(11) NOT NULL DEFAULT '0',
  `bs_completion_registered` enum('0','1') NOT NULL DEFAULT '0',
  `bs_subscription_date` datetime DEFAULT NULL,
  `bs_approved` enum('0','1','2') NOT NULL DEFAULT '2',
  `bs_bundle_details` text,
  `bs_payment_details` text,
  `bs_account_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `catalogs` (
  `id` int(11) NOT NULL,
  `c_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_code` varchar(200) DEFAULT NULL,
  `c_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_position` int(11) NOT NULL,
  `c_category` varchar(255) DEFAULT NULL,
  `c_groups` text NOT NULL,
  `c_is_free` enum('1','0') NOT NULL DEFAULT '0',
  `c_access_validity` enum('0','1','2') NOT NULL DEFAULT '0',
  `c_validity` int(11) NOT NULL,
  `c_validity_date` date DEFAULT NULL,
  `c_tax_method` int(11) NOT NULL,
  `c_courses` text NOT NULL,
  `c_price` int(11) NOT NULL,
  `c_discount` int(11) NOT NULL,
  `c_promo` varchar(255) NOT NULL,
  `c_image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `c_slug` varchar(255) NOT NULL,
  `c_route_id` int(11) NOT NULL,
  `c_meta` text NOT NULL,
  `c_meta_description` text NOT NULL,
  `c_status` enum('1','0') NOT NULL DEFAULT '0',
  `c_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `c_account_id` int(11) NOT NULL,
  `c_rating_enabled` enum('','1','0') NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_blocks` (
  `id` int(11) NOT NULL,
  `pb_name` varchar(255) NOT NULL,
  `pb_order` int(11) NOT NULL DEFAULT '0',
  `pb_account_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_fields` (
  `id` int(11) NOT NULL,
  `pf_block_id` int(11) NOT NULL,
  `pf_label` varchar(255) NOT NULL,
  `pf_name` varchar(50) NOT NULL,
  `pf_mandatory` enum('1','0') NOT NULL DEFAULT '0',
  `pf_auto_suggestion` enum('0','1') NOT NULL DEFAULT '0',
  `pf_order` int(11) NOT NULL,
  `pf_placeholder` varchar(255) NOT NULL,
  `pf_default_value` varchar(255) NOT NULL,
  `pf_field_input_type` enum('1','2') NOT NULL DEFAULT '1',
  `pf_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `profile_field_values` (
  `id` int(11) NOT NULL,
  `upf_user_id` int(11) NOT NULL,
  `upf_field_id` int(11) NOT NULL,
  `upf_field_value` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;