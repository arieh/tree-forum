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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
-- Table structure for user_pemissions
-- ----------------------------
DROP TABLE IF EXISTS `user_pemissions`;
CREATE TABLE `user_pemissions` (
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;