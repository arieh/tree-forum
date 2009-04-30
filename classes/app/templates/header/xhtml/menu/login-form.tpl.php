
<form id='login-form' action="<?php echo $this->bPath;?>user/login" method='post' onsubmit='javascript:setEncryption()'>
<fieldset>
 <ul>
 	<li><label for='userName'>user-name <em>(new-user)</em></label>: <input type='text' name='user-name' id='userName'/></li>
 	<li><label for='pass'>password <em>(1234)</em></label>: <input type='password' name='pass' id='pass' /></li>
 	<li>
 		<input type='submit' value='login'/>
 		<input type='hidden' id='tempKey' value = "<?php echo $this->key;?>"/>
 		<input type='hidden' id='hash' name='hash' value='' />
 	</li>
 </ul>
</fieldset>
</form>