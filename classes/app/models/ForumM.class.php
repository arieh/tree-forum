<?php
class ForumM extends TFModel{
	/**
	 * @var int default message limit per forum page (root meassages)
	 * @access private
	 */
	private $_default_limit = 10;
	
	/**
	 * @var array holder of forum messages
	 * @access private
	 */
	protected $_messages = array();
	
	/**
	 * @see <Model.class.php>
	 */
	protected $_default_action = 'open';
	
	/**
	 * @see <Model.class.php>
	 */
	protected $_actions = array(
		'open'=>'openForum',
		'create'=>'addForum',
		'add-users'=>'addUsers',
		'add-editors'=>'addEditors',
		'add-admins'=>'addAdmins',
		'restrict'=>'restrictForum',
		'free'=>'freeForum'
	);
	
	/**
	 * @param array allowed permissions for messages.
	 * @access private
	 */
	 private $_message_actions =  array('view','add','edit','remove','move');
	
	/**
	 * @param int forum id
	 * @access protected
	 */
	protected $_id = null;
	
	/**
	 * @param string forum name
	 * @access protected
	 */
	protected $_name = '';
	
	/**
	 * @param string forum description
	 * @access protected
	 */
	protected $_desc = '';
	
	/**
	 * @param int forum id
	 * @access protected
	 */
	protected $_admin = 0;
	
	/**
	 * @param int admins permission id
	 * @access protected
	 */
	protected $_admin_permission = 0;
	
	/**
	 * @param int editors permission id
	 * @access protected
	 */
	protected $_editor_permission = 0;
	
	/**
	 * @param int users permission id
	 * @access protected
	 */
	protected $_user_permission = 0;
	
	
	public function __construct($options=array()){
		parent::__construct($options);
		if ($this->getAction()=='create') return;
		$this->validateForumId();
	}
	
	/**
     * @see <Model.class.php>
     */
    protected function checkPermission(){
    	if ($this->doesHavePermissions()==false){
    		$this->setError('noPermission');
    		return false;
    	}
    		
    	$action = $this->getAction();
    	
    	if ($action=='open') {
    		$this->_id = $id = $this->getOption('id');
    		if (!$id || !is_numeric($id) || !$this->doesForumExists($id,true)) throw new ForumMException('forum id is invalid');
    	}
    	
    	if ($this->isError()){
    		$this->setError('noPermission');
    		return false;
    	}
		    	
    	while ($permission = $this->getPermission()){
    		if ($this->doesHavePermission($action,$permission,true)) return true;
    	}
    	$this->setError('noPermission');
    	return false;
    }
    
    /**
     * checks if a specific permission id is allowed for this specific action
     * 	@param string $action current action
     * 	@param int $permission a permission id
     * 	@param bool logg queries?
     * @access private
     * @return bool
     */
    private function doesHavePermission($action,$permission,$log=false){
    	$no_ids = array('create');
    	$globalPermission = (
    		NewDao::getInstance()
    			->countFields(
					'forum_actions',
					array(
						$action=>1,
						'permission_id'=>$permission,
						'forum_id'=>0
					),
					$log)>0
		);
    	if (in_array($this->getAction(),$no_ids)){
    		$globalBlock =( 
    			NewDao::getInstance()
    				->countFields(
						'forum_actions',
						array(
							$action=>0,
							'forum_id'=>$this->getId()
						),
						$log)>0
    		); 
    		$specificPermission = (
    			NewDao::getInstance()
    				->countFields(
						'forum_actions',
						array(
							$action=>1,
							'permission_id'=>$permission,
							'forum_id'=>$this->getId()
						),
						$log)>0
			);	
    	}else{
    		$globalBlock = true;
    		$specificPermission = true;
    	}
    	
    	return ($globalPermission && ($globalBlock && $specificPermission || !$globalBlock) );
    }
	
