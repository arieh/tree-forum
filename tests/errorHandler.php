<?php
function TF_Error_Handler($errno,$str,$file,$line){
	$error = "ERROR: "
			.$str." \n"
			."On Line: $line "
			."On File: $file "
			."\n";
	
	if (function_exists('fb')){
		fb($error);
	}	
}

set_error_handler("TF_Error_Handler");