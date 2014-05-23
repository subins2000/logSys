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
  `id` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `email` tinytext NOT NULL,
  `password` varchar(515) NOT NULL,
  `password_salt` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);
ALTER TABLE `users`
 MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;