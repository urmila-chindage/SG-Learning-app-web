SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `actions_token` (
  `id` int(11) NOT NULL,
  `at_token` text,
  `at_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'token status',
  `at_purpose` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=>Profile Approval',
  `at_params` text COMMENT 'Params to perform action.(JSON)',
  `at_expire` datetime DEFAULT NULL COMMENT 'expiration datetime',
  `at_account_id` int(11) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'token generated on.',
  `updated` datetime DEFAULT NULL COMMENT 'token used on.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `branch` (
  `id` int(11) NOT NULL,
  `branch_code` varchar(10) DEFAULT NULL,
  `branch_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;