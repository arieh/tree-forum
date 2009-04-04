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
 * 		errors:
 * 			- 'shortTitle' : title is too short or invalid
 * 			- 'shortContent' : content was too short or invalid
 *  + 'view' : open a message, and its chieldren's tree
 * 		required:
 * 			- 'id'(int) : message id
 * 		optional:
 * 			- 'children' : retrieve message children? default: true
 * + 'edit' : submmits an edited message
 * 		required:
 * 			- id (int) : message id
 * 			- 'message' (string) : new content - must be longer than 2 chars
 * 		errors:
 * 			- 'shortContent' : invalid content 
 * 		
 */ 
class MessageM extends Model{
	/**
	 * @see <Model.class.php>
	 */
	protected $_default_action = 'view';
	
	/**
	 * @see <Model.class.php>
	 */
	protected $_actions = array('view','add','edit'/*,'remove','move'*/);
	
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
	
	/**
	 * @param string message title
	 */
	private $_title = '';
	
	/**
	 * @param string message content
	 */
	private $_content = '';
	
	 /**
     * @see <Model.class.php>
     */
    protected function checkPermision(){
		$action = $this->getAction();
    	while ($perm = $this->getPermision()){
    				if ($this->doesHavePermision($action,$perm,true)) return true;
    			}
    			$this->setError('noPermision');
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
    	if ($this->_link->countFields('forum_permisions',array($action=>0,'forum_id'=>$this->getForumId(),'permision_id'=>$permision),$log)>0) return false;
    	
    	//if this permision is globaly allowed
    	if ($this->_link->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>0),$log)>0) return true;
    	
    	// if this permision is specificly allowed
    	return ($this->_link->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>$this->getForumId()),$log)>0);
    }
	
	/**
	 * @see <Model.class.php>
	 */
	public function execute(){
		$this->validateInputIds();
		
		if ($this->checkPermision()==false){
			$this->setError('noPermision');
			return false;
		}
		switch ($this->getAction()){
			case 'add':
				$this->addMessage();
			break;
			case 'view':
				$this->openMessage();
			break;
			case 'edit':
				$this->editMessage();
			break;
		}
	}
	
	/**
	 * validates forum id and message id acording to action
	 * @access private
	 */
	private function validateInputIds(){
		$action = $this->getAction();
    	
    	switch ($action){
    		case 'view':
    		case 'edit':
    		case 'remove':
    		case 'move':
    			$this->_id = $id = $this->getOption('id');
    			$forum = $this->_forum_id = $this->retrieveForumId($id,false);
    			if (!$id) $id = $this->getOption('id');
    			if (!$id || !is_numeric($id)) throw new MessageMException('badId');
    			if (!$this->doesMessageExists($id,$forum)) throw new MessageMException('badId');
    			
    			
    		break;
    		case 'add':
    			$forum_id = $this->getOption('forum');
    			if (!$forum_id || !is_numeric($forum_id)) throw new MessageMException('bad forum id');
    			$this->_id = $this->getOption('id');
    			$this->_forum_id = $forum_id;
    		break;
    	}
	}
	
	/**
	 * retrieves the forum id of a message
	 * 	@param int $id message id
	 * 	@param bool $log log queries?
	 * @access private
	 * @return bool 
	 */
	private function retrieveForumId($id,$log=false){
		$res = $this->_link->select('messages',array('forum_id'),array('id'=>$id),true,$log);
		return (int)$res['forum_id'];
	}
	
	/**
	 * creates a message
	 * @access private
	 */
	private function addMessage(){
		/*
		 * Input Validation:
		 */
		
		$forum = $this->getForumId();
		
		//base validtion
			$base = ($this->isOptionSet('base')) ? $this->getOption('base') : true;
			if (!$base && !$this->getOption('parent')) throw new MessageMException('parent id must be supplied');
		
		//parent validation
			$parent = $this->getOption('parent');
			if ($parent && $this->doesMessageExists($parent,$forum)==false) throw new MessageMException('parent is not a valid id in this forum:'.$parent);
			
		//title and message validation
			$title = $this->getOption('title');
			$message = $this->getOption('message');
			
			if (!$title || !is_string($title) || strlen($title)<2) $this->setError('shortTitle');
			if (!$message || !is_string($message) || strlen($message)<2) $this->setError('shortContent');
			
		if ($this->isError()) return false; 
		
		/*
		 * post message
		 */
		if ($base) $this->postBaseMessage($forum,$title,$message,true);
		else $this->postSubMessage($forum,$parent,$title,$message,true);
	}
	
	/**
	 * creates if a forum id exists
	 * 	@param int $forum forum id
	 * 	@param bool $log log query?
	 * @access private
	 * @return bool
	 */
	private function doesForumExists($forum,$log=false){
		return ($this->_link->countFields('forums',array('id'=>$forum),$log)>0);
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
		if ($forum) return ($this->_link->countFields('messages',array('forum_id'=>$forum,'id'=>$id),$log)>0);
		return ($this->_link->countFields('messages',array('id'=>$id),$log)>0);
	}
	
	/**
	 * posts a base message
	 * 	@param int    $forum forum id
	 * 	@param string $title message title
	 * 	@param string $message message content
	 * 	@param bool   $log log queries?
	 * @access private
	 */
	private function postBaseMessage($forum,$title,$message,$log=false){
		$this->_link->insert('messages',array('forum_id'=>$forum,'base'=>1),$log);
		$id = $this->_id = $this->_link->getLastId();
		
		$this->_link->update('messages',array('dna'=>$id,'root_id'=>$id),array('id'=>$id),$log);
		
		$this->_link->insert(
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
	private function postSubMessage($forum,$parent,$title,$message,$log=false){
		$parentIds = $this->retrieveMessageIds($parent,$log);
		$this->_link->insert('messages',array('forum_id'=>$forum,'base'=>0),$log);
		$id = $this->_id = $this->_link->getLastId();
		
		$dna = $parentIds['dna'].".".$id;
		$root = $parentIds['root_id'];
		
		$this->_link->update('messages',array('dna'=>$dna,'root_id'=>$root),array('id'=>$id),$log);
		
		$this->_link->insert('message_contents',array('message_id'=>$id,'title'=>$title,'message'=>$message,'non-html'=>strip_tags($message)),$log); 
	}
	
	/**
	 * returns a message`s details (without its contents)
	 * 	@param int $id message id
	 * @access private
	 * @return array
	 */
	private function retrieveMessageIds($id,$log=false){
		return $this->_link->select('messages',array('forum_id','root_id','dna','id'),array('id'=>$id),true,$log);
	}
	
	/**
	 * opens a message
	 * @access private
	 */
	private function openMessage(){
		$this->_id = $id = $this->getId();
		
		$msg = $this->retrieveMessageInfo($id,true);
		$this->_id = $id;
		$this->_title = $msg['title'];
		$this->_content = $msg['message'];
		
		if ($this->isOptionSet('children') && $this->getOption('children') == false) return;
		
		$this->retrieveMessages($msg['dna'],true);
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
		
		$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
		
		$query->addConditionSet(
			$query->createCondition('messages','id','=',$id)
		);
		
		$res = $this->_link->queryArray($query->generate(),$log);
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
		
		$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
		
		$query->addConditionSet(
			//this needs to be revised. should find a way to do this without a LIKE statement
			$query->createCondition('messages','dna','LIKE',$dna.".%")
		);
		
		$messages = $this->_link->queryArray($query->generate(),$log);
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
    private function editMessage(){
    	$id = $this->getId();
    	if (!$id) $id = $this->_id = $this->getOption('id');
    	if (!$id || !$this->doesMessageExists($id,true)) throw new MessageMException('bad id');
    	
    	$content = $this->getOption('message');
    	if (!$content || strlen($content)<2) $this->setError('shortContent');
    	
    	if ($this->isError()) return false;
    	
    	$this->_link->update('message_contents',array('message'=>$content,'non-html'=>strip_tags($content)),array('message_id'=>$id),true); 
    }
}

class MessageMException extends ModelException{}