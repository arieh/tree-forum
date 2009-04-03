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
		'title'=>'test message',
		'message'=>'this is a <strong>very important</strong> test',
		'forum'=>1,
		'base'=>false,
		'parent'=>9,
		'permisions'=>array(1)
	);
	$message = new MessageM($options);
	$message->execute();
	foreach ($message->getErrors() as $err) echo "$err<br>";
}catch (Exception $e){
	echo $e->getMessage()." ON ".$e->getLine(). " IN ".$e->getFile();
}
?>
