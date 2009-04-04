<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
try{
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
		
	$options= array(
		'action'=>'move',
		'new-parent'=>26,//switch between 19 to 26 to see affects
		'id'=>20,
		'permisions'=>array(1),
		'debug'=>true
	);
	$message = new MessageM($options);
	$message->execute();
	if ($message->isError()){
		$errs = $message->getErrors();
		foreach ($errs as $err) trigger_error($err);
	}
}catch (Exception $e){
	trigger_error($e->getMessage()." ON ".$e->getLine(). " IN ".$e->getFile());
}
?>