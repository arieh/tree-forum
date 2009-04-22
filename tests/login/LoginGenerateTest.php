<?php
	$login = new LoginM();
	$login->execute();
	echo $login->getKey();
	
	foreach ($login->getErrors() as $err) trigger_error($err);
?>
