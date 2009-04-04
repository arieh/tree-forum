<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
try{
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
		
	$options= array(
		'action'=>'add',
		'title'=>'a 3rd base message',
		'message'=>'<u>LOL. this is kinky</u>',
		'forum'=>1,
		'base'=>true,
		'permisions'=>array(1),
		'debug'=>true
	);
	$message = new MessageM($options);
	$message->execute();
	if ($message->isError()){
		$errs = $message->getErrors();
		foreach ($errs as $err) echo "$err<br>";
	}
}catch (Exception $e){
	echo $e->getMessage()." ON ".$e->getLine(). " IN ".$e->getFile();
}
?>
