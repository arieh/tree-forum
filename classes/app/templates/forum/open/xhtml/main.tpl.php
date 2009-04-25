<?php
$prevDepth =0;
$t = '';

while ($msg = $this->model->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*30);?>px'>
	<a href='<?php echo $this->bPath;?>message/view/<?php echo $msg->getId();?>'>
		<?php echo $msg->getTitle();?></a>
		<ul class='message-bdy'>
			<li>User: <?php echo $msg->getUserName();?></li>
			<li>On <?php echo $msg->getTime();?></li>
			<li><?php echo $msg->getMessage();?></li>
			<li>
				<a href='<?php echo $this->bPath;?>message/new/forum/<?php echo $this->model->getId();?>/parent/<?php echo $msg->getId();?>'>
					post a response</a>
			</li>
		</ul>
</div>
<?php endwhile;