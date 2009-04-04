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
 * + 'create': create a forum
 * 		required:
 * 			- name (string) forum name. must be longer than 3 chars
 * 			- description (string) forum description. must be longer than 3 chars
 *		optional:
 *			- forum-permisions (array) an array of associative arrays of the following structure:
 *				* permision_id (int) a permision's id
 *				* a list of action as 'action-name' => 1/0
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
	protected $_actions = array('open','create');
	
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
    	if ($this->_link->countFields('forum_permisions',array($action=>0,'forum_id'=>$this->getId()),$log)>0) return false;
    	
    	//if this permision is globaly allowed
    	if ($this->_link->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>0),$log)>0) return true;
    	
    	// if this permision is specificly allowed
    	return ($this->_link->countFields('forum_permisions',array($action=>1,'permision_id'=>$permision,'forum_id'=>$this->getId()),$log)>0);
    }
	
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
    		case 'create':
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
  		$this->validateForumId();
  		$id = $this->getId();
		$start = $this->getOption('start');
			if (!$start || !is_numeric($start)) $start = 0;
		
		$limit = $this->getOption('limit');
			if (!$limit || !is_numeric($limit)) $limit = $this->_default_limit;
		    	
		$this->retrieveMessages($id,$start,$limit,$this->isDebug());
		$this->orderMessages();
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
			$query->createCondition('messages','root_id','IN',$ids),
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
    	$perms = $this->getOption('forum-permisions');
    	if (is_array($perms) && count($perms)>0){
    		$this->addPermisions($perms,$this->isDebug());
    	}
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
    		$this->_link->insert('forum_permisions',$fields,$log); 
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
    	return ($this->_link->countFields('permisions',array('id'=>$per),$log)>0);
    }
}

class ForumMException extends ModelException{}
?>