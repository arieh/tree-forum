<?php
/*
 * action list:
 * 
 * + 'open': open a forum page
 * 		required: 
 * 			- id(int) forum id
 * 		optional: 
 * 			- start(int) how many root messages to skip
 * 			- limit(int) how many root messages to pull
 * 		accessors:
 * 			- getId() - returns forum id
 * 			- getMessage() - return a message accessor from the message-tree:
 * 					+ getId()
 * 					+ getTitle()
 * 					+ getMessage() - returns message content
 * 					+ getTime()
 * 					+ getUserId()
 * + 'create': create a forum
 * 		required:
 * 			- name (string) forum name. must be longer than 3 chars
 * 			- description (string) forum description. must be longer than 3 chars
 * 			- admin (int) admin user id
 *		optional:
 *			- forum-permisions (array) an array of associative arrays of the following structure:
 *				* permision_id (int) a permision's id
 *				* a list of action as 'action-name' => 1/0
 * 		errors: 
 * 			- 'shortName' : forum name is invalid (empty, too short, or not a string)
 * 			- 'shortDesc' : forum description is invalid (empty, too short, or not a string)
 * 			- 'noAdmin'   : no admin id was set
 * 		accessors: getId()
 * 
 * 
 * returned object for getMessage() will have the following methods:
 * getId(), getTitle(), getTime(), getContent(), getUserId(), getUserName()
 */

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
	protected $_actions = array('open'=>'openForum','create'=>'addForum','allow'=>'addAllowed');
	
	/**
	 * @param array allowed permisions for messages.
	 * @access private
	 */
	 private $_message_actions =  array('view','add','edit','remove','move');
	
	/**
	 * @param int forum id
	 * @access protected
	 */
	protected $_id = 0;
	
	protected $_name = '';
	
	protected $_desc = '';
	
	protected $_admin = 0;
	
	/**
     * @see <Model.class.php>
     */
    protected function checkPermision(){
    	
    	if ($this->doesHavePermisions()==false){
    		$this->setError('noPermision');
    		return false;
    	}
    		
    	$action = $this->getAction();
    	
    	if ($action=='open') {
    		$this->_id = $id = $this->getOption('id');
    		if (!$id || !is_numeric($id) || !$this->doesForumExists($id,true)) throw new ForumMException('forum id is invalid');
    	}
    	
    	if ($this->isError()){
    		$this->setError('noPermision');
    		return false;
    	}
    	
    	while ($permision = $this->getPermision()){
    		if ($this->doesHavePermision($action,$permision,true)) return true;
    	}
    	$this->setError('noPermision');
    	return false;
    }
    
    /**
     * checks if a specific permision id is allowed for this specific action
     * 	@param string $action current action
     * 	@param int $permision a permision id
     * 	@param bool logg queries?
     * @access private
     * @return bool
     */
    private function doesHavePermision($action,$permision,$log=false){
    	//if this action is forbiden for this permision
    	if (NewDao::getInstance()->countFields('forum_permisions',array($action=>0,'forum_id'=>$this->getId()),$log)>0) return false;
    	
    	//if this permision is globaly allowed
    	if (NewDao::getInstance()->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>0),$log)>0) return true;
    	
    	// if this permision is specificly allowed
    	return (NewDao::getInstance()->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>$this->getId()),$log)>0);
    }
	
  	/**
  	 * retrives all forum data for this action
  	 * @access protected
  	 */
  	protected function openForum(){
  		$this->validateForumId();
  		$id = $this->getId();
		$start = $this->getOption('start');
			if (!$start || !is_numeric($start)) $start = 0;
		
		$limit = $this->getOption('limit');
			if (!$limit || !is_numeric($limit)) $limit = $this->_default_limit;
		    	
		$this->retrieveMessages($id,$start,$limit,$this->isDebug());
  	}
  	
  	/**
  	 * get the forum id and validates it
  	 * @access private
  	 */
  	private function validateForumId(){
  		if (is_numeric($this->_id)) return;
  		
  		$this->_id =0;
  		if (!$this->getOption('id')){
  			throw new ForumMException('no forum id supplied');
  		
  		}else $id = $this->getOption('id');
  		
  		if (!$this->doesForumExists($id,false)){
  			throw new ForumMException('no forum id supplied');
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
    	$this->_name = $name = $this->getOption('name');
    	$this->_desc = $desc = $this->getOption('description');
    	$this->_admin = $admin = $this->getOption('admin');
    	$restrict = $this->isOptionSet('restrict') ? $this->getOption('restrict') : false;
		
		if ( !$name || strlen($name)<3 ) $this->setError('shortName');
		if ( !$desc || strlen($desc)<3 ) $this->setError('shortDesc');
		if ( !$admin ) $this->setError('noAdmin');
		elseif ( !is_numeric($admin) || !$this->doesUserExists($admin) ) throw new ForumMException('bad admin id');
		
		if ($this->isError()) return false;
		
		$this->createForum($name,$desc,$admin,$restrict,false);    	
    }
    
    /**
     * creates a forum
     * 	@param string $name forum name
     * 	@param string $desc forum description
     * 	@param int    $admin admin id
     * 	@param bool   $restrict make forum restricted
     * 	@param bool   $log 
     * @access private
     */
    private function createForum($name,$desc,$admin,$restrict=false,$log=false){
    	$this->_id = NewDao::getInstance()->insert('forums',array('name'=>$name,'description'=>$desc),$log);
    	
    	$this->setAdmin($admin,$log);
    	if ($restrict) $this->restrictForum($log);
    	
    	$perms = $this->getOption('forum-permisions');
    	
    	if (is_array($perms) && count($perms)>0){
    		$this->addPermisions($perms,$this->isDebug());
    	}
    }
    
    /**
     * sets a user forum admin
     * 	@param int $id user id
     * 	@param bool $log
     * @access private
     * @return bool sccess status
     */
    private function setAdmin($id,$log=false){
    	$adminPermission = $this->createAdminPermission($log);
    	return NewDao::getInstance()->insert('users_permisions',array('user_id'=>$id,'permision_id'=>$adminPermission),$log);
    }
    
    /**
     * creates an admin permission
     *	@param bool $log 
     * @access private
     * @return int new permission's id 
     */
    private function createAdminPermission($log=false){
    	$perm = NewDao::getInstance()->insert('permisions',array('name'=>$this->getName()));
    	NewDao::getInstance()->insert(
			'forum_permisions',
			array(
				'forum_id'=>$this->getId(),
				'permision_id'=>$perm,
				'open'=>1,
				'remove'=>1,
				'view'=>1,
				'create'=>1,
				'move'=>1
			),
			$log
		);
		return $perm;
    }
    
    /**
     * sets the forum to restrict mode
     * 	@param bool $log
     * @access private 
     */
    private function restrictForum($log=false){
    	NewDao::getInstance()->insert(
			'forum_permisions',
			array(
				'forum_id'=>$this->getId(),
				'permision_id'=>8
			),
			$log
		);
		
		NewDao::getInstance()->insert(
			'forum_permisions',
			array(
				'forum_id'=>$this->getId(),
				'permision_id'=>7
			),
			$log
		);
    }
    
    /**
     * adds permisions to the forum
     * 	@param array $pers an array of permision arrays
     * 	@param bool $log log queries?
     * @access private
     */
    private function addPermisions($pers,$log=false){
    	foreach ($pers as $per){
    		$per_id = $per['permision_id'];
    		if (!$this->doesPermisionExists($per_id,$log)) throw new FormMException("permision $per does not exist");
    		
			$fields = array();     		
    		foreach($per as $name=>$value){
    			if (
    				in_array( $name,$this->_message_actions ) 
    				|| in_array( $name,$this->_actions ) ){
    					$fields[$name] = $value;	
    				} 
    				 
    		}
    		$fields['forum_id'] = $this->getId();
    		$fields['permision_id'] = $per_id;
    		NewDao::getInstance()->insert('forum_permisions',$fields,$log); 
    	}
    }
    
    /**
     * checks if a specific permision exists
     * 	@param int $per permision id
     * 	@param bool $log log queries?
     * @access private
	 * @return bool
     */
    private function doesPermisionExists($per,$log=false){
    	return (NewDao::getInstance()->countFields('permisions',array('id'=>$per),$log)>0);
    }
	
	protected function addAllowed(){
		
	}
}

class ForumMException extends TFModelException{}
?>