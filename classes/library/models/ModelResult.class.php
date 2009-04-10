<?php
class ModelResultException extends Exception{}
/**
 * this class is to be used for wrapping model reuslts from the database,
 * to be accessible as objects.
 * 
 * paramaters will be accessible either as their lower-cased key name,
 * or as access functions as getParamname
 */
class ModelResult implements Iterator{
	/**
	 * @param array the array containing passed variables
	 * @access private
	 */
	private $_array = array();
	
	/**
	 * @param int current array position (used for array traversal)
	 * @access private
	 */
	private $_position = 0;
	
	/**
	 * @param array list of array keys  (used for array traversal)
	 * @access private
	 */
	private $_keys = array();
	
	/**
	 * @param mixed current array pointer  (used for array traversal)
	 * @access private
	 */
	private $_current = null;
	
	/**
	 * @param string name of singular form of the object's array (used to create nice synonim for pop)
	 * @access 
	 */
	private $_singular = '';
	
	/**
	 * a cunstructor function
	 * 
	 * @param array $arr a 1 dimentional assotiative array
	 * 
	 * @access public
	 */
	public function __construct($arr, $singular = false){
		if ($singular && is_string($singular) && strlen(trim($singular))>0) $this->_singular = trim($singular);
		$this->_array = $arr;
		$this->_keys = array_keys($arr);
		$this->_current = current($this->_array);
		
	}
	
	/**
	 * an access function to unknown functions
	 * 
	 * it recives funtions with the folowing name-pattern "getParamnane" where param name a
	 * wanted paramater that was recived from the database
	 * 
	 * @param string function name
	 * @param mixed  passed arguments
	 * 
	 * @access public
	 * @return mixed whatever the paramater of the name is
	 */
	public function __call($name,$pars){
		if (substr($name,0,3)=='get'){
			$vname = strtolower(substr($name,3));
			if ($vname == $this->_singular) return $this->pop();
			if (array_key_exists($vname,$this->_array)){
				if (is_array($this->_array[$vname])) return new modelResult($this->_array[$vname]);
				/*if (is_array($this->_array[$vname])){
					foreach ($this->_array[$vname] as &$val) $val = new modelResult($val);
					return $this->_array[$vname];
				}*/
				return $this->_array[$vname];
			}
		}
		return false;
	}
	
	/**
	 * equivilant of the count method for arrays
	 * @return int
	 */
	public function getCount(){
		return count($this->_array);
	}
	
	/**
	 * pops the first variable of the inner array
	 * 	@param bool $MdlRslt whether to returns arrays as model results
	 * @return mixed
	 */
	 public function pop($MdlRslt=true){
	 	$var = array_pop($this->_array);
	 	return (is_array($var) && $MdlRslt) ? new ModelResult($var) : $var;
	 }
	
	public function __toString(){return json_encode($this->_array);}
	
	public function rewind(){
		$this->_position = 0;
		$this->_current = reset($this->_array);
	}
	
	public function current() {
        if (is_array($this->_current)) return new ModelResult($this->_current);
        return $this->_current;
    }
    
    public function key() {
        return $this->_keys[$this->position];
    }
    
    public function next() {
        $this->_position++;
        $this->_current = next($this->_array);
    }
    
    public function valid() {
        return isset($this->_keys[$this->_position]);
    }
}