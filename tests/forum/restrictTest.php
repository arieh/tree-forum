<?php
TFUser::setId(4);
$options = array(
	'id'=>22,
	'action'=>'restrict',
	'permisions'=>TFUser::getInstance()->getPermissionIds(false),
	'debug'=>true,
	'close'=>true
);
$forum = new ForumM($options);
$forum->execute();