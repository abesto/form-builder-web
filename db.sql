-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 07, 2009 at 07:03 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `form`
--

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `html` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `public_forms`
--
CREATE TABLE IF NOT EXISTS `public_forms` (
`id` int(11)
,`name` varchar(100)
,`user_name` varchar(100)
,`html` text
);
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` char(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pass` char(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_action` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure for view `public_forms`
--
DROP TABLE IF EXISTS `public_forms`;

CREATE ALGORITHM=UNDEFINED DEFINER=`form`@`%` SQL SECURITY DEFINER VIEW `public_forms` AS (select `f`.`id` AS `id`,`f`.`name` AS `name`,`u`.`name` AS `user_name`,`f`.`html` AS `html` from (`users` `u` join `forms` `f` on((`u`.`id` = `f`.`user_id`))) where (`f`.`public` = 1));