  	/**
  	 * retrives all forum data for this action
  	 * @access protected
  	 */
  	protected function openForum(){
  		$dbug = $this->isDebug();
  		
  		if ($dbug) fb('Opening Forum','line '.__LINE__);
  		
  		if ($dbug) fb('Validating Id','line '.__LINE__);
  		$this->validateForumId();
  		
  		if ($this->isError()) return;
  		
  		$id = $this->getId();
		$start = $this->getOption('start');
			if (!$start || !is_numeric($start)) $start = 0;
		
		if ($dbug) fb('Fetching Forum Info','line '.__LINE__);
		$this->retrieveForumInfo($id,$this->isDebug());

		$limit = $this->getOption('limit');
			if (!$limit || !is_numeric($limit)) $limit = $this->_default_limit;
		    	
		if ($dbug) fb('Fetching Forum Messages','line '.__LINE__);
		$this->retrieveMessages($id,$start,$limit,$this->isDebug());
  	}
  	
  	/**
  	 * get the forum id and validates it
  	 * @access private
  	 */
  	private function validateForumId(){
  		if (!is_null($this->_id) && is_numeric($this->_id)) return;
  		
  		$id = $this->_id =0;
  		if (!$this->isOptionSet('id')){
  			$this->setError('noId');
  			return;
  		}else $this->_id = $id = $this->getOption('id');
  		if (!$this->doesForumExists($id,false)){
  			throw new ForumMException('supplied forum id ('.$id.') is invalid');
  		}
  		$this->_id = $id;
  	}
  	
  	/**
  	 * checks whether a forum id exists
  	 * 	@param int $id forum id
  	 * 	@param bool $log wether to log query or not
  	 * @access private
  	 * @return bool
  	 */
  	private function doesForumExists($id,$log=false){
  		return (NewDao::getInstance()->countFields('forums',array('id'=>$id),$log)>0);
  	}
  	
    /**
     * retrieves forum messages
     * 	@param int $id forum id
     * 	@param int $start what root message to start from (used for paging)
     * 	@param int $limit how many root messages to retrieve
     * 	@param bool $log wether to log query or not
     * @access private
     */
    private function retrieveMessages($id,$start,$limit,$log=false){
    	$query = NewDao::getGenerator();
    	
    	$query->addSelect('messages',array());
    	$query->addSelect('message_contents',array());
    	$query->addSelect('users',array('name'=>'username','id'=>'userid'));
    	
    	$query->addSelectFunction(array("DATE_FORMAT","'%d/%m/%Y - %H:%i:%S'"),'time','messages','posted');
    	
    	$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
    	$query->addInnerJoin(array('messages'=>'user_id'),array('users'=>'id'));
    	
    	$query->addConditionSet(
    		$query->createCondition('messages','forum_id','=',$id),
    		$query->createCondition('messages','base','=',1)
    	);
    	
    	$query->limit($start,$limit);
    	$query->orderBy('messages','last_update');
    	$query->orderDesc();
    	
    	$roots = NewDao::getInstance()->queryArray($query->generate(),$log);
		$ids = array();
		foreach ($roots as $msg){
			$ids[]=$msg['id'];
		}
		$query->resetConditions();
		$query->noLimit();
		
		$query->addConditionSet(
			$query->createCondition('messages','root_id','IN',$ids),
			$query->createCondition('messages','base','!=','1')
		);    	
		
		$sons = NewDao::getInstance()->queryArray($query->generate(),$log);
		
		foreach ($roots as $root){
			$r_id = $root['id'];
			$r_messages = array();
			foreach ($sons as &$son){
				if ($son['root_id']==$r_id){
					$r_messages[$son['dna']]=$son;
					unset($son);
				}
			}
			$r_messages = $this->orderMessages($r_messages);
			
			$this->_messages = array_merge($this->_messages,$r_messages);
			$this->_messages[] = $root;
		}
    }
    
    /**
     * orders the messages retrieved from the database
     * @access private
     */
    private function orderMessages($arr){
    	if (count($arr)==0) return array();
    	$keys = array_keys($arr);
    	natsort($keys);
    	$messages = $arr;
    	$arr = array();
    	foreach ($keys as $key){
    		$messages[$key]['depth'] = count(explode('.',$messages[$key]['dna']));
    		$arr[] = $messages[$key];
    	}
    	return array_reverse($arr);
    }
    
