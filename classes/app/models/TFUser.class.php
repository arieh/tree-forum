<?php
/**
 * this class handles the basic user operations for this library. 
 */
class TFUser extends TFModel{
	
	/**
	 * @param TFUser a sigelton for the user
	 * @access private
	 * @static
	 */
    static private $_instance = null;
    
    /**
     * @param int user id
     * @access private
	 * @static 
     */
    static private $_id = 1;
	
	/**
	 * @param bool whether to set the user to debug mode
	 * @access private
	 * @static
	 */
	static private $_is_debug = false;
	
	/**
	 * @param string user name
	 * @access protected
	 */	    
    protected $_name = 'Guest';
    
    /**
     * @param string user email
     * @access protected
     */
    protected $_email = '';
    
    /**
     * @param array a list of permission ids
     * @access protected
     */
    protected $_permission_ids = array();
    
    /**
     * whether user is logged in
     * @access public
     * @return bool
     * @static
     */
    static public function isLoggedIn(){
    	return (self::$_id>1);
    } 
    
    /**
     * logs the user out
     */
    static public function logOut(){
    	self::setId(1);
    }
    
    /**
     * sets the user's id. if an instance exists resets it.
     * 	@param int $id new user id
     * @access public
     * @static
     */
    static public function setId($id){
    	if (self::$_id != $id){
    		self::$_id = $id;
    		if (self::$_instance) self::$_instance = $_SESSION['TFUser'] = null;
    	}
    }
    
    /**
     * returns user id
     * @access public
     * @return int 
     * @static
     */
    static public function getId(){
    	return self::$_id;
    }
    
    /**
     * a factory method for singelton pattern
     * @access public
     * @return TFUser
     * @static
     */
    static public function getInstance(){
    	if (self::$_instance instanceof TFUser){
    		return self::$_instance;
    	}
    	
    	if (!isset($_SESSION)){
    		session_start();
    		session_regenerate_id();
    	}
    	if (isset($_SESSION['TFUser']) && $_SESSION['TFUser'] instanceof TFUser) self::$_instance = $_SESSION['TFUser'];
    	
    	if (!self::$_instance || self::$_instance->getId()!=self::$_id) self::$_instance = $_SESSION['TFUser'] = new TFUser();
    	else{
    		self::$_instance = $_SESSION['TFUser'] = new TFUser();
		}
    	
    	return self::$_instance;
    }
    
    /**
     * sets debug mode. if debug mode changes resets instance.
     * 	@param bool $on whether to turn on debug mode or not
     * @access public
     * @static
     */
    static public function setDebug($on=false){
    	if (self::$_is_debug != (bool) $on){
    		self::$_is_debug = (bool) $on;
    		self::$_instance = $_SESSION['TFUser'] = null;
    	}
    }
    
    /**
     * constructor for the class
     * @access private (not realy, but should be)
     */
   	function __construct(){
		$arr = array('debug'=>self::$_is_debug);    	
    	parent::__construct($arr);
    	if (!$this->doesUserExist(self::getId())) throw new TFUserException('invalid user id');
    	$this->retrieveUserInfo();
    	$this->retrievePermissionIds();
    }
    
   	/**
   	 * retrieves the user id from the database
   	 * @access private
   	 */
    private function retrieveUserInfo(){
    	$info = $this->_link->select('users',array(),array('id'=>self::getId()),true,$this->isDebug());
    	$this->_name = $info['name'];
    	$this->_email = $info['email'];
    }
    
    /**
     * retrieve the permission ids for this user
     * @access private
     */
    private function retrievePermissionIds(){
    	$id = self::getId();
    	$perms = $this->_link->select('users_permisions',array('permision_id'),array('user_id'=>$id),false,$this->isDebug());
    	foreach ($perms as $per) $this->_permission_ids[]=$per['permision_id'];
    }
    
    private function doesUserExist($id){
    	return ($this->_link->countFields('users',array('id'=>$id),$this->isDebug())>0);
    }
}

class TFUserException extends TFModelException{}
?>