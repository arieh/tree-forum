<?php

class TFUserM extends TFModel{
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
     * 
     */
    protected $_id = 1;
    
    /**
     * constructor for the class
     * @access private (not realy, but should be)
     */
   	public function __construct($options=array()){
    	parent::__construct($options);
    	if (isset($options['id'])) $this->_id = $options['id'];
    	if (isset($options['debug'])) $this->_debug = $options['debug'];
    	if (!$this->doesUserExist($this->getId())) throw new TFUserMException('invalid user id');
    	$this->retrieveUserInfo();
    	$this->retrievePermissionIds();
    }
    
    /*public function getId(){return $this->_id;}
    
    public function getName(){return $this->_name;}
    
    public function getEmail(){return $this->_email;}
    
    public function isDebug(){return $this->_debug;}*/
    
    public function getPermissionIds(){return $this->_permission_ids;}
    
   	/**
   	 * retrieves the user id from the database
   	 * @access private
   	 */
    private function retrieveUserInfo(){
    	$info = NewDao::getInstance()->select('users',array(),array('id'=>$this->getId()),true,$this->isDebug());
    	$this->_name = $info['name'];
    	$this->_email = $info['email'];
    }
    
    /**
     * retrieve the permission ids for this user
     * @access private
     */
    private function retrievePermissionIds(){
    	$id = $this->getId();
    	$perms = NewDao::getInstance()->select('users_permisions',array('permision_id'),array('user_id'=>$id),false,$this->isDebug());
    	foreach ($perms as $per) $this->_permission_ids[]=$per['permision_id'];
    }
    
    private function doesUserExist($id){
    	return (NewDao::getInstance()->countFields('users',array('id'=>$id),$this->isDebug())>0);
    }
}

class TFUserMException extends TFModelException {}