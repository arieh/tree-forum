<?php
/**
 * this class handles the basic user operations for this library. 
 */
class TFUser{
	
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
     * whether user is logged in
     * @access public
     * @return bool
     * @static
     */
    static public function isLoggedIn(){
    	return (self::$_instance->getId()>1);
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
    		self::regenerate();
    	}
    }
    
    /**
     * returns user id
     * @access public
     * @return int 
     * @static
     */
    static public function getId(){
    	return self::getInstance()->getId();
    }
    
    /**
     * a factory method for singelton pattern
     * @access public
     * @return TFUser
     * @static
     */
    static public function getInstance(){
    	if (self::$_instance instanceof TFUserM){
    		return self::$_instance;
    	}
    	if (!isset($_SESSION)){
    		session_start();
    		session_regenerate_id();
    	}
    	
    	if (isset($_SESSION['TFUser']) && is_numeric($_SESSION['TFUser'])){
    		self::$_id = $_SESSION['TFUser'];
    	}
		self::regenerate();
    	
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
    		self::regenerate();
    	}
    }
    
    static function isDebug(){
    	return self::$_is_debug;
    }
    
    /**
     * re-creates the user instance
     * @access public
     * @static
     */
    static public function regenerate(){
    	self::$_instance =  new TFUserM(array('id'=>self::$_id,'debug'=>self::isDebug()));
    	$_SESSION['TFUser'] = self::$_id;
    }
}

class TFUserException extends Exception{}
?>