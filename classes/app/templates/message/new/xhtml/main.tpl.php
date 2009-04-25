<?php
if (isset($this->model) && !$this->model->isError()):?>
<?php
if ($this->model->getBase()==false):
	$parent = $this->model->getParentMessage();?>
<fieldset id='original message'>
<legend>Original</legend>
<h3><?php echo $parent->getTitle();?></h3>
<?php echo $parent->getMessage();?>
</fieldset>
<?php endif;?>
<form name='new-message' id='new-message' action='<?php echo $this->bPath;?>message/add' method='post'>
<fieldset>
<dl>
	<dt><label for='title'>Title:</label></dt>
	<dd><input type='text' name='title' id='title'/></dd>
	
	<dt><label for='message'>Message:</label></dt>
	<dd><textarea name='message' id='message' cols='60' rows='30'></textarea></dd>
	<dd>
		<input type='submit' value='submit'/>
		<?php if ($this->model->getBase()):?>
			<input type='hidden' name='base' value='1' />
		<?php else:?>
			<input type='hidden' name='parent' value="<?php echo $this->model->getParent();?>" />
			<input type='hidden' name='base' value='0' />
		<?php endif;?>
		<input type='hidden' name='forum' value='<?php echo $this->model->getForumId();?>' />
	</dd>
</dl>
</fieldset>
</form>

<?php endif;