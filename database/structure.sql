/*
MySQL Data Transfer
Source Host: 192.168.2.104
Source Database: tree-forum
Target Host: 192.168.2.104
Target Database: tree-forum
Date: 02/04/2009 18:53:08
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '19');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '20');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '21');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '22');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '23');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '24');
INSERT INTO `message_contents` VALUES ('test message', 'this is a <strong>very important</strong> test', 'this is a very important test', '25');
INSERT INTO `messages` VALUES ('9', '9', '1', '9', '1');
INSERT INTO `messages` VALUES ('10', '10', '1', '10', '3');
INSERT INTO `messages` VALUES ('19', '9.19', '0', '9', '1');
INSERT INTO `messages` VALUES ('20', '9.19.20', '0', '9', '1');
INSERT INTO `messages` VALUES ('21', '9.21', '0', '9', '1');
INSERT INTO `messages` VALUES ('22', '9.22', '0', '9', '1');
INSERT INTO `messages` VALUES ('23', '9.19.20.23', '0', '9', '1');
INSERT INTO `messages` VALUES ('24', '9.19.24', '0', '9', '1');
INSERT INTO `messages` VALUES ('25', '9.19.20.23.25', '0', '9', '1');
