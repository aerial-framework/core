-- phpMyAdmin SQL Dump
-- version 3.2.2-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2010 at 02:40 PM
-- Server version: 5.1.38
-- PHP Version: 5.3.1


--
-- Database: `aerial_forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `createDate` datetime DEFAULT NULL,
  `modDate` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Category_User1` (`userId`)
) TYPE=InnoDB  AUTO_INCREMENT=8 ;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`id`, `userId`, `name`, `createDate`, `modDate`) VALUES
(3, 1, 'Birds', NULL, '2010-05-28 12:21:01'),
(4, 1, 'Bugs', NULL, '2010-05-28 11:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `postId` int(11) NOT NULL,
  `message` text,
  `createDate` datetime DEFAULT NULL,
  `modDate` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Comment_User` (`userId`),
  KEY `fk_Comment_Post1` (`postId`)
) TYPE=InnoDB AUTO_INCREMENT=1 ;

--
-- Dumping data for table `Comment`
--


-- --------------------------------------------------------

--
-- Table structure for table `Post`
--

CREATE TABLE IF NOT EXISTS `Post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `topicId` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `message` text,
  `createDate` datetime DEFAULT NULL,
  `modDate` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Post_User1` (`userId`),
  KEY `fk_Post_Topic1` (`topicId`)
) TYPE=InnoDB  AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Post`
--

INSERT INTO `Post` (`id`, `userId`, `topicId`, `title`, `message`, `createDate`, `modDate`) VALUES
(1, 1, 3, 'Gus', 'Is a bird.', NULL, '0000-00-00 00:00:00'),
(3, 1, 3, 'Tucan Sam', 'Is filthy rich.', NULL, '0000-00-00 00:00:00'),
(4, 1, 8, 'Use NaN', 'You can ...', NULL, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `Topic`
--

CREATE TABLE IF NOT EXISTS `Topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `description` text,
  `createDate` datetime DEFAULT NULL,
  `modDate` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `fk_Topic_Category1` (`categoryId`),
  KEY `fk_Topic_User1` (`userId`)
) TYPE=InnoDB  AUTO_INCREMENT=9 ;

--
-- Dumping data for table `Topic`
--

INSERT INTO `Topic` (`id`, `userId`, `categoryId`, `name`, `description`, `createDate`, `modDate`) VALUES
(3, 1, 3, 'Conure', 'Too loud.', NULL, '2010-05-28 12:22:02'),
(5, 1, NULL, 'Roketta', 'Top Speeds', NULL, '0000-00-00 00:00:00'),
(6, 1, 3, 'Duck', 'Tasty', NULL, '0000-00-00 00:00:00'),
(8, 1, 4, 'Nullable FK''s', 'How to handle.', NULL, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `createDate` datetime DEFAULT NULL,
  `modDate` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=InnoDB  AUTO_INCREMENT=4 ;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`id`, `username`, `password`, `createDate`, `modDate`) VALUES
(1, 'admin', 'admin', '2010-05-24 10:27:05', '2010-05-27 07:56:34'),
(2, NULL, NULL, NULL, '0000-00-00 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Category`
--
ALTER TABLE `Category`
  ADD CONSTRAINT `fk_Category_User1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Comment`
--
ALTER TABLE `Comment`
  ADD CONSTRAINT `fk_Comment_Post1` FOREIGN KEY (`postId`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Comment_User` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Post`
--
ALTER TABLE `Post`
  ADD CONSTRAINT `fk_Post_User1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Post_Topic1` FOREIGN KEY (`topicId`) REFERENCES `topic` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Topic`
--
ALTER TABLE `Topic`
  ADD CONSTRAINT `fk_Topic_Category1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_Topic_User1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
