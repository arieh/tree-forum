<?php

	TFUser::setId(7);
	TFUser::setDebug(true);
	
	$options = array(
		'id'=>22,
		'action'=>'add-users',
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'users'=>array(6),
		'debug'=>true
	);
	
	$forum = new ForumM($options);
	$forum->execute();
	
	$errors = $forum->getErrors();
	
	foreach ($errors as $err) echo($err);