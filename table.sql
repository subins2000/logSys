-- Host: localhost
-- Generation Time: May 14, 2014 at 08:26 PM
-- Server version: 5.5.35-1ubuntu1
-- PHP Version: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(10) NOT NULL,
	`email` tinytext NOT NULL,
	`password` varchar(515) NOT NULL,
	`password_salt` varchar(20) NOT NULL,
	`name` varchar(30) NOT NULL,
	`created` datetime NOT NULL,
	`attempt` varchar(15) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Table structure for table `resetTokens`
--

CREATE TABLE IF NOT EXISTS `resetTokens` (
	`token` varchar(40) NOT NULL COMMENT 'The Unique Token Generated',
	`uid` int(11) NOT NULL COMMENT 'The User Id',
	`requested` varchar(20) NOT NULL COMMENT 'The Date when token was created'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;