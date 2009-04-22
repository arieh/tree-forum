<?php
		
	$options= array(
		'action'=>'move',
		'new-parent'=>30,//switch between 30 to 31 to see affects
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
?>
