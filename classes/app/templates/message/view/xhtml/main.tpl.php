<div id='message'>
<h3><?php echo $this->model->getTitle();?></h3>
<?php echo $this->model->getContent();?>
</div>
<?php
$messages = $this->model->getMessages(); //this uses the ModelResult and TFModel support for plurals/singulars
while ($msg = $messages->getMessage()):?>
<div class='msg-container' style = 'margin-left:<?php echo ($msg->getDepth()*30);?>px'>
	<h3><a href='<?php echo $this->bPath;?>message/view/<?php echo $msg->getId();?>'>
		<?php echo $msg->getTitle();?></a></h3>
		<ul class='message'>
			<li>User: <?php echo $msg->getUserName();?></li>
			<li>On <?php echo $msg->getTime();?></li>
			<li class='body'><?php echo $msg->getMessage();?></li>
			<li>
				<a href='<?php echo $this->bPath;?>message/new/forum/<?php echo $this->model->getForumId();?>/parent/<?php echo $msg->getId();?>'>
					post a response</a>
			</li>
		</ul>
</div>
<?php endwhile;?>
<a href='<?php echo $this->bPath;?>forum/open/<?php echo $this->model->getForumId();?>'>Back To Forum</a>
