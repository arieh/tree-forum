<?php
/*
 * actions:
 *  + 'add' : add a new message:
 * 		required:
 * 			- 'forum'(string)    : forum id
 * 			- 'title' (string)   : message title
 * 			- 'message' (string) : message content
 * 			- 'parent' (int)     : the parent message. only required if not a base message
 * 		optional:
 * 			- 'base'(bool) : whether this is a base message or not (defualt: true)
 */ 
class MessageM extends Model{
	/**
	 * @see <Model.class.php>
	 */
	protected $_default_action = 'view';
	
	/**
	 * @see <Model.class.php>
	 */
	protected $_actions = array('view','edit','add','delete');
	
	/**
	 * @param int message id
	 * @access protected
	 */
	protected $_id = 0;
	
	/**
	 * @param array holder of sub-messages (when opening a message)
	 * @access protected
	 */
	protected $_messages = array();
	
	/**
	 * @see <Model.class.php>
	 */
	public function execute(){
		if ($this->checkPermision()==false){
			$this->setError('noPermision');
			return false;
		}
		
		switch ($this->getAction()){
			case 'add':
				$this->addMessage();
			break;
		}
	}
	
	/**
	 * creates a message
	 * @access private
	 */
	private function addMessage(){
		/*
		 * Input Validation:
		 */
		
		//forum id retrival
			$forum = $this->getOption('forum');
			if (!$forum) throw new MessageMException('forum id not supplied');
			if ($this->forumExists($forum)==false) throw new MessageMException('bad forum id:'.$forum);
		
		//base validtion
			$base = ($this->optionSet('base')) ? $this->getOption('base') : true;
			if (!$base && !$this->getOption('parent')) throw new MessageMException('parent id must be supplied');
		
		//parent validation
			$parent = $this->getOption('parent');
			if ($parent && $this->messageExists($parent,$forum)==false) throw new MessageMException('parent is not a valid id in this forum:'.$parent);
			
		//title and message validation
			$title = $this->getOption('title');
			$message = $this->getOption('message');
			
			if (!$title || !is_string($title) || strlen($title)<3) $this->setError('shortTitle');
			if (!$message || !is_string($message) || strlen($message)<2) $this->setError('shortMessage');
			
		if ($this->isError()) return false; 
		
		/*
		 * post message
		 */
		if ($base) $this->postBaseMessage($forum,$title,$message,false);
		else $this->postSubMessage($forum,$parent,$title,$message,false);
	}
	
	/**
	 * creates if a forum id exists
	 * 	@param int $forum forum id
	 * 	@param bool $log log query?
	 * @access private
	 * @return bool
	 */
	private function forumExists($forum,$log=false){
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
	private function messageExists($id,$forum,$log=false){
		return ($this->_link->countFields('messages',array('forum_id'=>$forum,'id'=>$id),$log)>0);
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
		$parentIds = $this->getMessageIds($parent,$log);
		$this->_link->insert('messages',array('forum_id'=>$forum,'base'=>0),$log);
		$id = $this->_id = $this->_link->getLastId();
		
		$dna = $parentIds['dna'].".".$id;
		$root = $parentIds['root_id'];
		
		$this->_link->update('messages',array('dna'=>$dna,'root_id'=>$root),array('id'=>$id),$log);
		
		$this->_link->insert('message_contents',array('id'=>$id,'title'=>$title,'message'=>$message),$log); 
	}
	
	/**
	 * returns a message`s details (without its contents)
	 * 	@param int $id message id
	 * @access private
	 * @return array
	 */
	private function getMessageIds($id,$log=false){
		return $this->_link->select('messages',array('forum_id','root_id','dna','id'),array('id'=>$id),true,$log);
	}
	
}

class MessageMException extends ModelException{}