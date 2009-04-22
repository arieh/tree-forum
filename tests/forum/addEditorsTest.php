<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('..'._SEP_.'autoloader.php');
require_once('..'._SEP_.'..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('..'._SEP_.'errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
	
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