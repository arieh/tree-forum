/*
MySQL Data Transfer
Source Host: 192.168.2.104
Source Database: tree-forum
Target Host: 192.168.2.104
Target Database: tree-forum
Date: 01/04/2009 17:52:06
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for forums_permisions
-- ----------------------------
DROP TABLE IF EXISTS `forums_permisions`;
CREATE TABLE `forums_permisions` (
  `add` tinyint(1) NOT NULL default '0',
  `delete` tinyint(1) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '1',
  `edit` tinyint(1) NOT NULL default '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for message_contents
-- ----------------------------
DROP TABLE IF EXISTS `message_contents`;
CREATE TABLE `message_contents` (
  `title` varchar(255) default NULL,
  `message` text,
  `non-html` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `dna` varchar(255) collate utf8_bin default NULL,
  `base` tinyint(1) NOT NULL default '0',
  `root_id` int(11) default NULL,
  `forum_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permisions
-- ----------------------------
DROP TABLE IF EXISTS `permisions`;
CREATE TABLE `permisions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `forums` VALUES ('1', 'test forum', 'testing');
INSERT INTO `messages` VALUES ('1', '1', '1', '1', '1');
INSERT INTO `messages` VALUES ('2', '1.2', '0', '1', '1');
INSERT INTO `messages` VALUES ('3', '3', '1', '3', '1');
INSERT INTO `messages` VALUES ('4', '1.4', '0', '1', '1');
INSERT INTO `messages` VALUES ('5', '3.5', '0', '3', '1');
INSERT INTO `messages` VALUES ('6', '1.2.6', '0', '1', '1');
