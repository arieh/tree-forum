<?php
	TFUser::setId(1);	
	$options= array(
		'action'=>'edit',
		'message'=>'an eddited message',
		'id'=>9,
		'permisions'=>array(1),
		'debug'=>true,
		'user'=>TFUser::getId()
	);
	$message = new MessageM($options);
	$message->execute();
	if ($message->isError()){
		$errs = $message->getErrors();
		foreach ($errs as $err) trigger_error($err);
	}
?>
