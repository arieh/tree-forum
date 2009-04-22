<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
NewDao::connect('mysql','localhost','root','rjntqvzz','tree-forum');
NewDao::setLogger('fb');

$file = $_GET['file'];
if (strlen($file)>1 && file_exists($file)) include $file;
else echo "file not found";
