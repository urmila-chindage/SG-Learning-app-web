

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `academic_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ay_year_code` varchar(55) NOT NULL,
  `ay_year_label` varchar(55) NOT NULL,
  `ay_active` int(11) NOT NULL,
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;



INSERT INTO `academic_year` (`id`, `ay_year_code`, `ay_year_label`, `ay_active`) VALUES
(1, '2018-2019', 'Academic year of 2018-2019', 1);



CREATE TABLE IF NOT EXISTS `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(255) NOT NULL,
  `banner_active` enum('1','0') NOT NULL,
  `banner_type` int(1) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_account_id` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gr_name` varchar(150) NOT NULL,
  `gr_range_from` int(11) NOT NULL DEFAULT '0',
  `gr_range_to` int(11) NOT NULL DEFAULT '0',
  `gr_account` int(11) NOT NULL DEFAULT '0',
  `gr_status` enum('0','1') NOT NULL DEFAULT '1',
  `gr_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `action` int(11) NOT NULL,
  `action_by` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,PRIMARY KEY (id)

) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;



INSERT INTO `grades` (`id`, `gr_name`, `gr_range_from`, `gr_range_to`, `gr_account`, `gr_status`, `gr_deleted`, `action`, `action_by`, `created`, `updated`) VALUES
(1, 'A+', 91, 100, 0, '0', '0', 2, 1, '2018-07-13 04:48:18', '2018-07-13 10:49:14'),
(2, 'A', 86, 90, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 10:13:36'),
(3, 'B+', 81, 85, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(4, 'B', 76, 80, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(5, 'C+', 71, 75, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(6, 'C', 66, 70, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(7, 'D+', 61, 65, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(8, 'D', 51, 60, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(9, 'E', 1, 50, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19'),
(10, '-', 0, 0, 0, '0', '0', 2, 1, '2018-07-12 23:18:18', '2018-07-13 01:52:19');



CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `module_link` varchar(255) NOT NULL,
  `is_module` tinyint(1) NOT NULL DEFAULT '0',
  `module_permissions` varchar(100) DEFAULT NULL,
  `icons` varchar(255) NOT NULL,
  `mod_status` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;


INSERT INTO `modules` (`id`, `module_name`, `controller`, `module_link`, `is_module`, `module_permissions`, `icons`, `mod_status`, `parent_id`, `sequence`, `account_id`) VALUES
(1, 'User Management', 'user', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(2, 'Institute Management', 'institutes', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(3, 'Course Management', 'course', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(4, 'Batch Management', 'batch', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(5, 'Course Content', 'course_content', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(6, 'Discussion Forum', 'course_forum', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(7, 'Student Enrollment', 'student_enrollment', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(8, 'Batch Enrollment', 'batch_enrollment', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(9, 'Event Management', 'event', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(10, 'Question Bank Management', 'question', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(11, 'Faculty Management', 'faculty', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(12, 'Announcement Management', 'announcement', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(13, 'Quiz Management', 'quiz_manager', '', 0, '1,2,3,4', '', 0, 3, 0, 0),
(14, 'Reports Management', 'report', '', 1, '1,3', '', 0, 0, 0, 0),
(15, 'Assign Faculty', 'assign_faculty', '', 0, '1,2,4', '', 0, 3, 0, 0),
(18, 'Course Backup', 'backups', '', 0, '1,2,4', '', 0, 3, 0, 0),
(19, 'Discount Coupon Management', 'promo_code', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(20, 'Order Management', 'orders', '', 0, '1', '', 0, 0, 0, 0),
(21, 'Bundle Management', 'bundle', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(22, 'Bundle Student Enrollment', 'bundle_student_enrollment', '', 0, '1,2,3,4', '', 0, 21, 0, 0),
(23, 'Page Management', 'page', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(24, 'Information Bar Management', 'notification', '', 0, '1,2,3,4', '', 0, 0, 0, 0),
(25, 'Review Management', 'review', '', 0, '1,3,4', '', 0, 3, 0, 0);



CREATE TABLE IF NOT EXISTS `roles_modules_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  `permissions` varchar(255) NOT NULL,
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1466 DEFAULT CHARSET=latin1;


INSERT INTO `roles_modules_meta` (`id`, `role_id`, `module_id`, `permissions`) VALUES
(1, 1, 1, '1,2,3,4'),
(4, 1, 2, '1,2,3,4'),
(7, 1, 3, '1,2,3,4'),
(10, 1, 4, '1,2,3,4'),
(13, 1, 5, '1,2,3,4'),
(16, 1, 6, '1,2,3,4'),
(19, 1, 7, '1,2,3,4'),
(22, 1, 8, '1,2,3,4'),
(25, 1, 9, '1,2,3,4'),
(28, 1, 10, '1,2,3,4'),
(31, 1, 11, '1,2,3,4'),
(34, 1, 12, '1,2,3,4'),
(37, 1, 13, '1,2,3,4'),
(40, 1, 14, '1,3,4'),
(43, 1, 15, '1,2,4'),
(46, 3, 1, '1,2,3,4'),
(49, 3, 2, '1,2,3,4'),
(52, 3, 3, '1,2,3,4'),
(55, 3, 4, '1,2,3,4'),
(58, 3, 5, '1,2,3,4'),
(61, 3, 6, '1,2,3,4'),
(64, 3, 7, '1,2,3,4'),
(67, 3, 8, '1,2,3,4'),
(70, 3, 9, '1,2,3,4'),
(73, 3, 10, '1,2,3,4'),
(76, 3, 11, '1,2,3,4'),
(79, 3, 12, '1,2,3,4'),
(82, 3, 13, '1,2,3,4'),
(85, 3, 14, '1,3,4'),
(88, 3, 15, '0'),
(91, 4, 1, ''),
(94, 4, 2, ''),
(97, 4, 3, '1,2,3,4'),
(100, 4, 4, ''),
(103, 4, 5, '1,2,3,4'),
(106, 4, 6, '1,2,3,4'),
(109, 4, 7, ''),
(112, 4, 8, ''),
(115, 4, 9, '1,2,3,4'),
(118, 4, 10, '1,2,3,4'),
(121, 4, 11, ''),
(124, 4, 12, '1,2,3,4'),
(127, 4, 13, '1,2,3,4'),
(130, 4, 14, ''),
(133, 4, 15, ''),
(136, 5, 1, '1,2,3,4'),
(139, 5, 2, '1,2,3,4'),
(142, 5, 3, '1,2,3,4'),
(145, 5, 4, '1,2,3,4'),
(148, 5, 5, '1,2,3,4'),
(151, 5, 6, '1,2,3,4'),
(154, 5, 7, '1,2,3,4'),
(157, 5, 8, '1,2,3,4'),
(160, 5, 9, '1,2,3,4'),
(163, 5, 10, '1,2,3,4'),
(166, 5, 11, '1,2,3,4'),
(169, 5, 12, '1,2,3,4'),
(172, 5, 13, '1,2,3,4'),
(175, 5, 14, '1,3,4'),
(178, 5, 15, '1,2,4'),
(181, 6, 1, '1,2,3,4'),
(184, 6, 2, '1,2,3,4'),
(187, 6, 3, '1,2,3,4'),
(190, 6, 4, '1,2,3,4'),
(193, 6, 5, '1,2,3,4'),
(196, 6, 6, '1,2,3,4'),
(199, 6, 7, '1,2,3,4'),
(202, 6, 8, '1,2,3,4'),
(205, 6, 9, '1,2,3,4'),
(208, 6, 10, '1,2,3,4'),
(211, 6, 11, '1,2,3,4'),
(214, 6, 12, '1,2,3,4'),
(217, 6, 13, '1,2,3,4'),
(220, 6, 14, '1,3,4'),
(223, 6, 15, '1,2,4'),
(226, 7, 1, '1,2,3,4'),
(229, 7, 2, '1,2,3,4'),
(232, 7, 3, '1,2,3,4'),
(235, 7, 4, '1,2,3,4'),
(238, 7, 5, '1,2,3,4'),
(241, 7, 6, '1,2,3,4'),
(244, 7, 7, '1,2,3,4'),
(247, 7, 8, '1,2,3,4'),
(250, 7, 9, '1,2,3,4'),
(253, 7, 10, '1,2,3,4'),
(256, 7, 11, '1,2,3,4'),
(259, 7, 12, '1,2,3,4'),
(262, 7, 13, '1,2,3,4'),
(265, 7, 14, '1,3,4'),
(268, 7, 15, '1,2,4'),
(271, 8, 1, '1,2,3,4'),
(274, 8, 2, ''),
(277, 8, 3, '1,2,3,4'),
(280, 8, 4, '1,2,3,4'),
(283, 8, 5, '1,2,3,4'),
(286, 8, 6, '1,2,3,4'),
(289, 8, 7, '1,2,3,4'),
(292, 8, 8, '1,2,3,4'),
(295, 8, 9, '1,2,3,4'),
(298, 8, 10, '1,2,3,4'),
(301, 8, 11, '1,2,3,4'),
(304, 8, 12, '1,2,3,4'),
(307, 8, 13, '1,2,3,4'),
(310, 8, 14, '1,3'),
(313, 8, 15, '1,2,4'),
(975, 1, 18, '1,2,3,4'),
(978, 3, 18, ''),
(981, 4, 18, ''),
(984, 5, 18, '1,2,3,4'),
(987, 6, 18, '1,2,3,4'),
(990, 7, 18, '1,2,3,4'),
(993, 8, 18, '1,2,4'),
(1151, 1, 19, '1,2,3,4'),
(1152, 3, 19, ''),
(1153, 4, 19, ''),
(1154, 5, 19, '1,2,3,4'),
(1155, 6, 19, '1,2,3,4'),
(1156, 7, 19, '1,2,3,4'),
(1157, 8, 19, '1,2,3,4'),
(1232, 8, 20, '1,2,3,4'),
(1233, 7, 20, '1'),
(1234, 6, 20, '1'),
(1235, 5, 20, '1'),
(1236, 4, 20, ''),
(1237, 3, 20, ''),
(1238, 1, 20, '1'),
(1240, 8, 21, '1,2,3,4'),
(1241, 7, 21, '1,2,3,4'),
(1242, 6, 21, '1,2,3,4'),
(1243, 5, 21, '1,2,3,4'),
(1244, 4, 21, ''),
(1245, 3, 21, ''),
(1246, 1, 21, '1,2,3,4'),
(1247, 1, 22, '1,2,3,4'),
(1248, 3, 22, '1,2,3,4'),
(1249, 4, 22, ''),
(1250, 5, 22, '1,2,3,4'),
(1251, 6, 22, '1,2,3,4'),
(1252, 7, 22, '1,2,3,4'),
(1253, 8, 22, '1,2,3,4'),
(1276, 8, 23, '1,2,3,4'),
(1277, 7, 23, '1,2,3,4'),
(1278, 6, 23, '1,2,3,4'),
(1279, 5, 23, '1,2,3,4'),
(1280, 4, 23, '1,2,3,4'),
(1281, 3, 23, ''),
(1282, 1, 23, '1,2,3,4'),
(1283, 1, 24, '1,2,3,4'),
(1284, 3, 24, ''),
(1285, 4, 24, ''),
(1286, 5, 24, '1,2,3,4'),
(1287, 6, 24, '1,2,3,4'),
(1288, 7, 24, '1,2,3,4'),
(1289, 8, 24, '1,2,3,4'),
(1338, 1, 25, '1,2,3,4'),
(1460, 3, 25, '1,2,3,4'),
(1461, 4, 25, '1,3,4'),
(1462, 5, 25, '1,2,3,4'),
(1463, 6, 25, '1,2,3,4'),
(1464, 7, 25, '1,2,3,4'),
(1465, 8, 25, '1,2,3,4');



CREATE TABLE IF NOT EXISTS `web_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wa_name` varchar(255) NOT NULL,
  `wa_code` varchar(255) NOT NULL,
  `wa_weight` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;


SET FOREIGN_KEY_CHECKS = 0;SET UNIQUE_CHECKS = 0;SET AUTOCOMMIT = 0;
INSERT INTO `web_actions` (`id`, `wa_name`, `wa_code`, `wa_weight`) VALUES
(1, 'Created', 'create', 0),
(2, 'Updated', 'update', 0),
(3, 'Activated', 'activate', 0),
(4, 'Deactivated', 'deactivate', 0),
(5, 'Deleted', 'delete', 0),
(6, 'Suspended', 'suspend', 0),
(7, 'Invited', 'invite', 0),
(8, 'Restored', 'restore', 0),
(9, 'Approval request', 'pending', 0);
SET FOREIGN_KEY_CHECKS = 1;SET UNIQUE_CHECKS = 1;SET AUTOCOMMIT = 1;