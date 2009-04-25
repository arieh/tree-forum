<?php
$prevDepth =0;
$t = '';

$messages = $this->model->getMessages(); //this uses the ModelResult and TFModel support for plurals/singulars
while ($msg = $messages->getMessage()):?>
<div style = 'margin-left:<?php echo ($msg->getDepth()*15);?>px'>
	<?php echo "ID:".$msg->getId()." :: ".$msg->getMessage()." : ".$msg->getTime()." <<>> User:".$msg->getUserName();?>
</div>
<?php endwhile;
