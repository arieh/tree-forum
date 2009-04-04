/*
MySQL Data Transfer
Source Host: 192.168.2.104
Source Database: tree-forum
Target Host: 192.168.2.104
Target Database: tree-forum
Date: 04/04/2009 11:48:32
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for forum_permisions
-- ----------------------------
DROP TABLE IF EXISTS `forum_permisions`;
CREATE TABLE `forum_permisions` (
  `permision_id` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL,
  `open` tinyint(1) NOT NULL default '0',
  `add` tinyint(1) NOT NULL default '0',
  `delete` tinyint(1) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `edit` tinyint(1) NOT NULL default '0',
  `create` tinyint(1) NOT NULL default '0',
  `remove` tinyint(1) NOT NULL default '0',
  `move` tinyint(1) NOT NULL default '0',
  KEY `permision_id` (`permision_id`),
  CONSTRAINT `forum_permisions_ibfk_3` FOREIGN KEY (`permision_id`) REFERENCES `permisions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `message_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for message_contents
-- ----------------------------
DROP TABLE IF EXISTS `message_contents`;
CREATE TABLE `message_contents` (
  `title` varchar(255) default NULL,
  `message` text,
  `non-html` text,
  `message_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `dna` varchar(255) collate utf8_bin default NULL,
  `base` tinyint(1) NOT NULL default '0',
  `root_id` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `root_id` (`root_id`),
  CONSTRAINT `root_id` FOREIGN KEY (`root_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `forum_id` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permisions
-- ----------------------------
DROP TABLE IF EXISTS `permisions`;
CREATE TABLE `permisions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
