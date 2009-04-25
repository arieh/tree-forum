<?php
if (isset($this->model) && !$this->model->isError()):?>
post successful. <a href='<?php echo $this->bPath;?>forum/open/<?php echo $this->model->getForumId();?>'>go back</a>
<?php endif;