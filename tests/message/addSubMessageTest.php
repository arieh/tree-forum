<?php
	
	TFUser::setId(6);
	TFUser::setDebug(true);
		
	$options= array(
		'action'=>'add',
		'title'=>'a 2nd sub message',
		'message'=>'a sub message to 2nd base message',
		'forum'=>22,
		'base'=>false,
		'parent'=>39,
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'debug'=>true,
		'user'=>TFUser::getId()
	);
	$message = new MessageM($options);
	$message->execute();
	foreach ($message->getErrors() as $err) echo "$err<br>";
?>
