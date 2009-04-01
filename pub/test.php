<?php
define('_SEP_',DIRECTORY_SEPARATOR);
require_once('autoloader.php');
require_once('..'._SEP_.'classes'._SEP_.'library'._SEP_.'firePHP'._SEP_.'fb.php');
try{
	NewDao::connect('mysql','localhost','root','rjntqvzz','treeForum');
	NewDao::setLogger('fb');	
		
	$options= array('id'=>1,'start'=>0,'limit'=>10);
	$forum= new ForumM($options);
	$forum->execute();
	$prevDepth =0;
	$t = '';
}catch (Exception $e){
	echo $e->getMessage()." ON ".$e->getLine(). " IN ".$e->getFile();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>bla</title></head><body>
<?php
while ($msg = $forum->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<?php echo $msg->getId();?>
</div>
<?php endwhile;
?>

</body>

</html>