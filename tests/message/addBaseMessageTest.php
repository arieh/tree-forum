<?php
	
	TFUser::setId(2);
	TFUser::setDebug(true);
		
	$options= array(
		'action'=>'add',
		'title'=>'a 3rd base message',
		'message'=>'<u>LOL. this is kinky</u>',
		'forum'=>22,
		'base'=>true,
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'debug'=>true,
		'user'=>TFUser::getId()
	);
	$message = new MessageM($options);
	$message->execute();
	if ($message->isError()){
		$errs = $message->getErrors();
		foreach ($errs as $err) echo "$err<br>";
	}
?>
