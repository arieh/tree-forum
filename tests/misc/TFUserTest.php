<?php
	
	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');
	TFUser::setDebug(true);
	TFUser::setId(1);
	print_r(TFUser::getInstance()->getPermissionIds(false));	
	echo TFUser::getInstance()->getName();
	echo TFUser::getInstance()->getEmail();
?>
