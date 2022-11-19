SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `course_lectures_type` (
  `id` int(11) NOT NULL,
  `clt_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `course_perfomance` (
  `id` int(11) NOT NULL,
  `cp_course_id` int(11) NOT NULL DEFAULT '0',
  `cp_institute_id` int(11) NOT NULL DEFAULT '0',
  `cp_course_institute_map` varchar(11) DEFAULT NULL,
  `cp_course_likes` int(11) NOT NULL DEFAULT '0',
  `cp_course_dislikes` int(11) NOT NULL DEFAULT '0',
  `cp_forum_likes` int(11) NOT NULL DEFAULT '0',
  `cp_forum_dislikes` int(11) NOT NULL DEFAULT '0',
  `cp_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_preview_time` (
  `id` int(11) NOT NULL,
  `cpt_user_id` int(11) NOT NULL,
  `cpt_course_id` int(11) NOT NULL,
  `cpt_course_time` int(11) DEFAULT NULL,
  `cpt_status` enum('0','1') NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_ratings` (
  `id` int(11) NOT NULL,
  `cc_course_id` int(11) NOT NULL,
  `cc_user_id` int(11) NOT NULL,
  `cc_institute_id` int(11) NOT NULL DEFAULT '0',
  `cc_user_name` varchar(100) NOT NULL,
  `cc_user_image` varchar(100) NOT NULL,
  `cc_status` enum('','0','1','2') NOT NULL DEFAULT '2',
  `cc_reviews` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_admin_reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cc_admin_rating_id` varchar(50) NOT NULL DEFAULT '0',
  `cc_rating` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `cc_account_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_reviews` (
  `id` int(11) NOT NULL,
  `rv_course_id` int(11) NOT NULL,
  `rv_user_id` int(11) NOT NULL,
  `rv_user_name` varchar(255) DEFAULT NULL,
  `rv_admin_rating_id` varchar(50) NOT NULL DEFAULT '0',
  `rv_reviews` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rv_admin_reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rv_rating_copy` int(11) NOT NULL DEFAULT '0',
  `rv_blocked` enum('1','0','2') NOT NULL DEFAULT '0' COMMENT '''1 => published, 2 => ignored, 0 => unpublished''',
  `rv_status` enum('0','1') NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2012-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_subscription` (
  `id` int(11) NOT NULL,
  `cs_course_id` int(11) NOT NULL,
  `cs_user_id` int(11) NOT NULL,
  `cs_user_name` varchar(255) DEFAULT NULL,
  `cs_user_groups` varchar(255) DEFAULT NULL,
  `cs_user_institute` int(11) NOT NULL DEFAULT '0',
  `cs_subscription_date` datetime DEFAULT NULL,
  `cs_start_date` date DEFAULT NULL,
  `cs_end_date` date NOT NULL,
  `cs_course_validity_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0 - unlimited, 1 - limited by days,2 - limited by date',
  `cs_approved` enum('0','1','2') NOT NULL DEFAULT '2' COMMENT '0=> Suspended,1=>Active,2=>Pending Approval',
  `cs_forum_blocked` enum('0','1') NOT NULL DEFAULT '0',
  `cs_certificate_issued` enum('1','0') NOT NULL DEFAULT '0',
  `cs_last_played_lecture` int(11) NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL DEFAULT '1',
  `action_by` int(11) NOT NULL DEFAULT '1',
  `cs_bundle_id` int(5) NOT NULL DEFAULT '0',
  `cs_download_certificate` text,
  `cs_completion_registered` enum('0','1') NOT NULL DEFAULT '0',
  `cs_percentage` int(11) NOT NULL DEFAULT '0',
  `cs_old_percentage` int(11) NOT NULL DEFAULT '0',
  `cs_lecture_log` text,
  `cs_auto_grade` char(3) DEFAULT '-',
  `cs_manual_grade` char(3) DEFAULT NULL,
  `cs_topic_progress` text,
  `cs_invalidate_topic` enum('0','1') NOT NULL DEFAULT '0',
  `cs_archived` int(11) NOT NULL,
  `cs_account_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_tutors` (
  `id` int(11) NOT NULL,
  `ct_course_id` int(11) NOT NULL,
  `ct_tutor_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_wishlist` (
  `id` int(11) NOT NULL,
  `cw_course_id` int(11) NOT NULL,
  `cw_user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;