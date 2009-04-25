<a href='<?php echo $this->bPath;?>message/new/forum/<?php echo $this->model->getId();?>'>add a message</a>
<?php
while ($msg = $this->model->getMessage()):?>
<div class='msg-container' style = 'margin-left:<?php echo ($msg->getDepth()*30);?>px'>
	<h3><a href='<?php echo $this->bPath;?>message/view/<?php echo $msg->getId();?>'>
		<?php echo $msg->getTitle();?></a></h3>
		<ul class='message'>
			<li>User: <?php echo $msg->getUserName();?></li>
			<li>On <?php echo $msg->getTime();?></li>
			<li class='body'><?php echo $msg->getMessage();?></li>
			<li>
				<a href='<?php echo $this->bPath;?>message/new/forum/<?php echo $this->model->getId();?>/parent/<?php echo $msg->getId();?>'>
					post a response</a>
			</li>
		</ul>
</div>
<?php endwhile;?>
<a href='<?php echo $this->bPath;?>message/new/forum/<?php echo $this->model->getId();?>'>add a message</a>