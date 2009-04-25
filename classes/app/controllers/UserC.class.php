<?php

class UserC extends MainController{
    protected $_model_name = 'UserM';
    
    protected $_template_dir = 'user';
    
    protected $_default_tpl_folder = 'open';
    
    protected $_tpl_folders = array('open','login','logout','create');
    
    protected $_envs = array('xhtml');
    
    protected $_def_env = 'xhtml';
}
?>