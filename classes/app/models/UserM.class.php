<?php
class UserM extends TFModel{
	
	/**
	 * @see <TFModel.class.php>
	 */
	protected $_actions = array(
		'new'=>'newUser',		
		'create'=>'createUser',
		'open'=>'openUser',
		'login'=>'login',
		'logout'=>'logout',
		'generate'=>'generate'
	);
	
	/**
	 * @see <TFModel.class.php>
	 */
	protected $_default_action = 'generate';
	
	/**
	 * @param int user id
	 * @access protected
	 */
	protected $_id = 1;
	
	/**
	 * @param string user name
	 * @access protected
	 */
	protected $_name = '';
	
	/**
	 * @param string user email
	 * @access protected
	 */
	protected $_email = '';
	
	/**
	 * @param string user's uniq id
	 * @access protected 
	 */
	protected $_uid = '';
	
	/**
	 * @param array a list of message ids posted by the user
	 */
	protected $_message_ids = array();
	
	/**
	 * @param int number of messages to retrieve from the database
	 */
	private $_message_limit = 10;
	
	/**
	 * @param string a key generated by userAtuh
	 * @access protected
	 */
	protected $_key = '';
	
	/**
	 * @param keyHandler a userAtuh key-kandler
	 * @access private
	 */
	private $_handler = null;
	
	/**
	 * @param string location of the userAtug config file
	 * @access protected
	 */
	protected $_ini = '../configs/userAtuh.ini';
	
	/**
	 * @see <TFModel.class.php>
	 */
	protected function checkpermission(){
		$free = array('login','logout','generate','open');
		$action = $this->getAction();
		
		if (in_array($action,$free)) return true;
		
		$perms = $this->getpermissions(false);
		
		$dbug = $this->isDebug();
		
		foreach ($perms as $perm){
			if ($this->isAdmin($perm,$dbug)) return true;
			if ($this->doesHavePermission($action,$perm,$dbug)) return true;
		}
		
		return false;
	}
	
	/**
	 * checks if a specific permission is an admin permission
	 * 	@param int $perm permission id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool
	 */
	private function isAdmin($perm,$log=false){
		return ($perm==1);
	}
	
	/**
	 * checks if a specific pair of permission and action is allowed
	 * 	@param string $action action name
	 * 	@param int    $perm   permission id
	 * 	@param book   $log    log queries?
	 * @access private
	 * @return bool
	 */
	private function doesHavePermission($action,$perm,$log=false){
		switch($action){
			case 'new':
				$action = 'create';
			case 'create':
			case 'open':
				$table = 'user_actions';
			
			break;
			case 'add-permission':
			case 'remove-permission':
				$table = 'forum_actions';
		}
		return (NewDao::getInstance()
					->countFields($table,array('permission_id'=>$perm,$action=>1),$log)>0);
	}
	
	/**
	 * a main action method for user creation
	 */
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
		
		foreach ($permissions as $permission) if ($this->doesPermissionExists($permission)==false) throw new UserMException('badPermission');
		
		if ($this->isError()) return false;
		
		if ($encrypt) $pass = sha1($pass);
		
		$uid = $this->generateUid();
		while ($this->doesUidExists($uid,$dbug)) $uid = $this->generateUid();
		
