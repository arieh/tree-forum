<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
try{
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
		
	$options= array(
		'action'=>'create',
		'name'=>'random forum',
		'description'=>'a forum with a permision',
		'permisions'=>array(1),
		'forum-permisions'=>array(
			array(			
				'permision_id'=>4,
				'open'=>1,
				'add'=>1,
				'view'=>1
			),
			array(
				'permision_id'=>5,
				'open'=>1
			)
		),
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
}catch (Exception $e){
	trigger_error($e);
}
?>
