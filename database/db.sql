/*
MySQL Data Transfer
Source Host: 192.168.1.101
Source Database: tree-forum
Target Host: 192.168.1.101
Target Database: tree-forum
Date: 19/05/2009 16:21:00
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for forum_actions
-- ----------------------------
DROP TABLE IF EXISTS `forum_actions`;
CREATE TABLE `forum_actions` (
  `permission_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL,
  `open` tinyint(1) NOT NULL default '0',
  `add` tinyint(1) NOT NULL default '0',
  `delete` tinyint(1) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `edit` tinyint(1) NOT NULL default '0',
  `create` tinyint(1) NOT NULL default '0',
  `remove` tinyint(1) NOT NULL default '0',
  `move` tinyint(1) NOT NULL default '0',
  `restrict` tinyint(1) NOT NULL default '0',
  `free` tinyint(1) NOT NULL default '0',
  `add-editors` tinyint(1) NOT NULL default '0',
  `add-users` tinyint(1) NOT NULL default '0',
  `add-admins` tinyint(1) NOT NULL default '0',
  KEY `permision_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  `url-name` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `message_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

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
  `dna` varchar(255) collate utf8_bin NOT NULL default '',
  `base` tinyint(1) NOT NULL default '0',
  `root_id` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL,
  `posted` timestamp NULL default NULL,
  `user_id` int(11) default '0',
  `last_update` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  `name` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`,`dna`),
  UNIQUE KEY `id` (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `root_id` (`root_id`),
  KEY `dna` (`dna`),
  CONSTRAINT `forum_id` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `root_id` FOREIGN KEY (`root_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `level` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for temp-keys
-- ----------------------------
DROP TABLE IF EXISTS `temp-keys`;
CREATE TABLE `temp-keys` (
  `ip` varchar(255) collate utf8_bin NOT NULL default '',
  `key` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`ip`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for user_actions
-- ----------------------------
DROP TABLE IF EXISTS `user_actions`;
CREATE TABLE `user_actions` (
  `permission_id` int(10) unsigned NOT NULL default '0',
  `open` tinyint(1) default '0',
  `create` tinyint(1) default '0',
  PRIMARY KEY  (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  KEY `user` (`user_id`),
  KEY `permision` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) default NULL,
  `password` varchar(255) NOT NULL,
  `uid` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `uid` (`uid`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `forum_actions` VALUES ('1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `forum_actions` VALUES ('6', '0', '1', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('7', '0', '1', '1', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('8', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('32', '22', '1', '0', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('31', '22', '1', '0', '0', '1', '0', '1', '0', '1', '0', '0', '0', '1', '0');
INSERT INTO `forum_actions` VALUES ('30', '22', '1', '0', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0');
INSERT INTO `forum_actions` VALUES ('35', '23', '1', '0', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('34', '23', '1', '0', '0', '1', '0', '1', '0', '1', '0', '0', '0', '1', '0');
INSERT INTO `forum_actions` VALUES ('33', '23', '1', '0', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0');
INSERT INTO `forums` VALUES ('22', 'a forum with a permision', 'random forum', 'random-forum');
INSERT INTO `forums` VALUES ('23', 'a forum with a permission set', 'another random forum', 'another-random-forum');
INSERT INTO `message_contents` VALUES ('base message', '<u>this a the base</u>', 'this a the base', '30');
INSERT INTO `message_contents` VALUES ('another base message', '<u>this another base</u>', 'this another base', '31');
INSERT INTO `message_contents` VALUES ('a 1st base message', '<u>LOL. this is kinky</u>', 'LOL. this is kinky', '32');
INSERT INTO `message_contents` VALUES ('a sub message', 'a first sub message to 1st base message', 'a first sub message to 1st base message', '33');
INSERT INTO `message_contents` VALUES ('a 2nd sub message', 'a 2nd sub message to 2nd base message', 'a 2nd sub message to 2nd base message', '34');
INSERT INTO `message_contents` VALUES ('a 3rd sub message', 'a 3rd sub message to 1st base message', 'a 3nd sub message to 3nd base message', '35');
INSERT INTO `message_contents` VALUES ('a sub-sub message', 'a sub-sub message to 3nd base message', 'a sub-sub message to 3nd base message', '36');
INSERT INTO `message_contents` VALUES ('another sub-sub message', 'another sub-sub message to 3nd base message', 'another sub-sub message to 3nd base message', '37');
INSERT INTO `message_contents` VALUES ('a 1st base message', '<u>LOL. this is kinky</u>', 'LOL. this is kinky', '38');
INSERT INTO `message_contents` VALUES ('a 3rd base message', '<u>LOL. this is kinky</u>', 'LOL. this is kinky', '39');
INSERT INTO `message_contents` VALUES ('a sub message', 'a sub message to 1nd base message', 'a sub message to 1nd base message', '40');
INSERT INTO `message_contents` VALUES ('a sub-sub message', 'a sub-sub message to 1nd base message', 'a sub-sub message to 1nd base message', '41');
INSERT INTO `message_contents` VALUES ('a 2nd sub message', 'a sub message to 2nd base message', 'a sub message to 2nd base message', '42');
INSERT INTO `message_contents` VALUES ('test', 'test', 'test', '47');
INSERT INTO `message_contents` VALUES ('this might be interesting', 'lets see how this goes', 'lets see how this goes', '48');
INSERT INTO `message_contents` VALUES ('this should work', 'i hope', 'i hope', '49');
INSERT INTO `message_contents` VALUES ('a new base message', 'i\'ll be happy if this works properly', 'i\'ll be happy if this works properly', '50');
INSERT INTO `message_contents` VALUES ('yey!', 'it worked!', 'it worked!', '51');
INSERT INTO `message_contents` VALUES ('cong.', 'pretty forum =]', 'pretty forum =]', '52');
INSERT INTO `messages` VALUES ('38', '38', '1', '38', '22', '2009-04-22 14:07:00', '2', '2009-04-26 15:47:50', null);
INSERT INTO `messages` VALUES ('39', '39', '1', '39', '22', '2009-04-22 14:07:23', '2', '2009-04-26 00:15:20', null);
INSERT INTO `messages` VALUES ('40', '38.40', '0', '38', '22', '2009-04-22 14:10:23', '4', '2009-05-01 13:12:12', 'a-sub-message');
INSERT INTO `messages` VALUES ('41', '38.40.41', '0', '38', '22', '2009-04-22 14:10:52', '5', '2009-04-22 14:27:10', null);
INSERT INTO `messages` VALUES ('42', '39.42', '0', '39', '22', '2009-04-22 14:11:15', '6', '2009-04-22 14:27:21', null);
INSERT INTO `messages` VALUES ('47', '38.40.41.47', '0', '38', '22', '2009-04-26 00:04:42', '2', '2009-04-26 00:04:42', null);
INSERT INTO `messages` VALUES ('48', '38.40.48', '0', '38', '22', '2009-04-26 00:11:17', '2', '2009-04-26 00:11:17', null);
INSERT INTO `messages` VALUES ('49', '39.49', '0', '39', '22', '2009-04-26 00:15:20', '2', '2009-04-26 00:15:20', null);
INSERT INTO `messages` VALUES ('50', '50', '1', '50', '22', '2009-04-26 00:20:48', '2', '2009-04-26 00:21:01', null);
INSERT INTO `messages` VALUES ('51', '50.51', '0', '50', '22', '2009-04-26 00:21:01', '2', '2009-04-26 00:21:01', null);
INSERT INTO `messages` VALUES ('52', '38.40.48.52', '0', '38', '22', '2009-04-26 15:47:50', '2', '2009-04-26 15:47:50', null);
INSERT INTO `permissions` VALUES ('1', 'admin', '100');
INSERT INTO `permissions` VALUES ('6', 'editor', '60');
INSERT INTO `permissions` VALUES ('7', 'user', '30');
INSERT INTO `permissions` VALUES ('8', 'guest', '0');
INSERT INTO `permissions` VALUES ('30', 'random forum-admin', '0');
INSERT INTO `permissions` VALUES ('31', 'random forum-editor', '0');
INSERT INTO `permissions` VALUES ('32', 'random forum-user', '0');
INSERT INTO `permissions` VALUES ('33', 'another random forum-admin', '0');
INSERT INTO `permissions` VALUES ('34', 'another random forum-editor', '0');
INSERT INTO `permissions` VALUES ('35', 'another random forum-user', '0');
INSERT INTO `temp-keys` VALUES ('174.112.85.248', '1d732b8a2180973008cce4263a02f4ab8d8a2201');
INSERT INTO `temp-keys` VALUES ('192.117.110.42', '1d97f23283a9d458d4a7f2fd48dc4bac2e86a8ee');
INSERT INTO `temp-keys` VALUES ('192.168.1.1', '5af2dd16a5d8f0ca40675c545bb31cbc68ae7397');
INSERT INTO `temp-keys` VALUES ('192.168.1.100', 'f08e2e999aa383c7e22a6a02e15c9440e18eed33');
INSERT INTO `temp-keys` VALUES ('212.143.71.233', '69f6e879eb4aee29b3fcc91d550fb9ab8f5c6bab');
INSERT INTO `temp-keys` VALUES ('212.179.4.146', '059a5b7c696793778c8b2c01da185442551d6253');
INSERT INTO `temp-keys` VALUES ('212.199.123.178', 'ad836ab2bf46515c99ba18db4beaee12d6f23429');
INSERT INTO `temp-keys` VALUES ('212.199.142.10', 'ebec0c15348aaa01a9fe0a97ab40415857da3c81');
INSERT INTO `temp-keys` VALUES ('212.235.68.51', '28958b03d4bcf64daf27d0de65eea5abe1dce47c');
INSERT INTO `temp-keys` VALUES ('213.13.230.1', 'e06f41cce015ae2e4254ce260fb5d4d31c55fb7e');
INSERT INTO `temp-keys` VALUES ('62.90.151.75', '0ffaee191f09c5fa301373811bcb3743b7902e65');
INSERT INTO `temp-keys` VALUES ('77.125.37.172', '7d72f647ead19897236a21e54349aa5d36e7c450');
INSERT INTO `temp-keys` VALUES ('79.177.55.229', 'a02c08b819bebc3b45dadcfffd87a98386b8cb6f');
INSERT INTO `temp-keys` VALUES ('79.178.20.93', '39344832ed31d41000f793609396a7d930c992f7');
INSERT INTO `temp-keys` VALUES ('82.80.140.16', 'dcf7eada990263da0b537ef367683c0ba621304f');
INSERT INTO `temp-keys` VALUES ('84.108.69.245', '77a3b774d66660bc48de7d0cd8d9c618cbd53d50');
INSERT INTO `temp-keys` VALUES ('84.109.170.179', '74bcf243db74697a9023aa8f7085aa20dbd3e2e0');
INSERT INTO `temp-keys` VALUES ('84.109.57.4', '4a8c70a47a344003d34d0a849aa83a3cf0ac7cfa');
INSERT INTO `temp-keys` VALUES ('85.243.95.142', '951cbd98f3379e2dc980e7a2fa30830227263243');
INSERT INTO `temp-keys` VALUES ('87.68.57.209', '674b6ebfa0a185b3858fb429cc6c11eb28526592');
INSERT INTO `temp-keys` VALUES ('89.138.50.72', '29d99cc39a723c4e588d6858ee843f865a34d422');
INSERT INTO `temp-keys` VALUES ('89.138.80.75', 'da4260bd8edbcbeb6fba6147c7d295b97531879c');
INSERT INTO `temp-keys` VALUES ('93.173.89.200', '97e6b9220c11ceb5c08cd76a6c953decdb3f04ab');
INSERT INTO `temp-keys` VALUES ('95.35.125.87', '8564b689a9b68848b141637f22a8d47100937a30');
INSERT INTO `temp-keys` VALUES ('98.233.135.25', 'b439ec6f43b40ed62c789f3e881cd47419e96d11');
INSERT INTO `user_actions` VALUES ('1', '1', '1');
INSERT INTO `user_actions` VALUES ('6', '1', '0');
INSERT INTO `user_actions` VALUES ('7', '1', '0');
INSERT INTO `user_actions` VALUES ('8', '0', '0');
INSERT INTO `user_permissions` VALUES ('1', '8');
INSERT INTO `user_permissions` VALUES ('2', '1');
INSERT INTO `user_permissions` VALUES ('4', '6');
INSERT INTO `user_permissions` VALUES ('5', '7');
INSERT INTO `user_permissions` VALUES ('6', '7');
INSERT INTO `user_permissions` VALUES ('6', '32');
INSERT INTO `user_permissions` VALUES ('5', '31');
INSERT INTO `user_permissions` VALUES ('4', '30');
INSERT INTO `user_permissions` VALUES ('4', '33');
INSERT INTO `user_permissions` VALUES ('5', '34');
INSERT INTO `user_permissions` VALUES ('6', '35');
INSERT INTO `user_permissions` VALUES ('2', '8');
INSERT INTO `user_permissions` VALUES ('10', '6');
INSERT INTO `user_permissions` VALUES ('2', '30');
INSERT INTO `users` VALUES ('1', 'guest', '', '', '');
INSERT INTO `users` VALUES ('2', 'arieh', 'a@b.co', 'b4475ce83c030510a45bdf6b34b32061393b217c', '');
INSERT INTO `users` VALUES ('4', 'new-user', 'arieh.glazer@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '321a54a0763a6b0011349d0b1f8540c9e875a18c');
INSERT INTO `users` VALUES ('5', '2nd-user', 'bla@bla.com', '1234', '12345');
INSERT INTO `users` VALUES ('6', '3rd-user', 'bla@bla.com', '1234', '12345');
INSERT INTO `users` VALUES ('10', 'itay', 'itay@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '586f7cc5fb62ceb8285d31e58ef28550c51c6733');
