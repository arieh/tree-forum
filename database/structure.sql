/*
MySQL Data Transfer
Source Host: 192.168.2.104
Source Database: tree-forum
Target Host: 192.168.2.104
Target Database: tree-forum
Date: 02/04/2009 14:17:28
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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
  `root_id` int(11) default NULL,
  `forum_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
INSERT INTO `forums` VALUES ('3', 'forum creation test', 'another test');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '9');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '10');
INSERT INTO `messages` VALUES ('1', '1', '1', '1', '1');
INSERT INTO `messages` VALUES ('2', '1.2', '0', '1', '1');
INSERT INTO `messages` VALUES ('3', '3', '1', '3', '1');
INSERT INTO `messages` VALUES ('4', '1.4', '0', '1', '1');
INSERT INTO `messages` VALUES ('5', '3.5', '0', '3', '1');
INSERT INTO `messages` VALUES ('6', '1.2.6', '0', '1', '1');
INSERT INTO `messages` VALUES ('7', '7', '1', '7', '1');
INSERT INTO `messages` VALUES ('8', '8', '1', '8', '1');
INSERT INTO `messages` VALUES ('9', '9', '1', '9', '1');
INSERT INTO `messages` VALUES ('10', '10', '1', '10', '3');
INSERT INTO `messages` VALUES ('12', '3.5.12', '0', '3', '1');
