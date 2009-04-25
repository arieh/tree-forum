<?php

class MessageC extends MainController{
	protected $_models = array('MessageM');
    
    protected $_model_name = 'MessageM';
    
    protected $_template_dir = 'message';
    
    protected $_default_tpl_folder = 'open';
    
    protected $_tpl_folders = array('view','add','edit','move','remove','new');
    
    protected $_envs = array('xhtml');
    
    protected $_def_env = 'xhtml';
    
    protected function setOptions(){
    	if (isset($this->_vars[0]) && is_numeric($this->_vars[0])){
			$this->setOption('id',array_shift($this->_vars));
		}
		
		switch ($this->_action){
			case 'new':
				if (isset($this->_vars[0])){
					while (count($this->_vars)>0){
						$name=array_shift($this->_vars);
						$value = array_shift($this->_vars);
						$this->setOption($name,$value);
					}
				}	
			break;
		}
		
		parent::setOptions();
    }
}

class MessageCException extends MainControllerException{}