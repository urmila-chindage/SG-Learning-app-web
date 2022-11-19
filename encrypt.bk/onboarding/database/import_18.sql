SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(100) NOT NULL,
  `t_other_detail` varchar(100) NOT NULL COMMENT 'location,designation, role status',
  `t_image` varchar(255) NOT NULL,
  `t_text` text NOT NULL,
  `t_featured` enum('1','0') NOT NULL DEFAULT '0',
  `t_status` enum('1','0') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_account_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `transition_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tc_content` text,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tutor_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tc_tutor_id` int(11) NOT NULL DEFAULT '0',
  `tc_categories` text,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assesment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uga_title` varchar(255) NOT NULL,
  `uga_category` int(11) NOT NULL,
  `uga_number_of_questions` int(11) NOT NULL,
  `uga_user_id` int(11) NOT NULL,
  `uga_duration` int(11) NOT NULL DEFAULT '1' COMMENT 'in minutes',
  `uga_show_categories` enum('0','1') NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uga_status` int(4) NOT NULL,PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_generated_assesment_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uga_assesment_id` int(11) NOT NULL,
  `uga_question_id` int(11) NOT NULL COMMENT 'in minutes',PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

