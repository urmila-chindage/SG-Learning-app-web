SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL,
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
  `updated_date` datetime NOT NULL DEFAULT '2017-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `invited_users` (
  `id` int(11) NOT NULL,
  `iu_email_id` varchar(255) NOT NULL,
  `iu_account_id` int(11) NOT NULL,
  `iu_invited_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `item_sort_order` (
  `id` int(11) NOT NULL,
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
  `iso_item_is_free` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `lecture_log` (
  `id` int(11) NOT NULL,
  `ll_user_id` int(11) NOT NULL,
  `ll_course_id` int(11) NOT NULL,
  `ll_lecture_id` int(11) NOT NULL,
  `ll_percentage` int(11) NOT NULL DEFAULT '0',
  `ll_attempt` int(11) NOT NULL DEFAULT '1',
  `ll_marks` int(11) NOT NULL DEFAULT '0' COMMENT 'in percentage'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `lecture_override` (
  `id` int(11) NOT NULL,
  `lo_course_id` int(11) NOT NULL DEFAULT '0',
  `lo_lecture_id` int(11) NOT NULL,
  `lo_lecture_type` int(11) NOT NULL,
  `lo_start_date` date DEFAULT NULL,
  `lo_end_date` date DEFAULT NULL,
  `lo_start_time` varchar(155) NOT NULL,
  `lo_end_time` varchar(155) NOT NULL,
  `lo_duration` int(55) NOT NULL,
  `lo_attempts` int(55) NOT NULL,
  `lo_period` int(11) NOT NULL,
  `lo_period_type` varchar(5) NOT NULL,
  `lo_override_batches` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_lectures` (
  `id` int(11) NOT NULL,
  `ll_lecture_id` int(11) NOT NULL,
  `ll_course_id` int(11) NOT NULL,
  `ll_studio_id` int(11) NOT NULL,
  `ll_date` date NOT NULL,
  `ll_time` time DEFAULT '00:00:00',
  `ll_duration` int(11) NOT NULL DEFAULT '0',
  `ll_is_online` enum('1','0','2') NOT NULL DEFAULT '0',
  `ll_mode` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1 => LMS, 2 => VC(CISCO)',
  `ll_files` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_lecture_recordings` (
  `id` int(11) NOT NULL,
  `llr_live_id` int(11) NOT NULL,
  `llr_course_id` int(11) NOT NULL,
  `llr_title` varchar(255) NOT NULL,
  `llr_clip_id` varchar(255) NOT NULL,
  `llr_type` int(11) NOT NULL DEFAULT '2',
  `llr_status` enum('0','1') NOT NULL DEFAULT '0',
  `llr_lecture_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_lecture_users` (
  `id` int(11) NOT NULL,
  `llu_live_id` int(11) NOT NULL,
  `llu_user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `live_presentation_details` (
  `id` int(11) NOT NULL,
  `lpd_user_name` varchar(100) NOT NULL,
  `lpd_file_name` varchar(255) NOT NULL,
  `lpd_swf_url` varchar(255) NOT NULL,
  `lpd_live_id` int(11) NOT NULL,
  `lpd_course_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mentor_ratings` (
  `id` int(11) NOT NULL,
  `mr_user_id` int(11) NOT NULL,
  `mr_mentor_id` int(11) NOT NULL,
  `mr_rating` enum('1','2','3','4','5') NOT NULL,
  `mr_review` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `certificate_manage` (
  `id` int(11) NOT NULL,
  `cm_filename` varchar(100) NOT NULL,
  `cm_image` varchar(255) DEFAULT NULL,
  `cm_account_id` int(11) NOT NULL,
  `cm_is_active` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `challenge_zone` (
  `id` int(11) NOT NULL,
  `cz_title` varchar(255) NOT NULL,
  `cz_category` int(11) NOT NULL,
  `cz_start_date` datetime DEFAULT NULL,
  `cz_end_date` datetime DEFAULT NULL,
  `cz_instructions` text,
  `cz_duration` int(11) NOT NULL DEFAULT '1' COMMENT 'in minutes',
  `cz_show_categories` enum('0','1') NOT NULL DEFAULT '1',
  `cz_status` enum('0','1') NOT NULL DEFAULT '0',
  `cz_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `cz_account_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_attempts` (
  `id` int(11) NOT NULL,
  `cza_challenge_zone_id` int(11) NOT NULL,
  `cza_user_id` int(11) NOT NULL,
  `cza_attempted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cza_duration` int(11) NOT NULL,
  `cza_valuated` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_questions` (
  `id` int(11) NOT NULL,
  `czq_challenge_zone_id` int(11) NOT NULL DEFAULT '0',
  `czq_question_id` int(11) NOT NULL DEFAULT '0',
  `czq_status` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_zone_report` (
  `id` int(11) NOT NULL,
  `czr_attempt_id` int(11) NOT NULL,
  `czr_question_id` int(11) NOT NULL,
  `czr_answer` text NOT NULL,
  `czr_duration` int(11) NOT NULL DEFAULT '0',
  `czr_mark` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cisco_recordings` (
  `id` int(11) NOT NULL,
  `cr_source_ip` varchar(255) DEFAULT NULL,
  `cr_destination_ip` varchar(255) DEFAULT NULL,
  `cr_filename` varchar(255) DEFAULT NULL,
  `cr_size` int(11) NOT NULL DEFAULT '0',
  `cr_modified` varchar(255) DEFAULT NULL,
  `cr_date` date DEFAULT NULL,
  `cr_created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_tests` (
  `id` int(11) NOT NULL,
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
  `dt_status` tinyint(2) NOT NULL DEFAULT '1',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_by` int(4) NOT NULL,
  `action_id` int(4) NOT NULL,
  `dt_description` text NOT NULL,
  `dt_last_date` date NOT NULL,
  `dt_lecture_id` int(11) NOT NULL,
  `dt_total_mark` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_test_answers` (
  `id` int(11) NOT NULL,
  `da_attempt_id` int(11) NOT NULL,
  `da_lecture_id` int(11) NOT NULL DEFAULT '0',
  `da_user_id` int(11) NOT NULL,
  `status` smallint(4) NOT NULL,
  `da_user_type` int(11) NOT NULL DEFAULT '0',
  `action_by` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `descrptive_test_user_answered` (
  `id` int(11) NOT NULL,
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
  `updated_date` datetime DEFAULT '2017-06-29 10:41:14'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL,
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
  `updated` timestamp NOT NULL DEFAULT '2018-11-10 23:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `event_participants` (
  `id` int(11) NOT NULL,
  `ep_user_id` int(11) NOT NULL,
  `ep_event_id` int(11) NOT NULL,
  `ep_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ep_added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `expert_lectures` (
  `id` int(11) NOT NULL,
  `el_title` varchar(128) NOT NULL,
  `el_url` varchar(250) NOT NULL,
  `el_image` varchar(100) NOT NULL,
  `el_thumbnail` varchar(255) NOT NULL,
  `el_status` tinyint(1) NOT NULL DEFAULT '0',
  `el_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `el_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(1) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL,
  `an_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `an_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `an_date` datetime NOT NULL,
  `an_course_id` int(11) NOT NULL,
  `an_sent_to` enum('1','2','3') NOT NULL DEFAULT '1',
  `an_batch_ids` text NOT NULL,
  `an_institution_ids` text NOT NULL,
  `an_created_by` varchar(25) NOT NULL,
  `an_created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `api_token` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `news_letter` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `mailchimp` text NOT NULL,
  `zoho` text NOT NULL,
  `n_account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `n_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_slug` varchar(255) NOT NULL,
  `n_route_id` int(128) NOT NULL,
  `n_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `n_seo_title` text NOT NULL,
  `n_meta` text NOT NULL,
  `n_new_window` enum('0','1') DEFAULT '0',
  `n_status` tinyint(1) NOT NULL DEFAULT '0',
  `n_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `n_account_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(1) NOT NULL,
  `action_by` int(11) NOT NULL,
  `n_expiry_date` date DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,
  `n_notification_bar_type` enum('1','2','') NOT NULL DEFAULT '1' COMMENT '1= Pop up, 2= Top Bar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ofabee_slabs` (
  `id` int(11) NOT NULL,
  `os_name` varchar(255) NOT NULL,
  `os_short_description` varchar(255) NOT NULL,
  `os_description` text NOT NULL,
  `os_price` int(11) NOT NULL,
  `os_status` enum('1','0') NOT NULL DEFAULT '0',
  `os_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payment_history` (
  `id` int(11) NOT NULL,
  `ph_order_id` varchar(255) NOT NULL,
  `ph_user_id` int(11) NOT NULL,
  `ph_user_details` text NOT NULL,
  `ph_item_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 => Course, 2 => Catalog',
  `ph_item_id` int(11) NOT NULL,
  `ph_item_name` varchar(255) DEFAULT NULL,
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
  `ph_status` enum('0','1','2') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `payment_shares` (
  `id` int(11) NOT NULL,
  `ps_payment_id` int(11) NOT NULL,
  `ps_teacher_id` int(11) NOT NULL,
  `ps_amount` float NOT NULL,
  `ps_payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ps_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `plans` (
  `id` int(11) NOT NULL,
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
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `promo_code` (
  `id` int(11) NOT NULL,
  `pc_type` enum('0','1') NOT NULL COMMENT '0 - Created promo code, 1 - Generated promo code',
  `pc_promo_code_name` varchar(191) NOT NULL,
  `pc_description` text NOT NULL,
  `pc_user_permission` enum('0','1') NOT NULL COMMENT '0 - Open to all, 1 - Open to n users',
  `pc_user_limit` int(11) NOT NULL,
  `pc_discount_type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0 - Flat discount, 1 - Percentage Discount',
  `pc_discount_rate` float NOT NULL,
  `pc_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0 - Inactive; 1 - Active',
  `pc_user_count` int(11) NOT NULL,
  `pc_user_detail` longtext NOT NULL,
  `pc_account_id` int(11) NOT NULL,
  `pc_expiry_date` datetime NOT NULL,
  `pc_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pc_updated_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchase_history` (
  `id` int(11) NOT NULL,
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
  `updated_date` datetime NOT NULL DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DELIMITER $$
CREATE TRIGGER `delete_assessment_questions_connection` AFTER DELETE ON `assessments`
 FOR EACH ROW BEGIN
DELETE FROM assessment_questions WHERE aq_assesment_id = OLD.id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_after_add` AFTER INSERT ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.aa_user_id AND cs_course_id = NEW.aa_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.aa_user_id AND `aa_course_id` = NEW.aa_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.aa_user_id AND `dtua_course_id` = NEW.aa_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.aa_course_id AND cs_user_id = NEW.aa_user_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_after_update` AFTER UPDATE ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

IF(NEW.aa_grade <> OLD.aa_grade) THEN

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.aa_user_id AND cs_course_id = NEW.aa_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.aa_user_id AND `aa_course_id` = NEW.aa_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.aa_user_id AND `dtua_course_id` = NEW.aa_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id);

SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.aa_course_id AND cs_user_id = NEW.aa_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_before_add` BEFORE INSERT ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.aa_grade; 
SET NEW.aa_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_before_update` BEFORE UPDATE ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

IF(NEW.aa_grade <> OLD.aa_grade) THEN

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.aa_grade; 
SET NEW.aa_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_bundle_sort_order` AFTER INSERT ON `catalogs`
 FOR EACH ROW BEGIN INSERT INTO item_sort_order (iso_item_type, iso_item_id, iso_item_name, iso_item_sort_order, iso_item_price, iso_item_discount_price, iso_item_status, iso_item_deleted) VALUES ('bundle', NEW.id, NEW.c_title, '0', NEW.c_price, NEW.c_discount, '0', '0'); END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_bundle_data_from_other_tables` BEFORE DELETE ON `catalogs`
 FOR EACH ROW BEGIN DELETE FROM item_sort_order WHERE iso_item_id = OLD.id AND iso_item_type = 'bundle'; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bundle_sort_oder` AFTER UPDATE ON `catalogs`
 FOR EACH ROW BEGIN UPDATE item_sort_order SET iso_item_name = NEW.c_title, iso_item_price = NEW.c_price, iso_item_discount_price = NEW.c_discount, iso_item_status = NEW.c_status, iso_item_deleted = NEW.c_deleted WHERE iso_item_type="bundle" AND iso_item_id = NEW.id; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_course_sort_order` AFTER INSERT ON `course_basics`
 FOR EACH ROW BEGIN INSERT INTO item_sort_order (iso_item_type, iso_item_id, iso_item_name, iso_item_sort_order, iso_item_price, iso_item_discount_price, iso_item_status, iso_item_deleted) VALUES ('course', NEW.id, NEW.cb_title, '0', NEW.cb_price, NEW.cb_discount, '0', '0'); END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_course_data_from_other_tables` BEFORE DELETE ON `course_basics`
 FOR EACH ROW BEGIN DELETE FROM announcement WHERE an_course_id = OLD.id; DELETE FROM course_lectures WHERE cl_course_id = OLD.id; DELETE FROM course_subscription WHERE cs_course_id = OLD.id; DELETE FROM course_perfomance WHERE cp_course_id = OLD.id; DELETE FROM announcement WHERE an_course_id = OLD.id; DELETE FROM course_tutors WHERE ct_course_id = OLD.id; DELETE FROM section WHERE s_course_id = OLD.id; DELETE FROM item_sort_order WHERE iso_item_id = OLD.id AND iso_item_type = 'course'; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_sort_oder` AFTER UPDATE ON `course_basics`
 FOR EACH ROW BEGIN UPDATE item_sort_order SET iso_item_name = NEW.cb_title, iso_item_price = NEW.cb_price, iso_item_discount_price = NEW.cb_discount, iso_item_status = NEW.cb_status, iso_item_deleted = NEW.cb_deleted WHERE iso_item_type="course" AND iso_item_id = NEW.id; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_subscribed_count` AFTER UPDATE ON `course_consolidated_report`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count,percentage_count INT(11);

SELECT SUM(ccr_total_enrolled) INTO enrolled_count
FROM course_consolidated_report
WHERE ccr_course_id = NEW.ccr_course_id;

SELECT SUM(ccr_total_completed) INTO percentage_count
FROM course_consolidated_report
WHERE ccr_course_id = NEW.ccr_course_id;

UPDATE course_basics SET cb_total_enrolled_users = enrolled_count WHERE id = NEW.ccr_course_id;

UPDATE course_basics SET cb_course_completed_count = percentage_count WHERE id = NEW.ccr_course_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_lecture_data_from_other_tables` BEFORE DELETE ON `course_lectures`
 FOR EACH ROW BEGIN
DELETE FROM lecture_override WHERE lo_lecture_id = OLD.id;
DELETE FROM assessments WHERE a_lecture_id = OLD.id;
DELETE FROM assessment_attempts WHERE aa_lecture_id = OLD.id;
DELETE FROM assessment_report WHERE ar_lecture_id = OLD.id;
DELETE FROM assessment_rules WHERE lecture_id = OLD.id;
DELETE FROM live_lectures WHERE ll_lecture_id = OLD.id;
DELETE FROM survey WHERE s_lecture_id = OLD.id;
DELETE FROM survey_questions WHERE sq_lecture_id = OLD.id;
DELETE FROM survey_user_response WHERE sur_lecture_id	 = OLD.id;
DELETE FROM descrptive_tests WHERE dt_lecture_id = OLD.id;
DELETE FROM descrptive_test_user_answered WHERE dtua_lecture_id = OLD.id;
DELETE FROM descrptive_test_answers WHERE da_lecture_id = OLD.id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_lecture_count` AFTER UPDATE ON `course_lectures`
 FOR EACH ROW BEGIN  

DECLARE total_lectures, active_section_lectures, old_section_lectures, new_section_lectures INT(11);



IF OLD.cl_status <> NEW.cl_status OR OLD.cl_deleted <> NEW.cl_deleted THEN 



    SELECT COUNT(id) INTO total_lectures 

    FROM course_lectures WHERE course_lectures.cl_lecture_type = NEW.cl_lecture_type AND course_lectures.cl_course_id = NEW.cl_course_id AND cl_status='1' AND cl_deleted='0';



    IF NEW.cl_lecture_type = '1' THEN

        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = NEW.cl_course_id; 

    ELSEIF NEW.cl_lecture_type = '2' THEN

        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = NEW.cl_course_id;

    ELSEIF NEW.cl_lecture_type = '3' THEN

        UPDATE course_basics SET course_basics.cb_assessment_count = total_lectures WHERE id = NEW.cl_course_id;
        
    ELSEIF NEW.cl_lecture_type = '4' THEN

        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = NEW.cl_course_id;
        
    ELSEIF NEW.cl_lecture_type = '5' THEN

        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = NEW.cl_course_id;

    ELSEIF NEW.cl_lecture_type = '7' THEN

        UPDATE course_basics SET course_basics.cb_live_count = total_lectures WHERE id = NEW.cl_course_id;

    END IF;



    SELECT COUNT(id) INTO active_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF active_section_lectures = 0 THEN

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



IF OLD.cl_order_no <> NEW.cl_order_no THEN 



    SELECT COUNT(id) INTO active_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF active_section_lectures = 0 

    THEN 

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE 

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



IF OLD.cl_section_id <> NEW.cl_section_id THEN 

    

    SELECT COUNT(id) INTO old_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = OLD.cl_section_id  AND cl_course_id = OLD.cl_course_id;



    IF old_section_lectures = 0 THEN 

        UPDATE section SET s_status = '0' WHERE section.id = OLD.cl_section_id;

    ELSE

        UPDATE section SET s_status = '1' WHERE section.id = OLD.cl_section_id;

    END IF;



    SELECT COUNT(id) INTO new_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF new_section_lectures = 0 THEN 

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE 

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_lecture_count_on_delete` AFTER DELETE ON `course_lectures`
 FOR EACH ROW BEGIN  
DECLARE total_lectures, active_section_lectures INT(11);

    SELECT COUNT(id) INTO total_lectures 
    FROM course_lectures WHERE course_lectures.cl_lecture_type = OLD.cl_lecture_type AND course_lectures.cl_course_id = OLD.cl_course_id AND cl_status='1' AND cl_deleted='0';

    IF OLD.cl_lecture_type = '1' THEN
        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = OLD.cl_course_id; 
    ELSEIF OLD.cl_lecture_type = '2' THEN
        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = OLD.cl_course_id;
    ELSEIF OLD.cl_lecture_type = '3' THEN
        UPDATE course_basics SET course_basics.cb_assessment_count = total_lectures WHERE id = OLD.cl_course_id;
    ELSEIF OLD.cl_lecture_type = '7' THEN
        UPDATE course_basics SET course_basics.cb_live_count = total_lectures WHERE id = OLD.cl_course_id;
    END IF;

    SELECT COUNT(id) INTO active_section_lectures 
    FROM course_lectures 
    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = OLD.cl_section_id  AND cl_course_id = OLD.cl_course_id;

    IF active_section_lectures = 0 THEN
        UPDATE section SET s_status = '0' WHERE section.id = OLD.cl_section_id;
    ELSE
        UPDATE section SET s_status = '1' WHERE section.id = OLD.cl_section_id;
    END IF;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `add_course_perfomance_sum` AFTER INSERT ON `course_perfomance`
 FOR EACH ROW BEGIN  

DECLARE course_likes,course_dislikes, forum_likes,forum_dislikes INT;

SELECT SUM(cp_course_likes), SUM(cp_course_dislikes), SUM(cp_forum_likes), SUM(cp_forum_dislikes) INTO course_likes,course_dislikes, forum_likes,forum_dislikes 
FROM course_perfomance WHERE cp_course_id = NEW.cp_course_id;


UPDATE course_basics SET cb_course_likes = course_likes, cb_course_dislikes = course_dislikes, cb_course_forum_likes = forum_likes, cb_course_forum_dislikes = forum_dislikes WHERE id = NEW.cp_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_perfomance_sum` AFTER UPDATE ON `course_perfomance`
 FOR EACH ROW BEGIN  

DECLARE course_likes,course_dislikes, forum_likes,forum_dislikes INT;

SELECT SUM(cp_course_likes), SUM(cp_course_dislikes), SUM(cp_forum_likes), SUM(cp_forum_dislikes) INTO course_likes,course_dislikes, forum_likes,forum_dislikes 
FROM course_perfomance WHERE cp_course_id = NEW.cp_course_id;


UPDATE course_basics SET cb_course_likes = course_likes, cb_course_dislikes = course_dislikes, cb_course_forum_likes = forum_likes, cb_course_forum_dislikes = forum_dislikes WHERE id = NEW.cp_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_perfomance` AFTER INSERT ON `course_ratings`
 FOR EACH ROW BEGIN 

SET 
  @totalrows = ( SELECT COUNT(*) FROM course_perfomance WHERE cp_course_id = NEW.cc_course_id AND cp_institute_id = NEW.cc_institute_id  ),

   @course_like = IF(NEW.cc_rating>=3,1,0),
   @course_dislike = IF(NEW.cc_rating<3,1,0);
 
IF( @totalrows > 0) THEN

UPDATE course_perfomance SET cp_course_likes = cp_course_likes+@course_like, cp_course_dislikes = cp_course_dislikes+@course_dislike WHERE cp_course_id = NEW.cc_course_id AND cp_institute_id = NEW.cc_institute_id;

ELSE

INSERT INTO course_perfomance (cp_course_id, cp_institute_id, cp_course_institute_map, cp_course_likes, cp_course_dislikes, cp_forum_likes, cp_forum_dislikes) VALUES (NEW.cc_course_id, NEW.cc_institute_id, CONCAT(NEW.cc_course_id,'_', NEW.cc_institute_id), @course_like, @course_dislike, '0', '0');

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `consolidated_count_update` AFTER UPDATE ON `course_subscription`
 FOR EACH ROW BEGIN

DECLARE completed_count INT(11);

IF( OLD.cs_completion_registered <> NEW.cs_completion_registered )  THEN

SELECT COUNT(*) INTO completed_count  
FROM course_subscription 
WHERE cs_course_id = NEW.cs_course_id AND cs_user_institute	= NEW.cs_user_institute AND cs_completion_registered = '1';

UPDATE course_consolidated_report SET ccr_total_completed = completed_count WHERE ccr_course_id = NEW.cs_course_id AND ccr_institute_id = NEW.cs_user_institute;

END IF;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_percentage_update` BEFORE UPDATE ON `course_subscription`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, completed_count INT(11);
DECLARE final_grade VARCHAR(3);

IF( (NEW.cs_percentage - OLD.cs_old_percentage) >= 5 ) THEN

SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.cs_user_id AND `aa_course_id` = NEW.cs_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.cs_user_id AND `dtua_course_id` = NEW.cs_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET NEW.cs_percentage = IF(NEW.cs_percentage IS NULL, 0, NEW.cs_percentage);


SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+NEW.cs_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

SET NEW.cs_auto_grade = final_grade;
SET NEW.cs_old_percentage = NEW.cs_percentage;

END IF;

IF( NEW.cs_percentage > 99 )  THEN
SET NEW.cs_completion_registered = '1';
END IF;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_user_name_for_subscription` BEFORE INSERT ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE user_name VARCHAR(255);
DECLARE user_institute INT(11);

SELECT users.us_name, users.us_institute_id INTO user_name, user_institute  
FROM users WHERE users.id = NEW.cs_user_id;

SET NEW.cs_user_name = user_name, NEW.cs_user_institute = user_institute;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_consolidated_report_of_enrolled` AFTER INSERT ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count INT(11);

SELECT COUNT(*) INTO enrolled_count 
FROM course_subscription 
WHERE cs_course_id = NEW.cs_course_id AND cs_user_institute	= NEW.cs_user_institute;

UPDATE course_consolidated_report SET ccr_total_enrolled = enrolled_count WHERE ccr_course_id = NEW.cs_course_id AND ccr_institute_id = NEW.cs_user_institute;

UPDATE course_basics SET cb_total_enrolled_users = enrolled_count WHERE id = NEW.cs_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_consolidated_report_on_unsubscribe` AFTER DELETE ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count,completed_count INT(11);

SELECT COUNT(*) INTO enrolled_count 
FROM course_subscription 
WHERE cs_course_id = OLD.cs_course_id AND cs_user_institute	= OLD.cs_user_institute;

UPDATE course_consolidated_report SET ccr_total_enrolled = enrolled_count WHERE ccr_course_id = OLD.cs_course_id AND ccr_institute_id = OLD.cs_user_institute;

SELECT COUNT(*) INTO completed_count  
FROM course_subscription 
WHERE cs_course_id = OLD.cs_course_id AND cs_user_institute	= OLD.cs_user_institute AND (cs_percentage > 99 OR cs_completion_registered = 1);

UPDATE course_consolidated_report SET ccr_total_completed = completed_count WHERE ccr_course_id = OLD.cs_course_id AND ccr_institute_id = OLD.cs_user_institute;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_after_add` AFTER INSERT ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.dtua_user_id AND cs_course_id = NEW.dtua_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.dtua_user_id AND `aa_course_id` = NEW.dtua_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.dtua_user_id AND `dtua_course_id` = NEW.dtua_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id);

SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.dtua_course_id AND cs_user_id = NEW.dtua_user_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_after_update` AFTER UPDATE ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

IF(NEW.dtua_grade <> OLD.dtua_grade) THEN

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.dtua_user_id AND cs_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;


SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.dtua_user_id AND `aa_course_id` = NEW.dtua_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.dtua_user_id AND `dtua_course_id` = NEW.dtua_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.dtua_course_id AND cs_user_id = NEW.dtua_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_before_add` BEFORE INSERT ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);


SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.dtua_grade; 
SET NEW.dtua_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_before_update` BEFORE UPDATE ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

IF(NEW.dtua_grade <> OLD.dtua_grade) OR (OLD.dtua_grade IS NULL) THEN

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.dtua_grade; 
SET NEW.dtua_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_point` AFTER INSERT ON `log_activity`
 FOR EACH ROW BEGIN 

IF(NEW.la_user_id != 0) THEN

UPDATE log_activity_points SET lap_user_points = lap_user_points + NEW.la_points WHERE lap_user_id = NEW.la_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `delete_field_on_block_removal` AFTER DELETE ON `profile_blocks`
 FOR EACH ROW BEGIN

DELETE FROM profile_fields WHERE pf_block_id = OLD.id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_tags_label_on_insert` BEFORE INSERT ON `questions`
 FOR EACH ROW BEGIN
DECLARE tags_label TEXT;
SELECT GROUP_CONCAT(tg_name) INTO tags_label FROM tags WHERE FIND_IN_SET (tags.id, (NEW.q_tags));
SET NEW.q_tags_label = tags_label;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_tags_label_on_update` BEFORE UPDATE ON `questions`
 FOR EACH ROW BEGIN
DECLARE tags_label TEXT;
SELECT GROUP_CONCAT(tg_name) INTO tags_label FROM tags WHERE FIND_IN_SET (tags.id, (NEW.q_tags));
SET NEW.q_tags_label = tags_label;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `survey_response_received` AFTER INSERT ON `survey_user_response`
 FOR EACH ROW BEGIN

UPDATE survey SET s_response_received = '1' WHERE survey.id = NEW.sur_survey_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_point_creator` AFTER INSERT ON `users`
 FOR EACH ROW BEGIN 

IF(NEW.us_role_id = 2) THEN

INSERT INTO `log_activity_points` (`lap_user_id`, `lap_user_name`, `lap_user_points`) VALUES (NEW.id, NEW.us_name, '0');

END IF;

INSERT INTO `user_messages` (`um_user_id`,`um_messages`) VALUES (NEW.id,'{}');

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_user_data_from_other_tables` BEFORE DELETE ON `users`
 FOR EACH ROW BEGIN DELETE FROM assessment_attempts WHERE aa_user_id = OLD.id; DELETE FROM assessment_report WHERE ar_user_id = OLD.id; DELETE FROM course_subscription WHERE cs_user_id = OLD.id; DELETE FROM course_tutors WHERE ct_tutor_id = OLD.id; DELETE FROM descrptive_test_answers WHERE da_user_id = OLD.id; DELETE FROM descrptive_test_user_answered WHERE dtua_user_id = OLD.id; DELETE FROM event_participants WHERE ep_user_id = OLD.id; DELETE FROM log_activity WHERE la_user_id = OLD.id; DELETE FROM log_activity_points WHERE lap_user_id = OLD.id; DELETE FROM survey_user_response WHERE sur_user_id = OLD.id OR sur_tutor_id = OLD.id; DELETE FROM bundle_subscription WHERE bs_user_id = OLD.id; DELETE FROM payment_history WHERE ph_user_id = OLD.id; DELETE FROM course_wishlist WHERE cw_user_id = OLD.id; DELETE FROM purchase_history WHERE ph_user_id = OLD.id; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_user_name_course_subscription` AFTER UPDATE ON `users`
 FOR EACH ROW BEGIN 

IF(NEW.us_name <> OLD.us_name OR NEW.us_groups <> OLD.us_groups) THEN

UPDATE course_subscription SET course_subscription.cs_user_name = NEW.us_name, course_subscription.cs_user_groups = NEW.us_groups WHERE course_subscription.cs_user_id = NEW.id;

END IF;

END
$$
DELIMITER ;


ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invited_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `item_sort_order`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `lecture_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `lecture_override`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `live_lectures`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `live_lecture_recordings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `live_lecture_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `live_presentation_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mentor_ratings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `certificate_manage`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `challenge_zone`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `challenge_zone_attempts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `challenge_zone_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `challenge_zone_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cisco_recordings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `descrptive_tests`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `descrptive_test_answers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `descrptive_test_user_answered`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `event_participants`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `expert_lectures`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `api_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

ALTER TABLE `news_letter`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ofabee_slabs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_shares`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `promo_code`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `purchase_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `institute_basics`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `account_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings_keys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `academic_year`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `roles_modules_meta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `web_actions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log_actions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `la_controller` (`la_controller`);

ALTER TABLE `log_activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log_activity_points`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `assessment_attempts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `assessment_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `assessment_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `assessment_rules`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_backups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_basics`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_consolidated_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_discussions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_discussion_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_language`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_lectures`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_lectures_type`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_perfomance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cp_course_institute_map` (`cp_course_institute_map`),
  ADD KEY `cp_course_institute_map_2` (`cp_course_institute_map`);

ALTER TABLE `course_preview_time`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_ratings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_reviews`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_subscription`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_tutors`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `course_wishlist`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bundle_ratings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bundle_reviews`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bundle_subscription`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `catalogs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `profile_blocks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `profile_fields`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `profile_field_values`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `site_admins`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `studio`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `study_plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subject_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscription_archive`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `super_admins`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `support_chat`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `survey`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `survey_user_response`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions_options`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions_subject`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questions_topic`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `recently_view_courses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `recently_view_pages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `recently_view_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transition_contents`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tutor_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_generated_assesment`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_generated_assesment_question`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_generated_assessment_attempt`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_generated_assessment_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_plan`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `web_languages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notification_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `menu_manager`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mobile_banners`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_activity_idx` (`last_activity`);

ALTER TABLE `conversion_queue`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `doc_unique_templates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `email_token`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `faculty_expertise`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `actions_token`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `invited_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `item_sort_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `lecture_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `lecture_override`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `live_lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `live_lecture_recordings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `live_lecture_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `live_presentation_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mentor_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `certificate_manage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `challenge_zone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `challenge_zone_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `challenge_zone_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `challenge_zone_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cisco_recordings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `descrptive_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `descrptive_test_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `descrptive_test_user_answered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `event_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `expert_lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `api_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `news_letter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ofabee_slabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `payment_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `promo_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `purchase_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `institute_basics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `account_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2731;
ALTER TABLE `settings_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=61;
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=582;
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=55;
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=36;
ALTER TABLE `academic_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
ALTER TABLE `roles_modules_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1466;
ALTER TABLE `web_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
ALTER TABLE `log_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=255;
ALTER TABLE `log_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `log_activity_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `assessment_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `assessment_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `assessment_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `assessment_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_basics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_consolidated_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_discussion_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_lectures_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_perfomance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_preview_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_tutors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `course_wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bundle_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bundle_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bundle_subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `catalogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `profile_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `profile_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `profile_field_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `site_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `studio`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `study_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `subject_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `subscription_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `super_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `support_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `survey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `survey_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `survey_user_response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions_subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `recently_view_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `recently_view_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `recently_view_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `transition_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tutor_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_generated_assesment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_generated_assesment_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_generated_assessment_attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_generated_assessment_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `web_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
ALTER TABLE `notification_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `menu_manager`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;
ALTER TABLE `mobile_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `conversion_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `doc_unique_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `email_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `faculty_expertise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `actions_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;