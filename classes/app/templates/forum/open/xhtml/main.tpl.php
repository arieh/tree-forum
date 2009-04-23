<?php
$prevDepth =0;
	$t = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>bla</title></head><body>
<?php
while ($msg = $this->model->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<a href='<?php echo $this->bPath;?>message/view/<?php echo $msg->getId();?>'>
		<?php echo $msg->getTitle();?></a>
		<ul class='message-bdy'>
			<li>User: <?php echo $msg->getUserName();?></li>
			<li>On <?php echo $msg->getTime();?></li>
			<li><?php echo $msg->getMessage();?></li>
</div>
<?php endwhile;
?>

</body>

</html>