<?php
	
	TFUser::setId(2);
	
	
	$options = array(
		'action'=>'create',
		'permisions'=>TFUser::getInstance()->getPermissionIds(),
		'name'=>'new-user',
		'email'=>'arieh.glazer@gmail.com',
		'password'=>'1234',
		'encrypt'=>false,
		'new-permissions'=>array(6),
		'debug'=>true
	);
	
	$user = new UserM($options);
	$user->execute();
	$errors = $user->getErrors();
	foreach ($errors as $err) trigger_error($err);
	
	echo $user->getId();