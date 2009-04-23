<?php
	TFUser::setId(1);
	TFUser::setDebug(true);
	
	$options= array(
		'id'=>22,
		'start'=>0,
		'limit'=>10,
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'debug'=>true
	);
	
	$forum= new ForumM($options);
	$forum->execute();
	$prevDepth =0;
	$t = '';
	foreach ($forum->getErrors() as $err) trigger_error($err);
