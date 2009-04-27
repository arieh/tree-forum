<?php
define('DEFAULT_VIEW_CONF_DIR','..' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'view.ini');

class Savant3View implements TFView{
	private $_savant = null;
	
	public function __construct($conf=''){
		if ($conf instanceof IniObject) $view_conf = $view_conf;
		else $view_conf = (strlen($conf)>0 && file_exists($conf)) ? new IniObject($conf) : new IniObject(DEFAULT_VIEW_CONF_DIR); 
		
		$this->_savant = new Savant3();
		$this->_savant->addPath('template',$view_conf->base_dir . $view_conf->templates);
		$this->_savant->assign('bPath',$view_conf->base_path);
	}
	
	public function addPath($type, $path){
		$this->_savant->addPath($type,$path);	
	}
	
	public function display($tpl = null){
		return $this->_savant->display($tpl);
	}
	
	public function fetch($tpl = null){
		return $this->_savant->fetch($tpl);
	}
	
	public function assign($arg0=null,$arg1=null){
		$this->_savant->assign($arg0,$arg1);
	} 	     
}