<?php
$messages = $this->model->getMessageIds();
?>
<h2>User Page</h2>
<ul>
	<li>name: <?php echo $this->model->getName();?></li>
	<li>email: <?php echo $this->model->getEmail();?></li>
	<li>
		message-ids: 
		<?php
		$sep =''; 
		foreach ($messages as $msg):
			echo $sep.$msg->getId();
			$sep=',';	
		endforeach;
		?>
	</li>
</ul>