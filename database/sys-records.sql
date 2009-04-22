INSERT INTO `permisions` VALUES ('1', 'admin');
INSERT INTO `permisions` VALUES ('6', 'editor');
INSERT INTO `permisions` VALUES ('7', 'user');
INSERT INTO `permisions` VALUES ('8', 'guest');

INSERT INTO `forum_permisions` VALUES ('1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `forum_permisions` VALUES ('6', '0', '1', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '0', '0');
INSERT INTO `forum_permisions` VALUES ('7', '0', '1', '1', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `forum_permisions` VALUES ('8', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0');

INSERT INTO `user_permissions` VALUES ('1', '1', '1');
INSERT INTO `user_permissions` VALUES ('6', '1', '1');
INSERT INTO `user_permissions` VALUES ('7', '1', '0');
INSERT INTO `user_permissions` VALUES ('8', '0', '0');