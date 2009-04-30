
<form method='post' onsubmit='javascript:encrypt()' id='new-user' action="<?php echo $this->bPath;?>user/create">
<fieldset>
<dl>
	<dt>name</dt>
	<dd><input type='text' id='name' name='name' /></dd>
	<dt>password</dt>
	<dd><input type='password' id='password' name='password' /></dd>
	<dt>email</dt>
	<dd><input type='text' id='email' name='email' /></dd>
	<dt>Permission</dt>
	<dd>
		<select id='new-permission' name='new-permission'>
		<?php while ($per = $this->model->getAllowedPermission()):?>
			<option value="<?php echo $per->getId();?>"><?php echo $per->getName();?></option>
		<?php endwhile;?>
		</select>
	</dd>
	<dd id='submit-dd'>
		<input type='submit' value='submit' />
	</dd>
</dl>
</fieldset>
</form>