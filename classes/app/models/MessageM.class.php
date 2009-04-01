<?php

class MessageM extends Model{
	protected $_default_action = 'view';
	
	protected $_actions = array('view','edit','add','delete');
	
	protected $_id = 0;
	
	protected $_messages = array();
	
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
	
	private function addMessage(){
		$forum = $this->getOption('forum');
		if (!$forum) throw new MessageMException('forum id not supplied');
		if ($this->forumExists($forum)==false) throw new MessageMException('bad forum id:'.$forum);
		
		$base = $this->getOption('base');
		if (!$base && !$this->getOption('parent')) throw new MessageMException('parent id must be supplied');
		
		$parent = $this->getOption('parent');
		if ($parent && $this->messageExists($parent,$forum)==false) throw new MessageMException('parent is not a valid id in this forum:'.$parent);
		
		$title = $this->getOption('title');
		$message = $this->getOption('message');
		
		if (!$title || !is_string($title) || strlen($title)<3) $this->setError('shortTitle');
		if (!$message || !is_string($message) || strlen($message)<2) $this->setError('shortMessage');
		
		if ($this->isError()) return false; 
		
		if ($base) $this->postBaseMessage($forum,$title,$message,false);
		else $this->postSubMessage($forum,$parent,$title,$message,false);
	}
	
	private function forumExists($forum,$log=false){
		return ($this->_link->countFields('forums',array('id'=>$forum),$log)>0);
	}
	
	private function messageExists($id,$forum,$log=false){
		return ($this->_link->countFields('messages',array('forum_id'=>$forum,'id'=>$id),$log)>0);
	}
	
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
	
}

class MessageMException extends ModelException{}