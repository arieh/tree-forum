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
    	if (isset($this->_vars[0]) ){
    		if (is_numeric($this->_vars[0])){
				$this->setOption('id',array_shift($this->_vars));
    		}elseif (is_string($this->_vars[0])) $this->setOption('name',array_shift($this->_vars));	
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
    
    protected function executeBefore(){
    	switch ($this->_action){
    		case 'view':
    			$this->addTitle($this->_model->getTitle());
    			$this->addCSS('view_message');
    		break;
    	}
    	parent::executeBefore();
    }
}

class MessageCException extends MainControllerException{}