<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');
	
NewDao::connect('mysql','localhost','root','pass','tree-forum');
NewDao::setLogger('fb');

$file = $_GET['file'];
if (strlen($file)>1 && file_exists($file)):
?>
<title><?php echo $file;?> :: TF Loader</title></head><body><?php 
 include $file;
else: ?>
<title>Not Found</title></head><body><strong>File Not Found</strong>
<?php
endif;?>
</body></html><?php
ob_flush();