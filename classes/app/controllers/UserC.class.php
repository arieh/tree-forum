<?php

class UserC extends MainController{
    protected $_model_name = 'UserM';
    
    protected $_template_dir = 'user';
    
    protected $_default_tpl_folder = 'open';
    
    protected $_tpl_folders = array('new','open','login','logout','create');
    
    protected $_envs = array('xhtml');
    
    protected $_def_env = 'xhtml';
    
    public function executeBefore(){
    	switch($this->_action){
    		case 'new':
    			$this->addJS('sha1','new-user');
    		break;
    	}
    	parent::executeBefore();
    }
    
    public function setOptions(){
    	switch ($this->_action){
    		case 'create':
    			$this->setOption('new-permissions',array(TFRouter::getParam('new-permission')));
    		break;
    		case 'open':
    			if (isset($this->_vars[0])){
    				switch (is_numeric($this->_vars[0])){
    					case (true):
    						$this->setOption('id',$this->_vars[0]);
    					break;
    					case (false):
    						$this->setOption('name',$this->_vars[0]);
    					break;
    				}
    			}
    		break;
    	}
    	parent::setOptions();
    }
}
?>