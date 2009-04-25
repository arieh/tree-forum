<?php

class MessageC extends MainController{
	protected $_models = array('MessageM');
    
    protected $_model_name = 'MessageM';
    
    protected $_template_dir = 'message';
    
    protected $_default_tpl_folder = 'open';
    
    protected $_tpl_folders = array('view','add','edit','move','remove');
    
    protected $_envs = array('xhtml');
    
    protected $_def_env = 'xhtml';
    
    protected function setOptions(){
    	if (isset($this->_vars[0])){
			switch(is_numeric($this->_vars[0])){
				case (true):
					$this->setOption('id',$this->_vars[0]);
				break;
				case (false):
					$this->setOption('name',$this->_vars[0]);
				break;
			}
		}
		
		parent::setOptions();
    }
}

class MessageCException extends MainControllerException{}