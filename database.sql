-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 19, 2013 at 04:05 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stspci`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for` tinyint(1) NOT NULL COMMENT '0 = purchase, 1 = item',
  `parent_id` int(10) unsigned NOT NULL,
  `comment_text` varchar(250) NOT NULL,
  `comment_added_by` bigint(20) NOT NULL,
  `comment_added_time` datetime NOT NULL,
  `comment_type` enum('comment','dispute') NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE IF NOT EXISTS `houses` (
  `house_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `house_name` varchar(100) NOT NULL COMMENT 'length is 100. max allowed without escape chars is 50.',
  `house_created_by` int(10) unsigned NOT NULL COMMENT 'user that created the house',
  `house_currency` char(3) NOT NULL DEFAULT 'GBP',
  `house_joined` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`house_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_description` varchar(50) NOT NULL,
  `item_house_id` int(11) NOT NULL,
  `user_owing` bigint(20) NOT NULL,
  `user_owed` bigint(20) NOT NULL,
  `item_active` int(11) NOT NULL DEFAULT '1' COMMENT 'active=1, deleted=0',
  `item_added_by` bigint(20) NOT NULL,
  `item_added_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `item_date` date NOT NULL DEFAULT '0000-00-00',
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `link_houses_users`
--

CREATE TABLE IF NOT EXISTS `link_houses_users` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `house_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `link_notifications_users`
--

CREATE TABLE IF NOT EXISTS `link_notifications_users` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `link_purchases_users`
--

CREATE TABLE IF NOT EXISTS `link_purchases_users` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `price` float NOT NULL COMMENT 'How much the user is contributing to the purchase',
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `log_page` varchar(20) NOT NULL,
  `log_user` bigint(20) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Active?',
  `time` datetime NOT NULL COMMENT 'Time of Notification',
  `text` varchar(250) NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_statuses`
--

CREATE TABLE IF NOT EXISTS `notification_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Set to 0 when user clicks "X"',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `notification_id` (`notification_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Keeps track of which users have seen which notifications' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE IF NOT EXISTS `purchases` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `payer` bigint(20) NOT NULL,
  `house_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) DEFAULT NULL,
  `split_type` enum('even','custom') NOT NULL COMMENT 'Split Type used when adding purchase - "Even Split" or "Custom Split"?',
  `edit_parent` int(10) unsigned DEFAULT NULL COMMENT 'Purchase from which this edit derives',
  `edit_generation` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0=current version,1=older,2=even older',
  PRIMARY KEY (`purchase_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `stsp_sessions`
--

CREATE TABLE IF NOT EXISTS `stsp_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `user_id_facebook` bigint(20) unsigned DEFAULT NULL COMMENT 'Facebook ID',
  `house_id` int(10) unsigned NOT NULL COMMENT 'House ID',
  `user_name` varchar(25) NOT NULL COMMENT 'Selected Name',
  `user_name_first` varchar(15) NOT NULL,
  `user_name_last` varchar(15) NOT NULL,
  `user_name_facebook` varchar(25) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_email_facebook` varchar(100) DEFAULT NULL,
  `user_mobile` varchar(20) DEFAULT NULL COMMENT 'Mobile Number, eg 441234567890',
  `user_mobile_confirm` varchar(4) NOT NULL DEFAULT '0' COMMENT '0=unconfirmed,1=confirmed,####=codetoconfirm',
  `conf_n_purchase_dispute` set('email','mobile','web') DEFAULT NULL COMMENT 'Notification required when purchase is disputed',
  `conf_n_purchase_comment` set('email','mobile','web') DEFAULT NULL COMMENT 'Notification required when purchase is commented on',
  `conf_n_purchase_add` set('email','mobile','web') DEFAULT NULL COMMENT 'Notification required when purchase is added',
  `conf_theme` enum('default') NOT NULL DEFAULT 'default',
  `conf_purchases_per_page` int(10) unsigned NOT NULL DEFAULT '25',
  `conf_purchases_order` enum('asc','desc') NOT NULL DEFAULT 'asc',
  `conf_purchases_order_by` enum('date','added_time') NOT NULL DEFAULT 'date',
  `conf_seensettings` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'set to 1 when settings updated, 0 until then',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id_facebook` (`user_id_facebook`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='User Details' AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
