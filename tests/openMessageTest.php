<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('errorHandler.php');

	NewDao::connect('mysql','localhost','root','pass','tree-forum');
	NewDao::setLogger('fb');	
	TFUser::setDebug(false);
	TFUser::setId(2);	
	$options= array(
		'id'=>30,
		'action'=>'open',
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'debug'=>true
	);
	$msgs= new MessageM($options);
	$msgs->execute();
	$prevDepth =0;
	$t = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>bla</title></head><body>
<?php
$messages = $msgs->getMessages(); //this uses the ModelResult and TFModel support for plurals/singulars
while ($msg = $messages->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<?php echo "ID:".$msg->getId()." :: ".$msg->getMessage()." : ".$msg->getTime()." <<>> User:".$msg->getUserName();?>
</div>
<?php endwhile;
?>

</body>

</html>