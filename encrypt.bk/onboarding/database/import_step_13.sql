SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



CREATE TABLE IF NOT EXISTS `menu_manager` (
  `id` int(100) NOT NULL,
  `mm_parent_id` int(200) NOT NULL DEFAULT '0',
  `mm_name` varchar(200) NOT NULL,
  `mm_item_type` varchar(15) NOT NULL,
  `mm_item_connected_id` int(100) NOT NULL,
  `mm_item_connected_slug` varchar(100) DEFAULT NULL,
  `mm_external_url` varchar(200) DEFAULT NULL,
  `mm_sort_order` int(100) NOT NULL DEFAULT '0',
  `mm_connected_as_external` int(5) DEFAULT NULL,
  `mm_new_window` int(5) DEFAULT NULL,
  `mm_show_in` int(5) NOT NULL DEFAULT '1',
  `mm_status` enum('','1','0') NOT NULL DEFAULT '1',
  `mm_account_id` int(11) NOT NULL,
  `mm_created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mobile_banners` (
  `id` int(11) NOT NULL,
  `mb_title` text NOT NULL,
  `mb_type` int(11) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;