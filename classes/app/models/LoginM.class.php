<?php

class LoginM extends TFModel{
	
	protected $_key = '';
	
	private $_handler = null;
	
	protected $_ini = '../configs/userAtuh.ini';
	
	protected $_actions = array('login'=>'login','logout'=>'logout','generate'=>'generate');
	
	protected $_default_action = 'generate';
	
	private function setHandler(){
		$this->_handler = new keyHandler(NewDao::getLink(),$this->getIni());
	}
	
	protected function generate(){
		if ($this->isOptionSet('ini')) $this->_ini = $this->getOption('ini');
		if (!file_exists($this->getIni())){
			$this->setError('badIni');
		}
		
		if ($this->isError()) return;
		
		$this->setHandler();
		$this->_handler->generateKey();
		$this->_key = $this->_handler->getKey(); 
	}
	
	protected function login(){
		if ($this->isOptionSet('ini')) $this->_ini = $this->getOption('ini');
		if (!file_exists($this->getIni())){
			$this->setError('badIni');
		}
		
		$name = $this->getOption('user-name');
		if (!is_string($name) || strlen($name)<2 || $this->doesNameExists($name,$this->isDebug())==false) $this->setError('badName');
		
		$hash  = $this->getOption('hash');
		if (!is_string($hash) || strlen($hash)<40) $this->setError('badHash');
		
		$encoded = ($this->isOptionSet('encoded')) ? (bool)$this->getOption('encoded') : true;
		
		if ($this->isError()) return;
		
		if ($this->_handler->authenticate($name,$hash,$encoded)){
			UserM::setId($this->retrieveUserId($name,$this->isDebug()));
		}else $this->setError('badMatch');
	}   
	
	private function doesUserExists($name,$log=false){
		return ($this->_link->countFieldsLCASE('users',array('name'=>strtolower($name)),$log)>0);
	}
	
	private function retrieveUserId($name,$log=false){
		$res = $this->_link->selectLCASE('users',array('id'),array('name'=>strtolower($name)),true,$log);
		return $res['id'];
	}
	
	protected function logout(){
		UserM::setId(1);
	}
}

class LoginMException extends TFModelException{}
?>