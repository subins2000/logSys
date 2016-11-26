-- Host: localhost
-- Generation Time: May 14, 2014 at 08:26 PM
-- Server version: 5.5.35-1ubuntu1
-- PHP Version: 5.5.9-1ubuntu4
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`username` varchar(10) NOT NULL,
	`email` tinytext NOT NULL,
	`password` varchar(255) NOT NULL,
	`name` varchar(30) NOT NULL,
	`created` datetime NOT NULL,
	`attempt` varchar(15) NOT NULL DEFAULT '0'
);

--
-- Table structure for table `resetTokens`
--

CREATE TABLE IF NOT EXISTS `resetTokens` (
	`token` varchar(40) NOT NULL,
	`uid` INTEGER NOT NULL,
	`requested` varchar(20) NOT NULL
);

--
-- Table structure for table `user_devices`
--

CREATE TABLE IF NOT EXISTS `user_devices` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uid` INTEGER NOT NULL,
  `token` varchar(15) NOT NULL,
  `last_access` varchar(20) NOT NULL
);