    /**
     * adds a forum
     * @access protected
     */
    protected function addForum(){
    	$dbug=$this->isDebug();
    	
    	if ($dbug) fb('Creating a Forum','line '.__LINE__);
    	
    	if ($dbug) fb('Checking Input','line '.__LINE__);
    	$this->_name = $name = $this->getOption('name');
    	$this->_desc = $desc = $this->getOption('description');
    	$admins =  $this->getOption('admins');
    	
    	$editors = $this->getOption('editors');
    	if (!$editors) $editors = array();
    	
    	$users =   $this->getOption('users');
    	if (!$users) $users = array();
    	
    	$restrict = $this->getOption('restricted');
    	$closed   = $this->getOption('closed');
		
		if ( !$name || strlen($name)<3 ) $this->setError('shortName');
		if ( !$desc || strlen($desc)<3 ) $this->setError('shortDesc');
		if ( !$admins ) $this->setError('noAdmins');
		
		foreach ($admins as $admin)
			if ( !is_numeric($admin)  || !$this->doesUserExists($admin,$dbug) ) throw new ForumMException('bad admin id:'.$admin);
		
		foreach ($editors as $editor)
			if ( !is_numeric($editor) || !$this->doesUserExists($editor,$dbug) ) throw new ForumMException('bad editor id:'.$editor);
			
		foreach ($users as $user)
			if ( !is_numeric($user)   || !$this->doesUserExists($user,$dbug) ) throw new ForumMException('bad user id:'.$editor);
		
		if ($this->isError()) return false;
		
		if ($dbug) fb('Creating Forum in DB','line '.__LINE__);
		$this->createForum($name,$desc,$dbug);    	
		
		if ($dbug) fb('Creating Forum Permissions','line '.__LINE__);
		$this->createForumPermissions($this->getId(),$dbug);
    	
    	if ($dbug) fb('Setting Forum Admins','line '.__LINE__);
    	$this->setAdmins($admins,$dbug);
    	
    	if ($editors){
    		if ($dbug) fb('Setting Forum Editors','line '.__LINE__);
    		$this->setEditors($editors,$dbug);
    	}
    	
    	if ($users){
    		if ($dbug) fb('Setting Forum Users','line '.__LINE__);
    		$this->setUsers($users,$dbug);
    	}
    	
    	if ($dbug) fb('Setting Forum Restrictions','line '.__LINE__);
    	if ($closed)   $this->setRestricted($this->getId(),true,$dbug);
    	elseif ($restrict) $this->setRestricted($this->getId(),false,$dbug);
    	
    	
    	$perms = $this->getOption('forum-permissions');
    	
    	if (is_array($perms) && count($perms)>0){
    		if ($dbug) fb('Setting Additional Permissions','line '.__LINE__);
    		$this->addpermissions($perms,$this->isDebug());
    	}
    }
    
    /**
     * creates a forum
     * 	@param string $name forum name
     * 	@param string $desc forum description
     * 	@param bool   $log 
     * @access private
     */
    private function createForum($name,$desc,$log=false){
    	$this->_id = NewDao::getInstance()->insert('forums',array('name'=>$name,'description'=>$desc),$log);
    }
    
    /**
     * sets users as forum admin
     * 	@param int $id user id
     * 	@param bool $log
     * @access private
     */
    private function setAdmins($ids,$log=false){
    	$adminPermission = $this->getAdminPermission();
    	foreach ($ids as $id)
    		NewDao::getInstance()->insert('user_permissions',array('user_id'=>$id,'permission_id'=>$adminPermission),$log);
    }
    
    /**
     * sets users as forum editors
     * 	@param int $id user id
     * 	@param bool $log
     * @access private
     */
    private function setEditors($ids,$log=false){
    	$editorPermission = $this->getEditorPermission();
    	foreach ($ids as $id)
    		NewDao::getInstance()->insert('user_permissions',array('user_id'=>$id,'permission_id'=>$editorPermission),$log);
    }
    
    /**
     * sets users as forum users (good for restricted forums)
     * 	@param int $id user id
     * 	@param bool $log
     * @access private
     */
    private function setUsers($ids,$log=false){
    	$userPermission = $this->getUserPermission();
    	foreach ($ids as $id)
    		NewDao::getInstance()->insert('user_permissions',array('user_id'=>$id,'permission_id'=>$userPermission),$log);
    }
    
