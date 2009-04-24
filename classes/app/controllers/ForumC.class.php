<?php
class ForumC extends TFController{
	
    protected $_model_name = 'ForumM';
    
    protected $_template_dir = 'forum';
    
    protected $_action = 'open';
    
    protected $_default_tpl_folder = 'open';
    
    protected $_tpl_folders = array('open','create','add-users','add-editors','add-admins','restrict','free');
    
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
		
		switch ($this->_action){
			case 'open':
				$start = (isset($this->_vars[1]) && is_numeric(($this->_vars[1]))) ? $this->_vars[1] : TFRouter::getParam('start');
				$limit = (isset($this->_vars[2]) && is_numeric(($this->_vars[2]))) ? $this->_vars[2] : TFRouter::getParam('limit');
				$this->setOption('start',$start);
				$this->setOption('limit',$limit);
			break;
		} 
		
		parent::setOptions();
    }
}

class ForumCException extends TFControllerException{}