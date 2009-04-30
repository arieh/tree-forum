<?php
abstract class MainController extends TFController{
	protected $_css = array('main');
	protected $_js  = array();
	protected $_headers = array();
	protected $_title = array();
	
	protected function addCSS($css=''){
		if (is_array($css)){
			$this->_css = array_merge($this->_css,$css);
		}elseif (is_string($css)) $this->_css[]=$css; 
	}    
	
	protected function addJS($js=''){
		if (is_array($js)){
			$this->_ss = array_merge($this->_ss,$js);
		}else{
			$args = func_get_args();
			foreach ($args as $arg) if (is_string($arg)) $this->_js[]=$arg;
		} 
	}
	
	protected function addHeader($header=''){
		if (is_array($header)){
			$this->_headers = array_merge($this->_headers,$header);
		}elseif (is_string($header)) $this->_headers[]=$header; 
	}
	
	protected function addTitle($title=''){
		$this->_title[]=$title;
	}
	
	protected function executeBefore(){
		$this->addTitle('Tree-Forum Example');
		if (TFRouter::getEnv()=='xhtml'){
			$this->addJS('mootools-1.2.1-core');
			$this->_view->assign('css',$this->_css);
			$this->_view->assign('title',$this->_title);
			
			$this->_output .= $this->_view->fetch('header' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'main.tpl.php');
			
			if (TFUser::isLoggedIn()==false){
				$this->addJS('loginForm','sha1');
				
				$userM = new UserM();
				$userM->execute();
				$this->_view->assign('key',$userM->getKey());
				$this->_output .= $this->_view->fetch('header'. DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'menu' .DIRECTORY_SEPARATOR. 'login-form.tpl.php');
			}else{
				$this->_output .= $this->_view->fetch('header'. DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'menu' .DIRECTORY_SEPARATOR . 'main.tpl.php');
			}
					
		}
		
	}
	
	protected function executeAfter(){
		if (TFRouter::getEnv()=='xhtml'){
			$this->_view->assign('js',$this->_js);
			$this->_output .= $this->_view->fetch('footer' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'main.tpl.php');
		}
		
		foreach ($this->_headers as $header) header($header);
	}
	
	protected function setOptions(){
		parent::setOptions();
		
		$this->setOption('user',TFUser::getInstance()->getId());
    	$this->setOption('permissions',TFUser::getInstance()->getPermissionIds(false));
    	if (defined('_DEBUG_')) $this->setOption('debug',_DEBUG_);
		
		$inputs = array($_GET,$_POST);
    	foreach ($inputs as $input){
    		foreach ($input as $key=>$var){
    			$this->setOption($key,$var);
    		}
    	}
	}
}

class MainControllerException extends TFControllerException{}