<?php
class UserM extends TFModel{
	protected $_actions = array('create'=>'createUser','open'=>'openUser','add-permission'=>'addPermission','remove-permission'=>'removePermission');
	
	protected $_default_action = 'open';
	
	protected $_id = 1;
	
	protected $_name = '';
	
	protected $_email = '';
	
	protected $uid = '';
	
	protected function checkPermision(){
		$perms = $this->getPermisions(false);
		$action = $this->getAction();
		$dbug = $this->isDebug();
		if ($action=='open') return true;
		foreach ($perms as $perm){
			if ($this->isAdmin($perm,$dbug)) return true;
			if ($this->doesHavePermission($action,$perm,$dbug)) return true;
		}
		return false;
	}
	
	private function isAdmin($perm,$log=false){
		return ($perm==1);
	}
	
	private function doesHavePermission($action,$perm,$log=false){
		switch($action){
			case 'create':
			case 'open':
				$table = 'user_permissions';
			break;
			case 'add-permission':
			case 'remove-permission':
				$table = 'forum_permisions';
		}
		return (NewDao::getInstance()
					->countFields($table,array('permision_id'=>$perm,$action=>1),$log)>0);
	}
	
	protected function createUser(){
		$dbug = $this->isDebug();
		
		$permissions = $this->getOption('new-permissions');
		$this->_name  = $name = $this->getOption('name');
		$pass = $this->getOption('password');
		$this->_email = $email = $this->getOption('email');
		$encrypt = ($this->isOptionSet('encrypt')) ? $this->getOption('encrypt') : true;

		if (count($permissions)<1) $this->setError('no-permissions');
		if (strlen($name)<2) $this->setError('noName');
		if ($this->isNameTaken($name,$dbug)) $this->setError('nameTaken');
		if (!is_string($pass)) $this->setError('badPass');
		if ($this->isValidEmail($email)==false) $this->setError('badEmail');
		
		if ($this->isError()) return false;
		
		if ($encrypt) $pass = sha1($pass);
		
		$uid = $this->generateUid();
		while ($this->doesUidExists($uid,$dbug)) $uid = $this->generateUid();
		
		$this->_id = $this->postUser($name,$pass,$email,$uid,$dbug);
		$this->setPermissions($this->getId(),$permissions,$dbug);
		
	}
	
	private function isNameTaken($name,$log=false){
		return (NewDao::getInstance()
				->countFieldsLCASE('users',array('name'=>strtolower($name)),$log)>0
		);
	}
	
	private function isValidEmail($email){
		/*$chars = "/[a-zA-Z0-9_-.]+@[a-zA-Z0-9-]+.[.a-zA-Z]+/i";
		if(strstr($email, '@') && strstr($email, '.')) {
			if (preg_match($chars, $email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}*/
		return true;
	}
	
	private function generateUid(){
		return sha1(uniqid(substr(sha1($this->getName() . rand()) ,10) , true ));
	}
	
	private function doesUidExists($uid,$log=false){
		return (NewDao::getInstance()
				->countFields('users',array('uid'=>$uid),$log)>0
		);
	}
	
	private function postUser($name,$pass,$email,$uid,$log=false){
		NewDao::getInstance()->insert('users',array('name'=>$name,'password'=>$pass,'email'=>$email,'uid'=>$uid),$log);
		return NewDao::getInstance()->getLastId();
	}
	
	private function setPermissions($id,$perms,$log=false){
		$ins = NewDao::getInstance();
		foreach ($perms as $perm){
			$ins->insert('users_permisions',array('user_id'=>$id,'permision_id'=>$perm),$log);
		}
	}
}

class UserMException extends TFModelException {}