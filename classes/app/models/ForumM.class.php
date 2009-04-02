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
 * 		errors: 'noForum' : forum id is invalid
 * + 'add': create a forum
 * 		required:
 * 			- name (string) forum name. must be longer than 3 chars
 * 			- description (string) forum description. must be longer than 3 chars
 * 		errors: 
 * 			- 'shortName' : forum name is invalid (empty, too short, or not a string)
 * 			- 'shortDesc' : forum description is invalid (empty, too short, or not a string)
 */

class ForumM extends Model{
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
	protected $_actions = array('open','add');
	
	
	protected $_id = 0;
    /**
	 * @see <Model.class.php>
	 */
    public function execute() {
    	if ($this->checkPermision()==false){
    		$this->setError('noPermision');
    		return false;
    	}
    	switch ($this->getAction()){
    		case 'open':
				$this->openForum();    		
    		break;
    		case 'add':
    			$this->addForum();
    		break;
    		default:
    			throw new FormMException("no valid action was set");
    		break;
    	}
    }
    
  	/**
  	 * retrives all forum data for this action
  	 * @access private
  	 */
  	private function openForum(){
  		if (!$this->getOption('id')){
  			throw new ForumMException('no forum id supplied');
  		
  		}else $id = $this->getOption('id');
  		
  		if (!$this->forumExists($id,false)){
  			$this->setError('noForum');
  			return false;
  		}
  		
		$start = $this->getOption('start');
			if (!$start || !is_numeric($start)) $start = 0;
		
		$limit = $this->getOption('limit');
			if (!$limit || !is_numeric($limit)) $limit = $this->_default_limit;
		    	
		$this->retrieveMessages($id,$start,$limit,true);
		$this->orderMessages();
  	}
  	
  	/**
  	 * checks whether a forum id exists
  	 * 	@param int $id forum id
  	 * 	@param bool $log wether to log query or not
  	 * @access private
  	 * @return bool
  	 */
  	private function forumExists($id,$log=false){
  		return ($this->_link->countFields('forums',array('id'=>$id),$log)>0);
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
    	
    	//$query->addSelect('message_contents',array());
    	
    	//$query->addInnerJoin(array('messages'=>'id'),array('message_contents'=>'message_id'));
    	
    	$query->addConditionSet(
    		$query->createCondition('messages','forum_id','=',$id),
    		$query->createCondition('messages','base','=',1)
    	);
    	
    	$query->limit($start,$limit);
    	
    	$roots = $this->_link->queryArray($query->generate(),$log);
		$ids = array();
		foreach ($roots as $msg){
			$ids[]=$msg['id'];
		}
		$query->resetConditions();
		
		$query->addConditionSet(
			$query->createCondition('messages','root_id','IN','('.implode(',',$ids).')'),
			$query->createCondition('messages','base','!=','1')
		);    	
		
		$sons = $this->_link->queryArray($query->generate(),$log);
		
		foreach ($roots as $msg) $this->_messages[$msg['dna']]=$msg;
		foreach ($sons  as $msg) $this->_messages[$msg['dna']]=$msg;
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
     * adds a forum
     * @access private
     */
    private function addForum(){
    	$name = $this->getOption('name');
    	$desc = $this->getOption('description');
		
		if (!$name || strlen($name)<3) $this->setError('shortName');
		if (!$desc || strlen($desc)<3) $this->setError('shortDesc');
		
		if ($this->isError()) return false;
		
		$this->createForum($name,$desc,false);    	
    }
    
    /**
     * creates a forum
     * 	@param string $name forum name
     * 	@param string $desc forum description
     * @access private
     */
    private function createForum($name,$desc,$log=false){
    	$this->_link->insert('forums',array('name'=>$name,'description'=>$desc),$log);
    	$this->_id = $this->_link->getLastId();
    }
}

class ForumMException extends ModelException{}
?>