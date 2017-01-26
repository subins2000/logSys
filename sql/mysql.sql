SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(10) NOT NULL,
	`email` tinytext NOT NULL,
	`password` varchar(255) NOT NULL,
	`name` varchar(30) NOT NULL,
	`created` datetime NOT NULL,
	`attempt` varchar(15) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Table structure for table `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
	`token` varchar(40) NOT NULL COMMENT 'The generated unique token',
	`uid` int(11) NOT NULL COMMENT 'The User ID',
	`requested` varchar(20) NOT NULL COMMENT 'The date when token was created'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_devices`
--

CREATE TABLE IF NOT EXISTS `user_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'The user''s ID',
  `token` varchar(15) NOT NULL COMMENT 'A unique token for the user''s device',
  `last_access` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
