SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `course_consolidated_report` (
  `id` int(11) NOT NULL,
  `ccr_course_id` int(11) NOT NULL DEFAULT '0',
  `ccr_institute_id` int(11) NOT NULL DEFAULT '0',
  `ccr_total_enrolled` int(11) NOT NULL DEFAULT '0',
  `ccr_total_completed` int(11) NOT NULL DEFAULT '0',
  `ccr_academic_year_id` int(11) NOT NULL,
  `ccr_academic_year_code` varchar(55) NOT NULL,
  `ccr_account_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `course_discussions` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_title` text NOT NULL,
  `comment` text NOT NULL,
  `parent_id` int(11) NOT NULL,
  `comment_deleted` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;