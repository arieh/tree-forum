<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');

	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
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
