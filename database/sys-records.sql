INSERT INTO `permissions` VALUES ('1', 'admin', '100');
INSERT INTO `permissions` VALUES ('6', 'editor', '60');
INSERT INTO `permissions` VALUES ('7', 'user', '30');
INSERT INTO `permissions` VALUES ('8', 'guest', '0');

INSERT INTO `forum_actions` VALUES ('1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `forum_actions` VALUES ('6', '0', '1', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('7', '0', '1', '1', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_actions` VALUES ('8', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0');

INSERT INTO `user_actions` VALUES ('1', '1', '1');
INSERT INTO `user_actions` VALUES ('6', '1', '1');
INSERT INTO `user_actions` VALUES ('7', '1', '0');
INSERT INTO `user_actions` VALUES ('8', '0', '0');