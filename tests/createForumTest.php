<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','rjntqvzz','treeforum');
	NewDao::setLogger('fb');	
	
	TFUser::setId(2);
	TFUser::setDebug(true);
	
	$options= array(
		'action'=>'create',
		'name'=>'random forum',
		'description'=>'a forum with a permision',
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'admins'=>array(4),
		'editors'=>array(5),
		'users'=>array(6),
		'restrict'=>true,
		'debug'=>true
	);
	
	$forum= new ForumM($options);
	$forum->execute();
	if (!$forum->isError())
		echo $forum->getId();
	else{
		$errs = $forum->getErrors();
		foreach ($errs as $err) echo "$err<br>";
	}
?>
