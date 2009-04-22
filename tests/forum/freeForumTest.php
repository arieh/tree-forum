<?php
TFUser::setId(4);
$options = array(
	'id'=>22,
	'action'=>'free',
	'permisions'=>TFUser::getInstance()->getPermissionIds(false),
	'debug'=>true,
);
$forum = new ForumM($options);
$forum->execute();