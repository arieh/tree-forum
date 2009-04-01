<?php
class ModelException extends Exception{}

class Model{
	/**
	 * @var NewDao a database handler object
	 * @access protected
	 */
	protected $_link = null;
	
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
	 * a constructor method for the object. sets the databse holder and the model options
	 * 	@param array $options paramaters for the model
	 * 
	 * @access public
	 * @return void 
	 */
	public function __construct($options = array()){
		$this->_link = NewDao::getInstance();	
		
		foreach ($options as $name => $value){
			if (is_string($name)) $this->setOption($name,$value);
		}
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
	 * returns a JSON representation of the object
	 * @access public
	 * @return string
	 */
	public function toJSON(){}
	
	/**
	 * executes the model's logic
	 * @access public
	 */
	public function execute(){}
	
	/**
	 * sets default getters and setters (getParamname(), setParamname())
	 */
	public function __call($name,$args){
		$action = substr($name,0,3);
		$var = ".".strtolower(substr($name,3));
		if (!isset($this->$var) && !property_exists($this,$var)) throw new ModelException("No Method Exists:".$name);
		switch ($action){
			case 'get':
				if (is_array($this->$var)) return new modelResult($this->$var);
				return $this->$var;
			break;
			case 'set':
				$this->$var = $args[0];
			break;
		}
	}
}
