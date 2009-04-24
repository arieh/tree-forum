<?php
class TFModelException extends Exception{}

/*
 * paramaters:
 * 	- permisions (array) a list of permisions ids the current user has
 * 	- actions (string)   current action
 */
class TFModel{
	
	/**
	 * @param string class version
	 * @access private
	 * @static
	 */
	static private $_TF_MODEL_VERSION = '0.5';
	
	/**
	 * @var array holder of paramater options
	 * @access protected
	 */
	protected $_options = array();
	
	/**
	 * @var array holder of the model's inner errors
	 * @access protected
	 */
	protected $_errors = array();
	
	/**
	 * @param array holds all legal actions for the model and their paired methods
	 * @access protected
	 */
	protected $_actions = array();
	
	/**
	 * @param string holds default action for the model
	 * @access protected
	 */
	protected $_default_action = '';
	
	/**
	 * @param string current model action
	 * @access protected
	 */
	protected $_action = false;
	
	/**
	 * @param array a set of current permision ids
	 * @access protected
	 */
	protected $_permisions = array();
	
	/**
	 * @param bool debug mode
	 * @access protected
	 */
	 protected $_debug = false;
	
	/**
	 * a constructor method for the object. sets the databse holder and the model options
	 * 	@param array $options paramaters for the model
	 * 
	 * @access public
	 * @return void 
	 */
	public function __construct($options = array()){
		foreach ($options as $name => $value){
			if (is_string($name)) $this->setOption($name,$value);
		}
		if ($this->isOptionSet('debug')){
			$this->_debug = ($this->getOption('debug')==true);
			$this->setOption('debug',null); 
		}
		
		if ($this->isOptionSet('permisions')){
			$this->_permisions = $this->getOption('permisions');
			$this->setOption('permisions',null);
		} 
		$this->setAction();
	}
	
	/**
	 * executes the model's logic
	 * @access public
	 */	
	public function execute(){
		if ($this->checkPermision()==false){
    		$this->setError('noPermision');
    		return false;
    	}
		
		if ($this->getAction()=='') return;
		$action_method = $this->_actions[$this->getAction()];
		$this->$action_method();
	}
	
	public function __destruct(){
  		if (isset($this->_link) && method_exists($this->_link,'__destruct')) $this->_link->__destruct();
  	}
	
	/**
	 * returns a model paramater
	 * 	@param string $name a parameter name
	 * @access protected
	 * @return mixed|bool if paramter is set returns it, otherwise return false
	 */
	protected function getOption($name){
		if (isset($this->_options[$name])) return $this->_options[$name];
		return false;
	}
	
	/**
	 * sets a model paramater
	 * 	@param string $name paramater name
	 * 	@param mixed $value a paramter value
	 * @access protected
	 */
	protected function setOption($name,$value){
		$this->_options[$name] = $value;
	}
	
	/**
	 * checks if a specific option is set (good when an option is expected to be boolean)
	 * 	@param string $name option name
	 * @return bool
	 */
	protected function isOptionSet($name){
		return (isset($this->_options[$name]));
	}
	/**
	 * sets the action for the model
	 * 	@param string $action action name
	 * @access protected;
	 */
	public function setAction($action=false){
		$actions = array_keys($this->_actions);
		if ($action && in_array($action,$actions)){
			$this->_action = $action;
			return;
		}
		if ($action = $this->getOption('action')){
			if (in_array($action,$actions)) $this->_action = $action;
			return;
		}
		$this->_action = $this->_default_action;
	}
	
	/**
	 * returns current action
	 * @access protected
	 * @return string
	 */
	 protected function getAction(){
	 	if (!$this->_action) return $this->_default_action;
	 	return $this->_action;
	 }
	 
	 /**
	  * get a permision from the permision list
	  * @return int permision id
	  * @access protected
	  */
	 protected function getPermision(){
	 	return array_pop($this->_permisions);
	 }
	 
	 /**
	  * gets permision list
	  * @access protected
	  * @return array
	  */
	 protected function getPermisions(){
	 	return $this->_permisions;
	 }
	 
	 /**
	  * checks if any permisions were set
	  *	@access protected
	  *	@return bool
	  */
	 protected function doesHavePermisions(){
	 	return (count($this->_permisions)>0);
	 }
	 
	/**
	 * checks if a specific user has permision to access the page
	 * @return bool
	 */
	protected function checkPermision(){return true;}
	
	/**
	 * sets an internal error
	 * 	@param string $name error name
	 * @access protected
	 */
	protected function setError($name){
		$this->_errors[$name] = true;
	}
	
	/**
	 * unsets an internal error
	 * 	@param string $name erro name to unset
	 * @access protected
	 */
	protected function unsetError($name){
		if (isset($this->_errors[$name])) unset($this->_errors[$name]);
	}
	
	/**
	 * checks if an error exists for the model. 
	 * if no name is specified, checks if any errors were set
	 * 	@param string $name error name to check
	 * @access public
	 * @return bool
	 */
	public function isError($name=false){
		if ($name && isset($this->_errors[$name])) return $this->_errors[$name]; 
		return (count($this->_errors)>0); 
	}
	
	/**
	 * returns an array of errors
	 * @access public
	 * @return array
	 */
	public function getErrors(){
		return array_keys($this->_errors);
	}
	
	/**
	 * check if in debug mode
	 * @access protected
	 * @return bool
	 */
	 protected function isDebug(){
	 	return ($this->_debug==true);
	 }
	
	/**
	 * returns a JSON representation of the object
	 * @access public
	 * @return string
	 */
	public function toJSON(){return '{}';}

	
	/**
	 * sets default getters and setters (getParamname(), setParamname())
	 * 
	 * properties access by this method must be protected or public. if a property 
	 * is an array, and it's name is plural, accessing it with singular form will pop its first 
	 * variable. also, if that variable is an array, it will be passed as a model result.
	 * 
	 * for get<ParamName>:
	 * 	@param bool whether to return arrays as ModelResults
	 * 
	 * for set<ParamName>:
	 * 	if paramater is an array, all passed paramaters will be pushed
	 */
	public function __call($name,$args){
		$action = substr($name,0,3);
		$pVar = misc::explodeCase(substr($name,3));
		$pVar = "_".implode("_",$pVar);
		$sVar = $pVar.'s';
		$pVarExists = (isset($this->$pVar) || property_exists($this,$pVar));
		$sVarExists = (
			(isset($this->$sVar) || property_exists($this,$sVar)) 
			&& is_array($this->$sVar)
		);
		if (!$pVarExists && !$sVarExists) throw new TFModelException("No Method Exists:".$name);
		
		switch ($action){
			case 'get':
				$mr = (isset($args[0]) && !$args[0]) ? false : true ;
				if ($sVarExists){
					
					$var = array_pop($this->$sVar);
					if ($var){
						 return (is_array($var) && $mr)? new ModelResult($var) : $var;
					}
					return false;
				}
				$singular = (substr($pVar,-1)=='s') ? substr($pVar,1,(strlen($pVar)-2)) : $pVar;
				return (is_array($this->$pVar) && $mr) ? new ModelResult($this->$pVar,$singular) : $this->$pVar;
			break;
		}
	}
	
	public function __get($name){
		if ($name == '_link'){
			$this->_link = NewDao::getInstance();
		}
	}
	
	/**
	 * get the current version of TFModel
	 * @access public
	 * @static
	 * @return string
	 */
	static public function getVersion(){return self::$_TF_MODEL_VERSION;}
}
