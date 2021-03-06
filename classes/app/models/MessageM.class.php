<?php
/*
 * actions:
 *  + 'add' : add a new message:
 * 		required:
 * 			- 'forum'(string)    : forum id
 * 			- 'title' (string)   : message title - must be longer than 2 chars
 * 			- 'message' (string) : message content - must be longer than 2 chars
 * 			- 'parent' (int)     : the parent message. only required if not a base message
 * 		optional:
 * 			- 'base'(bool) : whether this is a base message or not (defualt: true)
 * 			- 'user'(int)  : a user id
 * 		errors:
 * 			- 'shortTitle' : title is too short or invalid
 * 			- 'shortContent' : content was too short or invalid
 *  + 'view' : open a message, and its chieldren's tree
 * 		required:
 * 			- 'id'(int) : message id
 * 		optional:
 * 			- 'children' : retrieve message children? default: true
 * 		accessors:
 * 			- getId()
 * 			- getTitle()
 * 			- getContent()
 * 			- getUserId()
 * 			- getUserName
 * 			- getMessage() - return a message accessor from the sub-message tree:
 * 					+ getId()
 * 					+ getTitle()
 * 					+ getMessage() returns message's content
 * 					+ getTime()
 * 					+ getUserId()
 * 					+ getUserName()
 * + 'edit' : submmits an edited message
 * 		required:
 * 			- id (int) : message id
 * 			- 'message' (string) : new content - must be longer than 2 chars
 * 			- 'user' (int) : user id
 * 		optional:
 * 			-'editors-can-edit' (bool) whether or not editors and admins can edit messages
 * 		errors:
 * 			- 'shortContent' : invalid content 
 * + 'move' : moves a message from one tree to another
 * 		requierd:
 * 			- id (int) : message id
 * 		optional:
 * 			- new-parent (id) : new parent's id. requierd if not base
 * 			- base (bool) : whether to make the message a root message
 * + 'remove' : removes a message and its siblings from the database
 * 		requierd:
 * 			-id (int) : message id 		
 */ 
class MessageM extends TFModel{
	/**
	 * @see <Model.class.php>
	 */
	protected $_default_action = 'view';
	
	/**
	 * @see <Model.class.php>
	 */
	protected $_actions = array('new'=>'newMessage','view'=>'openMessage','add'=>'addMessage','edit'=>'editMessage','move'=>'moveMessage','remove'=>'removeMessage');
	
	/**
	 * @param int message id
	 * @access protected
	 */
	protected $_id = 0;
	
	/**
	 * @param int forum id
	 * @access protected
	 */
	protected $_forum_id = 0;
	
	/**
	 * @param array holder of sub-messages (when opening a message)
	 * @access protected
	 */
	protected $_messages = array();
	
	protected $_parent = 0;
	
	protected $_base = true;
	
	protected $_parent_message = array();
	
	/**
	 * @param string message title
	 * @access private
	 */
	protected $_title = '';
	
	/**
	 * @param string message content
	 * @access private
	 */
	protected $_content = '';
	
	/**
	 * @param bool whether or not edit shoudl be restricted to the user who submitted it or to allow editors/admin to edit as well
	 * @access private
	 */
	private $_editSelfOnly = true;
	
	 /**
     * @see <Model.class.php>
     */
    protected function checkpermission(){
		$action = $this->getAction();
    	while ($perm = $this->getpermission()){
    				if ($this->doesHavepermission($action,$perm,true)) return true;
    			}
    			$this->setError('noPermission');
    }
	
