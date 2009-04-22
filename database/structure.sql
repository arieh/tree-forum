/*
MySQL Data Transfer
Source Host: localhost
Source Database: treeforum
Target Host: localhost
Target Database: treeforum
Date: 22/04/2009 14:29:02
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for forum_permisions
-- ----------------------------
DROP TABLE IF EXISTS `forum_permisions`;
CREATE TABLE `forum_permisions` (
  `permision_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL,
  `open` tinyint(1) NOT NULL DEFAULT '0',
  `add` tinyint(1) NOT NULL DEFAULT '0',
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `create` tinyint(1) NOT NULL DEFAULT '0',
  `remove` tinyint(1) NOT NULL DEFAULT '0',
  `move` tinyint(1) NOT NULL DEFAULT '0',
  `restrict` tinyint(1) NOT NULL DEFAULT '0',
  `free` tinyint(1) NOT NULL DEFAULT '0',
  `add-editors` tinyint(1) NOT NULL DEFAULT '0',
  `add-users` tinyint(1) NOT NULL DEFAULT '0',
  `add-admins` tinyint(1) NOT NULL DEFAULT '0',
  KEY `permision_id` (`permision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for message_contents
-- ----------------------------
DROP TABLE IF EXISTS `message_contents`;
CREATE TABLE `message_contents` (
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `non-html` text,
  `message_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dna` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `base` tinyint(1) NOT NULL DEFAULT '0',
  `root_id` int(10) unsigned DEFAULT NULL,
  `forum_id` int(10) unsigned NOT NULL,
  `posted` timestamp NULL DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`dna`),
  UNIQUE KEY `id` (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `root_id` (`root_id`),
  KEY `dna` (`dna`),
  CONSTRAINT `forum_id` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `root_id` FOREIGN KEY (`root_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permisions
-- ----------------------------
DROP TABLE IF EXISTS `permisions`;
CREATE TABLE `permisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for temp-keys
-- ----------------------------
DROP TABLE IF EXISTS `temp-keys`;
CREATE TABLE `temp-keys` (
  `ip` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `key` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `permission_id` int(10) unsigned NOT NULL DEFAULT '0',
  `open` tinyint(1) DEFAULT '0',
  `create` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `uid` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `uid` (`uid`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_permisions
-- ----------------------------
DROP TABLE IF EXISTS `users_permisions`;
CREATE TABLE `users_permisions` (
  `user_id` int(11) NOT NULL,
  `permision_id` int(11) NOT NULL,
  KEY `user` (`user_id`),
  KEY `permision` (`permision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;