<?php if (isset($this->model)):?>
<ol>
<?php
foreach ($this->model->getErrors() as $error):?>
	<li><?php echo $error ?></li>
<?php endforeach;
?>
</ol>
<?php endif;?>