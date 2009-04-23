<?php
	$forum = $_GET['forum'];
	if (!$forum) die();
	$message = (isset($_GET['message']) && $_GET['message']) ? $_GET['message'] : false;
?>
<!doctype html>
<html>
	<head>
		<title>New Message</title>
	</head>
	<body>
		<form metho='post' action='forum/add'>
		<fieldset>
		<dl>
			<dt>Title</dt>
			<dd><input type='text' name='title'/></dd>
			<dt>Message</td>
			<dd><textarea name='message' rows='30' cols='60'></textarea></dd>
			<dd>
				<input type='submit' value='submit'/>
				<input type='hidden' name='forum' value='<?php echo $forum;?>'/>
				<?php if ($message):?>
					<input type='hidden' name='parent' value='<?php echo $message;?>' />
				<?php else:?>
					<input type='hidden' name='base' value='1'/>
				<?php endif;?>
			</dd>
		</dl>
		</fieldset>
		</form>
	</body>
</html>