    /**
     * creates permissions (admin,editor and user) for a forum
     * 	@param int $id forum id
     * 	@param bool $log
     * @access private
     */
    private function createForumPermissions($id,$log=false){
    	$admin  = $this->_admin_permission  = NewDao::getInstance()->insert('permissions',array('name'=>$this->getName() . '-admin'),$log);
    	$editor = $this->_editor_permission = NewDao::getInstance()->insert('permissions',array('name'=>$this->getName() . '-editor'),$log);
    	$user   = $this->_user_permission   = NewDao::getInstance()->insert('permissions',array('name'=>$this->getName() . '-user'),$log);
    	
    	$options = array('open'=>1,	'view'=>1, 'create'=>1);
    	
    	$this->insertForumPermission($id,$user,$options,$log);
    	
    	$options['move']=1;
    	$options['add-users']=1;
    	$this->insertForumPermission($id,$editor,$options,$log);
    	
    	$options['remove']=1;
    	$options['add-editors']=1;
    	$options['restrict']=1;
    	$options['free']=1;
    	$this->insertForumPermission($id,$admin,$options,$log);
    }
    
    /**
     * retrieves a forum's main permissions (admin editor and users)
     * 	@param string $name forum name
     * 	@param bool $log
     * @access private
     */
    private function retrieveForumPermissions($name,$log=false){
    	$query = NewDao::getGenerator();
    	
    	$query->addSelect('permissions',array('id','name'));
    	
    	$query->addConditionSet($query->createCondition('permissions','name','=',$name . '-admin'));
    	$query->addConditionSet($query->createCondition('permissions','name','=',$name . '-editor'));
    	$query->addConditionSet($query->createCondition('permissions','name','=',$name . '-user'));
    	
    	$perms = NewDao::getInstance()->queryArray($query->generate(),$log);
    	
    	foreach ($perms as $perm) {
    		switch ($perm['name']){
    			case $name . '-admin':
    				$this->_admin_permission = $perm['id'];
    			break;
    			case $name . '-editor':
    				$this->_editor_permission = $perm['id'];
    			break;
    			case $name . '-user':
    				$this->_user_permission = $perm['id'];
    			break;
    		}
    	}
    }
    
    /**
     * sets the forum to restrict mode
     * 	@param int  $id  forum id
     * 	@param bool $all whether to close for all users, or just for guests
     * 	@param bool $log
     * @access private 
     */
    private function setRestricted($id,$all=false,$log=false){
    	if ($this->doesForumPermissionExists($id,8,$log)){
    		$this->deleteForumPermission($id,8,$log);
    	}
    	
    	$this->insertForumPermission($this->getId(),8,array(),$log);
    	
    	if (!$all) return;
    	
    	if ($this->doesForumPermissionExists($id,7,$log)){
    		$this->deleteForumPermission($id,7,$log);
    	}
    	
    	$this->insertForumPermission($this->getId(),7,array(),$log);
    }
    
    private function doesForumPermissionExists($forum,$permission,$log){
    	return(
    		NewDao::getInstance()
    		->countFields('forum_actions',array('forum_id'=>$forum,'permission_id'=>$permission),$log)>0
    	);
    }
    
    /**
     * adds permissions to the forum
     * 	@param array $pers an array of permission arrays
     * 	@param bool $log log queries?
     * @access private
     */
    private function addPermissions($pers,$log=false){
    	foreach ($pers as $per){
    		$per_id = $per['permission_id'];
    		if (!$this->doespermissionExists($per_id,$log)) throw new FormMException("permission $per does not exist");
    		
			$fields = array();     		
    		foreach($per as $name=>$value){
    			if (
    				in_array( $name,$this->_message_actions ) 
    				|| in_array( $name,$this->_actions ) ){
    					$fields[$name] = $value;	
    				} 
    				 
    		}
    		$fields['forum_id'] = $this->getId();
    		$fields['permission_id'] = $per_id;
    		NewDao::getInstance()->insert('forum_actions',$fields,$log); 
    	}
    }
    
    /**
     * checks if a specific permission exists
     * 	@param int $per permission id
     * 	@param bool $log log queries?
     * @access private
	 * @return bool
     */
    private function doespermissionExists($per,$log=false){
    	return (NewDao::getInstance()->countFields('permissions',array('id'=>$per),$log)>0);
    }
	
