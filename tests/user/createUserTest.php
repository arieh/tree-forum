<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pass','treeforum');
	NewDao::setLogger('fb');	
	
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