	/**
     * checks if a specific permission id is allowed for this specific action
     * 	@param string $action current action
     * 	@param int $permission a permission id
     * 	@param bool logg queries?
     * @access private
     * @return bool
     */
    private function doesHavepermission($action,$permission,$log=false){
    	$no_ids = array('create');
    	if ($action=='new') $action='add';
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
							'forum_id'=>$this->getForumId()
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
							'forum_id'=>$this->getForumId()
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
	 * @see <Model.class.php>
	 */
	public function execute(){
		$this->validateInputIds();
		
		parent::execute();
	}
	
	/**
	 * validates forum id and message id acording to action
	 * @access private
	 */
	private function validateInputIds(){
		$action = $this->getAction();
    	
    	switch ($action){
    		case 'edit':
    		case 'remove':
			case 'view':
				if ($this->isOptionSet('name')){
					$this->_name = $name = strtolower($this->getOption('name'));
					if ($this->doesMessageExists($name,false,$this->isDebug())){
						$id = $this->_id = $this->retrieveMessageId($name,$this->isDebug());
					}
				}
    		case 'move':
				if (!isset($id)) $this->_id = $id = $this->getOption('id');
				
				if (!$id || !is_numeric($id)) throw new MessageMException('badId');
    			
    			$forum = $this->_forum_id = $this->retrieveForumId($id,false);

    			if (!$this->doesMessageExists($id,$forum)) throw new MessageMException('badId');
    		break;
    		case 'add':
    		case 'new':
    			$forum_id = $this->getOption('forum');
    			fb($forum_id);
    			if (!$forum_id || !is_numeric($forum_id)) throw new MessageMException('bad forum id');
    			$this->_id = $this->getOption('id');
    			$this->_forum_id = $forum_id;
    		break;
    	}
	}
	
	private function retrieveMessageId($name,$log=false){
		$res = NewDao::getInstance()->selectLCASE('messages',array('id'),array('name'=>$name),false,$log);
		return $res[0]['id'];
	}
	
	/**
	 * retrieves the forum id of a message
	 * 	@param int $id message id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool 
	 */
	private function retrieveForumId($id,$log=false){
		$res = NewDao::getInstance()->select('messages',array('forum_id'),array('id'=>$id),true,$log);
		return (int)$res['forum_id'];
	}
	
	/**
	 * creates a message
	 * @access private
	 */
	protected function addMessage(){
		/*
		 * Input Validation:
		 */
		
		$forum = $this->getForumId();
		
		//base validtion
			$base = ($this->isOptionSet('base')) ? $this->getOption('base') : true;
			if (!$base && !$this->getOption('parent')) throw new MessageMException('parent id must be supplied');
		
		//parent validation
			$this->_parent = $parent = $this->getOption('parent');
			if ($parent && $this->doesMessageExists($parent,$forum)==false) throw new MessageMException('parent is not a valid id in this forum:'.$parent);
			
		//title and message validation
			$title = $this->getOption('title');
			$message = $this->getOption('message');
			$user = $this->getOption('user');

			if (!$title || !is_string($title) || strlen($title)<2) $this->setError('shortTitle');
			if (!$message || !is_string($message) || strlen($message)<2) $this->setError('shortContent');
			
		if ($this->isError()) return false; 
		
		/*
		 * post message
		 */
		if ($base) $this->postBaseMessage($forum,$title,$message,$user,$this->isDebug());
		else $this->postSubMessage($forum,$parent,$title,$message,$user,$this->isDebug());
	}
	
	/**
	 * creates if a forum id exists
	 * 	@param int $forum forum id
	 * 	@param bool $log log query?
	 * @access private
	 * @return bool
	 */
	private function doesForumExists($forum,$log=false){
		return (NewDao::getInstance()->countFields('forums',array('id'=>$forum),$log)>0);
	}
	
	/**
	 * checks if a message exists for a specific forum
	 * 	@param int $id     message id
	 * 	@param int $forum  forum id
	 * 	@param bool $log   log query?
	 * @access private
	 * @return bool
	 */
	private function doesMessageExists($id,$forum=false,$log=false){
		if (!is_numeric($id) && is_string($id)){
			return (
				NewDao::getInstance()
				->countFieldsLCASE('messages',array('name'=>$id),$log)>0
			);
		}
		if ($forum) return (NewDao::getInstance()->countFields('messages',array('forum_id'=>$forum,'id'=>$id),$log)>0);
		return (NewDao::getInstance()->countFields('messages',array('id'=>$id),$log)>0);
	}
	
	/**
	 * posts a base message
	 * 	@param int    $forum forum id
	 * 	@param string $title message title
	 * 	@param string $message message content
	 * 	@param bool   $log log queries?
	 * @access private
	 */
	private function postBaseMessage($forum,$title,$message,$user=0,$log=false){
		
		$name = str_replace(' ','-',misc::strip_symbols(strtolower($name)));
		
		NewDao::getInstance()->insert(
			'messages',
			array(
				'forum_id'=>$forum,
				'base'=>1,
				'posted'=>'NOW()',
				'last_update'=>'NOW()',
				'user_id'=>$user,
				'name'=>$name
				),
			$log
		);
		$id = $this->_id = NewDao::getInstance()->getLastId();
		
		NewDao::getInstance()->update('messages',array('dna'=>$id,'root_id'=>$id),array('id'=>$id),$log);
		
		NewDao::getInstance()->insert(
			'message_contents',
			array(
				'message_id'=>$id,
				'title'=>$title,
				'message'=>$message,
				'non-html'=>strip_tags($message)
			),
			$log
		);
	}
	
	/**
	 * posts a sub message message
	 * 	@param int    $forum forum id
	 * 	@param int    $parent parent message`s id
	 * 	@param string $title message title
	 * 	@param string $message message content
	 * 	@param bool   $log log queries?
	 * @access private
	 */
	private function postSubMessage($forum,$parent,$title,$message,$user=0,$log=false){
		$parentIds = $this->retrieveMessageIds($parent,$log);
		NewDao::getInstance()->insert(
			'messages',
			array(
				'forum_id'=>$forum,
				'base'=>0,
				'posted'=>'NOW()',
				'last_update'=>'NOW()',
				'user_id'=>$user
				),
				$log
			);
		$id = $this->_id = NewDao::getInstance()->getLastId();
		
		$dna = $parentIds['dna'].".".$id;
		$root = $parentIds['root_id'];
		
		NewDao::getInstance()->update('messages',array('dna'=>$dna,'root_id'=>$root),array('id'=>$id),$log);
		
		NewDao::getInstance()->insert(
				'message_contents',
				array(
					'message_id'=>$id,
					'title'=>$title,
					'message'=>$message,
					'non-html'=>strip_tags($message)
				),
				$log
		); 
		NewDao::getInstance()->update('messages',array('last_update'=>'NOW()'),array('id'=>$root),$this->isDebug());
	}
	
	/**
	 * returns a message`s details (without its contents)
	 * 	@param int $id message id
	 * @access private
	 * @return array
	 */
	private function retrieveMessageIds($id,$log=false){
		return NewDao::getInstance()->select('messages',array('forum_id','root_id','dna','id'),array('id'=>$id),true,$log);
	}
	
	/**
	 * opens a message
	 * @access private
	 */
	protected function openMessage(){
		$this->_id = $id = $this->getId();
		
		$msg = $this->retrieveMessageInfo($id,$this->isDebug());
		$this->_id = $id;
		$this->_title = $msg['title'];
		$this->_content = $msg['message'];
		
		if ($this->isOptionSet('children') && $this->getOption('children') == false) return;
		
		$this->retrieveMessages($msg['dna'],$this->isDebug());
		$this->orderMessages();
	}
	
	/**
	 * retrieves the message info
	 * 	@param int $id message id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return array
	 */
	private function retrieveMessageInfo($id,$log=false){
		$query = NewDao::getGenerator();
		
		$query->addSelect('messages',array());
		$query->addSelect('message_contents',array('title','message'));
		$query->addSelect('users',array('name'=>'username','id'=>'userid'));
		$query->addSelectFunction(array("DATE_FORMAT","'%d/%m/%Y - %H:%i:%S'"),'time','messages','posted');
		
		$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
		$query->addInnerJoin(array('messages'=>'user_id'),array('users'=>'id'));
		
		$query->addConditionSet(
			$query->createCondition('messages','id','=',$id)
		);
		
		$res = NewDao::getInstance()->queryArray($query->generate(),$log);
		return $res[0];
	}
	
	/**
	 * retrieves the messages
	 * 	@param string $dna the message's parent list
	 * 	@param bool   $log log queries?
	 * @access private
	 */
	private function retrieveMessages($dna,$log=false){
		$query = NewDao::getGenerator();
		
		$query->addSelect('messages',array());
		$query->addSelect('message_contents',array('title','message'));
		$query->addSelect('users',array('name'=>'username','id'=>'userid'));
		$query->addSelectFunction(array("DATE_FORMAT","'%d/%m/%Y - %H:%i:%S'"),'time','messages','posted');
		
		$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
		$query->addInnerJoin(array('messages'=>'user_id'),array('users'=>'id'));
		
		$query->addConditionSet(
			//this needs to be revised. should find a way to do this without a LIKE statement
			$query->createCondition('messages','dna','LIKE',$dna.".%")
		);
		
		$messages = NewDao::getInstance()->queryArray($query->generate(),$log);
		foreach ($messages as $msg) $this->_messages[$msg['dna']] = $msg;
	}
	
	/**
     * orders the messages retrieved from the database
     * @access private
     */
    private function orderMessages(){
    	if (count($this->_messages)==0) return;
    	
    	$keys = array_keys($this->_messages);
    	natsort($keys);
    	$messages = $this->_messages;
    	$this->_messages = array();
    	foreach ($keys as $key){
    		$messages[$key]['depth'] = count(explode('.',$messages[$key]['dna']));
    		$this->_messages[] = $messages[$key];
    	}
    	$this->_messages = array_reverse($this->_messages);
    }
    
    /**
     * submits an eddited message
     */
    protected function editMessage(){
    	$id = $this->getId();
    	if (!$id) $id = $this->_id = $this->getOption('id');
    	if (!$id || !$this->doesMessageExists($id,true)) throw new MessageMException('bad id');
    	
    	$content = $this->getOption('message');
    	if (!$content || strlen($content)<2) $this->setError('shortContent');
    	
    	$user = $this->getOption('user-id');
    	if ( !is_numeric($user) ) throw new MessageMException('user id is invalid');
    	
    	$this->_editSelfOnly = (bool)$this->getOption('editors-can-edit');
    	
    	if ($this->isError()) return false;
    	
    	if ($this->isUserAllowedToEdit($user,$this->_editSelfOnly,$this->isDebug()) == false){
    		$this->setError('noPermission');
    		return false;
    	};
    	
    	NewDao::getInstance()->update('message_contents',array('message'=>$content,'non-html'=>strip_tags($content)),array('message_id'=>$id),$this->isDebug()); 
    }
    
    private function isUserAllowedToEdit($id,$selfEdit,$log=false){
    	if ($selfEdit==false) return true;
    	return (NewDao::getInstance()->countFields('messages',array('id'=>$this->getId(),'user_id'=>$id),$log)>0);
    }
    
    /**
     * moves a mesage to a different tree
     */
    protected function moveMessage(){
    	$id = $this->getId();
    	$forum = $this->getForumId();
    	$oldDna = $this->retrieveDna($id,$this->isDebug());
    	$base = ($this->isOptionSet('base')) ? $this->getOption('base') : false;
    	if ($base){
    		$newDna = $this->getId();
    		$new_root = $this->getId();
    	}else{
    		$newParent = $this->getOption('new-parent');
    	
	    	if (!$newParent || !is_numeric($newParent) || !$this->doesMessageExists($newParent,$forum))
	    		throw new MessageMException('bad new parent');
	    	
	    	$parentDna = $this->retrieveDna($newParent,$this->isDebug());
	    	$newDna = $parentDna.".".$id;	
	    	$new_root = $this->retrieveRoot($newParent,$this->isDebug());
    	}
    	
    	$children = $this->retrieveChildrenIds($oldDna,$this->isDebug());
		
		$newRecords[] = array('id'=>$id,'dna'=>$newDna);

		foreach ($children as $child){
			$raw_dna = explode($oldDna.".",$child['dna']);
			$raw_dna = $raw_dna[1];
			$arr = array('id'=>$child['id'],'dna'=>$newDna.".".$raw_dna);
			$newRecords[]=$arr;
		}
		
		foreach ($newRecords as $record){
			NewDao::getInstance()->update('messages',array('dna'=>$record['dna'],'root_id'=>$new_root),array('id'=>$record['id']),$this->isDebug());
		} 
		 
		if ($base){
			NewDao::getInstance()->update('messages',array('base'=>true),array('id'=>$id),$this->isDebug());
		} 		
    }
    
    /**
     * returns a message's dna
     * 	@param int $id a message id
     * 	@param bool $log log queries?
     * @access private
     */
    private function retrieveDna($id,$log=false){
    	$res = NewDao::getInstance()->select('messages',array('dna'),array('id'=>$id),true,$log);
    	return $res['dna'];
    }
    
    /**
     * returns the root_id of a message
     * 	@param int $id message id
     * 	@param bool $log log queries?
     * @access private
     */
    private function retrieveRoot($id,$log=false){
    	$res = NewDao::getInstance()->select('messages',array('root_id'),array('id'=>$id),true,$log);
    	return $res['root_id'];
    }
    
    /**
     * returns a message's children's ids
     * 	@param string $dna a message's dna
     * 	@param bool $log log queries?
     * @access private
     */
    private function retrieveChildrenIds($dna,$log=false){
    	$query = NewDao::getGenerator();
		
		$query->addSelect('messages',array());
		
		$query->addConditionSet(
			//this needs to be revised. should find a way to do this without a LIKE statement
			$query->createCondition('messages','dna','LIKE',$dna.".%")
		);
		
		return NewDao::getInstance()->queryArray($query->generate(),$log);
    }
    
    /**
     * removes a message and its siblings form the database
     * @access private
     */
    protected function removeMessage(){
    	$id = $this->getId();
    	$dbug = $this->isDebug();
    	$dna = $this->retrieveDna($id,$dbug);
    	
    	$children = $this->retrieveChildrenIds($dna,$dbug);
    	
    	foreach ($children as $child){
    		NewDao::getInstance()->delete('messages',array('id'=>$child['id']),$dbug);
    	}
    	NewDao::getInstance()->delete('messages',array('id'=>$id),$dbug);
    }
    
    /**
     * retrieves needed information for creating a new message form
     */
    protected function newMessage(){
    	$this->_base = $this->getOption('base');
    	$this->_parent = $this->getOption('parent');
    	
    	if (!$this->_parent) $this->_base = true;
    	else{
    		if ($this->doesMessageExists($this->_parent)==false) throw new MessageMException('bad parent id: '.$this->_parent);
    		$this->_parent_message = $this->retrieveMessageInfo($this->_parent,$this->isDebug());
    	}
    }
}

class MessageMException extends TFModelException{}