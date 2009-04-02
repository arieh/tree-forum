<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
try{
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
		
	$options= array('action'=>'add','name'=>'another test','description'=>'forum creation test');
	$forum= new ForumM($options);
	$forum->execute();
	if (!$forum->isError())
		echo $forum->getId();
	else{
		$errs = $forum->getErrors();
		foreach ($errs as $err) echo "$err<br>";
	}
}catch (Exception $e){
	echo $e->getMessage()." ON ".$e->getLine(). " IN ".$e->getFile();
}
?>
