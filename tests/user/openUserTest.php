<?php
	
	NewDao::connect('mysql','localhost','root','pass','treeforum');
	NewDao::setLogger('fb');	
	TFUser::setId(2);
	
	$options = array(
		'action'=>'open',
		'permisions'=>TFUser::getInstance()->getPermissionIds(),
		'name'=>'arieh',
		'debug'=>true
	);
	
	$user = new UserM($options);
	$user->execute();
	$errors = $user->getErrors();
	foreach($errors as $err) trigger_error($err);
	
	if ($errors) exit(0);
	echo $user->getName();
	while ($msg = $user->getMessageId()) echo "<br/>{$msg->getId()}";
	
	