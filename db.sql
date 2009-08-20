
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;




CREATE TABLE `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `html` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `public_forms` (
`id` int(11)
,`name` varchar(100)
,`user_name` varchar(100)
,`html` text
);


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` char(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pass` char(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_action` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `public_forms`;

CREATE ALGORITHM=UNDEFINED DEFINER=`form`@`%` SQL SECURITY DEFINER VIEW `public_forms` AS (select `f`.`id` AS `id`,`f`.`name` AS `name`,`u`.`name` AS `user_name`,`f`.`html` AS `html` from (`users` `u` join `forms` `f` on((`u`.`id` = `f`.`user_id`))) where (`f`.`public` = 1));
