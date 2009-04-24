<?php
echo 'Success!';
echo TFUser::getInstance()->getName()." is Logged in!";
?>
<hr/>
<a href = '<?php echo $this->bPath;?>forum/open/22'>go to forum page</a>