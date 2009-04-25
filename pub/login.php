<?php
ob_start();
define ("_SEP_", DIRECTORY_SEPARATOR);
define ("_DEBUG_",true);
require_once 'autoloader.php';
require_once 'errorHandler.php';
require_once ".." . _SEP_ . "classes" . _SEP_ . "library" . _SEP_ .'firePHP' . _SEP_ . "fb.php";

NewDao::connect('mysql',".." . _SEP_ . "configs" . _SEP_ . "db.ini");
NewDao::setLogger(array('FB','log'));
session_start();

$login = new UserM(array('action'=>'generate'));
$login->execute();
?>
<!doctype html>
<html>
	<head>
		<title>Login Page</title>
	</head>
	<body>
		 use username:arieh/ pass:12345
		 <form id='login-form' action="user/login" method='post'>
		 <fieldset>
		 <dl>
		 	<dt>user-name:</dt>
		 	<dd><input type='text' name='user-name' id='userName'/></dd>
		 	<dt>password</dt>
		 	<dd><input type='password' name='pass' id='pass' /></dd>
		 	<dd>
		 		<input type='submit' value='login'/>
		 		<input type='hidden' id='tempKey' value = "<?php echo $login->getKey();?>"/>
		 		<input type='hidden' id='hash' name='hash' value='' />
		 	</dd>
		 </dl>
		 </fieldset>
		 </form>
		
		<script type='text/javascript' src='js/sha1.js'></script>
		<script type='text/javascript' src='js/loginForm.js'></script>
		<script type='text/javascript' src='js/mootools-1.2.1-core.js'></script>
		<script type='text/javascript'>
		<!--//
		$('login-form').addEvent('submit',function(){setEncryption()});
		//-->
		</script>  
	</body>
</html>