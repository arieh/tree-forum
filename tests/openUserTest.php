<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pas','treeforum');
	NewDao::setLogger('fb');	
	TFUser::setId(2);
	
	$options = array(
		'action'=>'open',
		'permisions'=>TFUser::getInstance()->getPermissionIds(),
		'id'=>2,
		'debug'=>true
	);
	
	$user = new UserM($options);
	$user->execute();
	$errors = $user->getErrors();
	foreach($errors as $err) trigger_error($err);
	
	if ($errors) exit(0);
	echo $user->getName();
	while ($msg = $user->getMessageId()) echo "<br/>{$msg->getId()}";
	
	