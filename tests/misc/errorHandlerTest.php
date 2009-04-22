<?php
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
