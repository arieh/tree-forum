<?php
class ForumM extends Model{
	
	private $_defLimit = 10;
	
	private $_messages = array();
	
    public function execute() {
    	$id    = ($this->getOption('id') || false);
    	$start = ($this->getOption('start') || 0);
    	$limit = ($this->getOption('limit') || $this->_defLimit);
    	
    	$this->getMessages($id,$start,$limit,true);
    	$this->orderMessages();
    }
    
    private function getMessages($id,$start,$limit,$log=false){
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
    
    private function orderMessages(){
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
    
    public function getMessage(){
    	$msg = array_pop($this->_messages);
    	if ($msg) return new ModelResult($msg);
    	return false;
    }
}

class ForumMException extends ModelException{}
?>