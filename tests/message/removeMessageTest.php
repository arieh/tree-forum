<?php
		
	$options= array(
		'action'=>'remove',
		'id'=>21,
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
