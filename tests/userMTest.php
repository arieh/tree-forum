<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');
	TFUser::setDebug(true);
	TFUser::setId(1);
	print_r(TFUser::getInstance()->getPermissionIds(false));	
	echo TFUser::getInstance()->getName();
	echo TFUser::getInstance()->getEmail();
?>
