<?php
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
