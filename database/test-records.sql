INSERT INTO `forums` VALUES ('1', 'test forum', 'testing');
INSERT INTO `forums` VALUES ('3', 'forum creation test', 'another test');
INSERT INTO `forums` VALUES ('14', 'a forum with a permision', 'random forum');
INSERT INTO `message_contents` VALUES ('base message', '<u>this a the base</u>', 'this a the base', '30');
INSERT INTO `message_contents` VALUES ('another base message', '<u>this another base</u>', 'this another base', '31');
INSERT INTO `message_contents` VALUES ('a 3rd base message', '<u>LOL. this is kinky</u>', 'LOL. this is kinky', '32');
INSERT INTO `message_contents` VALUES ('a sub message', 'a first sub message to 1st base message', 'a first sub message to 1st base message', '33');
INSERT INTO `message_contents` VALUES ('a 2nd sub message', 'a 2nd sub message to 2nd base message', 'a 2nd sub message to 2nd base message', '34');
INSERT INTO `message_contents` VALUES ('a 3rd sub message', 'a 3nd sub message to 3nd base message', 'a 3nd sub message to 3nd base message', '35');
INSERT INTO `message_contents` VALUES ('a sub-sub message', 'a sub-sub message to 3nd base message', 'a sub-sub message to 3nd base message', '36');
INSERT INTO `message_contents` VALUES ('another sub-sub message', 'another sub-sub message to 3nd base message', 'another sub-sub message to 3nd base message', '37');
INSERT INTO `messages` VALUES ('30', '30', '1', '30', '1', '2009-04-04 12:47:29', '2', '2009-04-04 12:50:50');
INSERT INTO `messages` VALUES ('31', '31', '1', '31', '1', '2009-04-04 12:47:59', '2', '2009-04-04 12:51:21');
INSERT INTO `messages` VALUES ('32', '32', '1', '32', '1', '2009-04-04 12:48:17', '2', '2009-04-04 12:50:13');
INSERT INTO `messages` VALUES ('33', '30.33', '0', '30', '1', '2009-04-04 12:49:44', '2', '2009-04-04 12:49:44');
INSERT INTO `messages` VALUES ('34', '31.34', '0', '31', '1', '2009-04-04 12:50:02', '2', '2009-04-04 12:50:02');
INSERT INTO `messages` VALUES ('35', '32.35', '0', '32', '1', '2009-04-04 12:50:13', '2', '2009-04-04 12:50:13');
INSERT INTO `messages` VALUES ('36', '30.33.36', '0', '30', '1', '2009-04-04 12:50:50', '2', '2009-04-04 12:50:50');
INSERT INTO `messages` VALUES ('37', '31.34.37', '0', '31', '1', '2009-04-04 12:51:21', '2', '2009-04-04 12:51:21');
INSERT INTO `users` VALUES ('1', 'guest', '', '', '');
INSERT INTO `users` VALUES ('2', 'arieh', 'a@b.co', '12345', '');
INSERT INTO `users` VALUES ('4', 'new-user', 'arieh.glazer@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '321a54a0763a6b0011349d0b1f8540c9e875a18c');
INSERT INTO `users_permisions` VALUES ('1', '8');
INSERT INTO `users_permisions` VALUES ('2', '1');
INSERT INTO `users_permisions` VALUES ('4', '6');
