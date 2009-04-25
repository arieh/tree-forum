<div class='footer'>
<?php foreach ($this->js as $js):?>
	<script type='text/javascript' src='<?php echo $this->bPath;?>js/<?php echo $js;?>.js'></script>
<?php endforeach;?>
</div>
</body>
</html>