		$this->_id = $this->postUser($name,$pass,$email,$uid,$dbug);
		$this->setPermissions($this->getId(),$permissions,$dbug);
		
	}
	
	/**
	 * checks if a specific name is already taken
	 * 	@param string $name user name
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool
	 */
	private function isNameTaken($name,$log=false){
		return (NewDao::getInstance()
				->countFieldsLCASE('users',array('name'=>strtolower($name)),$log)>0
		);
	}
	
	/**
	 * checks if a given email is valid
	 * 	@param string $email an email address
	 * @access private
	 * @return bool
	 * 
	 * @todo provide a simple email validation
	 * @todo provide a full email check (not only valid but exists) 
	 */
	private function isValidEmail($email){
		return true;
	}
	
	/**
	 * checks if a given permission exists
	 * 	@param int $perm permission id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool
	 */
	private function doesPermissionExists($perm,$log=false){
		return(
			NewDao::getInstance()
			->countFields('permissions',array('id'=>$perm),$log)>0
		);
	}
	
	
	/**
	 * generates a uniq id for the user
	 * @access private
	 * @return string
	 */
	private function generateUid(){
		return sha1(uniqid(substr(sha1($this->getName() . rand()) ,10) , true ));
	}
	
	/**
	 * checks if a given uid is already taken
	 * 	@param string $uid a uniq id to check for
	 * 	@param bool   $log log queries?
	 * @access private
	 * @return bool
	 */
	private function doesUidExists($uid,$log=false){
		return (NewDao::getInstance()
				->countFields('users',array('uid'=>$uid),$log)>0
		);
	}
	
	/**
	 * posts the user's info to the db
	 * 	@param string $name user name
	 * 	@param string $pass user password
	 * 	@param string $email user email
	 * 	@param string $uid  user's uniq id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return int new user's id
	 */
	private function postUser($name,$pass,$email,$uid,$log=false){
		NewDao::getInstance()->insert('users',array('name'=>$name,'password'=>$pass,'email'=>$email,'uid'=>$uid),$log);
		return NewDao::getInstance()->getLastId();
	}
	
	/**
	 * sets new permissions to a user
	 * 	@param int $id user id
	 * 	@param int $perm permission id
	 * 	@param bool $log log queries?
	 * @access private
	 */
	private function setPermissions($id,$perms,$log=false){
		$ins = NewDao::getInstance();
		foreach ($perms as $perm){
			$ins->insert('user_permissions',array('user_id'=>$id,'permission_id'=>$perm),$log);
		}
	}
	
	/**
	 * opens a user
	 */
	protected function openUser(){
		$dbug = $this->isDebug();
		$this->_id = $id = $this->getOption('id');
		if (!$id){
			$name = $this->_name = $this->getOption('name');
			if (!$name) $this->setError('noId');
			elseif ($this->doesUserExists($name,$dbug)) $id = $this->_id = $this->retrieveUserId($name,$dbug);
			else $this->setError('noId');
		}else{
			if (!$this->doesUserExists($id,$dbug)) $this->setError('badId');
			else $this->_name = $name = $this->retrieveUserName($id,$dbug);
		} 
		
		if (!$id || !$this->doesUserExists($id,$dbug)) $this->setError('badId');
		
		if ($this->isError()) return false;
		$this->_email = $this->retrieveUserEmail($this->getId(),$dbug);
		
		$this->_message_ids = $this->retrieveMessageIds($id,$dbug); 
	}
	
	/**
	 * checks if a user exists by id/name
	 * 	@param int|string $value user id|name
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool
	 */
	private function doesUserExists($value,$log=false){
		if (is_numeric($value)){
			return (
				NewDao::getInstance()
				->countFields('users',array('id'=>$value),$log)>0
			);	
		}
		return(
		 	NewDao::getInstance()
		 	->countFieldsLCASE('users',array('name'=>strtolower($value)),$log)>0
		);		
	}
	
	/**
	 * returns a users id by his name
	 * 	@param string $name user name
	 * 	@param bool $log
	 * @access private
	 * @return int
	 */
	private function retrieveUserId($name,$log=false){
		$res = 	NewDao::getInstance()
				->selectLCASE('users',array('id'),array('name'=>strtolower($name)),true,$log);
		return $res['id'];
	}
	
	/**
	 * returns a user name by his id
	 * 	@param int $id user id
	 * 	@param bool $log
	 * @access private
	 * @return string
	 */
	private function retrieveUserName($id,$log=false){
		$res = NewDao::getInstance()
				->select('users',array('name'),array('id'=>$id),true,$log);
		return $res['name'];
	}
	
	/**
	 * returns a user email by his id
	 * 	@param int $id
	 * 	@param bool $log
	 * @access private
	 * @return string
	 */
	private function retrieveUserEmail($id,$log=false){
		$res = NewDao::getInstance()
				->select('users',array('email'),array('id'=>$id),true,$log);
		return $res['email'];
	}
	
	/**
	 * returns a list of ids of messages posted by a user
	 * 	@param int $id
	 * 	@param bool $log
	 * @access private
	 * @return array list of message ids
	 */
	private function retrieveMessageIds($id,$log=false){
		$query = NewDao::getGenerator();
		
		$sql = 
		$query
		->addSelect('messages',array('id'))
		->limit($this->_message_limit)
		->addConditionSet( $query->createCondition('messages','user_id','=',$id) )
		->generate();
		
		return NewDao::getInstance()->queryArray($sql,$log);		
	}
	
	/**
	 * sets the keyHandler
	 * @access private
	 */
	private function setHandler(){
		$this->_handler = new keyHandler(NewDao::getLink(),$this->getIni());
	}
	
	/**
	 * generates a key using keyHandler
	 * @access protected
	 */
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
	
	/**
	 * attempts to login
	 * @access protected
	 */
	protected function login(){
		if ($this->isOptionSet('ini')) $this->_ini = $this->getOption('ini');
		if (!file_exists($this->getIni())){
			$this->setError('badIni');
		}
		
		$name = $this->getOption('user-name');
		if (!is_string($name) || strlen($name)<2 || $this->doesUserExists($name,$this->isDebug())==false) $this->setError('badName');
		
		$hash  = $this->getOption('hash');
		if (!is_string($hash) || strlen($hash)<40) $this->setError('badHash');
		
		$encoded = ($this->isOptionSet('encoded')) ? (bool)$this->getOption('encoded') : true;

		if ($this->isError()) return;
		
		$this->setHandler();
		
		if ($this->_handler->authenticate($name,$hash,$encoded)){
			TFUser::setId($this->retrieveUserId($name,$this->isDebug()));
		}else $this->setError('badMatch');
	}   
	
	/**
	 * logout the user
	 */
	protected function logout(){
		TFUser::logOut();
	}
	
	protected function newUser(){
		$userLevel = $this->retrieveUserTopLevel(TFUser::getInstance()->getId(),$this->isDebug());
		$this->_allowed_permissions = $this->retrieveAllowedPermissions($userLevel,$this->isDebug());
	}
	
	private function retrieveUserTopLevel($id,$log=false){
		$query = NewDao::getGenerator();
		$sql = $query->addSelect('permissions',array('level'))
		->addInnerJoin(array('permissions'=>'id'),array('user_permissions'=>'permission_id'))
		->addConditionSet(
			$query->createCondition('permissions','level','>',0),
			$query->createCondition('user_permissions','user_id','=',$id)
		)
		->limit(1)
		->orderBy('permissions','level')
		->orderDesc()
		->generate();
		$res = NewDao::getInstance()->queryArray($sql,$log);
		return $res[0]['level'];
	}
	
	private function retrieveAllowedPermissions($level,$log=false){
		$query = NewDao::getGenerator();
		$sql = $query->addSelect('permissions',array())
		->addConditionSet(
			$query->createCondition('permissions','level','>',0),
			$query->createCondition('permissions','level','<',$level)
		)
		->generate();
		return NewDao::getInstance()->queryArray($sql,$log);
	}
	
}

class UserMException extends TFModelException {}