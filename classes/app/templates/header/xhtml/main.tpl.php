<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>
		<?php
		$sep = '';
		foreach ($this->title as $title){
			 echo $sep.$title;
			 $sep = '>';
		} 
		?>
	</title>
	<?php foreach ($this->css as $css): ?>
		<link type='text/css' rel='stylesheet' href='<?php echo $this->bPath;?>css/<?php echo $css;?>.css' />
	<?php endforeach;?>
</head>
<body>