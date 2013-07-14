-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 03, 2013 at 10:35 PM
-- Server version: 5.1.41-community-log
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

-- Dumping data for table `comments`
INSERT INTO `comments` (`comment_id`, `for`, `parent_id`, `comment_text`, `comment_added_by`, `comment_added_time`, `comment_type`) VALUES
(1, 0, 1, 'aoeu', 1, '2011-10-22 10:51:50', 'comment');

-- Dumping data for table `houses`
INSERT INTO `houses` (`house_id`, `house_name`, `house_created_by`, `house_currency`, `house_joined`) VALUES
(1, 'The Demo House', 1, 'GBP', '2011-10-22 10:43:18'),
(2, 'My Other Place', 1, 'GBP', '2011-12-24 04:06:54');

-- Dumping data for table `link_houses_users`
INSERT INTO `link_houses_users` (`link_id`, `house_id`, `user_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(5, 2, 1),
(6, 2, 2),
(7, 2, 4),
(8, 2, 5);

-- Dumping data for table `link_purchases_users`
INSERT INTO `link_purchases_users` (`link_id`, `purchase_id`, `user_id`, `price`) VALUES
(1, 1, 1, 10),
(2, 1, 2, 10),
(3, 1, 3, 10);

-- Dumping data for table `purchases`
INSERT INTO `purchases` (`purchase_id`, `description`, `added_by`, `added_time`, `status`, `payer`, `house_id`, `date`, `deleted_time`, `deleted_by`, `split_type`, `edit_parent`, `edit_child`) VALUES
(1, 'Rent Repayment', 1, '2011-10-22 10:51:50', 'ok', 1, 1, '2011-10-22', NULL, NULL, 'even', NULL, NULL),
(2, 'Winter Gas Bill', 2, '2011-10-22 10:51:50', 'ok', 1, 1, '2011-10-22', NULL, NULL, 'even', NULL, NULL),
(3, 'Electricity Bill', 3, '2011-10-22 10:51:50', 'ok', 1, 1, '2011-10-22', NULL, NULL, 'even', NULL, NULL);

-- Dumping data for table `users`
INSERT INTO `users` (`user_id`, `user_id_facebook`, `house_id`, `user_name`, `user_name_first`, `user_name_last`, `user_name_facebook`, `user_email`, `user_email_facebook`, `user_mobile`, `user_mobile_confirm`, `conf_n_purchase_dispute`, `conf_n_purchase_comment`, `conf_n_purchase_add`, `conf_theme`, `conf_purchases_per_page`, `conf_purchases_order`, `conf_purchases_order_by`, `conf_seensettings`, `conf_landingpage`) VALUES
(1, 100004986727805, 1, 'John Y', 'John', 'Yangwitz', '', NULL, NULL, NULL, '0', NULL, NULL, NULL, 'default', 25, 'asc', 'date', 0, 'home'),
(2, 100004978868645, 1, 'Maria S', 'Maria', 'Seligsteinescu', '', NULL, NULL, NULL, '0', NULL, NULL, NULL, 'default', 25, 'asc', 'date', 0, 'home'),
(3, 100006270039617, 1, 'Elizabeth', 'Elizabeth', 'Wongberg', '', NULL, NULL, NULL, '0', NULL, NULL, NULL, 'default', 25, 'asc', 'date', 0, 'home'),
(4, 100004973678573, 2, 'Susan C', 'Susan', 'Changstein', '', NULL, NULL, NULL, '0', NULL, NULL, NULL, 'default', 25, 'asc', 'date', 0, 'home'),
(5, 100006286741993, 2, 'Mike M', 'Mike', 'Martinazzisky', '', NULL, NULL, NULL, '0', NULL, NULL, NULL, 'default', 25, 'asc', 'date', 0, 'home');
/* users:
John Amdihfgbghje Yangwitz  100004986727805 jvxafyd_yangwitz_1357381291@tfbnw.net 
Maria Amdighhfhfde Seligsteinescu 100004978868645 xioxbhl_seligsteinescu_1357382119@tfbnw.net 
Elizabeth Amfbgkcifag Wongberg  100006270039617 ynsjlhf_wongberg_1372891885@tfbnw.net 
Susan Amdigcfghegc Changstein 100004973678573 ecvnhmd_changstein_1357381299@tfbnw.net 
Mike Amfbhfgdaiic Martinazzisky 100006286741993 ogjyjuy_martinazzisky_1372891875@tfbnw.net  
*/

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
