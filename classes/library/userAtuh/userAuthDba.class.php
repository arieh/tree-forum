<?php
/*
	Permission is hereby granted, free of charge, to any person obtaining
	a copy of this software and associated documentation files (the
	"Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:
	
	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
	CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
	TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
require_once('dbaInterface.class.php');

class userAuthDba implements dbaInterface {

	/**
	 * @var string location of the config file
	 */
	private $_configLocation = "../configs/userAtuh.ini";
	
	/**
	 * @var mysql_link a link to the database
	 * @access private
	 */
	private $_link = null;
	
	/**
	 * @var array configurations for the database
	 * @access private
	 */
	 
	private $_configs = array();
	
	
	/**
	 * a constructor for the object
	 * 
	 * @param mysql_link $link a link to the database
	 */
    public function __construct(&$link,$ini=''){
    	$this->_link = $link;
    	if (is_string($ini) && @file_exists($ini)) $this->_configs = parse_ini_file($ini);
    	else $this->_configs = parse_ini_file($this->_configLocation);
    }
    
    /**
     * insertes the key that was generated for this session into the database
     * 
     * @param string $key a random key
     * 
     * @access public
     */
    public function insertKey($key){
    	$ip = getenv('REMOTE_ADDR');
    	
    	$sql = "SELECT COUNT(*) as `c` FROM `temp-keys` WHERE `ip`='$ip'";
    	
    	$query = mysql_query($sql,$this->_link) or die(mysql_error());
    	$result = mysql_fetch_assoc($query);
    	
    	if ((int)$result['c']==0){
    		$sql = "INSERT INTO `temp-keys`(`ip`,`key`) VALUES ('$ip','$key')";
    	}else{
    		$sql = "UPDATE `temp-keys` SET `key`='$key' WHERE `ip`='$ip'";
    	}
    	if (_DEBUG_) fb($sql);
    	mysql_query($sql) or die(mysql_error());
    }    
    /**
     * retrives the last key that was stored for a specific ip
     * 
     * @param string $ip an ip address
     * @access public
     * @return string the key that was generated for this ip 
     */
    public function getKeyFromDB($ip){
    	
    	if ($this->keyExists($ip)==false) return false;
    	
    	$sql    = "SELECT `key` FROM `temp-keys` WHERE `ip`='$ip'";
    	if (_DEBUG_) fb($sql);
    	$query  = mysql_query($sql,$this->_link) or die(mysql_error());
    	$result = mysql_fetch_assoc($query);
    	return $result['key'];
    }
    
    /**
     * checks if a specific user exists
     * 
     * @param string $name a user name
     * 
     * @uses $GLOBALS['_suCnfgs_'] a global variable that holds database structure information
     * @access public
     * @return bool whether a user with this name exists
     */
    public function userExists($name){
    	$dbConfigs = &$this->_configs;
    	if ($dbConfigs['escapeInput']>0) $name = $dbConfigs['escapeFunction']($name);
    	$sql = "SELECT COUNT(*) as `c`
					FROM `".$dbConfigs['tableName']."`
					WHERE `".$dbConfigs['nameField']."` = '$name'";
    	if (_DEBUG_) fb($sql);
    	$query = mysql_query($sql) or die(mysql_error());
    	$result = mysql_fetch_assoc($query);
    	return ((int)$result['c']>0);
    }
    
    /**
     * retrives a password from the database for a specific user
     *
     * @param string $name a user name
     * 
     * @uses $GLOBALS['_suCnfgs_'] a global variable that holds database structure information
     * @access public
     * @return string the password for that user
     */
    public function getPass($name){
    	$dbConfigs = &$this->_configs;
    	
    	if ($dbConfigs['escapeInput']) $name = $dbConfigs['escapeFunction']($name);
    	
    	$sql = "SELECT `".$dbConfigs['passField']."` as `pass`
					FROM `".$dbConfigs['tableName']."`
					WHERE `".$dbConfigs['nameField']."` = '$name'";
		if (_DEBUG_) fb($sql);
    	$query = mysql_query($sql,$this->_link) or die(mysql_error());
    	$result = mysql_fetch_assoc($query);
    	return $result['pass'];
    }
    
    protected function keyExists($ip){
    	$sql = "SELECT COUNT(*) as `c` FROM `temp-keys` WHERE `ip`='$ip'";
    	if (_DEBUG_) fb($sql);
    	$query = mysql_query($sql,$this->_link) or die(mysql_error());
    	$result = mysql_fetch_assoc($query);
    	return ((int)$result['c']>0);
    }
}
?>