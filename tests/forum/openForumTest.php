<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('..'._SEP_.'autoloader.php');
require_once('..'._SEP_.'..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
require_once('..'._SEP_.'errorHandler.php');
	
	NewDao::connect('mysql','localhost','root','rjntqvzz','tree-forum');
	NewDao::setLogger('fb');	
	
	TFUser::setId(6);
	TFUser::setDebug(true);
	
	$options= array(
		'id'=>22,
		'start'=>0,
		'limit'=>10,
		'permisions'=>TFUser::getInstance()->getPermissionIds(false),
		'debug'=>true
	);
	
	$forum= new ForumM($options);
	$forum->execute();
	$prevDepth =0;
	$t = '';
	foreach ($forum->getErrors() as $err) trigger_error($err);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>bla</title></head><body>
<?php
while ($msg = $forum->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<?php echo "ID:".$msg->getId()." :: ".$msg->getMessage()." : ".$msg->getTime();?> By User: <em><?php echo $msg->getUserName();?></em>
</div>
<?php endwhile;
?>

</body>

</html>