	/**
	 * adds users to the forum's user list
	 * @access protected
	 */
	protected function addUsers(){
		
		$users = $this->getOption('users');
		if (!$users) $this->setError('noUsers');
		foreach ($users as $userid)
			if (!is_numeric($userid) || !$this->doesUserExists($userid,$this->isDebug())) throw new ForumMException('user id ('.$userid.') is invalid');		
		
		if ($this->isError()) return;
		
		$name = $this->getName();
		if (!$name){
			$this->retrieveForumInfo($this->getId(),$this->isDebug());
			$name = $this->getName();
		} 
		
		$this->retrieveForumPermissions($name,$this->isDebug());
		
		$this->setUsers($users,$this->isDebug());		
	}
	
	/**
	 * adds users to the forum's editors list
	 * @access protected
	 */
	protected function addEditors(){
		
		$users = $this->getOption('users');
		if (!$users || !is_array($users)) $this->setError('noUsers');
		
		foreach ($users as $userid)
			if (!is_numeric($userid) || !$this->doesUserExists($userid,$this->isDebug())) throw new ForumMException('user id ('.$userid.') is invalid');		
		
		if ($this->isError()) return;
		
		$name = $this->getName();
		if (!$name){
			$this->retrieveForumInfo($this->getId(),$this->isDebug());
			$name = $this->getName();
		} 
		
		$this->retrieveForumPermissions($name,$this->isDebug());
		
		$this->setEditors($users,$this->isDebug());		
	}
	
	/**
	 * adds users to the forum's admins list
	 * @access protected
	 */
	protected function addAdmins(){
		
		$users = $this->getOption('users');
		if (!$users) $this->setError('noUsers');
		foreach ($users as $userid)
			if (!is_numeric($userid) || !$this->doesUserExists($userid,$this->isDebug())) throw new ForumMException('user id ('.$userid.') is invalid');		
		
		if ($this->isError()) return;
		
		$name = $this->getName();
		if (!$name){
			$this->retrieveForumInfo($this->getId(),$this->isDebug());
			$name = $this->getName();
		} 
		
		$this->retrieveForumPermissions($name,$this->isDebug());
		
		$this->setAdmins($users,$this->isDebug());		
	}
	
	/**
	 * retrieves the forums info
	 * 	@param int $id forum id
	 * 	@param bool $log
	 * @access private
	 */
	private function retrieveForumInfo($id,$log=false){
		$res = NewDao::getInstance()->select('forums',array('name','description'),array('id'=>$id),true,$log);
		
		$this->_name = $res['name'];
		$this->_desc = $res['description'];
	}

	/**
	 * checks if a specific user id exists
	 * 	@param int $id user id
	 * 	@param bool $log
	 * @access private
	 */	
	private function doesUserExists($id,$log=false){
		return (
			NewDao::getInstance()
			->countFields('users',array('id'=>$id),$log)>0
		);
	}
	
	/**
	 * sets the forum as restricted
	 * @access protected
	 */
	protected function restrictForum(){
		$this->validateForumId();
  		
  		$close = $this->getOption('close');
  		
  		if ($this->isError()) return;
  		
  		$id = $this->getId();
  		
  		$this->setRestricted($id,$close,$this->isDebug());
	}
	
	/**
	 * unrestricts a forum
	 * @access protected
	 */
	protected function freeForum(){
		$this->validateForumId();
  		
  		if ($this->isError()) return;
  		
  		$id = $this->getId();
  		
  		$this->setFree($id);
	}
	
	/**
	 * removes any global binding restriction permission
	 * 	@param int $id forum id
	 * 	@param bool $log
	 * @access private
	 */
	private function setFree($id,$log=false){
		if ($this->doesForumPermissionExists($id,8,$log)){
    		$this->deleteForumPermission($id,8,$log);
    	}
    	
    	if ($this->doesForumPermissionExists($id,7,$log)){
    		$this->deleteForumPermission($id,7,$log);
    	}
	}
	
	private function deleteForumPermission($forum,$perm,$log=false){
   		NewDao::getInstance()->delete(
   			'forum_actions',
   			array(
				'forum_id'=>$forum,
				'permission_id'=>$perm
			),
			$log
   		);
	}
	
	private function insertForumPermission($forum,$perm,$options=array(),$log=false){
		if (!is_array($options)) $options=array();
		$options['forum_id']=$forum;
		$options['permission_id']=$perm;
		NewDao::getInstance()->insert('forum_actions',$options,$log);
	}
}

class ForumMException extends TFModelException{}
?>