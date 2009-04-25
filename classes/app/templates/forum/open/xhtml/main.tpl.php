<?php
$prevDepth =0;
$t = '';

while ($msg = $this->model->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<a href='<?php echo $this->bPath;?>message/view/<?php echo $msg->getId();?>'>
		<?php echo $msg->getTitle();?></a>
		<ul class='message-bdy'>
			<li>User: <?php echo $msg->getUserName();?></li>
			<li>On <?php echo $msg->getTime();?></li>
			<li><?php echo $msg->getMessage();?></li>
		</ul>
</div>
<?php endwhile;