<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');
	UserM::setDebug(true);
	UserM::setId(1);
	print_r(UserM::getInstance()->getPermissionIds(false));	
	echo UserM::getInstance()->getName();
	echo UserM::getInstance()->getEmail();
?>
