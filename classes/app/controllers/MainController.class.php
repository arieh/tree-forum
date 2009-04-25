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
			$this->_css = array_merge($this->_css,$js);
		}elseif (is_string($js)) $this->_css[]=$js; 
	}
	
	protected function addHeader($header=''){
		if (is_array($header)){
			$this->_css = array_merge($this->_css,$header);
		}elseif (is_string($header)) $this->_css[]=$header; 
	}
	
	protected function addTitle($title=''){
		$this->_title[]=$title;
	}
	
	protected function executeBefore(){
		$this->addTitle('Tree-Forum Example');
		if (TFRouter::getEnv()=='xhtml'){
			$this->_view->assign('css',$this->_css);
			$this->_view->assign('title',$this->_title);
			$this->_output .= $this->_view->fetch('header' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'main.tpl.php');		
		}
	}
	
	protected function executeAfter(){
		if (TFRouter::getEnv()=='xhtml'){
			$this->_view->assign('js',$this->_js);
			$this->_output .= $this->_view->fetch('footer' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'main.tpl.php');
		}
		
		foreach ($this->_headers as $header) header($header);
	}
}

class MainControllerException extends TFControllerException{}