<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
	UserM::setId(1);	
	$options= array(
		'action'=>'edit',
		'message'=>'an eddited message',
		'id'=>9,
		'permisions'=>array(1),
		'debug'=>true,
		'user'=>UserM::getId()
	);
	$message = new MessageM($options);
	$message->execute();
	if ($message->isError()){
		$errs = $message->getErrors();
		foreach ($errs as $err) trigger_error($err);
	}
?>
