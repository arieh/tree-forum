<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
try{
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');
	
	$login = new LoginM();
	$login->execute();
	echo $login->getKey();
	
	foreach ($login->getErrors() as $err) trigger_error($err);
}catch (Exception $e){
	trigger_error($e);
}
?>