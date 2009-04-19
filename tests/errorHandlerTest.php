<?php
define("_SEP_",DIRECTORY_SEPARATOR);
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
function a(){
	b();
}

function b(){
	c();
}

function c(){
	throw new Exception('a');
}

a();
?>
