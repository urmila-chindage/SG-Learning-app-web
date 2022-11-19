SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `course_discussion_report` (
  `id` int(11) NOT NULL,
  `rt_parent_id` int(11) NOT NULL,
  `rt_child_id` int(11) NOT NULL,
  `rt_reason` text NOT NULL,
  `rt_user_id` int(11) NOT NULL,
  `rt_course_id` int(11) NOT NULL,
  `rt_status` int(11) NOT NULL,
  `inserted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_language` (
  `id` int(11) NOT NULL,
  `cl_lang_name` varchar(520) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `course_lectures` (
  `id` int(11) NOT NULL,
  `cl_lecture_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_lecture_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cl_lecture_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cl_filename` varchar(255) DEFAULT NULL,
  `cl_org_file_name` varchar(255) DEFAULT NULL,
  `cl_total_page` int(11) NOT NULL DEFAULT '0',
  `cl_course_id` int(11) NOT NULL,
  `cl_section_id` int(11) NOT NULL DEFAULT '0',
  `cl_order_no` int(11) NOT NULL DEFAULT '0',
  `cl_lecture_type` int(11) NOT NULL DEFAULT '1',
  `cl_downloadable` enum('1','0') NOT NULL DEFAULT '0',
  `cl_limited_access` int(11) NOT NULL DEFAULT '0',
  `cl_conversion_status` enum('1','2','3','4','5') NOT NULL DEFAULT '1' COMMENT '1 => Upload complete, 2 => Conversion  Started, 3 => Conversion Completed, 4 => S3 Upload Completed, 5 => Conversion Error',
  `cl_width` int(11) NOT NULL DEFAULT '100',
  `cl_height` int(11) NOT NULL DEFAULT '100',
  `cl_duration` int(11) NOT NULL DEFAULT '0',
  `lecture_image` varchar(100) DEFAULT NULL,
  `cl_status` enum('1','0') NOT NULL DEFAULT '0',
  `cl_access_restriction` text,
  `cl_support_files` text,
  `cl_deleted` enum('1','0') NOT NULL DEFAULT '0',
  `cl_sent_mail_on_lecture_creation` enum('1','0') NOT NULL DEFAULT '0',
  `cl_account_id` int(11) NOT NULL DEFAULT '0',
  `cl_lecture_preview` enum('0','1','') DEFAULT NULL,
  `action_id` int(11) NOT NULL DEFAULT '1',
  `action_by` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
<<<<<<< HEAD:onboarding/database/import_step_7_3.sql
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
=======
  `updated_date` datetime DEFAULT '2016-01-01 00:00:00',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `course_language` (`id`, `cl_lang_name`, `created_date`, `updated_date`) VALUES
(1, 'Assamese', '2016-12-20 09:03:24', NULL),
(2, 'Bengali', '2016-12-20 09:03:24', NULL),
(3, 'Bodo', '2016-12-20 09:03:56', NULL),
(4, 'Dogri', '2016-12-20 09:03:56', NULL),
(5, 'Gujarati', '2016-12-20 09:04:16', NULL),
(6, 'Hindi', '2016-12-20 09:04:16', NULL),
(7, 'Kannada', '2016-12-20 09:04:41', NULL),
(8, 'Kashmiri', '2016-12-20 09:04:41', NULL),
(9, 'Konkani', '2016-12-20 09:05:01', NULL),
(10, 'Maithili', '2016-12-20 09:05:01', NULL),
(11, 'Malayalam', '2016-12-20 09:05:25', NULL),
(12, 'Manipuri', '2016-12-20 09:05:25', NULL),
(13, 'Marathi', '2016-12-20 09:05:44', NULL),
(14, 'Nepali', '2016-12-20 09:05:44', NULL),
(15, 'Odia', '2016-12-20 09:06:09', NULL),
(16, 'Punjabi', '2016-12-20 09:06:09', NULL),
(17, 'Sanskrit', '2016-12-20 09:06:33', NULL),
(18, 'Santali', '2016-12-20 09:06:33', NULL),
(19, 'Sindhi', '2016-12-20 09:06:51', NULL),
(20, 'Tamil', '2016-12-20 09:06:51', NULL),
(21, 'Telugu', '2016-12-20 09:07:06', NULL),
(22, 'Urdu', '2016-12-20 09:07:06', NULL),
(23, 'English', '2016-12-20 09:07:37', NULL);
>>>>>>> star_enfinlabs:onboarding/database/import_9.sql
