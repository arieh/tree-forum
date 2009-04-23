<?php

class TFController {
    
    protected $_model = null;
    
    protected $_default_model = '';
    
    protected $_model_name = 'ForumM';
    
    protected $_view = null;
    
    protected $_template_dir = '';
    
    protected $_options = array();
    
    protected $_vars = array();
    
    protected $_default_tpl_folder = '';
    
    protected $_tpl_folders = array();
    
    protected $_envs = array('xhtml');
    
    protected $_def_env = 'xhtml';
    
    protected $_action = '';
    
    public function __construct($vars=array(),$view){
    	if (!defined('_SEP_')) define (_SEP_,DIRECTORY_SEPARATOR); 
    	
    	if (count($vars)>0){
    		$this->_action = $this->setOption('action',strtolower(array_shift($vars)));
    	}
    	
    	$this->_vars = $vars;
    	$this->_view = $view;
    	
    	$this->setOption('user',TFUser::getId());
    	$this->setOption('permisions',TFUser::getInstance()->getPermissionIds(false));
    	if (defined('_DEBUG_')) $this->setOption('debug',_DEBUG_);
    	
    	$this->setOptions();
    	
    	$this->execute();
    	$name = $this->_model_name;
    	$this->_model = new $name($this->getOptions());
		
		$this->_model->execute();	
    	
    	$this->_view->assign('model',$this->_model);
    	if (in_array(TFRouter::getEnv(),$this->_envs)) $folder = TFRouter::getEnv();
		else $folder = $this->_def_env;
		
		if (strlen($this->_action)>0 && in_array($this->_action,$this->_tpl_folders)) 
			$location = $this->_template_dir . _SEP_ . $this->_action . _SEP_ . $folder . _SEP_;
		else $location = $this->_template_dir . _SEP_ . $this->_default_tpl_folder . _SEP_ . $folder . _SEP_;	
		
		$file = (is_null($this->_model) || $this->_model->isError()) ? 'errors.tpl.php' : 'main.tpl.php';
		$output = $this->_view->fetch($location . $file);
		print_r($output);
    }
    
    public function execute(){}
    
    protected function setOption($name,$value){
    	$this->_options[$name]=$value;
    	return $value;
    }
    
    protected function getOptions(){
    	return $this->_options;
    }
    
    protected function isOptionSet($name){
    	if (array_key_exists($name,$this->_options)) return true;
    	return false;
    }
    
    protected function setOptions(){
    	$inputs = array($_GET,$_POST);
    	foreach ($inputs as $input){
    		foreach ($input as $key=>$var){
    			$this->setOption($key,$var);
    		}
    	}
    }
}

class TFControllerException extends Exception{}
?>