<?php
	TFUser::setId(4);
	TFUser::setDebug(true);
	
	$options = array(
		'id'=>22,
		'action'=>'add-editors',
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'users'=>array(7),
		'debug'=>true
	);
	
	$forum = new ForumM($options);
	$forum->execute();
	
	$errors = $forum->getErrors();
	
	foreach ($errors as $err) echo($err);